@extends(app_email_layout())

@section('content')
    <div style="width: 480px">
        <div style="text-align: center">
            <h2 style="text-transform: uppercase;margin: 0;">Código Levantamento</h2>
            @if(@$shipment->service->code == 'RED')
                <p style="margin: 0 0 40px; font-size: 15px;">Use o código abaixo para levantar a sua encomenda nas nossas instalações.</p>
            @else
                <p style="margin: 0 0 40px; font-size: 15px;">Use o código abaixo para levantar a sua encomenda.</p>
            @endif
            <img src="{{ $qrCode }}" height="200">
            <h2>TRK #{{ $shipment->tracking_code }}</h2>
            @if(@$shipment->service->code != 'RED')
            <p>Entrega prevista em {{ $shipment->delivery_date }}</p>
            @endif
        </div>
        <hr/>
        <p>
            Olá {{ $shipment->recipient_attn ? $shipment->recipient_attn : $shipment->recipient_name }},<br/>
            @if(@$shipment->service->code == 'RED')
                O nosso cliente {{ @$shipment->customer->name }} solicitou-nos a entrega de uma encomenda com levantamento nas nossas instalações.
            @else
                O nosso cliente {{ @$shipment->customer->name }} solicitou-nos a entrega de uma encomenda na sua morada.
            @endif
        </p>
        @if(@$shipment->service->code == 'RED')
            <table style="width: 100%; text-align: left">
                <tr>
                    <th style="vertical-align: top; font-weight: bold">LOCAL DE LEVANTAMENTO</th>
                </tr>
                <tr>
                    <td style="vertical-align: top">
                        <p style="margin-top: 0">
                            {{ @$shipment->agency->company }}<br/>
                            {{ @$shipment->agency->address }}<br/>
                            {{ @$shipment->agency->zip_code }} {{ @$shipment->agency->city }}
                        </p>
                        <p style="margin-top: 0">
                            Tlf: {{ @$shipment->agency->phone }} @if(@$shipment->agency->mobile) / Tlm:{{ @$shipment->agency->mobile }} @endif
                        </p>
                    </td>
                </tr>
            </table>
        @else
            <table style="width: 100%; text-align: left">
                <tr>
                    <th style="vertical-align: top; font-weight: bold">REMETENTE</th>
                    <th style="vertical-align: top; font-weight: bold">DESTINATÁRIO</th>
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
                Acompanhe a entrega através do endereço que se segue.<br/>
                <a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
            </p>
        @endif
        @if($shipment->charge_price || $shipment->obs)
            <p>
                <b>Informações Adicionais:</b><br/>
                @if($shipment->charge_price)
                    Esta é uma entrega à cobrança.
                    Por favor tenha preparado o valor de {{ money($shipment->charge_price, Setting::get('app_currency')) }} para pagamento no ato da entrega.
                    <br/>
                @endif

                @if($shipment->obs)
                    {{ $shipment->obs }}
                @endif
            </p>
        @endif
    </div>
@stop