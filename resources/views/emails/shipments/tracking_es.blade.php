@extends(app_email_layout())

@section('content')
    @if(config('app.source') == 'ontimeservices')
        <h5 style="font-size: 16px">Estimado/a {{ @$shipment->customer->name }},</h5>
        <p>
            Su carga para {{ @$shipment->recipient_name }} ha sido registrado con el número {{ $shipment->tracking_code }}.
            <br/>
            <table style="width: 100%">
                <tr>
                    <td style="width: 50%">
                        <h4 style="margin: 0">CARGA</h4>
                        <b>
                            {{ $shipment->sender_name }}<br/>
                            {{ $shipment->sender_address }}<br/>
                            {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                        </b>
                        <br/>
                        Fecha: {{ $shipment->shipping_date }}
                    </td>
                    <td style="width: 50%">
                        <h4 style="margin: 0">DESCARGA</h4>
                        <b>
                            {{ $shipment->recipient_name }}<br/>
                            {{ $shipment->recipient_address }}<br/>
                            {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                        </b>
                        <br/>
                        Fecha: {{ $shipment->delivery_date }}
                    </td>
                </tr>
            </table>
        </p>
        <p>
            Puede seguir el estado del pedido en nuestro portal online.<br/>
            <h4 style="margin-top: 10px; margin-bottom: 0">Número de Rastreo: {{ $shipment->tracking_code }}</h4>
            <h4 style="margin: 0; float: left">Rastreo Online:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
            <br/>
        </p>
    @else
        <h5>Estimado/a {{ $shipment->recipient_name }},</h5>
        {{--@if(@$shipment->service->code == 'RED')
            <p>
                O nosso cliente {{ $shipment->sender_name }} instruiu-nos para lhe entregar a uma encomenda nas nossas instalações.
                <br/>
                Deverá deslocar-se até às nossas instalações para proceder ao levantamento da sua encomenda.
                <br/>
                <b>
                    {{ @$shipment->agency->company }}<br/>
                    {{ @$shipment->agency->address }}<br/>
                    {{ @$shipment->agency->zip_code }} {{ @$shipment->agency->city }}
                </b>
                <br/>
                Tlf: {{ @$shipment->agency->phone }} / Tlm:{{ @$shipment->agency->mobile }}
            </p>
            <h4>
                Número de encomenda: <span style="font-weight: bold">{{ $shipment->tracking_code }}</span>
            </h4>
        @else--}}
        <p>
            @if(in_array(Setting::get('app_mode'), ['freight', 'cargo']))
                Nuestro cliente {{ @$shipment->customer->name }} nos ha solicitado el siguiente servicio:
                <br/>
                <table style="width: 100%">
                    <tr>
                        <td style="width: 50%">
                            <h4 style="margin: 0">CARGA</h4>
                            <b>
                                {{ $shipment->sender_name }}<br/>
                                {{ $shipment->sender_address }}<br/>
                                {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                            </b>
                            <br/>
                            Fecha: {{ $shipment->shipping_date }}
                        </td>
                        <td style="width: 50%">
                            <h4 style="margin: 0">DESCARGA</h4>
                            <b>
                                {{ $shipment->recipient_name }}<br/>
                                {{ $shipment->recipient_address }}<br/>
                                {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                            </b>
                            <br/>
                            Fecha: {{ $shipment->delivery_date }}
                        </td>
                    </tr>
                </table>
            @else
                Nuestro cliente {{ @$shipment->customer->name }} nos instruyó para hacer una entrega en la siguiente dirección:
                <br/>
                <b>
                    {{ $shipment->recipient_name }}<br/>
                    {{ $shipment->recipient_address }}<br/>
                    {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                </b>
            @endif
        </p>
        <p>
            Puede seguir el estado del pedido en nuestro portal online.<br/>
            <h4 style="margin-top: 10px; margin-bottom: 0">Número de Rastreo: {{ $shipment->tracking_code }}</h4>
            <h4 style="margin: 0; float: left">Rastreo Online:</h4>&nbsp;&nbsp;<a href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}">{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}</a>
            <br/>
        </p>
        {{--@endif--}}

        @if($shipment->charge_price || $shipment->obs)
            <p>
                <b>Informaciones Adicionales:</b><br/>
                @if($shipment->charge_price)
                    Este es un pedido con coste de entrega. Por favor, tenga preparada la cantidad de {{ money($shipment->charge_price, Setting::get('app_currency')) }} para pagar en el acto de la entrega.
                    <br/>
                @endif

                @if($shipment->obs)
                    {{ $shipment->obs }}
                @endif
            </p>
        @endif
    @endif
@stop