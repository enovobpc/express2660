<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title">Adicionar Carregamento de Conta</h4>
</div>
<div class="modal-body">
    <div class="step-01" style="display: non">
        <div class="row">
            <div class="col-sm-12">
                <label style="font-weight: normal">Escolha um valor para carregar</label>
                <div style="margin-bottom: 30px">
                    <ul class="list-inline m-t-10 select-amount">
                        @foreach($amounts as $amount)
                        <li>
                            <label>
                                {{ Form::radio('amount_pack', $amount) }}
                                <h4 style="margin: 0; display: inline-block">{{ $amount }}{{ Setting::get('app_currency') }}</h4>
                            </label>
                        </li>
                        @endforeach
                        <li>
                            <label>
                                {{ Form::radio('amount_pack', 'other') }}
                                <h4 style="margin: 0; display: inline-block">Outro</h4>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-12 add-custom-amount" style="margin-top: -20px; margin-bottom: 20px; display: none">
                <div class="form-group is-required">
                    {{ Form::label('amount', 'Escolha um valor a carregar') }}
                    <div class="input-group">
                        {{ Form::text('amount', null, ['class' => 'form-control decimal input-lg', 'required']) }}
                        <div class="input-group-addon">
                            {{ Setting::get('app_currency') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <label style="font-weight: normal">Escolha uma forma de pagamento.</label>
                <div style="border: 1px solid #999; border-radius: 4px;">
                    @if(Setting::get('wallet_payment_methods'))
                    <ul class="list-inline m-t-10">
                        @if($customer->billing_country == 'pt')
                            @if(in_array('mb', Setting::get('wallet_payment_methods')))
                                <li style="margin-right: 28px; margin-left: 25px;">
                                    <label>
                                        {{ Form::radio('payment_method', 'mb', true) }}
                                        <img src="{{ asset('assets/img/default/mb.svg') }}" style="height: 33px"/>
                                    </label>
                                </li>
                            @endif
                            @if(in_array('mbway', Setting::get('wallet_payment_methods')))
                                <li style="margin-right: 15px">
                                    <label>
                                        {{ Form::radio('payment_method', 'mbway') }}
                                        <img src="{{ asset('assets/img/default/mbway.svg') }}" style="height: 29px"/>
                                    </label>
                                </li>
                            @endif
                        @endif
                        @if(in_array('visa', Setting::get('wallet_payment_methods')))
                        <li>
                            <label>
                                {{ Form::radio('payment_method', 'visa') }}
                                <img src="{{ asset('assets/img/default/visa.svg') }}" style="height: 18px"/>
                                <img src="{{ asset('assets/img/default/mastercard.svg') }}" style="height: 23px; margin-left: 5px"/>
                            </label>
                        </li>
                        @endif
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="step-mb" style="display: none">
        <div class="text-center">
            <img src="{{ asset('assets/img/default/mb.svg') }}" style="height: 40px"/>
            <h4 class="bold">Carregamento por Multibanco</h4>
        </div>
        <hr/>
        <div class="text-center fs-15 m-b-20 mb-loading">
            <i class="fas fa-spin fa-circle-notch"></i> A gerar dados de pagamento...
        </div>
        <div class="mb-details" style="display: none">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <ul class="list-unstyled fs-16 m-0">
                        <li class="m-b-4"><b>Entidade:</b> <span class="mb-entity">22356</span></li>
                        <li class="m-b-4"><b>Referência:</b> <span class="mb-reference">223 156 032</span></li>
                        <li class="m-b-4"><b>Montante:</b> <span class="mb-amount">23.45</span></li>
                    </ul>
                </div>
            </div>
            <hr/>
            <p class="text-muted" style="font-size: 13px">
                A referência está válida para pgamento durante 48h.<br/>O talão emitido faz prova de pagamento.
            </p>
        </div>
    </div>
    <div class="step-cc" style="display: none">
        <div class="text-center">
            <img src="{{ asset('assets/img/default/visa.svg') }}" style="height: 27px"/>
            <img src="{{ asset('assets/img/default/mastercard.svg') }}" style="height: 32px; margin-left: 5px"/>
            <h4 class="bold">Carregamento por Visa/Mastercard</h4>
        </div>
        <hr/>
        <div class="cc-details">
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('first_name', 'Primeiro Nome') }}
                        {{ Form::text('first_name', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('last_name', 'Último Nome') }}
                        {{ Form::text('last_name', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {{ Form::label('card', 'Número do cartão') }}
                        {{ Form::text('card', '4188530033090061', ['class' => 'form-control number']) }}
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group">
                        {{ Form::label('validity', 'Validade') }}
                        <div class="row row-5">
                            <div class="col-sm-8">
                                {{ Form::select('validity_m', ['' => ''] + trans('datetime.list-month'), null, ['class' => 'form-control select2', 'data-placeholder' => 'Mês']) }}
                            </div>
                            <div class="col-sm-4">
                                {{ Form::select('validity_y', ['' => ''] + listArr(date('Y'), date('Y') + 10), null, ['class' => 'form-control select2', 'data-placeholder' => 'Ano']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('cvc', 'CVC') }}
                        {{ Form::text('cvc', 351, ['class' => 'form-control number', 'maxlength' => 3]) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="cc-loading text-center" style="display: none">
            <h4 class="m-t-0"><i class="fas fa-spin fa-circle-notch"></i> A confirmar pagamento...</h4>
            <p>Vai ser redirecionado para outra página dentro de momentos.</p>
        </div>
        <div class="cc-canceled text-center" style="display: none">
            <h4 class="m-t-0 text-red"><i class="fas fa-exclamation-triangle"></i> Pagamento recusado</h4>
            <p>Não foi possível proceder ao pagamento.</p>
        </div>
    </div>
    <div class="step-mbw" style="display: none">
        <div class="text-center">
            <img src="{{ asset('assets/img/default/mbway.svg') }}" style="height: 45px"/>
            <h4 class="bold">Carregamento por MB Way</h4>
        </div>
        <hr/>
        <div class="mbway-details">
            <div class="form-group">
                {{ Form::label('phone', 'Nº de telemóvel para pagamento') }}
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fas fa-fw fa-mobile-alt"></i> +351
                    </div>
                    {{ Form::text('phone', $auth->mobile, ['class' => 'form-control input-lg phone', 'maxlength' => 9]) }}
                </div>
            </div>
            <p class="text-muted" style="font-size: 13px">
                Vamos enviar uma notificação através da aplicação MB WAY para o telemóvel que indicou para confirmação do pagamento.
            </p>
        </div>
        <div class="mbway-loading text-center" style="display: none">
            <h4 class="m-t-0"><i class="fas fa-spin fa-circle-notch"></i> Pagamento pendente <span class="mbwtimer">05:00</span></h4>
            <p>Aceite o pagamento no seu telemóvel.</p>
        </div>
        <div class="mbway-success text-center" style="display: none">
            <h4 class="m-t-0 text-green"><i class="fas fa-check"></i> Pagamento recebido</h4>
            <p>Pagamento recebido com sucesso.</p>
        </div>
        <div class="mbway-canceled text-center" style="display: none">
            <h4 class="m-t-0 text-red"><i class="fas fa-exclamation-triangle"></i> Pagamento recusado ou expirado</h4>
            <p>Não foi possível proceder ao pagamento.</p>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
    <button type="button" class="btn btn-black btn-proceed">{{ trans('account/global.word.proceed') }} <i class="fas fa-angle-right"></i></button>
    <button type="button" class="btn btn-success btn-conclude" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde..." style="display: none">{{ trans('account/global.word.submit') }}</button>
</div>

<style>
    .select-amount .iradio_minimal-blue{
        margin-top: -5px;
    }

    .select-amount h4 {
        color: #333;
    }
</style>
<script>
    $('.select2').select2(Init.select2());

    $('[name="amount_pack"]').on('change', function(){
        value = $(this).val();

        if(value == 'other') {
            $('.add-custom-amount').show();
            $('[name="amount"]').val('');
        } else {
            $('.add-custom-amount').hide();
            $('[name="amount"]').val(value);
        }
    })

    $('.btn-proceed').on('click', function(e){

        var amount  = parseFloat($('.modal [name="amount"]').val());
        var payment = $('.modal [name="payment_method"]:checked').val();
        var min_amount  = parseFloat("{{ Setting::get('wallet_min_amount') }}");

        if(amount == '' || isNaN(amount)) {
            Growl.error('Não selecionou o montante a carregar.');
        } else if(amount < min_amount && amount >= 0.00) {
            Growl.error('O montante mínimo para carregamento deve ser de {{ money(Setting::get('wallet_min_amount'), Setting::get('app_currency')) }}');
        } else if(payment == '') {
            Growl.error('Não selecionou o método de pagamento.');
        } else {

            $('.step-01, .step-cc, .step-mbw, .step-mb').hide();

            if(payment == 'visa') {
                $('.step-cc').show();
                $('.btn-proceed').hide();
                $('.btn-conclude').show();
            } else if(payment == 'mbway') {
                $('.step-mbw').show();
                $('.btn-proceed').hide();
                $('.btn-conclude').show();
            } else {
                $('.step-mb').show();
                $('.btn-proceed').hide();
                $('.btn-conclude').hide();

                data = {
                    'amount' : amount,
                    'method' : payment
                }

                $.post('{{ route("account.wallet.store") }}', data, function(data){
                    if(data.result) {
                        $('.mb-loading').hide();
                        $('.mb-entity').html(data.entity);
                        $('.mb-reference').html(data.reference)
                        $('.mb-amount').html(data.amount)
                        $('.mb-details').show();
                        $('.mb-loading').hide();
                    } else {
                        $('.mb-loading').html('<span class="text-red"><i class="fas fa-exclamation-triangle"></i> ' + data.feedback + '</span>')
                    }
                }).fail(function(){
                    $('.mb-loading').html('<span class="text-red"><i class="fas fa-exclamation-triangle"></i>Falha de servidor ao gerar pagamento. Tente novamente.</span>')
                })
            }
        }
    });

    $('.btn-conclude').on('click', function(e){

        var amount      = parseFloat($('.modal [name="amount"]').val());
        var payment     = $('.modal [name="payment_method"]:checked').val();
        var first_name  = $('.modal [name="first_name"]').val();
        var last_name   = $('.modal [name="last_name"]').val();
        var card        = $('.modal [name="card"]').val();
        var month       = parseInt($('.modal [name="validity_m"]').val());
        var year        = parseInt($('.modal [name="validity_y"]').val());
        var cvc         = $('.modal [name="cvc"]').val();
        var phone       = $('.modal [name="phone"]').val();
        var $button     = $(this);

        if(payment == 'visa' || payment == 'cc') {

            if(year == '{{ date('Y') }}' && month < '{{ date('n') }}') {
                Growl.error('Data de validade inválida.')
            } else if(cvc.length < 3) {
                Growl.error('O código de segurança deve ter 3 dígitos.')
            } else {

                data = {
                    'method': payment,
                    'amount': amount,
                    'first_name': first_name,
                    'last_name' : last_name,
                    'card' : card,
                    'cvc' : cvc,
                    'month' : month,
                    'year' : year
                };

                $button.button('loading');
                $.post('{{ route("account.wallet.store") }}', data, function (data) {
                    if (data.result) {
                        $('#modal-remote-xs').modal('hide');
                        window.location = data.conclude_url
                        $button.button('reset');
                    } else {

                        $('.cc-loading').hide();
                        $('.cc-canceled').show();
                    }
                }).fail(function () {
                    $('.cc-loading').html('<span class="text-red"><i class="fas fa-exclamation-triangle"></i>Falha de servidor ao gerar pagamento. Tente novamente.</span>')
                }).always(function(){
                    $button.button('reset')
                })
            }

        } else if(payment == 'mbway') {

            if(phone.length < 9) {
                Growl.error('O número de telemóvel tem digitos em falta.')
            } else if(!(phone.startsWith("91") || phone.startsWith("92") || phone.startsWith("93") || phone.startsWith("96"))) {
                Growl.error('O número de telemóvel inserido é inválido.')
            } else {

                data = {
                    'method': payment,
                    'amount': amount,
                    'phone': phone
                };

                $button.button('loading');
                $.post('{{ route("account.wallet.store") }}', data, function (data) {
                    if (data.result) {
                        $('.mbway-details').hide();
                        $('.mbway-loading').show();
                        $('.btn-conclude').hide();
                        startTimer(5 * 60, $('.mbwtimer'));
                        checkPayment(data)
                    } else {
                        $('.mbway-loading').html('<span class="text-red"><i class="fas fa-exclamation-triangle"></i> ' + data.feedback + '</span>')
                    }
                }).fail(function () {
                    $('.mbway-loading').html('<span class="text-red"><i class="fas fa-exclamation-triangle"></i>Falha de servidor ao gerar pagamento. Tente novamente.</span>')
                }).always(function(){
                    $button.button('reset')
                })
            }
        }
    })

    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.text(minutes + ":" + seconds);

            if (--timer < 0) {
                timer = duration;
            }
        }, 1000);
    }

    /**
     * Check payment
     */
    function checkPayment(data) {

        var paymentChecker = setInterval(function () {
            $.post('{{ route("account.wallet.check.payment") }}', {id:data.id}, function(data){
                if(data.paid && data.paid_at) {
                    $('.mbway-loading').hide();
                    $('.mbway-success').show();
                    $('.btn-conclude').hide();
                    $('.wallet-amount').html(data.wallet_amount);
                    clearInterval(paymentChecker);
                    return;
                } else if(data.timeout) {
                    $('.mbway-loading').hide();
                    $('.mbway-canceled').show();
                    $('.btn-conclude').hide();
                    $('.wallet-amount').html(data.wallet_amount);
                    clearInterval(paymentChecker);
                    return;
                }
            }).fail(function(){
                clearInterval(paymentChecker);
                return;
            });
        }, 3000);
    }

</script>

