@extends(app_email_layout())

@section('content')
<h5>Estimado parceiro,</h5>
<p>
    Solicitamos que proceda à recolha no remetente e morada abaixo:
    <br/>
    @if($shipment->sender_attn)
        A/C: {{ $shipment->sender_attn }}<br/>
    @endif
    <b>
        {{ $shipment->sender_name }}<br/>
        {{ $shipment->sender_address }}<br/>
        {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}<br/>
        Tlf: {{ $shipment->sender_phone ? $shipment->sender_phone : 'N/A' }}
    </b>
</p>
<p>
    Número Pedido ({{ @$shipment->provider->name }}): {{ $shipment->provider_tracking_code }}<br/>
</p>
<hr/>
<p>
    <h4 style="margin-top: 10px; margin-bottom: 0">Tracking Code: {{ $shipment->tracking_code }}</h4>
    <h4 style="margin: 0; float: left">Seguimento Online:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
    <br/>
</p>
<p>
    <b>Serviço:</b> {{ @$shipment->service->name }}<br/>
    <b>Data Recolha:</b> {{ @$shipment->date }}
</p>
@stop