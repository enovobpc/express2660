<div class="modal" id="modal-shipment-payment" style="
    z-index: 10000;
    margin-top: 0;
    padding-top: 80px;
    background: rgb(255 255 255 / 53%);">
    {{ Form::open() }}
    <div class="modal-dialog modal-xs" style="width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Efetuar Pagamento</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <ul class="list-inline">
                            <li>
                                <h4 class="m-0 text-center" style="margin-top: -10px;font-weight: bold;">
                                    <small style="color: #222;">Subtotal</small><br />
                                    <span class="billing-subtotal">{{ money(@$shipment->billing_subtotal) }}</span>
                                    <span>{{ Setting::get('app_currency') }}</span>
                                </h4>
                            </li>
                            <li>
                                <h4 class="m-0 text-center" style="margin-top: -10px;font-weight: bold;">
                                    <small style="color: #222;">IVA</small><br />
                                    <span class="billing-vat">{{ money(@$shipment->billing_vat) }}</span>
                                    <span>{{ Setting::get('app_currency') }}</span>
                                </h4>
                            </li>
                            <li>
                                <h2 class="m-0 text-center " style="margin-top: -10px;font-weight: bold;">
                                    <small style="font-size: 16px;color: #222;">Total a Pagar</small><br />
                                    <span class="billing-total">{{ money(@$shipment->billing_total) }}</span>
                                    <span>{{ Setting::get('app_currency') }}</span>
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
                                        <img src="{{ asset('assets/img/default/wallet.svg') }}" style="height: 33px" />
                                    </label>
                                </li>
                                <li style="margin-right: 28px; margin-left: 25px;">
                                    <label>
                                        {{ Form::radio('payment_method', 'mb', false) }}
                                        <img src="{{ asset('assets/img/default/mb.svg') }}" style="height: 33px" />
                                    </label>
                                </li>
                                <li style="margin-right: 15px">
                                    <label>
                                        {{ Form::radio('payment_method', 'mbway', false) }}
                                        <img src="{{ asset('assets/img/default/mbway.svg') }}" style="height: 29px" />
                                    </label>
                                </li>
                                {{-- <li>
                                    <label>
                                        {{ Form::radio('payment_method', 'visa', false) }}
                                        <img src="{{ asset('assets/img/default/visa.svg') }}" style="height: 18px"/>
                                        <img src="{{ asset('assets/img/default/mastercard.svg') }}" style="height: 23px; margin-left: 5px"/>
                                    </label>
                                </li> --}}
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
                                        Disponível: <span class="wallet-amount">{{ money(Auth::guard('customer')->user()->wallet_balance, Setting::get('app_currency')) }}</span><br />
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
                                        {{ Form::select('card_year', yearsArr(date('Y'), date('Y') + 5), null, ['class' => 'form-control select2', 'placeholder' => 'Ano']) }}
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
                <div class="row loading-payment" style="display: none; padding: 30px 50px 15px;">
                    <div class="text-center">
                        <h1 class="m-0"><i class="fas fa-spin fa-circle-notch"></i></h1>
                        <h4>A processar...</h4>
                    </div>
                </div>
                <div class="row result-payment" style="display: none; padding: 30px 50px 15px;"></div>
            </div>
            <div class="modal-footer">
                {{ Form::hidden('shipment_id', @$shipment->id) }}
                <button class="btn btn-secondary" class="close" id="btn-close" data-answer="0" type="button" style="display: none;">
                    {{ trans('account/global.word.close') }}
                </button>
                <button class="btn btn-default" id="btn-later" data-answer="0" type="button">
                    Pagar depois
                </button>
                <button class="btn btn-success" id="btn-pay" data-answer="1" type="button">
                    <i class="fas fa-check"></i> Efetuar Pagamento
                </button>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>

<style>
    .bootstrap-growl {
        z-index: 99999 !important;
    }
</style>

