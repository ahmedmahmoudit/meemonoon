<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="check-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#checkut6">
                <span class="number">{{ $order }}</span>Order Review</a>
        </h4>
    </div>
    <div id="checkut6" class="panel-collapse collapse in">
        <div class="panel-body">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="table-responsive">
                    <table class="tablec">
                        <tr>
                            <td>Product Name</td>
                            <td>Price</td>
                            <td>Qty</td>
                            <td>Subtotal</td>
                        </tr>
                        @foreach($cart->items as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td><p class="tabletextp">{{ $product->sale_price }} KD</p></td>
                                <td>{{ $product->quantity }}</td>
                                <td><p class="tabletextp">{{ $product->grand_total }}  KD</p></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3">
                                <p class="tabletext">Total</p>
                                {!! (isset($coupon) && $coupon->value > 0) ? '<p class="tabletext">Coupon Value :</p>' : null !!}
                                {!! (isset($coupon) && $coupon->value > 0) ? '<p class="tabletext">After Coupon :</p>' : null !!}
                                <p class="tabletext">Shipping & Handling Fee :</p>
                                <p class="tabletext">Grand Total :</p>


                            </td>
                            <td>
                                <p class="tabletextp">{{ $cart->grandTotal }} KD</p>
                                {!! (isset($coupon) && $coupon->value > 0) ? '<p class="tabletextp">'.$couponDiscountValue .'KD</p>' : null !!}
                                {!! (isset($coupon) && $coupon->value > 0) ? '<p class="tabletextp">'.$amountAfterCoupon .'KD</p>' : null !!}
                                <p class="tabletextp">{{ $shippingCost }} KD ({{ $shippingCountry->name }})</p>
                                <p class="tabletextp">{{ $finalAmount }} KD</p>


                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="button-check">
                                    <div class="">
                                        <span class="left-btn"><a href="{{ action('Frontend\CartController@index') }}">Forgot an Item? Edit Your Cart</a></span>
                                        <button type="submit" class="btn right-btn custom-button">Continue</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>