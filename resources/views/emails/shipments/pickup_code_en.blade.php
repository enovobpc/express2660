@extends(app_email_layout())

@section('content')
    <div style="width: 480px">
        <div style="text-align: center">
            <h2 style="text-transform: uppercase;margin: 0;">PICKUP CODE</h2>
            <p style="margin: 0 0 40px; font-size: 15px;">Use the code below to collect your order.</p>
            <img src="{{ $qrCode }}" height="200">
            <h2>TRK #{{ $shipment->tracking_code }}</h2>
            <p>Delivery scheduled for {{ $shipment->delivery_date }}</p>
        </div>
        <hr/>
        <p>
            Hi {{ $shipment->recipient_attn ? $shipment->recipient_attn : $shipment->recipient_name }},<br/>
            Our client {{ $shipment->customer->name }} requested us to deliver an shipment to your address.
        </p>
        <table style="width: 100%; text-align: left">
            <tr>
                <th style="vertical-align: top; font-weight: bold">SENDER</th>
                <th style="vertical-align: top; font-weight: bold">RECIPIENT</th>
            </tr>
            <tr>
                <td style="vertical-align: top">
                    <p style="margin-top: 0">
                        {{ $shipment->sender_name }}<br/>
                        {{ $shipment->sender_address }}<br/>
                        {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                    </p>
                </td>
                <td style="vertical-align: top">
                    <p style="margin-top: 0">
                        {{ $shipment->recipient_name }}<br/>
                        {{ $shipment->recipient_address }}<br/>
                        {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                    </p>
                </td>
            </tr>
        </table>
        <hr/>
        <p style="margin-top: 0">
            Track delivery via the address below.<br/>
            <a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
        </p>
        @if($shipment->charge_price || $shipment->obs)
            <p>
                <b>Adicional Information:</b><br/>
                @if($shipment->charge_price)
                    This is a charge delivery.
                    Please have prepared the amount of {{ money($shipment->charge_price, Setting::get('app_currency')) }} in cash.
                    <br/>
                @endif

                @if($shipment->obs)
                    {{ $shipment->obs }}
                @endif
            </p>
        @endif
    </div>
@stop