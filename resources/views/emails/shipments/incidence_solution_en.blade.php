@extends(app_email_layout())

@section('content')
<h5>Dear partner,</h5>
<p>
    We inform that we have responded to the incidence generated for sending

    <?php $providerTrk = $shipment->tracking_code ?>
    @if($shipment->webservice_method && $shipment->provider_tracking_code)
        @if($shipment->webservice_method == 'gls_zeta')
            <?php $providerTrk = $shipment->provider_tracking_code ?>
            <b>GLS</b> with barcode <b>{{ $providerTrk }}</b>
        @elseif($shipment->webservice_method == 'envialia')
            <?php $providerTrk = $shipment->provider_cargo_agency . ' ' . $shipment->provider_tracking_code ?>
            <b>Enviália</b> with barcode <b>{{ $providerTrk }}</b>
        @elseif($shipment->webservice_method == 'tipsa')
            <?php $providerTrk = $shipment->provider_cargo_agency . ' ' . $shipment->provider_tracking_code ?>
            <b>TIPSA</b> with barcode <b>{{ $providerTrk }}</b>
        @else
            <?php $providerTrk = $shipment->provider_tracking_code ?>
            with barcode <b>{{ $providerTrk }}</b>
        @endif
    @elseif($shipment->webservice_method == 'ctt' || $shipment->webservice_method == 'ctt_correios')
        <?php
        $providerTrk = explode(',', $shipment->provider_tracking_code);
        $providerTrk = @$providerTrk[0];
        ?>
        <b>CTT</b> with barcode <b>{{ $providerTrk }}</b>
    @else
        with barcode <b>{{ $shipment->tracking_code }}</b>
    @endif
<hr>
<table>
    <tr>
        <td style="width: 50px">
            <img src="{{ asset('assets/img/default/mail-box-check.png') }}" height="50" style="width: 50px; margin-top: 5px;margin-left: 15px;margin-right: 15px;">
        </td>
        <td style="width: 130px; border-right: 1px solid #ccc">
            <p style="margin: 0">
                <b>SOLUTION</b><br/>
                <small>{{ @$resolution->created_at }}</small>
            </p>
        </td>
        <td>
            <p style="margin: 0 0 0 10px">
                @if(@$resolution->type->code != '9999')
                    {{ @$resolution->type->name }}<br/>
                @endif
                @if(@$resolution->obs)
                    <i>{!! nl2br($resolution->obs) !!}</i>
                @endif
            </p>
        </td>
    </tr>
</table>
<table style="margin-top: 10px">
    <tr>
        <td style="width: 50px">
            <img src="{{ asset('assets/img/default/mail-box-incidence.png') }}" height="50" style="width: 50px; margin-top: 5px;margin-left: 15px;margin-right: 15px;">
        </td>
        <td style="width: 130px; border-right: 1px solid #ccc">
            <p style="margin: 0">
                <b>INCIDENCE</b><br/>
                <small>{{ @$resolution->history->created_at }}</small>
            </p>
        </td>
        <td>
            <p style="margin: 0 0 0 10px">
                {{ @$resolution->history->incidence->name }}<br/>
                @if(@$resolution->history->obs)
                    <i>{!! nl2br(@$resolution->history->obs) !!}</i>
                @endif
            </p>
        </td>
    </tr>
</table>
</p>
<hr>
<h4 style="margin: 10px 0 5px;">SHIPMENT DETAIL</h4>
<p style="margin-bottom: 0;">
    Provider: {{ $shipment->provider->name }}<br/>
    Tracking: {{ $providerTrk }}
</p>
<table style="width: 100%; margin-top: 0px">
    <tr>
        <td style="width: 225px">
            <b>Sender</b>
            <p style="font-size: 12px">
                {{ $shipment->sender_name }}<br/>
                {{ $shipment->sender_address }}<br/>
                {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}<br/>
                {{ trans('country.' . $shipment->sender_country) }}<br/>
            </p>
        </td>
        <td style="width: 225px">
            <b>Recipient</b>
            <p style="font-size: 12px">
                {{ $shipment->recipient_name }}<br/>
                {{ $shipment->recipient_address }}<br/>
                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}<br/>
                {{ trans('country.' . $shipment->recipient_country) }}<br/>
            </p>
        </td>
        <td style="width: 150px">
            <p style="font-size: 12px">
                <b>Date:</b> {{ $shipment->date }}<br/>
                <b>Ref.:</b> TRK{{ $shipment->tracking_code }}<br/>
                <b>COD:</b> {{ money($shipment->charge_price, Setting::get('app_currency')) }}<br/>
                @if($shipment->obs)
                <b>Obs:</b> {{ $shipment->obs }}
                @endif
            </p>
        </td>
    </tr>
</table>
<p>
    Please request that you proceed to the resolution accordingly as soon as possible.
    <br/>
    If you need, talk to us through the contacts below.
</p>

@stop