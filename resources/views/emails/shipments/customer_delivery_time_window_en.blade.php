@extends(app_email_layout())

@section('content')
<h5>Estimado {{ $shipment->recipient_name }},</h5>
<p>
    Our customer {{ $shipment->sender_name }} has instructed us to deliver you an order at the following address:
</p>
<br/>
<b>
    {{ $shipment->recipient_name }}<br/>
    {{ $shipment->recipient_address }}<br/>
    {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
</b>
<p>
    We plan to deliver on the following date and time:<br/>
    <b>
        Date : {{@date_format($shipment->delivery_date, 'Y-m-d')}}<br/>
        Time Window : Between {{@$shipment->start_hour . ' / ' . @$shipment->end_hour}}
    </b>
</p>

<p>
    Check your delivery at any time on our website.<br/>
    <h4 style="margin-top: 10px; margin-bottom: 0">Tracking Code: {{ $shipment->tracking_code }}</h4>
    <h4 style="margin: 0; float: left">Online Tracking:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
    <br/>
</p>


@if($shipment->charge_price || $shipment->obs)
    <p>
        <b>Additional Information:</b><br/>
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
@stop