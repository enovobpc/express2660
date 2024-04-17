@extends(app_email_layout())

@section('content')
<h5>Estimado {{ $shipment->customer->name }},</h5>

@if($shipment->is_collection)
    <p>
        O seu pedido de recolha em {{ $shipment->recipient_name }}, com o número {{ $shipment->tracking_code }},
        está agora no estado <b>{{ $statusName }}</b>.
    </p>
    <p>
        Pode acompanhar o estado do serviço através do nosso portal online.<br/>
        <h4 style="margin-top: 10px; margin-bottom: 0">Tracking Code: {{ $shipment->tracking_code }}</h4>
        <h4 style="margin: 0; float: left">Seguimento Online:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
        <br/>
    </p>
@else
    @if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
        @if(Setting::get('app_mode') == 'food')
            <p>
                O seu pedido com o número {{ $shipment->tracking_code }} para entrega em {{ $shipment->recipient_name }}
                foi <b>Finalizado</b> com sucesso.
            </p>
        @elseif(Setting::get('app_mode') == 'cargo')
            <p>
                A sua carga com o número e-CMR {{ $shipment->tracking_code }} foi <b>Entregue</b>.<br/>
                Anexamos uma cópia da carta de porte para seu conhecimento ou aquivo.
            </p>
        @else
            <p>
                O seu envio com o número {{ $shipment->tracking_code }} para {{ $shipment->recipient_name }}
                foi <b>Entregue</b> com sucesso.
            </p>
        @endif
    @else
        @if(Setting::get('app_mode') == 'food')
            <p>
                O seu pedido com o número {{ $shipment->tracking_code }} para entrega em {{ $shipment->recipient_name }}
                está agora no estado <b>{{ $statusName }}</b>.
            </p>
        @else
            <p>
                O seu pedido com o número {{ $shipment->tracking_code }} para {{ $shipment->recipient_name }}
                está agora no estado <b>{{ $statusName }}</b>.
            </p>
        @endif
    @endif
    @if(in_array(Setting::get('app_mode'), ['freight', 'cargo']))
    <p>
        Expedidor: {{ $shipment->shipper_name }}
        <br/>
        Consignatário: {{ $shipment->delivery_name }}
        <br/>
        Local Carga: {{ $shipment->sender_name }} ({{ strtoupper($shipment->sender_country) }})
        <br/>
        Local Descarga: {{ $shipment->recipient_name }} ({{ strtoupper($shipment->recipient_country) }})
    </p>
    <p>
        <h4 style="margin-top: 10px; margin-bottom: 0">Número e-CMR: {{ $shipment->tracking_code }}</h4>
        @if($shipment->reference)
        <h4 style="margin-top: 0; margin-bottom: 0">Referência: {{ $shipment->reference }}</h4>
        @endif
        <h4 style="margin: 0; float: left">Consulta online e-CMR:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
        <br/>
    </p>
    @else
    <p>
        Pode acompanhar o estado do serviço através do nosso portal online.<br/>
        <h4 style="margin-top: 10px; margin-bottom: 0">Tracking Code: {{ $shipment->tracking_code }}</h4>
        <h4 style="margin: 0; float: left">Seguimento Online:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
        <br/>
    </p>
    @endif
@endif

@if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
    <hr/>
    <table>
        <tr>
            <td>
                <img src="http://quickbox.test/assets/img/default/delivery.png" style="width: 50px; margin-top: 5px;margin-left: 15px;margin-right: 15px;">
            </td>
            <td>
                <p style="margin: 0">
                    <b>DETALHES DA ENTREGA</b><br/>
                    Data/Hora: {{ $history->created_at }}<br/>
                    @if($history->receiver)
                        Entregue a: {{ $history->receiver }}<br/>
                    @endif
                    @if($history->latitude)
                        GPS: {{ $history->latitude }}, {{ $history->longitude }}<br/>
                    @endif
                    @if($history->obs)
                        {!! $history->obs !!}
                    @endif
                    <br/>
                    A prova de entrega encontra-se anexada a este e-mail.
                </p>
            </td>
        </tr>
    </table>
@elseif($history->status_id == \App\Models\ShippingStatus::INCIDENCE_ID)
    <hr>
    <table>
        <tr>
            <td>
                <img src="{{ asset('assets/img/default/error_256.png') }}" style="width: 50px; margin-top: 5px;margin-left: 15px;margin-right: 15px;">
            </td>
            <td>
                <p style="margin: 0">
                    <b>DETALHES DA INCIDÊNCIA</b><br/>
                    Motivo: {{ $incidenceName }}<br/>
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
            NOTAS ADICIONAIS:
            @if($history->obs)
            <br/>
            {!! $history->obs !!}
            @endif
            @if($history->city)
                <br/>
                Localização: {!! $history->city !!}
            @endif

        </p>
    @endif
@endif
<hr>
<table style="width: 100%;">
<tr>
    <td>
        <p style="margin-top: 0">
            O histórico de serviços, provas de entrega e faturas encontram-se disponíveis na sua área de cliente.
        </p>
        <p style="text-align: center;">
            <br/>
            <a href="{{ route('account.index') }}" class="button-link">Entrar na Área de Cliente</a>
        </p>
    </td>
</tr>
</table>
@stop