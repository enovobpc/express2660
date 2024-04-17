@extends(app_email_layout())

@section('content')
<h5>Estimado {{ $shipment->sender_name }},</h5>
<p>Enviamos em anexo os documentos para impressão referente ao serviço nº {{$shipment->tracking_code}}.</p>
<p>Tenha estes documentos impressos e prontos quando o motorista chegar para recolher a mercadoria.</p>

<p>
    <table style="width: 100%">
        <tr>
            <td style="width: 50%">
                <h4 style="margin: 0">CARGA</h4>
                <div>
                    {{ $shipment->sender_name }}<br/>
                    {{ $shipment->sender_address }}<br/>
                    {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                </div>
                <br/>
                Data: {{ $shipment->shipping_date }}
            </td>
            <td style="width: 50%">
                <h4 style="margin: 0">DESCARGA</h4>
                <div>
                    {{ $shipment->recipient_name }}<br/>
                    {{ $shipment->recipient_address }}<br/>
                    {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                </div>
                <br/>
                Data: {{ $shipment->delivery_date }}
            </td>
        </tr>
    </table>
</p>



@stop