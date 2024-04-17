@extends(app_email_layout())

@section('content')
    <div style="width: 700px">
        <h2>Alteração/Reagendamento do envio {{ $shipment->tracking_code }}</h2>
        <table style="width: 100%">
            <tr>
                <td style="width: 50%">
                    <h5>Antigo:</h5>
                        <strike>{{ $oldShipment->recipient_address }}<br/>
                        {{ $oldShipment->recipient_zip_code }} {{ $oldShipment->recipient_city }}
                    <br/>
                    Data: {{ $oldShipment->date }} <br>
                    Observações: {{ $shipment->obs }}</strike>
                </td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td style="width: 50%">
                    <h5>Novo:</h5>
                        {{ $shipment->recipient_address }}<br/>
                        {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                    <br/>
                    Data: {{ $shipment->date }} <br>
                    Observações: {{ $shipment->obs }}
                </td>
            </tr>
        </table>
    </div>
@stop