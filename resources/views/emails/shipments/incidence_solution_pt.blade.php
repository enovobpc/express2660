@extends(app_email_layout())

@section('content')
<h5>Estimado parceiro,</h5>
<p>
    Informamos que respondemos à incidência gerada para o envio

    <?php $providerTrk = $shipment->tracking_code ?>
    @if($shipment->webservice_method && $shipment->provider_tracking_code)
        @if($shipment->webservice_method == 'gls_zeta')
            <?php $providerTrk = $shipment->provider_tracking_code ?>
            <b>GLS</b> com o código de barras <b>{{ $providerTrk }}</b>
        @elseif($shipment->webservice_method == 'envialia')
            <?php $providerTrk = $shipment->provider_cargo_agency . ' ' . $shipment->provider_tracking_code ?>
            <b>Enviália</b> com o código <b>{{ $providerTrk }}</b>
        @elseif($shipment->webservice_method == 'tipsa')
            <?php $providerTrk = $shipment->provider_cargo_agency . ' ' . $shipment->provider_tracking_code ?>
            <b>TIPSA</b> com o código <b>{{ $providerTrk }}</b>
        @elseif($shipment->webservice_method == 'ctt' || $shipment->webservice_method == 'ctt_correios')
            <?php
            $providerTrk = explode(',', $shipment->provider_tracking_code);
            $providerTrk = @$providerTrk[0];
            ?>
            <b>CTT</b> com o código <b>{{ $providerTrk }}</b>
        @else
            <?php $providerTrk = $shipment->provider_tracking_code ?>
            com o código <b>{{ $providerTrk }}</b>
        @endif
    @else
        com o código <b>{{ $shipment->tracking_code }}</b>
    @endif
<hr>
<table>
    <tr>
        <td style="width: 50px">
            <img src="{{ asset('assets/img/default/mail-box-check.png') }}" height="50" style="width: 50px; margin-top: 5px;margin-left: 15px;margin-right: 15px;">
        </td>
        <td style="width: 130px; border-right: 1px solid #ccc">
            <p style="margin: 0">
                <b>SOLUÇÃO</b><br/>
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
                <b>INCIDÊNCIA</b><br/>
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
<h4 style="margin: 10px 0 5px;">DETALHE DO ENVIO</h4>
<p style="margin-bottom: 0;">
    Fornecedor: {{ $shipment->provider->name }}<br/>
    Código Envio: {{ $providerTrk }}
</p>
<table style="width: 100%; margin-top: 0px">
    <tr>
        <td style="width: 225px">
            <b>Remetente</b>

            <p style="font-size: 12px">
                {{ $shipment->sender_name }}<br/>
                {{ $shipment->sender_address }}<br/>
                {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}<br/>
                {{ trans('country.' . $shipment->sender_country) }}<br/>
            </p>
        </td>
        <td style="width: 225px">
            <b>Destinatário</b>
            <br/>
            <p style="font-size: 12px">
                {{ $shipment->recipient_name }}<br/>
                {{ $shipment->recipient_address }}<br/>
                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}<br/>
                {{ trans('country.' . $shipment->recipient_country) }}<br/>
            </p>
        </td>
        <td style="width: 150px">
            <p style="font-size: 12px">
                <b>Data:</b> {{ $shipment->date }}<br/>
                <b>Ref.:</b> TRK{{ $shipment->tracking_code }}<br/>
                <b>Volumes</b> {{ $shipment->volumes }}<br/>
                <b>Peso (Kg)</b> {{ $shipment->weight }}<br/>
                <b>Cobrança</b> {{ money($shipment->charge_price, Setting::get('app_currency')) }}<br/>
                <b>Observ:</b> {{ $shipment->obs }}
            </p>
        </td>
    </tr>
</table>
<p>
    Por favor, solicitamos que procedam à resolução em conformidade o mais breve possível.
    <br/>
    Caso necessite, fale connosco pelos contactos abaixo.
</p>

@stop