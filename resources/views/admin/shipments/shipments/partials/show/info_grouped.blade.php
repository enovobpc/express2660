<div class="spacer-5"></div>
<div class="form-horizontal">
    <div class="row row-5 main-block">
        <div class="col-sm-8">
            <div class="row row-5">
                <div class="col-sm-4">
                    {{--<h4 class="service-preview">
                        <small>{{ trans('account/global.word.service') }}</small><br/>
                        {{ @$shipment->service->name ? @$shipment->service->name : 'Aguarda atribuição' }}
                    </h4>--}}
                    <div class="form-group m-b-8" style="margin-top: -3px">
                        <label class="col-sm-4 control-label" style="padding: 0">Serviço</label>
                        <div class="col-sm-8">
                            <p class="m-0">{{ @$shipment->service->name ? @$shipment->service->name : 'Aguarda atribuição' }}</p>
                        </div>
                    </div>
                    <div class="form-group m-b-8" style="margin-top: -3px">
                        <label class="col-sm-4 control-label" style="padding: 0">Fornecedor</label>
                        <div class="col-sm-8">
                            <p class="m-0">
                                <span class="label" style="background: {{ @$shipment->provider->color }}">
                                    {{ @$shipment->provider->name }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group m-b-0" style="margin-top: -3px">
                        <label class="col-sm-4 control-label" style="padding: 0">Data Envio</label>
                        <div class="col-sm-8">
                            <p class="m-0">
                                @if($shipment->shipping_date)
                                    <?php $shipment->shipping_date = new Date($shipment->shipping_date)?>
                                    {{ $shipment->shipping_date->format('Y-m-d') }} {{ $shipment->shipping_date->format('H:i') == '00:00' ? '' : $shipment->shipping_date->format('H:i') }}
                                @else
                                    {{ $shipment->date }}
                                    @if($shipment->start_hour)
                                        <br/>{{ $shipment->start_hour }}
                                    @endif
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    @if(Auth::user()->showPrices() && (!$userAgencies || $userAgencies && in_array($shipment->agency_id, $userAgencies)))
                    <div class="form-group m-b-2">
                        <label class="col-sm-4 control-label" style="padding: 0">Cliente Paga</label>
                        <div class="col-sm-8">
                            <a href="{{ route('admin.customers.edit', $shipment->customer_id) }}" target="_blank" class="m-0 text-uppercase" data-toggle="tooltip" title="{{ @$shipment->customer->code }} - {{ @$shipment->customer->name }}">
                            {{ str_limit(@$shipment->customer->name, 18) }} <i class="fas fa-external-link-square-alt"></i>
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="form-group m-b-2">
                        <label class="col-sm-4 control-label" style="padding: 0">Agência Paga</label>
                        <div class="col-sm-8">
                            <p class="m-0">{{ @$shipment->agency->name }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="form-group m-b-2">
                        <label class="col-sm-4 control-label" style="padding: 0">A. Origem</label>
                        <div class="col-sm-8">
                            <p class="m-0">{{ @$shipment->senderAgency->name }}</p>
                        </div>
                    </div>
                    <div class="form-group m-b-2">
                        <label class="col-sm-4 control-label" style="padding: 0">A. Destino</label>
                        <div class="col-sm-8">
                            <p class="m-0">{{ @$shipment->recipientAgency->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group m-b-2">
                        <label class="col-sm-5 control-label" style="padding: 0">Prev. Entrega</label>
                        <div class="col-sm-7">
                            <p class="m-0">
                                <?php $shipment->delivery_date = new Date($shipment->delivery_date)?>
                                {{ $shipment->delivery_date->format('Y-m-d H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="form-group m-b-2" style="margin-top: -3px">
                        <label class="col-sm-5 control-label" style="padding: 0">Entrege em</label>
                        <div class="col-sm-7">
                            <p class="m-0">
                                @if(@$shipment->lastHistory->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
                                    <?php $shipment->shipping_date = new Date($shipment->shipping_date)?>
                                    {{ $shipment->lastHistory->created_at->format('Y-m-d H:i') }}
                                @else
                                    ---
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        <label class="col-sm-5 control-label" style="padding: 0">
                            @if($shipment->is_collection)
                            Tempo Recolha
                            @else
                            Tempo Entrega
                            @endif
                        </label>
                        <div class="col-sm-7">
                            <p class="m-0">
                                {{ $shipment->transit_time }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">

            @if(Auth::user()->showPrices() && (!$userAgencies || $userAgencies && in_array($shipment->agency_id, $userAgencies)))
                @if(hasModule('statistics'))
                    <h3 class="price-preview bold pull-right">
                        <small>Ganhos</small>
                        <div class="m-t-3">
                            @if(@$shipmentTotals['gain'] > 0.00)
                                <span class="text-green" style="display: block; line-height: 15px">
                                <b>{{ money(@$shipmentTotals['gain'], Setting::get('app_currency')) }}</b>
                            </span>
                            @else
                                <span class="text-red" style="display: block; line-height: 15px">
                                <b>{{ money(@$shipmentTotals['gain'], Setting::get('app_currency')) }}</b>
                            </span>
                            @endif
                        </div>
                        <div><small>{{ money(@$shipmentTotals['gain_percent'], '%') }}</small></div>
                    </h3>
                @endif
                <h3 class="price-preview bold pull-right" style="line-height: 19px">
                    <small>
                        Preço
                        @if($shipment->payment_at_recipient)
                            <span class="label label-warning" data-toggle="tooltip" title="Portes no Destino">PGD</span>
                        @endif
                        @if($shipment->ignore_billing || $shipment->invoice_id)
                            <span class="label label-success" data-toggle="tooltip" title="O envio foi marcado como pago.">PAGO</span>
                        @endif
                    </small>
                    <div class="m-t-3">
                        <b>
                            @if($shipment->payment_at_recipient)
                                {{ money($shipment->total_price_for_recipient + $shipment->total_expenses, Setting::get('app_currency')) }}
                            @else
                                @if($groupedShipments)
                                    {{ money(@$shipmentTotals['price'], Setting::get('app_currency')) }}
                                @else
                                    {{ money(@$shipmentTotals['price'], Setting::get('app_currency')) }}
                                @endif
                            @endif
                        </b>

                    </div>
                    <div><small>Custo: {{ money(@$shipmentTotals['cost'], Setting::get('app_currency')) }}</small></div>
                </h3>
            @endif

            @if($shipment->charge_price > 0.00)
                <h4 class="price-preview text-blue pull-right">
                    <small class="text-blue">Cobrança</small><br/>
                    <i class="fas fa-hand-holding-usd"></i> {{ money($shipment->charge_price, Setting::get('app_currency'))}}
                </h4>
            @endif

        </div>
    </div>

    @if(!$groupedShipments->isEmpty())
        @foreach($groupedShipments as $groupedShipment)
            <div class="row">
                <div class="col-xs-12">
                    <div style="padding: 10px;margin-bottom: 8px;border: 1px solid #fff; border-left: 5px solid #ff5f01; border-radius: 0 4px 4px 0;background: #fff;">
                        <div class="row">
                            <div class="col-sm-2">
                                <h4 class="m-0 m-b-5 bold">{{ $groupedShipment->tracking_code }}</h4>
                                <span class="label" style="background: {{ @$groupedShipment->provider->color }}">
                                    {{ @$groupedShipment->provider->name }}
                                </span>
                                <div class="m-t-5">
                                    <a href="{{ route('admin.shipments.history.create', $groupedShipment->id) }}" data-toggle="modal" data-target="#modal-remote">
                                        <span class="label" style="background: {{ @$groupedShipment->status->color }}">
                                            {{ @$groupedShipment->status->name }}
                                        </span>
                                    </a>
                                </div>
                                <p class="m-t-5">
                                    {{ $groupedShipment->reference ? 'Ref#:' . $groupedShipment->reference : '' }}<br/>
                                    {{ $groupedShipment->reference2 ? 'Ref#2:' . $groupedShipment->reference2 : '' }}
                                </p>
                                <p class="m-0">
                                    Preço: {{ money($groupedShipment->total_price + $groupedShipment->total_expenses, Setting::get('app_currency')) }}<br/>
                                    Custo: {{ money($groupedShipment->cost_price + $groupedShipment->total_expenses_cost, Setting::get('app_currency')) }}<br/>
                                </p>
                            </div>
                            <div class="col-sm-2">
                                <table class="table-condensed m-0 table-list">
                                    <tr>
                                        <td class="w-80px">Volumes</td>
                                        <td>{{ $groupedShipment->volumes }}</td>
                                    </tr>
                                    <tr>
                                        <td>LDM</td>
                                        <td>{{ $groupedShipment->ldm }}</td>
                                    </tr>
                                    <tr>
                                        <td>Kms</td>
                                        <td>{{ $groupedShipment->kms }}</td>
                                    </tr>
                                    <tr>
                                        <td>Peso Real</td>
                                        <td>{{ money($groupedShipment->weight) }}kg</td>
                                    </tr>
                                    <tr>
                                        <td>Peso Vol.</td>
                                        <td>{{ money($groupedShipment->volumetric_weight) }}kg</td>
                                    </tr>
                                    <tr>
                                        <td>Peso Tax.</td>
                                        <td>{{ money($groupedShipment->weight > $groupedShipment->volumetric_weight ? $groupedShipment->weight : $groupedShipment->volumetric_weight) }}kg</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-7" style="width: 63%;">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="local-title">
                                            <b>CARGA</b>
                                            <span style="position: absolute; right: 20px;">
                                                <i class="fas fa-calendar-alt"></i> {{ substr($groupedShipment->shipping_date, 0, 16) }}
                                            </span>
                                        </p>
                                        <p style="min-height: 85px">
                                            {{ $groupedShipment->sender_name }}<br/>
                                            <small class="text-muted text-uppercase">
                                                {{ $groupedShipment->sender_address }}<br/>
                                                {{ $groupedShipment->sender_zip_code }} {{ $groupedShipment->sender_city }}
                                                {{ trans('country.' . $groupedShipment->sender_country) }}
                                            </small>
                                        </p>
                                        @if($groupedShipment->obs)
                                        <p style="border-top: 1px solid #ccc;" class="m-0">
                                            {{ $groupedShipment->obs }}
                                        </p>
                                        @endif
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="local-title">
                                            <b>DESCARGA</b>
                                            <span style="position: absolute; right: 20px;">
                                                <i class="fas fa-calendar-alt"></i> {{ substr($groupedShipment->delivery_date, 0, 16) }}
                                            </span>
                                        </p>
                                        <p style="min-height: 85px">
                                            {{ $groupedShipment->recipient_name }}<br/>
                                            <small class="text-muted text-uppercase">
                                                {{ $groupedShipment->recipient_address }}<br/>
                                                {{ $groupedShipment->recipient_zip_code }} {{ $groupedShipment->recipient_city }}
                                                {{ trans('country.' . $groupedShipment->recipient_country) }}
                                            </small>
                                        </p>
                                        @if($groupedShipment->delivery_obs)
                                        <p style="border-top: 1px solid #ccc;" class="m-0">
                                            {{ $groupedShipment->delivery_obs }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-1 btn-options">
                                <div class="btn-group btn-table-actions pull-right">
                                    <button type="button" class="btn btn-xs btn-primary dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="fas fa-cog"></i>
                                        <span class="caret"></span>
                                        <span class="sr-only">Opções Extra</span>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                        <li>
                                            <a href="{{ route('admin.shipments.edit', $groupedShipment->id) }}"
                                            data-toggle="modal" data-target="#modal-remote-xlg">
                                                <i class="fas fa-fw fa-pencil-alt"></i> Editar
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="{{ route('admin.printer.shipments.transport-guide', $groupedShipment->id) }}" target="_blank">
                                                <i class="fas fa-fw fa-print"></i> Guia de Transporte
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.printer.shipments.labels', $groupedShipment->id) }}" target="_blank">
                                                <i class="fas fa-fw fa-print"></i> Etiquetas
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.printer.shipments.cmr', $groupedShipment->id) }}" target="_blank">
                                                <i class="fas fa-fw fa-print"></i> CMR Internacional
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.printer.shipments.shipping-instructions', $groupedShipment->id) }}" target="_blank">
                                                <i class="fas fa-fw fa-print"></i> Instruções de Carga
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="{{ route('admin.shipments.email.edit', [$groupedShipment->id, 'provider']) }}"
                                                data-toggle="modal"
                                                data-target="#modal-remote-lg">
                                                <i class="fas fa-fw fa-envelope"></i> Confirmação/Instruções Carga
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="{{ route('admin.shipments.destroy', [$groupedShipment->id]) }}"
                                               data-method="delete"
                                               data-confirm="Confirma a remoção do registo selecionado?"
                                               class="text-red">
                                                <i class="fas fa-trash-alt"></i> Eliminar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <hr style="margin: 5px 0 10px; border-color: #ccc;"/>
        @endforeach
    @endif
</div>
<style>
    .table-list td {
        padding: 2px 0 !important;
    }

    .local-title {
        margin-bottom: 4px;
        border-radius: 3px;
        background: #eee;
        border: 1px solid #ddd;
        padding: 2px;
    }

    .btn-options {
        position: absolute;
        right: -36px;
    }
</style>