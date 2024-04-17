<?php
$showFullAddr = false;
?>

@if($shipments->isEmpty())
    <div class="m-t-140 m-b-145 text-center">
        <h3 class="text-muted">
            <i class="fas fa-truck fs-40"></i><br/>
            @trans('Sem serviços associados')
        </h3>
        <p class="text-muted">
            @trans('Este manifesto não tem nenhum serviço adicionado. Utilize o botão acima para adicionar serviços.')
        </p>
    </div>
@else
<table class="table table-shipments table-condensed table-hover table-dashed m-t-10 m-b-0">
    <thead>
    <tr>
        <th class="bg-gray w-1">{{ Form::checkbox('select-all', '') }}</th>
{{--        <th class="bg-gray w-1">#</th>--}}
        <th class="bg-gray w-100px">TRK</th>
        <th class="bg-gray w-100px">@trans('Referência')</th>
        {{-- <th class="bg-gray">@trans('Origem')</th> --}}
        <th class="bg-gray w-1px">@trans('Operação')</th>
        <th class="bg-gray">@trans('Destino')</th>
        <th class="bg-gray w-1">@trans('Serviço')</th>
        <th class="bg-gray w-1">@trans('Mercadoria')</th>
        @if(app_mode_cargo())
            <th class="bg-gray w-90px">@trans('Carga')</th>
            <th class="bg-gray w-95px">@trans('Descarga')</th>
        @else
            <th class="bg-gray w-90px">@trans('Recolha')</th>
            <th class="bg-gray w-95px">@trans('Prv. Entrega')</th>
            <th class="bg-gray w-70px">@trans('Prv. Fim')</th>
        @endif
        <th class="bg-gray w-90px">@trans('Ult. Estado')</th>
{{--        <th class="bg-gray w-50px">@trans('Janela Horária')</th>
        <th class="bg-gray w-50px">@trans('Hora Fim')</th>--}}
        <th class="bg-gray w-100px">@trans('Preço')</th>
        <th class="bg-gray w-1"></th>
    </tr>
    </thead>
    <tbody class="sortable">
    <?php
    $totalVolumes = 0;
    $totalWeight  = 0;
    $totalVolume  = 0;
    $totalLDM     = 0;
    $totalCharge  = 0;
    $totalCOD     = 0;
    $totalPrice   = 0;
    $rowId        = 0;
    ?>
    @foreach($shipments as $key => $shipment)
        <?php
        $totalVolumes+= $shipment->volumes;
        $totalWeight+= $shipment->weight;
        $totalLDM+= $shipment->ldm;
        $totalVolume+= $shipment->fator_m3;
        $totalCharge+= $shipment->charge_price;
        $totalCOD+= !empty($shipment->cod) ? $shipment->billing_subtotal : 0;
        $totalPrice+= $shipment->billing_subtotal;
        $rowId++;
        ?>
        <tr data-id="{{ $shipment->id }}" 
            data-trk="{{ $shipment->tracking_code }}" 
            data-lat="{{ $shipment->recipient_latitude }}" 
            data-lng="{{ $shipment->recipient_longitude }}"
            data-addr="{{ $shipment->recipient_full_address }}"
            data-html="{{ view('admin.maps.partials.map_infowindow_shipment', compact('shipment')) }}"
            style="line-height: 15px;">
            <td class="w-1">
                <?php $row = $shipment; ?>
                @include('admin.partials.datatables.select')
                {{--<div class="text-center">{{ $rowId }}</div>--}}
            </td>
            <td>
                <a href="{{ route('admin.shipments.show', $shipment->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xl"
                   class="bold text-blue">
                    {{ $shipment->tracking_code }}
                </a>
                <a href="{{ route('admin.shipments.history.create', $shipment->id) }}" data-toggle="modal" data-target="#modal-remote" style="cursor: pointer">
                    <span class="label" style="background: {{ @$shipment->status->color }}">
                        {{ @$shipment->status->name }}
                    </span>
                </a>
            </td>
            <td>
                @if($shipment->reference)
                <div>{{ $shipment->reference }}</div>
                @endif
                @if($shipment->reference2)
                    <div>{{ $shipment->reference2 }}</div>
                @endif
                @if($shipment->reference3)
                    <div>{{ $shipment->reference3 }}</div>
                @endif
            </td>
            <td class="text-center">
                {{ @$shipment->transport_type->name }}
            </td>
            {{-- <td style="line-height: 15px;">
                {{ $shipment->sender_name }}<br/>
                <small class="text-muted">
                    @if($showFullAddr)
                    {{ $shipment->sender_address }}<br/>
                    @endif
                    <i class="flag-icon flag-icon-{{ $shipment->sender_country }}"></i> {{ $shipment->sender_zip_code }} {{ $shipment->sender_city }}
                </small>
            </td> --}}
            <td style="line-height: 15px;">
                {{ $shipment->recipient_name }}<br/>
                <small class="text-muted">
                    @if($showFullAddr)
                    {{ $shipment->recipient_address }}<br/>
                    @endif
                    <i class="flag-icon flag-icon-{{ $shipment->recipient_country }}"></i> {{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}
                </small>
            </td>
            <td class="text-center">
                {{ @$shipment->service->display_code }}<br/>
                <span class="label" style="background: {{ @$shipment->provider->color }}">
                    {{ @$shipment->provider->name }}
                </span>
            </td>
            <td>
                @include('admin.shipments.shipments.datatables.volumes')
            </td>
            <td>
                {{ $shipment->date }}
                <?php
                $shpDt = new Date($shipment->date);
                $manifestDate = new Date($trip->pickup_date);
                $days = $shpDt->diffInDays($manifestDate);
                ?>
                <div><small class="text-muted italic">{{ $days }} @trans('dias')</small></div>

                {{--@if($row->tags)
                    {!! $row->tagsHtml !!}
                @endif

                @if($row->obs)
                <span class="label bg-aqua m-r-3 p-l-6 p-r-6" data-toggle="tooltip" title="Obs: {{ $row->obs }}">
                    <i class="fas fa-info"></i>
                </span>
                @endif

                @if($row->obs_internal)
                <span class="label bg-blue m-r-3 p-l-6 p-r-6" data-toggle="tooltip" title="Obs Internas: {{ $row->obs_internal }}">
                    <i class="fas fa-info"></i>
                </span>
                @endif--}}
            </td>
            @if(app_mode_cargo())
                <td>{{ $shipment->delivery_date ? $shipment->delivery_date->format('Y-m-d H:i') : '' }}</td>
            @else
                <td class="text-center">
                    <div class="bold">
                        @if($shipment->start_hour && $shipment->end_hour > '00:00')
                            {{ $shipment->start_hour }} - {{ $shipment->end_hour }}
                        @endif
                    </div>
                </td>
                <td class="text-center">{{ $shipment->estimated_delivery_finish ?? '' }}</td>
            @endif

            <td class="text-center">
                @if($shipment->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
                <small class="status-date text-green">
                    <i class="fas fa-circle"></i> {{ $shipment->delivered_date }}
                </small>
                @endif
            </td>
            {{--<td>{{ $shipment->start_hour ? $shipment->start_hour : $shipment->estimated_delivery_time_min }}</td>
                        <td>{{ $shipment->end_hour ? $shipment->end_hour : $shipment->estimated_delivery_time_max }}</td>--}}

            <td>
                <div class="text-right">
                    <a href="{{ route('admin.shipments.edit', [$shipment->id, 'origin' => 'delivery-maps']) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xl" class="text-black bold">
                        @if($shipment->cod == 'D')
                            <span class="label bg-orange" data-toggle="tooltip" title="Portes no Destino">
                                <b>D</b> {{ money($shipment->billing_subtotal, $shipment->currency) }}
                            </span>
                        @elseif($shipment->cod == 'S')
                            <span class="label bg-orange" data-toggle="tooltip" title="Portes na Recolha">
                                <b>R</b> {{ money($shipment->billing_subtotal, $shipment->currency) }}
                            </span>
                        @elseif(!empty($shipment->requested_by) && $shipment->requested_by != $shipment->customer_id)
                            <span class="text-orange" data-toggle="tooltip" title="Portes no Destino. Faturação mensal ao destinatário">
                                <i class="fas fa-user"></i> {{ money($shipment->billing_subtotal ? $shipment->shipping_price : 0, $shipment->currency) }}
                            </span>
                        @elseif($shipment->ignore_billing)
                            <strike class="text-muted" data-target="tooltip" title="Portes Pagos. Ignorado da faturação">
                                {{ money($shipment->billing_subtotal, $shipment->currency) }}
                            </strike>
                        @elseif(!$shipment->price_fixed && (empty($shipment->shipping_price) || $shipment->shipping_price == 0.00))
                            <div class="text-red">
                                <i class="fas fa-exclamation-circle"></i> N/A
                            </div>
                        @else
                            <span data-total="{{ $shipment->billing_subtotal }}">
                                {{ money($shipment->billing_subtotal, $shipment->currency) }}
                            </span>
                        @endif
                    </a>
                    @if($shipment->cost_billing_subtotal > 0.00)
                    <div>
                        <small class="text-muted">
                            {{ money($shipment->cost_billing_subtotal, $shipment->currency) }}
                            <?php $balance = number($shipment->gain_percent, 0); ?>
                            <div class="{{ $balance > 0.00 ? 'text-green' : 'text-red' }}">(<i class="fas fa-caret-up"></i>{{ $balance }}%)</div>
                        </small>
                    </div>
                    @endif
                </div>
                @if($shipment->charge_price)
                <div class="text-right">
                    <span class="label bg-purple" data-toggle="tooltip" title="@trans('Cobrança'): {{ money($shipment->charge_price, Setting::get('app_currency')) }}"><i class="fas fa-euro-sign"></i></span>
                </div>
                @endif
            </td>
            <td>
                <div class="btn-group btn-group-xs">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Opções Extra</span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="{{ route('admin.shipments.edit', [$shipment->id, 'origin' => 'trip']) }}"
                               data-toggle="modal"
                               data-target="#modal-remote-xl">
                                <i class="fas fa-fw fa-pencil-alt"></i> @trans('Editar Serviço')
                            </a>
                        </li>
                        <li class="divider"></li>
                        @if(app_mode_cargo())
                        <li>
                            <a href="{{ route('admin.printer.shipments.cmr', [$shipment->id]) }}"
                               target="_blank">
                                <i class="fas fa-fw fa-print"></i> CMR
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ route('admin.printer.shipments.transport-guide', [$shipment->id]) }}"
                               target="_blank">
                                <i class="fas fa-fw fa-print"></i> @trans('Guia Transporte')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.printer.shipments.labels', [$shipment->id]) }}"
                               target="_blank">
                                <i class="fas fa-fw fa-print"></i> @trans('Etiquetas')
                            </a>
                        </li>
                        @if(hasModule('invoices') && Auth::user()->ability(Config::get('permissions.role.admin'),'billing'))
                            <li role="separator" class="divider"></li>
                            @if($row->invoice_doc_id && $row->invoice_draft)
                                <li>
                                    <a href="{{ route('admin.invoices.edit', ['0', 'customer' => $row->customer_id, 'invoice-id' => $row->invoice_doc_id, 'key' => $row->invoice_key]) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote-xl">
                                        <i class="fas fa-fw fa-pencil-alt"></i> @trans('Editar Rascunho')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.invoices.convert', [$row->customer_id, $row->invoice_doc_id, 'key' => $row->invoice_key]) }}"
                                       data-method="post"
                                       data-confirm="@trans('Confirma a conversão do rascunho criado em fatura?')"
                                       data-confirm-title="@trans('Confirmar conversão de rascunho.')"
                                       data-confirm-label="@trans('Converter')"
                                       data-confirm-class="btn-success">
                                        <i class="fas fa-fw fa-exchange-alt"></i> @trans('Converter em Fatura')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id, $row->invoice_doc_id, 'id' => $row->invoice_id, 'key' => $row->invoice_key, 'serie' => $row->doc_type]) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote"
                                       class="text-red">
                                        <i class="fas fa-fw fa-trash-alt"></i> @trans('Anular Rascunho')
                                    </a>
                                </li>
                            @elseif($row->invoice_type == 'nodoc')
                                <li>
                                    <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id, $row->invoice_doc_id, 'key' => $row->invoice_key, 'serie' => $row->doc_type]) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote"
                                       class="text-red">
                                        <i class="fas fa-fw fa-trash-alt"></i> @trans('Anular como faturado')
                                    </a>
                                </li>
                            @elseif($row->invoice_doc_id && !$row->invoice_draft)
                                <li>
                                    <a href="{{ route('admin.invoices.download', [$row->customer_id, $row->invoice_doc_id, 'key' => $row->invoice_key]) }}"
                                       target="_blank">
                                        <i class="fas fa-fw fa-print"></i> @trans('Imprimir Fatura')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.invoices.summary', [$row->customer_id, $row->invoice_doc_id, 'key' => $row->invoice_key]) }}" target="_blank">
                                        <i class="fas fa-fw fa-print"></i> @trans('Imprimir Resumo')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.invoices.email.edit', [$row->customer_id, $row->invoice_doc_id, 'key' => $row->invoice_key]) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote">
                                        <i class="fas fa-fw fa-envelope"></i> @trans('Enviar por E-mail')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id, $row->invoice_doc_id, 'id' => $row->invoice_id, 'key' => $row->invoice_key, 'serie' => $row->doc_type]) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote"
                                       class="text-red">
                                        <i class="fas fa-fw fa-trash-alt"></i> @trans('Anular Fatura')
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a href="{{ route('admin.shipments.invoices.create', $row->id) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote-xl"
                                       class="text-blue">
                                        <i class="fas fa-fw fa-file-alt"></i> @trans('Emitir Fatura Individual')
                                    </a>
                                </li>
                            @endif
                        @endif
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('admin.trips.shipments.add-selected', ['id[]' => $shipment->id]) }}"
                                data-toggle="modal"
                                data-target="#modal-remote-lg"
                                data-action-url="datatable-action-url">
                                <i class="fas fa-fw fa-exchange-alt"></i> @trans('Transferir de mapa')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.trips.shipments.remove', [$trip->id, $shipment->id]) }}" data-method="post"
                               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                                <i class="fas fa-fw fa-trash-alt"></i> @trans('Remover do Mapa')
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="row">
    <div class="col-sm-12">
        <div class="shipments-totals">
            <ul class="list-inline pull-right text-right">
                <li>
                    <h4>
                        <small>@trans('Volumes')</small><br/>
                        <i class="fas fa-pallet"></i> {{ $totalVolumes }}
                    </h4>
                </li>
                <li>
                    <h4>
                        <small>@trans('Peso')</small><br/>
                        {{ money($totalWeight) }}
                    </h4>
                </li>
                <li>
                    <h4>
                        <small>@trans('Volume')</small><br/>
                        {{ money($totalVolume) }}
                    </h4>
                </li>
                @if(app_mode_cargo())
                <li>
                    <h4>
                        <small>LDM</small><br/>
                        {{ money($totalLDM, 'mt') }}
                    </h4>
                </li>
                @endif
                <li>
                    <h4>
                        <small>@trans('Reembolsos')</small><br/>
                        {{ money($totalCharge, Setting::get('app_currency')) }}
                    </h4>
                </li>
                <li>
                    <h4>
                        <small>@trans('Portes Dest.')</small><br/>
                        {{ money($totalCOD, Setting::get('app_currency')) }}
                    </h4>
                </li>
            </ul>
        </div>

    </div>
</div>
@endif