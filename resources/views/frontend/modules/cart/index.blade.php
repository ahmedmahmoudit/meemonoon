@extends('frontend.layouts.master')
@section('body')
    <div class="feature-product-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="feature-headline section-heading text-center">
                        <h2>{{ trans('cart.shopping_cart') }}</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @if(!$cart->getItemsCount() > 0)
                        <div class="text-center">
                            <h1>{{ trans('cart.empty') }}</h1>
                            <p><a href={{route('home')}}> {{trans('cart.browse')}} </a></p>
                        </div>
                        @else
                                <!-- table start -->
                        {!! Form::open(['action' => ['Frontend\CartController@updateCart'], 'method' => 'post'], ['class'=>'']) !!}
                        <div class="table-content table-responsive">
                            <table>
                                <thead>
                                <tr>
                                    <th class="product-remove">{{ trans('cart.remove') }}</th>
                                    <th class="product-thumbnail">{{ trans('cart.image') }}</th>
                                    <th class="product-name">{{ trans('cart.product_name') }}</th>
                                    <th class="real-product-price">{{ trans('cart.unit_price') }}</th>
                                    <th class="product-quantity">{{ trans('cart.qty') }}</th>
                                    <th class="product-subtotal">{{ trans('cart.sub_total') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($cart->items as $product)
                                    <tr>
                                        <td class="product-remove">
                                            <a href="{{ route('cart.remove',$product->id) }}">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>

                                        <td class="product-thumbnail">
                                            <a href="#">
                                                <img src="{{ url('img/uploads/thumbnail/').'/'.$product->image}}"
                                                     width="140" height="180" alt=""/>
                                            </a>
                                        </td>

                                        <td class="product-name">
                                            <a href="#">{{ $product->name }}</a>
                                        </td>

                                        <td class="real-product-price">
                                            @if($product->price !== $product->sale_price)
                                                <span class="sale_price">{{$product->price}} KD </span>
                                            @endif
                                            <span class="amounte">{{ $product->sale_price }} KD </span>
                                        </td>

                                        <td class="product-quantity">
                                            <input type="number" name="quantity_{{$product->id}}"
                                                   value="{{ $product->quantity }}"/>
                                        </td>

                                        <td class="product-subtotal">{{ $product->grand_total }} KD</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="cart-s-btn">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12">
                                        <div class="buttons-cart">
                                            <a href="{{ route('home') }}">{{ trans('cart.continue_shopping') }}</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12">
                                        <div class="buttons-cart button-cart-right">
                                        <span class="shopping-btn">
                                            <button type="submit"
                                                    class="btn custom-button">{{ trans('cart.update_shopping_cart') }}
                                            </button>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}


                                <!-- Panel Default -->
                        <!-- Panel Default -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="check-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#checkut5">
                                        <span class="number"></span>{{ trans('cart.have_a_coupon') }}</a>
                                </h4>
                            </div>
                            @if(Auth::user())
                                <div id="checkut5" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="hidden" id="grandTotal"
                                             value="{!! $cart->grandTotal  !!}">{!! $cart->grandTotal !!}</div>
                                        <div class="hidden" id="api_token">{!! auth()->user()->api_token !!}</div>
                                        <div class="col-lg-12 col-md-12 col-sm-12" id="couponApp">

                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div><!-- End Panel Default -->
                        <!-- table end -->
                        <!-- place selection start -->
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 pull-right">
                                <div class="place-section">
                                    {!! Form::open(['route' => ['cart.checkout'], 'method' => 'POST'], ['class'=>'']) !!}
                                    <div class="place-headline">
                                        <h4>Estimate Shipping and Tax</h4>
                                        <P>{{ trans('cart.enter_ur_destination') }}</P>
                                    </div>
                                    <div class="search-categori">
                                        <h5>Country</h5>
                                        <div class="categori">
                                            {{ Form::select('shipping_country',$countries,$shippingCountry,['class'=>'orderby','placeholder'=>'Choose Shipping Country']) }}
                                        </div>
                                    </div>
                                    <div class="rate-subtotal">
                                        {{--<h4>Subtotal <span>{{ $cart->subTotal }} KD</span></h4>--}}
                                        <h2>{{ trans('general.grand_total') }}
                                            <span>{{ $cart->grandTotal }} {{ trans('currency.kd') }}</span></h2>
                                        <button type="submit"
                                                class="col-lg-12 btn custom-button">{{ trans('cart.proceed_to_checkout') }}
                                        </button>
                                    </div>
                                    {!! Form::close() !!}


                                </div>

                            </div>
                        </div>

                        <div class="row" style="margin-top: 30px">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 pull-right">
                                <div class="place-section">
                                    {!! Form::open(['route' => 'cart.clear', 'method' => 'Post'], ['class'=>'']) !!}
                                    <button type="submit"
                                            class="col-lg-12 btn custom-button">{{ trans('cart.clear_shopping_cart') }}
                                        clear shopping cart
                                    </button>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>

                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@if(Auth::check())
@section('customScripts')
    @parent
    {{--REACT COUPON APP HERE--}}
    <script type="text/javascript" src="{{ asset('js/coupon-app.js') }}"></script>
@endsection
@endif
