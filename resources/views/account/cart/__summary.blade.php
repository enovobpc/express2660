@section('title')
    {{ trans('global.menu.cart') }} |
@stop

@section('metatags')
    <meta name="description" content="">
    <meta property="og:title" content="{{ trans('global.menu.cart') }}">
    <meta property="og:description" content="">
    <meta property="og:image" content="{{ asset('assets/img/og_image.png') }}">
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <br>
                </ol>
            </div>
            <div class="col-sm-12">
                <h1 class="title text-center">{{ trans('global.cart.conclude.page-title') }} #{{ $order->order_no }}</h1>
            </div>
        </div>

        @if(@$order->payment_method->method == 'mb' && @$paymentDetails->status == 'failed')
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <img src="{{ asset('assets/img/logo_mb.png') }}" style="height: 50px; margin-top: 30px">
                                    <br/>
                                    <h3 class="media-heading font-weight-normal m-t-5  mbw-feedback-title">
                                        O pagamento por MB falhou
                                    </h3>
                                    <p class="bigger-120 m-t-5 text-red">
                                        {{ @$paymentDetails->feedback }}
                                    </p>
                                    <div class="spacer-20"></div>
                                </div>
                                <div class="col-sm-4 col-sm-offset-4 text-center">
                                    {{ Form::open(['route' => ['cart.checkout.payment.try-again', $order->id], 'method' => 'POST', 'ajax-form']) }}
                                    <a href="{{ route('cart.checkout', 'payment') }}" class="btn btn-default"><i class="fa fa-angle-left"></i> Anterior</a>
                                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gerar pagamento">Tentar novamente</button>
                                    {{ Form::close() }}
                                    <div class="spacer-50"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="spacer-30"></div>
                </div>
            </div>

        {{-- FALHA NO PAGAMENTO MBWAY --}}
        @elseif(@$order->payment_method->method == 'mbw' && @$paymentDetails->status == 'failed')
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <img src="data:image/png;base64,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" style="height: 50px; margin-top: 30px">
                                    <br/>
                                    @if(@$paymentDetails->customer_phone)
                                        <h3 class="media-heading font-weight-normal m-t-5  mbw-feedback-title">
                                            O pagamento por MB Way falhou
                                        </h3>
                                        <p class="bigger-120 m-t-5 text-red">
                                            {{ @$paymentDetails->last_error }}
                                        </p>
                                    @else
                                        <h3 class="media-heading font-weight-normal m-t-5 mbw-feedback-title">
                                            Indique o seu número de telemóvel.
                                        </h3>
                                    @endif
                                    <div class="spacer-20"></div>
                                </div>
                                <div class="col-sm-4 col-sm-offset-4 text-center">
                                    {{ Form::open(['route' => ['cart.checkout.payment.try-again', $order->id], 'method' => 'POST', 'ajax-form']) }}
                                    <div class="form-group form-group-lg text-center">
                                        @if(@$paymentDetails->customer_phone)
                                        {{ Form::label('mobile', 'Tente de novo ou escolha outra forma de pagamento.') }}
                                        @endif
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                {{ trans('global.word.mobile') }}
                                            </div>
                                            {{ Form::text('mobile', substr($paymentDetails->customer_phone, 0, 9), ['class' => 'form-control', 'maxlength' => 9, 'required']) }}
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary m-b-20 m-t-10" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A tentar pagamento">
                                            @if(@$paymentDetails->customer_phone)
                                                Tentar novamente
                                            @else
                                                <i class="fas fa-check"></i> Pagar agora
                                            @endif
                                        </button>
                                        <br/>
                                        <a href="{{ route('cart.checkout', 'payment') }}"><i class="fa fa-angle-left"></i> Voltar à página anterior</a>
                                    </div>
                                    {{ Form::close() }}
                                    <div class="spacer-50"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="spacer-30"></div>
                </div>
            </div>
        {{-- AGUARDA PAGAMENTO MBWAY --}}
        @elseif(@$order->payment_method->method == 'mbw' && @$paymentDetails->status == 'pending')
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="col-sm-12 text-center">
                                <i class="fa fa-spin fa-circle-o-notch bigger-200"></i>
                                <img src="data:image/png;base64,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" style="height: 50px; margin-top: 50px">
                                <br/>
                                <h3 class="media-heading font-weight-normal m-t-5">Aceite o pagamento no seu telemóvel.</h3>
                                <p class="bigger-120 m-t-5">
                                    Dentro de momentos vai receber uma notificação da aplicação MBWay no seu telemóvel.
                                    <br/>
                                    Para finalizar a compra, selecione a opção "Aceitar" dentro do tempo limite.
                                </p>
                                <h2>
                                    <i class="fas fa-spin fa-spinner"></i>
                                    <span class="timer-countdown">05:00</span>
                                </h2>
                                <div class="spacer-40"></div>
                            </div>
                        </div>
                    </div>
                    <div class="spacer-30"></div>
                </div>
            </div>
            {{-- PAGAMENTO VISA / MASTERCARD --}}
        @elseif((@$order->payment_method->method == 'visa' || @$order->payment_method->method == 'stripe')
        && (@$paymentDetails->status == 'pending' || @$paymentDetails->status == 'failed'))
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <div class="clearfix text-center">
                                        <img src="{{ asset('assets/img/mastercard.svg') }}" style="margin-top: 50px; height: 50px;"/>
                                        <img src="{{ asset('assets/img/visa.svg') }}" style="margin-top: 45px; height: 40px; margin-left: 10px;"/>
                                    </div>
                                    <br/>
                                    @if(@$paymentDetails->status == 'failed' && !empty($paymentDetails->card_name))
                                        <h3 class="media-heading font-weight-normal m-t-5 text-red mbw-feedback-title">
                                            <i class="fas fa-exclamation-triangle"></i> Pagamento rejeitado
                                        </h3>
                                        <p class="bigger-120 m-t-5 text-red">
                                            {{ @$paymentDetails->last_error }}
                                        </p>
                                    @elseif(@$paymentDetails->status == 'success')
                                        <h4 class="media-heading font-weight-normal text-green m-t-5 mbw-feedback-title">
                                            <i class="fas fa-shield-alt"></i> Pagamento seguro.
                                        </h4>
                                    @else
                                        <h4 class="media-heading font-weight-normal text-green m-t-5 mbw-feedback-title">
                                            <i class="fas fa-shield-alt"></i> Pagamento pronto para ser efetuado.
                                        </h4>
                                    @endif
                                    <div class="spacer-20"></div>
                                </div>
                                <div class="col-sm-4 col-sm-offset-4">
                                    {{ Form::open(['route' => ['cart.checkout.payment.try-again', $order->id], 'method' => 'POST', 'class' => 'credit-card-form']) }}
                                    <div class="row row-5">
                                        <div class="col-sm-12">
                                            <div class="form-group form-group-lg">
                                                {{ Form::label('card_name', 'Nome Titular Cartão') }}
                                                {{ Form::text('card_name', @$paymentDetails->card_name, ['class' => 'form-control', 'required']) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-lg">
                                        {{ Form::label('card_number', 'Número Cartão') }}
                                        {{ Form::text('card_number', substr(@$paymentDetails->card_number, 0, 19), ['class' => 'form-control number nospace', 'maxlength' => 19, 'required']) }}
                                    </div>
                                    <div class="row row-5">
                                        <div class="col-sm-9">
                                            <div class="row row-5">
                                                <div class="col-sm-7">
                                                    <div class="form-group form-group-lg">
                                                        {{ Form::label('card_month', 'Validade Cartão') }}
                                                        {{ Form::select('card_month', ['' => ''] + trans('datetime.month') , @$paymentDetails->card_month, ['class' => 'form-control select2', 'required', 'data-placeholder' => 'Mês']) }}
                                                    </div>
                                                </div>
                                                <div class="col-sm-5">
                                                    <div class="form-group form-group-lg">
                                                        {{ Form::label('card_year', '  ') }}
                                                        {{ Form::select('card_year', ['' => ''] + yearsArr(date('Y'), (date('Y') + 10)), @$paymentDetails->card_year, ['class' => 'form-control select2', 'required', 'data-placeholder' => 'Ano']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-lg">
                                                {{ Form::label('card_cvc', 'CVC') }}
                                                {{ Form::text('card_cvc', @$paymentDetails->card_cvc, ['class' => 'form-control number nospace', 'maxlength' => 3, 'required']) }}
                                            </div>
                                        </div>
                                    </div>


                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary m-b-20 m-t-10" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A tentar pagamento">
                                            @if(@$paymentDetails->status == 'failed')
                                                Tentar novamente
                                            @else
                                                <i class="fas fa-check"></i> Pagar agora
                                            @endif
                                        </button>
                                        <br/>
                                        <a href="{{ route('cart.checkout', 'payment') }}"><i class="fa fa-angle-left"></i> Voltar à página anterior</a>
                                    </div>
                                    {{ Form::close() }}
                                    <div class="spacer-50"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="spacer-30"></div>
                </div>
            </div>
        @else
            {{-- RESULTADO PAGAMENTO OK --}}

            <div class="row">
                @if(@$order->payment->status == \App\Models\PaymentNotification::STATUS_FAILED)
                    <div class="col-sm-12 text-center">
                        <img src="{{ asset('assets/img/check_bag.svg') }}" style="height: 50px">
                        <br/>
                        <p class="bigger-120 m-t-5 text-red"><i class="fa fa-exclamation-circle"></i> {{ trans('global.cart.conclude.feedback.payment-fail') }}</p>
                        <div class="spacer-30"></div>
                    </div>
                @else
                    <div class="col-xs-12">
                        <h3 class="media-heading text-green text-center font-weight-normal m-t-5">
                            <i class="fas fa-check-circle"></i> {{ trans('global.cart.conclude.feedback.success') }}
                        </h3>
                        <p class="bigger-120 text-center m-t-5">
                            {{ trans('global.cart.conclude.feedback.success-subtitle', ['status' => @$order->status->name]) }}
                        </p>
                    </div>
                @endif
                <div class="col-xs-12 col-lg-10 col-lg-offset-1">
                    <div class="order-summary-papper">
                        <div class="row">
                            <div class="col-xs-12">
                                <p class="bigger-120 m-t-5 pull-right">{{ $order->created_at->format('d/m/Y') }}</p>
                                <p class="bigger-120 m-t-5">{{ trans('global.word.order_no') }} <b>{{ $order->order_no }}</b></p>
                                <hr/>
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <h4 class="text-primary">{{ trans('global.cart.conclude.shipping-address') }}</h4>
                                <p>
                                    <b>{{ $order->shipping_method->name }}</b><br/>
                                    @if($order->delivery_name)
                                    {{ $order->delivery_name }}<br/>
                                    {{ $order->delivery_address }}<br/>
                                    {{ $order->delivery_zip_code }} {{ $order->delivery_city }}<br/>
                                    {{ trans('country.' . $order->delivery_country) }}
                                    {{ $order->delivery_vat }}<br/>
                                    T: {{ $order->delivery_phone }}</p>
                                    @endif
                                </p>
                            </div>
                            <div class="col-xs-12 col-sm-3 col-md-4">
                                <h4 class="text-primary">{{ trans('global.cart.conclude.payment-data') }}</h4>
                                <p>
                                    <b>{{ trans('global.word.tin') }}: {{ $order->vat ? $order->vat : '999999999' }}</b><br/>
                                    {{ $order->name }}<br/>
                                    {{ $order->address }}<br/>
                                    {{ $order->zip_code }} {{ $order->city }}<br/>
                                    {{ trans('country.' . $order->country) }}<br/>
                                    @if($order->phone)
                                        T: {{ $order->phone }}</p>
                                    @endif
                                </p>
                            </div>
                            <div class="col-xs-12 col-sm-5 col-md-4">
                                <h4 class="text-primary">{{ trans('global.cart.conclude.payment-data') }}</h4>
                                <p><b>{{ @$order->payment_method->name }}</b> {{ $order->payment_price ? '('.money($order->payment_price, '€').')' : $order->payment_price }}</p>
                                @if(@$order->payment_notification_id)
                                    @if($order->payment->method == 'mb')
                                    <div class="panel panel-info p-5px m-t-15 m-b-0">
                                        <div class="row row-5">
                                            <div class="col-sm-12">
                                                <div class="pull-left p-t-3">
                                                    <img src="{{ asset('assets/img/logo_mb.png') }}" class="height-60 width-60px">
                                                </div>
                                                <div class="pull-left p-l-15">
                                                    <p class="m-l-0 m-t-0 m-b-0 bigger-110">
                                                        <b>Entidade:</b> {{ @$order->payment->entity }}<br>
                                                        <b>Referência:</b> {{ chunk_split(@$order->payment->reference, 3, ' ') }}<br>
                                                        <b>Montante: </b> {{ money($order->total_price, '€') }}<br>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <p style="line-height: 16px">{{ trans('global.cart.conclude.payment-mb') }}</p>
                                    @elseif($order->payment->method == 'mbw')
                                        <img src="data:image/png;base64,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" class="height-20">
                                        <div class="clearfix"></div>
                                        @if($order->payment->status == \App\Models\PaymentNotification::STATUS_FAILED)
                                            <span class="label bg-red bold"><i class="fa fa-exclamation-circle"></i> {{ trans('global.cart.conclude.payment-failed') }}</span><br/>
                                            <a href="" class="btn btn-sm btn-default m-t-5">{{ trans('global.cart.conclude.payment-again') }}</a>
                                        @else
                                            <span class="label bg-green bold"><i class="fa fa-check-circle"></i> {{ trans('global.cart.conclude.payment-confirmed') }}</span><br/>
                                        @endif
                                    @elseif($order->payment->method == 'cc')
                                        <img src="{{ asset('assets/img/visa.svg') }}" class="height-20">
                                        <img src="{{ asset('assets/img/mastercard.svg') }}" class="height-30 m-l-5">
                                        <div class="clearfix"></div>
                                        @if($order->payment->status == \App\Models\PaymentNotification::STATUS_FAILED)
                                        <span class="label bg-red bold"><i class="fa fa-exclamation-circle"></i> {{ trans('global.cart.conclude.payment-failed') }}</span><br/>
                                        <a href="{{ $order->payment->visa_url }}" class="btn btn-sm btn-default m-t-5">{{ trans('global.cart.conclude.payment-again') }}</a>
                                        @else
                                        <span class="label bg-green bold"><i class="fa fa-check-circle"></i> {{ trans('global.cart.conclude.payment-confirmed') }}</span><br/>
                                        @endif
                                    @endif
                                @elseif(@$order->payment->method == 'cashOnDelivery')
                                    <p>{{ trans('global.cart.conclude.payment-on-delivery') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="spacer-30"></div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th colspan="2" style="text-align: left; padding: 5px">{{ trans('global.word.product') }}</th>
                                <th>{{ trans('global.word.price') }}</th>
                                <th>{{ trans('global.word.total') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $item)
                                <tr>
                                    <td>
                                        @if($item->options->photo)
                                            <img src="{{ asset($item->options->photo) }}" style="height: 50px; width: 50px">
                                        @else
                                            <img src="{{ asset('assets/img/default/default_thumb.png') }}" style="height: 50px; width: 50px">
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item->qty }}x {{ $item->name }}
                                        <br/>
                                        <small><i>{{ $item->options->attributes }}</i></small>
                                    </td>
                                    <td style="text-align: center">{{ money($item->price, '€') }}</td>
                                    <td style="text-align: center">{{ money($item->total, '€') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr/>
                        <div class="row">
                            <div class="col-sm-8">
                                @if($order->coupon_code)
                                    <h4 class="margin-0 line-height-1p4">
                                        <small>{{ trans('global.cart.coupons.code') }}</small>
                                        <br/>
                                        {{ $order->coupon_code }}
                                    </h4>
                                    @if($order->coupon->type == 'discount_percent')
                                        {{ trans('global.word.discount_of', ['value' => money($order->coupon_discount, '%')]) }}
                                    @elseif($order->coupon->type == 'discount_price')
                                        {{ trans('global.word.discount_of', ['value' => money($order->coupon_discount, '€')]) }}
                                    @elseif($order->coupon->type == 'offer')
                                            {{ trans('global.word.discount_of', ['value' => money($order->coupon_discount, '€')]) }}
                                    @endif
                                @endif
                            </div>
                            <div class="col-sm-4">
                                <table class="width-100 text-right final-prices">
                                    <tr>
                                        <td>{{ trans('global.word.subtotal') }}</td>
                                        <td><h5 class="margin-0">{{ money($order->net_price, '€') }}</h5></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('global.cart.summary.shipping-label') }}</td>
                                        <td><h5 class="margin-0">{{ $order->shipping_price + $order->payment_price > 0.00 ? money($order->shipping_price + $order->payment_price, '€') : 'Grátis' }}</h5></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('global.word.vat') }} ({{ Setting::get('vat_rate_normal') }}%)</td>
                                        <td><h5 class="margin-0">{{ money($order->vat_price, '€') }}</h5></td>
                                    </tr>
                                    @if($order->coupon_code)
                                        <tr>
                                            <td>{{ trans('global.word.code') }}: {{ $order->coupon_code }}</td>
                                            @if($order->coupon->type == 'discount_percent')
                                            <td><h5 class="margin-0">-{{ money($order->coupon_discount, '%') }}</h5></td>
                                            @elseif($order->coupon->type == 'discount_price')
                                            <td><h5 class="margin-0">-{{ money($order->coupon_discount, '€') }}</h5></td>
                                            @elseif($order->coupon->type == 'offer')
                                            <td><h5 class="margin-0">-{{ money($order->coupon_discount, '€') }}</h5></td>
                                            @endif
                                        </tr>
                                    @endif
                                    <tr class="font-size-22px">
                                        <td>{{ trans('global.word.total') }}</td>
                                        <td><b>{{ money($order->total_price, '€') }}</b></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="spacer-30"></div>
                    </div>
                </div>

                <div class="col-xs-12 col-lg-10 col-lg-offset-1 text-center no-print">
                    <div class="spacer-30"></div>
                    <a href="{{ route('customers.index') }}" class="btn btn-primary">{{ trans('global.cart.conclude.account-button') }}</a>
                    <a href="#" class="btn btn-default hidden-xs btn-print"><i class="fa fa-print"></i> {{ trans('global.word.print') }}</a>
                    <div class="spacer-50"></div>
                </div>
            </div>
        @endif
        </div>
    </div>
@stop

@section('scripts')

    <script>
        $('[type="submit"]').on('click', function() {
            $(this).button('loading');
        })

        $('.btn-print').on('click', function(e){
            e.preventDefault();
            window.print();
        })
    </script>
    @if(@$order->payment_method->method == 'mbw' && @$paymentDetails->status == 'pending')
    <script>
        setInterval(function(){validateMbw();}, 3000);

        function validateMbw(){
            var feedback = $.ajax({
                type: "POST",
                url: "{{ route('cart.checkout.payment.check', $order->id) }}",
                async: false
            }).success(function(data){
                if(data.expired) {
                    window.location = "{{ route('cart.checkout.summary', $order->order_no) }}"
                } else if(data.result) {
                    $(document).find('.mbw-success').remove();
                    $('.timer-countdown').closest('h2').hide();
                    $('.timer-countdown').closest('h2').after('<h4 class="text-green mbw-success margin-top-10"><i class="fas fa-check"></i> Pagamento aceite. Aguarde por favor...</i>');
                    //location.reload();
                    window.location = "{{ route('cart.checkout.summary', $order->order_no) }}"
                }
            }).error(function(){

            });

        }

        function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.html(minutes + ":" + seconds);

                if (--timer < 0) {
                    timer = duration;
                }
            }, 1000);
        }

        window.onload = function () {
            var fiveMinutes = 60 * 5;
            var display     = $('.timer-countdown')
            startTimer(fiveMinutes, display);
        };
    </script>
    @endif
@stop
