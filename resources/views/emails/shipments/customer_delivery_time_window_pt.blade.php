@extends(app_email_layout())

@section('content')
<h5>Estimado {{ $shipment->recipient_name }},</h5>
<p>
    O nosso cliente {{ @$shipment->customer->name }} instruiu-nos para fazer uma entrega na seguinte morada:
</p>
<br/>
<b>
    {{ $shipment->recipient_name }}<br/>
    {{ $shipment->recipient_address }}<br/>
    {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
</b>
<p>
    Prevemos fazer a entrega na seguinte data e horário:<br/>
    <b>
        Data : {{@date_format($shipment->delivery_date, 'Y-m-d')}}<br/>
        Janela Horária : Entre {{@$shipment->start_hour . ' / ' . @$shipment->end_hour}}
    </b>
</p>

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