@extends(app_email_layout())

@section('content')
    @if($payment->status == \App\Models\GatewayPayment\Base::STATUS_REJECTED)
        <div style="text-align: center">
            <img src="{{ asset('assets/img/default/exclamation.png') }}" style="height: 38px; margin-right: 10px; float: left" height="38"/>
            <h4 style="font-size: 19px; margin-top: 5px; margin-bottom: 0; float: left">Pagamento não recebido</h4>
        </div>
        <div style="clear: both"></div>
        <hr/>
        <p>
            Estimado(a) {{ @$payment->customer->name ? @$payment->customer->name : @$payment->customer_name }},
            <br/>
            Informamos que não foi possível receber corretamente o seu pagamento referente aos serviços:
        </p>
        <p>
            <b>{{ $payment->description }}</b>
            <br/>
            Valor: {{ money($payment->value, Setting::get('app_currency')) }}
            <br/>
            Método: {{ trans('admin/billing.gateway-payment-methods.' . $payment->method) }}
        </p>
    @else
        <div style="text-align: center">
            <img src="{{ asset('assets/img/default/check.png') }}" style="height: 38px; margin-right: 10px; float: left" height="38"/>
            <h4 style="font-size: 19px; margin-top: 5px; margin-bottom: 0; float: left">Pagamento Recebido</h4>
        </div>
        <div style="clear: both"></div>
        <hr/>
        <p>
            Estimado(a) {{ @$payment->customer->name ? @$payment->customer->name : @$payment->customer_name }},
            <br/>
            Informamos que recebemos corretamente o seu pagamento referente aos serviços:
        </p>
        <p>
            <b>{{ $payment->description }}</b>
            <br/>
            Valor: {{ money($payment->value, Setting::get('app_currency')) }}
            <br/>
            Método: {{ trans('admin/billing.gateway-payment-methods.' . $payment->method) }}
        </p>
        @if($payment->invoice_id)
        <p>
            Segue em anexo a respetiva fatura-recibo.<br/>
            Obrigado pela sua preferência.
        </p>
        @endif
    @endif
    <p>
        Pode consultar os seus pagamentos e carregamentos de conta na sua área de cliente.
        <br/>
        Aceda aqui para <a href="{{ route('account.login') }}">Iniciar Sessão</a> na sua conta.
    </p>
@stop