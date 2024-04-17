@extends(app_email_layout())

@section('content')
    @if(config('app.source') == 'ontimeservices')
        <h5 style="font-size: 16px">Dear {{ @$shipment->customer->name }},</h5>
        <p>
            Your load to {{ @$shipment->recipient_name }} was created with Tracking ID {{ $shipment->tracking_code }}.
            <br/>
            <table style="width: 100%">
                <tr>
                    <td style="width: 50%">
                        <h4 style="margin: 0">LOAD</h4>
                        <b>
                            {{ $shipment->sender_name }}<br/>
                            {{ $shipment->sender_address }}<br/>
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                        </b>
                        <br/>
                        Data: {{ $shipment->shipping_date }}
                    </td>
                    <td style="width: 50%">
                        <h4 style="margin: 0">DISCHARGE</h4>
                        <b>
                            {{ $shipment->recipient_name }}<br/>
                            {{ $shipment->recipient_address }}<br/>
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                        </b>
                        <br/>
                        Date: {{ $shipment->delivery_date }}
                    </td>
                </tr>
            </table>
        </p>
        <p>
            You can track the status of your order through our online portal.<br/>
        <h4 style="margin-top: 10px; margin-bottom: 0">Tracking Code: {{ $shipment->tracking_code }}</h4>
        <h4 style="margin: 0; float: left">Track & Trace:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
        <br/>
        </p>
    @else
        <h5>Dear {{ $shipment->recipient_name }},</h5>
        <p>
            Our customer {{ $shipment->sender_name }} has instructed us to deliver you an order at the following address:
            <br/>
            <b>
                {{ $shipment->recipient_name }}<br/>
                {{ $shipment->recipient_address }}<br/>
                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
            </b>
        </p>

        @if(in_array(Setting::get('app_mode'), ['freight', 'cargo']))
            <p>
                Check your delivery at any time on our website.<br/>
                <h4 style="margin-top: 10px; margin-bottom: 0">e-CMR Number: {{ $shipment->tracking_code }}</h4>
                <h4 style="margin: 0; float: left">Online e-CMR Tracking:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
                <br/>
            </p>
        @else
            <p>
                Check your delivery at any time on our website.<br/>
                <h4 style="margin-top: 10px; margin-bottom: 0">Tracking Code: {{ $shipment->tracking_code }}</h4>
                <h4 style="margin: 0; float: left">Online Tracking:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
                <br/>
            </p>
        @endif        
        @if($shipment->charge_price || $shipment->obs)
            <p>
                <b>Adicional Information:</b><br/>
                @if($shipment->charge_price)
                    This is a charge delivery.
                    Please have prepared the amount of {{ money($shipment->charge_price, Setting::get('app_currency')) }}.
                    <br/>
                @endif

                @if($shipment->obs)
                    {{ $shipment->obs }}
                @endif
            </p>
        @endif
    @endif
@stop