<script>
    var modalShipmentPayment = {
        show: function(trkid, subtotal, vat, total, paidCallback, cancelCallback) {
            $('#modal-remote-xl').hide();

            var $modal = $('#modal-shipment-payment');
            var $form = $('#modal-shipment-payment > form');

            // Resets the form
            $form[0].reset();

            $form.find('input').prop('disabled', false);
            $form.find('.result-payment').css('display', 'none');
            $form.find('#btn-pay').show();
            $('[data-payment]').hide();

            // Change pay amounts
            $('.billing-subtotal').text(subtotal);
            $('.billing-vat').text(vat);
            $('.billing-total').text(total);

            $modal.add('in').show();

            $('#modal-shipment-payment [data-answer]').unbind('click');
            $('#modal-shipment-payment [data-answer]').on('click', function(e) {
                if ($(this).data('answer') != '1') {
                    $modal.modal('hide');
                    cancelCallback();
                    return;
                }

                $modal.find('[name="shipment_id"]').val(trkid);

                var paymentMethod = $modal.find('[name="payment_method"]:checked').val();
                if (!paymentMethod) {
                    return Growl.error('Nenhum método selecionado.');
                }

                if (paymentMethod == 'mbway') {
                    if ($('[name="mbw_phone"]').val() == '') {
                        return Growl.error('Número telemóvel obrigatório.')
                    } else if ($('[name="mbw_phone"]').val().length < 9) {
                        return Growl.error('Número telemóvel inválido.')
                    }
                } else if (paymentMethod == 'visa') {
                    var curYear = new Date().getFullYear();
                    var curMonth = new Date().getMonth();

                    if ($('[name="card_no"]').val() == '' ||
                        $('[name="card_month"]').val() == '' ||
                        $('[name="card_year"]').val() == '' ||
                        $('[name="card_cvc"]').val() == '' ||
                        $('[name="card_first_name"]').val() == '' ||
                        $('[name="card_last_name"]').val() == '') {
                        return Growl.error('Preencha todos os dados do cartão')
                    } else if ($('[name="card_no"]').val().length < 16) {
                        return Growl.error('Número cartão inválido.')
                    } else if ($('[name="card_cvc"]').val().length < 3) {
                        return Growl.error('Código segurança inválido.')
                    } else if ($('[name="card_year"]').val() == curYear && $('[name="card_month"]').val() < curMonth) {
                        return Growl.error('Cartão Expirado.')
                    }
                }

                $('.loading-payment').show();
                $('[data-payment]').hide();

                $.post(ROUTE_SET_PAYMENT, $form.serialize(), function(data) {
                    if (!data.result) {
                        $('[data-payment="' + paymentMethod + '"]').show();
                        return Growl.error(data.feedback);
                    }

                    if (data.wallet) {
                        $('.wallet-amount').html(data.wallet)
                    }

                    if (data.html) {
                        $modal.find('.result-payment').html(data.html).show();
                    } else {
                        $('#modal-remote-xl').modal('hide');
                    }

                    // Disables payment options
                    $modal.find('.payment-options input').prop('disabled', 'true');

                    // Action buttons
                    $modal.find('#btn-close').show();
                    $modal.find('#btn-later').hide();
                    $modal.find('#btn-pay').hide();

                    if (paymentMethod == 'mbway') {
                        checkPayment(data);
                    }

                    Growl.success(data.feedback)
                }).fail(function() {
                    $('[data-payment="' + paymentMethod + '"]').show();
                    Growl.error500();
                }).always(function() {
                    $('.loading-payment').hide();
                });
            });
        },
        close: function(callback) {
            $('#modal-shipment-payment').modal('hide');
            callback();
        }
    };

    window.addEventListener('load', function() {
        $('.modal [name="payment_method"]').on('change', function(e) {
            var method = $(this).val();
            var $modal = $('#modal-shipment-payment');

            $('[data-payment]').hide();
            $modal.css('padding-top', '80px')

            $('[data-payment="' + method + '"]').show();

            if (method == 'wallet') {
                $modal.css('padding-top', '70px')
            } else if (method == 'mbway') {
                $modal.css('padding-top', '60px')
            } else if (method == 'visa') {
                $modal.css('padding-top', '30px')
            }
        });
    });

    function checkPayment(data) {
        var paymentChecker = setInterval(function() {
            $.post('{{ route('account.wallet.check.payment') }}', {
                id: data.id
            }, function(data) {
                if (data.paid && data.paid_at) {
                    $('.mbway-loading').hide();
                    $('.mbway-success').show();
                    $('.btn-conclude').hide();
                    $('.wallet-amount').html(data.wallet_amount);
                    clearInterval(paymentChecker);
                    return;
                } else if (data.timeout) {
                    $('.mbway-loading').hide();
                    $('.mbway-canceled').show();
                    $('.btn-conclude').hide();
                    $('.wallet-amount').html(data.wallet_amount);
                    clearInterval(paymentChecker);
                    return;
                }
            }).fail(function() {
                clearInterval(paymentChecker);
                return;
            });
        }, 3000);
    }
</script>
