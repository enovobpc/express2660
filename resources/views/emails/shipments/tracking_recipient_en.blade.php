@extends(app_email_layout())

@section('content')
<h5>Dear {{ $shipment->recipient_name }},</h5>
@if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
    <p>
        Your parcel with number {{ $shipment->tracking_code }} shipped by {{ $shipment->sender_name }}
        was <b>delivered</b> successfully.
    </p>
@else
    <p>
        Your parcel with number {{ $shipment->tracking_code }} shipped by {{ $shipment->sender_name }}
        is now in the state <b>{{ $statusName }}</b>.
    </p>
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
                <img src="http://quickbox.test/assets/img/default/error_256.png" style="width: 50px; margin-top: 5px;margin-left: 15px;margin-right: 15px;">
            </td>
            <td>
                <p style="margin: 0">
                    <b>INCIDENCE DETAILS</b><br/>
                    Reason: {{ $statusName }}<br/>
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
            This is a charge delivery. Please have prepared the amount of {{ money($shipment->charge_price, Setting::get('app_currency')) }} in cash.
            <br/>
        @endif

        @if($shipment->obs)
            {!! $shipment->obs !!}
        @endif
    </p>
@endif
@stop