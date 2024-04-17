@extends(app_email_layout())

@section('content')
<h5>Estimado {{ $shipment->recipient_name }},</h5>

@if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
    @if(Setting::get('app_mode') == 'food')
        <p>
            O seu pedido com o número {{ $shipment->tracking_code }} para levantamento em {{ $shipment->sender_name }}
            foi <b>finalizado</b> com sucesso.
        </p>
    @elseif(in_array(Setting::get('app_mode'), ['freight', 'cargo']))
        <p>
            O seu serviço com o número {{ $shipment->tracking_code }} para carga em {{ $shipment->sender_name }} e descarga em {{ $shipment->recipient_name }}
            foi <b>entregue</b> com sucesso.
        </p>
    @else
        <p>
            A sua encomenda com o número {{ $shipment->tracking_code }} remetida por {{ $shipment->sender_name }}
            foi <b>entregue</b> com sucesso.
        </p>
    @endif
@else
    @if(Setting::get('app_mode') == 'food')
        <p>
            O seu pedido com o número {{ $shipment->tracking_code }} para levantamento em {{ $shipment->sender_name }}
            está agora no estado <b>{{ $statusName }}</b>.
        </p>
    @elseif(in_array(Setting::get('app_mode'), ['freight', 'cargo']))
        <p>
            O seu serviço com o número {{ $shipment->tracking_code }} para carga em {{ $shipment->sender_name }}
            e descarga em {{ $shipment->recipient_name }} está agora no estado <b>{{ $statusName }}</b>
        </p>
    @else
        <p>
            A sua expedição com o número {{ $shipment->tracking_code }} remetida por {{ $shipment->sender_name }}
            está agora no estado <b>{{ $statusName }}</b>.
        </p>
    @endif
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
                    <b>DETALHES DA ENTREGA</b><br/>
                    Data/Hora: {{ $history->created_at }}<br/>
                    @if($history->receiver)
                        Recebido por: {{ $history->receiver }}<br/>
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
        Esta é uma entrega à cobrança. Por favor tenha preparado o valor de {{ money($shipment->charge_price, Setting::get('app_currency')) }} para pagamento no ato da entrega.
            <br/>
        @endif

        @if($shipment->obs)
            {{ $shipment->obs }}
        @endif
    </p>
@endif
@stop