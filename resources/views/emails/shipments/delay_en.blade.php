@extends(app_email_layout())

@section('content')
<h5>Dear {{ @$shipment->customer->name }},</h5>
<p>Sorry, it was not possible to complete your order during the course of today.</p>
<p>
    Check your delivery at any time on our website.<br/>
    <h4 style="margin-top: 10px; margin-bottom: 0">Tracking Code: {{ $shipment->tracking_code }}</h4>
    <h4 style="margin: 0; float: left">Online Tracking:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
    <br/>
</p>
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
@stop