<?php

namespace App\Http\Controllers\Frontend;

use App\Core\PrimaryController;
use App\Core\Services\PrimaryEmailService;
use App\Events\NewOrder;
use App\Http\Requests;
use App\Src\Cart\Cart;
use App\Src\Cart\ShippingManager;
use App\Src\Country\Country;
use App\Src\Coupon\Coupon;
use App\Src\Order\OrderRepository;
use App\Src\Product\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Validator;

class CheckoutController extends PrimaryController
{

    private $cart;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var Country
     */
    private $country;
    /**
     * @var ShippingManager
     */
    private $shippingManager;
    public $coupon;

    /**
     * CartController constructor.
     * @param Cart $cart
     * @param ProductRepository $productRepository
     * @param Country $country
     * @param ShippingManager $shippingManager
     */
    public function __construct(Cart $cart, ProductRepository $productRepository, Country $country, ShippingManager $shippingManager, Coupon $coupon)
    {
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->country = $country;
        $this->shippingManager = $shippingManager;
        $this->coupon = $coupon;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('shipping_country')) {

            Session::put('SHIPPING_COUNTRY', $request->shipping_country);

        } else {
            if (!Session::has('SHIPPING_COUNTRY')) {

                $validator = Validator::make($request->all(), [
                    'shipping_country' => 'required|numeric|exists:countries,id'
                ]);

                if ($validator->fails()) {
                    return redirect()->route('cart.index')
                        ->withErrors($validator)
                        ->withInput();
                }

                Session::put('SHIPPING_COUNTRY', $request->shipping_country);
            }
        }

        $shippingCountry = Session::get('SHIPPING_COUNTRY');

        // @todo : use actual logged in user
        $user = auth()->check() ? auth()->user() : auth()->loginUsingId(1);

        $cartItems = $this->cart->getItems();

        $products = $this->productRepository->model->has('product_meta')->whereIn('id', $cartItems->keys())->get();

        // Prepare the cart to display
        $cart = $this->cart->make($products);

        if (!count($cart->items)) {

            //@todo handle empty cart
            dd('cart empty');

        }

        $countries = $this->country->has('currency')->get();

        $shippingCountry = $countries->where('id', (int)$shippingCountry)->first();

        if (!$shippingCountry) {

            dd('no shipment to this country');
            // handle no shipment countries
        }

        $shippingCost = $this->shippingManager->calculateCost($cart->netWeight, $shippingCountry->name);

        if (Cache::has('coupon.' . Auth::id())) {

            $couponCache = Cache::get('coupon.' . Auth::id());

            $coupon = $this->coupon->where(['id' => $couponCache['id'], 'code' => $couponCache['code']])->first();

            $couponDiscountValue = ($coupon->value > 0) ? -(($coupon->value / 100) * $cart->grandTotal) : null;

            $amountAfterCoupon = ($coupon->value > 0) ? $cart->grandTotal + $couponDiscountValue : null;

        }

        $finalAmount = (isset($amountAfterCoupon) && $amountAfterCoupon > 0) ? $amountAfterCoupon + $shippingCost : $cart->grandTotal + $shippingCost;

        Session::put('ORDER', [
            'coupon_id' => (isset($coupon)) ? $coupon->id : 0,
            'couponQty' => (isset($coupon)) ? 1 : 0,
            'couponCode' => (isset($coupon)) ? $coupon->code : 0,
            'couponValue' => (isset($coupon)) ? abs($couponDiscountValue) : 0,
            'amount' => $cart->subTotal,
            'sale_amount' => $cart->grandTotal,
            'net_amount' => $finalAmount,
            'shippingCountry' => 'shippingCost.country.' . $shippingCountry->name,
            'shippingCost' => (isset($shippingCost) && $shippingCost > 0) ? $shippingCost : 0,
            'shippingQty' => 1
        ]);

        return view('frontend.modules.checkout.index',
            compact('user', 'cart', 'shippingCountry', 'shippingCost', 'finalAmount', 'coupon', 'couponDiscountValue', 'amountAfterCoupon'));
    }

    public function checkout(Request $request, OrderRepository $orderRepository)
    {
        $this->validate($request, [
            'shipping_country' => 'required|numeric|exists:countries,id'
        ]);

        $user = auth()->user();

        $cartItems = $this->cart->getItems();

        $products = $this->productRepository->model->has('product_meta')->whereIn('id', $cartItems->keys())->get();

        $cart = $this->cart->make($products);

        $shippingCountry = $this->country->find($request->shipping_country);

        $shippingCost = $this->shippingManager->calculateCost($cart->netWeight, $shippingCountry->name);

        $orderDetails = session()->get('ORDER');

        $address = '';
        //if shipping kuwait
        if ($request->shipping_country === '414') {
            $address .= 'Area ' . $request->area . ' ';
            $address .= 'Block ' . $request->block . ' ';
            $address .= 'Street ' . $request->street . ' ';
            $address .= 'Building ' . $request->building . ' ';
            $address .= 'Floor ' . $request->floor . ' ';
            $address .= 'Apartment ' . $request->apartment;
        } else {
            $address .= $request->address1 . ' ';
            $address .= $request->address2;
        }

        $order = $orderRepository->model->create([
            'status' => 'pending',
            'user_id' => $user->id,
            'country_id' => $request->shipping_country,
            'coupon_id' => $orderDetails['coupon_id'],
            'coupon_value' => $orderDetails['couponValue'],
            'amount' => $cart->subTotal,
            'shipping_cost' => $shippingCost,
            'sale_amount' => $orderDetails['sale_amount'],
            'net_amount' => $orderDetails['net_amount'],
            'email' => $request->email,
            'address' => $address,
            'payment_method' => $request->payment,
        ]);


        $cart->items->map(function ($item) use ($order) {
            $order->order_metas()->create([
                'product_id' => $item->id,
                'product_attribute_id' => $item->product_attribute_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'sale_price' => $item->sale_price,
            ]);
        });


        //cash case
        if ($request->payment == 'cash') {

            $this->cart->flushCart();

            return redirect()->to('/')->with('success', trans('general.message.order_created'));

        }
        // my_fatoorah case
        $paymentStatus = Event::fire(new NewOrder($cart, $orderDetails));


        // i stopped here
        dd($paymentStatus);
        /// whenever the paymend is done .. please do the following == get the response if success do what are mentioned below :::
        if ($paymentStatus[0]->responseMessage) {

            /*            // please refere to OrderObservers
                                - coupons consumed if applied
                           // firing event NewOrder
                                - emails
                                - payment
            */

//            - after saving the order + sending emails + consuming the coupon if exists - flush the whole cart session
            $this->cart->flushCart();

            return redirect()->secure($paymentStatus[0]->paymentURL);

        }

        return redirect('cart')->with('error', 'Not completed');
    }

}
