<?php

/**
 * Created by PhpStorm.
 * User: usamaahmed
 * Date: 9/25/16
 * Time: 11:33 AM
 */
namespace App\Observers;

use App\Core\Services\Email\PrimaryEmailService;
use App\Events\NewOrder;
use App\Src\Coupon\Coupon;
use App\Src\Order\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderObservers
{
    /**
     * Listen to the Order created event.
     *
     * @param  Order $order
     * @return void
     */
    public function created(Order $order)
    {
        if (app()->environment() != 'local') {

            // do some events and job here , ( sending emails + recording some information about the order)
            // 1- Confirmation Email

            // 2- Invoice Email
//            $email->sendInvoice($order);
        }
        // consuming the coupon
        $coupon = Coupon::whereId($order->coupon_id)->update(['consumed' => true]);
        // removing the cache
        cache()->forget('coupon.' . Auth::id());

//        Mail::to($order->email)
//            ->queue(new NewsLetterCampaign($order));
//
//        Mail::to($order->email)
//            ->queue(new NewsLetterCampaign($order));
    }

    /**
     * Listen to the User deleting event.
     *
     * @param  User $user
     * @return void
     */
    public function deleting(User $user)
    {
        //
    }
}