<div id="modal-shipment-payment">    
    {{ Form::open() }}

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Efetuar Pagamento #{{$shipment->tracking_code}}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <ul class="list-inline">
                            <li>
                                <h4 class="m-0 text-center" style="margin-top: -10px;font-weight: bold;">
                                    <small style="color: #222;">Subtotal</small><br/>
                                    <span class="billing-subtotal">{{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}</span>
                                </h4>
                            </li>
                            <li>
                                <h4 class="m-0 text-center" style="margin-top: -10px;font-weight: bold;">
                                    <small style="color: #222;">IVA</small><br/>
                                    <span class="billing-vat">{{ money($shipment->billing_vat, Setting::get('app_currency')) }}</span>
                                </h4>
                            </li>
                            <li>
                                <h2 class="m-0 text-center " style="margin-top: -10px;font-weight: bold;">
                                    <small style="font-size: 16px;color: #222;">Total a Pagar</small><br/>
                                    <span class="billing-total">{{ money($shipment->billing_total, Setting::get('app_currency')) }}</span>
                                </h2>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row payment-options">
                    <div class="col-sm-12">
                        <label style="font-weight: normal">Escolha uma forma de pagamento.</label>
                        <div style="border: 1px solid #999;border-radius: 4px;">
                            <ul class="list-inline m-t-10 text-center">
                                <li>
                                    <label>
                                        {{ Form::radio('payment_method', 'wallet', false) }}
                                        <img src="{{ asset('assets/img/default/wallet.svg') }}" style="height: 33px"/>
                                    </label>
                                </li>
                                <li style="margin-right: 28px; margin-left: 25px;">
                                    <label>
                                        {{ Form::radio('payment_method', 'mb', false) }}
                                        <img src="{{ asset('assets/img/default/mb.svg') }}" style="height: 33px"/>
                                    </label>
                                </li>
                                <li style="margin-right: 15px">
                                    <label>
                                        {{ Form::radio('payment_method', 'mbway', false) }}
                                        <img src="{{ asset('assets/img/default/mbway.svg') }}" style="height: 29px"/>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        {{ Form::radio('payment_method', 'visa', false) }}
                                        <img src="{{ asset('assets/img/default/visa.svg') }}" style="height: 18px"/>
                                        <img src="{{ asset('assets/img/default/mastercard.svg') }}" style="height: 23px; margin-left: 5px"/>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row" data-payment="wallet" style="display: none; padding: 30px 50px 15px;">
                    <div class="col-sm-10 col-sm-offset-1">
                        <table>
                            <tr>
                                <td>
                                    <img src="{{ asset('assets/img/default/wallet.svg') }}" style="height: 75px; margin-right: 15px;">
                                </td>
                                <td>
                                    <h4 class="bold">Saldo de Conta</h4>
                                    <p style="line-height: 27px;">
                                        Disponível: <span class="mb-ref">{{ money($customer->wallet_balance, Setting::get('app_currency')) }}</span><br/>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row" data-payment="mb" style="display: none; padding: 30px 50px 15px;">
                    <div class="col-sm-10 col-sm-offset-1">
                        <p class="text-muted text-center">O pedido será aceite e processado após recebimento do pagamento.</p>
                    </div>
                </div>
                <div class="row" data-payment="mbway" style="display: none; padding: 30px 50px 15px;">
                    <div class="col-sm-4 text-right">
                        <img src="{{ asset('assets/img/default/mbway.svg') }}" style="height: 38px;text-align: right;margin-right: 15px;">
                    </div>
                    <div class="col-sm-7">
                        <div class="form-group">
                            <div class="input-group input-group-lg">
                                <div class="input-group-addon" style="background: transparent">
                                    <i class="fas fa-mobile-alt fs-20"></i>
                                </div>
                                {{ Form::text('mbw_phone', null, ['class' => 'form-control phone', 'maxlength' => 9, 'placeholder' => trans('account/global.word.phone')]) }}
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row" data-payment="visa" style="display: none; padding: 30px 20px 0;">
                    <div class="col-sm-4 text-right">
                        <img src="{{ asset('assets/img/default/visa.svg') }}" style="height: 25px;text-align: right;margin-right: 15px;margin-bottom: 10px;margin-top: 35px;">
                        <img src="{{ asset('assets/img/default/mastercard.svg') }}" style="height: 38px; margin-right: 25px;">
                    </div>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        {{ Form::text('card_no', null, ['class' => 'form-control nospace number', 'maxlength' => 16, 'placeholder' => 'Número Cartão']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-8 p-l-0">
                                <div class="row row-0">
                                    <div class="col-sm-8">
                                        {{ Form::select('card_month', trans('datetime.list-month'), null, ['class' => 'form-control select2', 'placeholder' => 'Mês']) }}
                                    </div>
                                    <div class="col-sm-4">
                                        {{ Form::select('card_year', yearsArr(date('Y'), date('Y')+5), null, ['class' => 'form-control select2', 'placeholder' => 'Ano']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {{ Form::text('card_cvc', null, ['class' => 'form-control number', 'maxlength' => 3, 'placeholder' => 'CVC']) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::text('card_first_name', null, ['class' => 'form-control p-r-10', 'maxlength' => 25, 'placeholder' => 'Nome']) }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group p-l-10">
                                    {{ Form::text('card_last_name', null, ['class' => 'form-control p-l-10', 'maxlength' => 25, 'placeholder' => 'Apelido']) }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row loading-payment" style="display: none; padding: 30px 50px 15px;"></div>
                <div class="row result-payment" style="display: none; padding: 30px 50px 15px;"></div>
            </div>
            <div class="modal-footer">
                {{ Form::hidden('shipment_id', @$shipment->id) }}
                <button id="btn-close" type="button" class="btn btn-secondary" class="close" data-dismiss="modal">
                    {{ trans('account/global.word.close') }}
                </button>
                <button id="btn-pay" type="button" class="btn btn-success" data-answer="1">
                    <i class="fas fa-check"></i> Efetuar Pagamento
                </button>
            </div>
        </div>
    
    {{ Form::close() }}
</div>

<script>
$('.modal [name="payment_method"]').on('change', function(){
    console.log("ok");
    var method = $(this).val();
    $('[data-payment]').hide();
    // $('#modal-shipment-payment').css('padding-top', '80px')

    $('[data-payment="'+method+'"]').show();

    // if(method == 'wallet') {
    //     $('#modal-shipment-payment').css('padding-top', '70px')
    // } else if(method == 'mbway') {
    //     $('#modal-shipment-payment').css('padding-top', '60px')
    // } else if(method == 'visa') {
    //     $('#modal-shipment-payment').css('padding-top', '30px')
    // }
})


</script>


