@extends(app_email_layout())

@section('content')
<h5>Estimado {{ @$shipment->customer->name }},</h5>
<p>Lamentamos mas não foi possivel concluir a sua encomenda no decorrer do dia de hoje.</p>
<p>
    Pode acompanhar o estado do serviço através do nosso portal online.<br/>
    <h4 style="margin-top: 10px; margin-bottom: 0">Tracking Code: {{ $shipment->tracking_code }}</h4>
    <h4 style="margin: 0; float: left">Seguimento Online:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
    <br/>
</p>

@if($shipment->charge_price || $shipment->obs)
    <p>
        <b>Informações Adicionais:</b><br/>
        @if($shipment->charge_price)
            Esta é uma entrega à cobrança.
            Por favor tenha preparado o valor de {{ money($shipment->charge_price, Setting::get('app_currency')) }} para pagamento no ato da entrega.
            <br/>
        @endif

        @if($shipment->obs)
            {{ $shipment->obs }}
        @endif
    </p>
@endif
@stop