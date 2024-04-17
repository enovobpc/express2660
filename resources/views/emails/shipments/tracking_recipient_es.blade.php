@extends(app_email_layout())

@section('content')
<h5>Estimado/a {{ $shipment->recipient_name }},</h5>
@if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
    <p>
        Su paquete con el número {{ $shipment->tracking_code }} enviado por {{ $shipment->sender_name }}
        ha sido <b>entregado</b> con éxito.
    </p>
@else
    <p>
        Su paquete con el número {{ $shipment->tracking_code }} enviado por {{ $shipment->sender_name }}
        se enquentra en el estado <b>{{ $statusName }}</b>.
    </p>
@endif

@if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
    <hr>
    <table>
        <tr>
            <td>
                <img src="http://quickbox.test/assets/img/default/delivery.png" style="width: 50px; margin-top: 5px;margin-left: 15px;margin-right: 15px;">
            </td>
            <td>
                <p style="margin: 0">
                    <b>DETALLES DE ENVÍO</b><br/>
                    Día/Hora: {{ $history->created_at }}<br/>
                    @if($history->receiver)
                        Recibido por: {{ $history->receiver }}<br/>
                    @endif
                    @if($history->obs)
                        {!! $history->obs !!}
                    @endif
                </p>
            </td>
        </tr>
    </table>
@elseif($history->status_id == \App\Models\ShippingStatus::INCIDENCE_ID)
    <hr>
    <table>
        <tr>
            <td>
                <img src="http://quickbox.test/assets/img/default/error_256.png" style="width: 50px; margin-top: 5px;margin-left: 15px;margin-right: 15px;">
            </td>
            <td>
                <p style="margin: 0">
                    <b>DETALLES DE INCIDENCIA</b><br/>
                    Motivo: {{ $statusName }}<br/>
                    @if($history->obs)
                        {!! $history->obs !!}
                    @endif
                </p>
            </td>
        </tr>
    </table>
@else
    @if($history->obs || $history->city)
        <hr>
        <p>
            NOTAS ADICIONALES:
            @if($history->obs)
                <br/>
                {!! $history->obs !!}
            @endif
            @if($history->city)
                <br/>
                Localización: {!! $history->city !!}
            @endif

        </p>
    @endif
@endif
<hr>
<p>
    Consulta tu pedido en cualquier momento en nuestro website.<br/>
    <h4 style="margin-top: 10px; margin-bottom: 0">Número de rastreo: {{ $shipment->tracking_code }}</h4>
    <h4 style="margin: 0; float: left">Rastreo Online:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
    <br/>
</p>
@if($shipment->charge_price || $shipment->obs)
    <p>
        <b>Información adicional:</b><br/>
        @if($shipment->charge_price)
        
            Este es un pedido con coste de entrega. Por favor, tenga preparada la cantidad de {{ money($shipment->charge_price, Setting::get('app_currency')) }} en dinero.
            <br/>
        @endif

        @if($shipment->obs)
            {!! $shipment->obs !!}
        @endif
    </p>
@endif
@stop