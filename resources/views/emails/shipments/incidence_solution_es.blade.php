@extends(app_email_layout())

@section('content')
<h5>Estimado parcero,</h5>
<p>
    Informamos que hemos respondido al incidencia generada por el envío

    <?php $providerTrk = $shipment->tracking_code ?>
    @if($shipment->webservice_method && $shipment->provider_tracking_code)
        @if($shipment->webservice_method == 'gls_zeta')
            <?php $providerTrk = $shipment->provider_tracking_code ?>
            <b>GLS</b>con el albáran <b>{{ $providerTrk }}</b>
        @elseif($shipment->webservice_method == 'envialia')
            <?php $providerTrk = $shipment->provider_cargo_agency . ' ' . $shipment->provider_tracking_code ?>
            <b>Enviália</b>con el albáran <b>{{ $providerTrk }}</b>
        @elseif($shipment->webservice_method == 'tipsa')
            <?php $providerTrk = $shipment->provider_cargo_agency . ' ' . $shipment->provider_tracking_code ?>
            <b>TIPSA</b> con el albáran <b>{{ $providerTrk }}</b>
        @else
            <?php $providerTrk = $shipment->provider_tracking_code ?>
            con el albáran <b>{{ $providerTrk }}</b>
        @endif
    @elseif($shipment->webservice_method == 'ctt' || $shipment->webservice_method == 'ctt_correios')
        <?php
        $providerTrk = explode(',', $shipment->provider_tracking_code);
        $providerTrk = @$providerTrk[0];
        ?>
        <b>CTT</b> con el albáran <b>{{ $providerTrk }}</b>
    @else
        con el albáran <b>{{ $shipment->tracking_code }}</b>
    @endif
<hr>
<table>
    <tr>
        <td style="width: 50px">
            <img src="{{ asset('assets/img/default/mail-box-check.png') }}" height="50" style="width: 50px; margin-top: 5px;margin-left: 15px;margin-right: 15px;">
        </td>
        <td style="width: 130px; border-right: 1px solid #ccc">
            <p style="margin: 0">
                <b>SOLUCIÓN</b><br/>
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
                <b>INCIDENCIA</b><br/>
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
<h4 style="margin: 10px 0 5px;">DETALLE DE ENVIO</h4>
<p style="margin-bottom: 0;">
    Proveedor: {{ $shipment->provider->name }}<br/>
    Albaran: {{ $providerTrk }}
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
            <b>Destinatario</b>
            <p style="font-size: 12px">
                {{ $shipment->recipient_name }}<br/>
                {{ $shipment->recipient_address }}<br/>
                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}<br/>
                {{ trans('country.' . $shipment->recipient_country) }}<br/>
            </p>
        </td>
        <td style="width: 150px">
            <p style="font-size: 12px">
                <b>Fecha:</b> {{ $shipment->date }}<br/>
                <b>Ref.:</b> TRK{{ $shipment->tracking_code }}<br/>
                <b>Cobro:</b> {{ money($shipment->charge_price, Setting::get('app_currency')) }}<br/>
                @if($shipment->obs)
                <b>Obs:</b> {{ $shipment->obs }}
                @endif
            </p>
        </td>
    </tr>
</table>
<p>
    Solicitamos que proceda a la resolución en consecuencia lo antes posible.
    <br/>
    Si lo necesita, hable con nosotros a través de los contactos a continuación.
</p>

@stop