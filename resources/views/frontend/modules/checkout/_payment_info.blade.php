<!-- Panel Default -->
<!-- Panel Default -->
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="check-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#checkut5">
                <span class="number">{{ $order }}</span>{{ trans('general.payment_information') }}</a>
        </h4>
    </div>
    <div id="checkut5" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-xs-3">
                <div class="form-group">
                    <label for="payment">{{ trans('general.my_fatorrah') }}</label>
                    <input type="radio" name="payment" checked="checked" value="my_fatoorah"/>
                </div>
            </div>
            <div class="col-xs-3">
                <div class="form-group">
                    <label for="payment">{{ trans('general.cash_on_delivery') }}</label>
                    <input type="radio" name="payment" value="cash"/>
                </div>
            </div>
            <p>
                <ol>
                <li>K-net,Visa,master card (processed by MyFatoorah)</li>
                <li>Cash on delivery. Other than Kuwait: Visa,master card (Processed by MyFatoorah).</li>
            </ol>

            </p>
        </div>
    </div>
</div>
</div><!-- End Panel Default -->