@extends(app_email_layout())

@section('content')
<div>
    <h2 style="margin-top: 0">Nova oferta de carga - #{{$shipment->tracking_code}}</h4>
</div>
<hr/>
<table style="width: 700px">
    <tr>
        <td style="width: 50%">
            <h5 style="margin: 0; font-size: 18px" >Origem/Carga</h4>
            <p style="margin-top: 5px">
                {{ $shipment->sender_name }}<br/>
                {{-- {{ $shipment->sender_address }}<br/> --}}
                {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
            </p>
        </td>
        <td style="width: 50%">
            <h5 style="margin: 0; font-size: 18px">Destino/Descarga</h4>
            <p style="margin-top: 5px">
                {{ $shipment->recipient_name }}<br/>
                {{-- {{ $shipment->recipient_address }}<br/> --}}
                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
            </p>
        </td>
    </tr>
</table>
<hr/>
<h3 style="margin-bottom: 5px">Outros detalhes</h3>
<table style="width: 100%">
    <tr>
        <td style="width: 50%">
            <table style="width: 100%">
                <tr>
                    <td style="width: 100px">Disponível até</td>
                    <td><b>{{ $limitDay }}</b></td>
                </tr>
                <tr>
                    <td style="width: 100px">Data Serviço</td>
                    <td>{{ $shipment->date }}</td>
                </tr>
                <tr>
                    <td style="width: 100px">Tipo Carga</td>
                    <td>{{ @$shipment->pack_dimensions->first()->pack_type->name ?? 'Geral' }}</td>
                </tr>
            </table>
        </td>
        <td style="width: 50%">
            <table style="width: 100%">
                <tr>
                    <td style="width: 100px">Volumes/Peso</td>
                    <td>{{ $shipment->volumes }}vol, {{ $shipment->weight }}kg</td>
                </tr>
                <tr>
                    <td style="width: 100px">LDM</td>
                    <td>{{ $shipment->ldm ?? 'N/D' }} mt</td>
                </tr>
                <tr>
                    <td style="width: 100px">Notas</td>
                    <td>{{ $shipment->obs }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p style="text-align: center">
    Submeta a sua proposta até dia <b>{{$limitDay}}</b>.
    <br/>
    Clique na ligação abaixo para fazer a sua proposta.<br/><br/><br/>

    <a href="{{ route('auction.show', base64_encode($shipment->tracking_code.'_'.config('app.source').'_'.str_pad($shipment->agency_id, 3, '0', STR_PAD_LEFT).$shipment->id)) }}" class="button-link" style="padding: 15px; font-size: 18px">Fazer uma proposta valor</a>
</p>
@stop

