@extends(app_email_layout())

@section('content')
<h5>Dear {{ $shipment->customer->name }},</h5>

@if($shipment->is_collection)
    <p>
        Your pickup request in {{ $shipment->recipient_name }},  with number {{ $shipment->tracking_code }},
        is now in the state <b>{{ $statusName }}</b>.
    </p>
@else

    @if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
        <p>
            Your shipment with number {{ $shipment->tracking_code}} to {{ $shipment->recipient_name}} was <b>Delivered</b> successfully.
        </p>
    @else
        <p>
            Your shipping with number {{ $shipment->tracking_code }} to {{ $shipment->recipient_name }}
            is now in the state <b>{{ $statusName }}</b>.
        </p>
    @endif
@endif
<p>
    Check your delivery at any time on our website.<br/>
    <h4 style="margin-top: 10px; margin-bottom: 0">Tracking Code: {{ $shipment->tracking_code }}</h4>
    <h4 style="margin: 0; float: left">Online Tracking:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
    <br/>
</p>

@if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
    <hr/>
    <table>
        <tr>
            <td>
                <img src="http://quickbox.test/assets/img/default/delivery.png" style="width: 50px; margin-top: 5px;margin-left: 15px;margin-right: 15px;">
            </td>
            <td>
                <p style="margin: 0">
                    <b>DELIVERY DETAILS</b><br/>
                    Date/Hour: {{ $history->created_at }}<br/>
                    @if($history->receiver)
                        Received by: {{ $history->receiver }}<br/>
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
                <img src="{{ asset('assets/img/default/error_256.png') }}" style="width: 50px; margin-top: 5px;margin-left: 15px;margin-right: 15px;">
            </td>
            <td>
                <p style="margin: 0">
                    <b>INCIDENCE DETAILS</b><br/>
                    Reason: {{ $incidenceName }}<br/>
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
            ADICIONAL NOTES:
            @if($history->obs)
                <br/>
                {!! $history->obs !!}
            @endif
            @if($history->city)
                <br/>
                Location: {!! $history->city !!}
            @endif

        </p>
    @endif
@endif
<hr>
<table style="width: 100%;">
<tr>
    <td>
        <p style="margin-top: 0">
            Consult all your shipments, refunds and invoices in your customer area at any time.
        </p>
        <p style="text-align: center;">
            <br/>
            <a href="{{ route('account.index') }}" class="button-link">Login into Client Area</a>
        </p>
    </td>
</tr>
</table>
@stop