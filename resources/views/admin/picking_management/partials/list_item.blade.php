@if($shipment->exists)
    @if($editMode)
        <tr data-id="{{ $shipment->tracking_code }}"
            data-shpid="{{ $shipment->id }}"
            data-readed-code="{{ $readedTrk }}"
            data-volumes="{{ $shipment->volumes }}"
            data-weight="{{ $shipment->weight }}"
            data-charge="{{ $shipment->charge_price }}"
            data-cod="{{ $shipment->total_price_for_recipient }}"
            class="tr"
            style="{{ $editMode && !$shipment->hasSyncError() ? '' : 'background: #ffcccc' }}">
            <td>
                {{ Form::checkbox('select[]') }}
            </td>
            <td>
                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'shipments'))
                <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="bold" data-toggle="modal" data-target="#modal-remote-xl">
                    {{ $shipment->tracking_code }}
                </a>
                @else
                {{ $shipment->tracking_code }}
                @endif
                @if($shipment->hasSync())
                    <div><small>{{ $shipment->provider_tracking_code }}</small></div>
                @elseif($shipment->hasSyncError())
                    <small class="text-red"><i class="fas fa-exclamation-triangle"></i></small>
                @endif
                <input type="hidden" name="check_list" value="{{ $shipment->check_list }}">
                <input type="hidden" name="code[]" value="{{ $shipment->tracking_code }}">
                <span class="label" style="background: {{ @$shipment->status->color }}">{{ @$shipment->status->name }}</span>
            </td>
            <td>
                {{ $shipment->recipient_name }}<br/>
                <small class="text-muted italic">{{ $shipment->recipient_zip_code }} {{ $shipment->recipient_city }}</small>
            </td>
            <td class="p-l-5">
                <div>{{ $shipment->date }}</div>
                <small>
                    @if($shipment->obs)
                        <span class="label label-info m-r-2" data-toggle="tooltip" title="{{ $shipment->obs }}">
                            <i class="fas fa-info"></i>
                        </span>
                    @endif
                    @if($shipment->charge_price)
                        <span class="label bg-purple m-r-2" data-toggle="tooltip" title="{{ money($shipment->charge_price, Setting::get('app_currency')) }}">
                        <i class="fas fa-euro-sign"></i>
                    </span>
                    @endif
                </small>
            </td>
            <td class="input-sm inpt" style="vertical-align: middle">
                @if($editMode)
                    <div class="h-3px"></div>
                    {{ Form::select('provider', $providers, $shipment->provider_id, ['class' => 'form-control select2 input-sm tr-inpt']) }}
                @else
                    <label class="label" style="background: {{ @$shipment->provider->color }}">{{ @$shipment->provider->name }}</label>
                @endif
            </td>
            <td class="text-center inpt" style="vertical-align: middle">
                @if($editMode)
                    {{ Form::text('bx_volumes', $shipment->volumes, ['class' => 'form-control input-sm tr-inpt']) }}
                @else
                    {{ $shipment->volumes }}
                @endif
            </td>
            <td class="text-center inpt" style="vertical-align: middle">
                @if($editMode)
                    {{ Form::text('bx_weight', $shipment->weight, ['class' => 'form-control input-sm tr-inpt']) }}
                @else
                    {{ money($shipment->weight) }}
                @endif
            </td>
            @if(Setting::get('shipments_custom_provider_weight'))
            <td class="text-center inpt" style="vertical-align: middle">
                @if($editMode)
                {{ Form::text('label_weight', $shipment->label_weight, ['class' => 'form-control input-sm tr-inpt']) }}
                @endif
            </td>
            @endif
            <td class="text-center" style="vertical-align: middle">
                @if($editMode)
                <i class="fas fa-times-circle text-red"></i>
                @endif
            </td>
            <td style="vertical-align: middle">
                @if($editMode)
                <div class="btn-group btn-table-actions">
                    <a href="{{ route('admin.shipments.edit', $shipment->id) }}" class="btn btn-sm btn-default"
                       data-toggle="modal"
                       data-target="#modal-remote-xl">
                        Editar
                    </a>
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Opções</span>
                    </button>

                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="{{ route('admin.shipments.show', $shipment->id) }}"
                               data-toggle="modal"
                               data-target="#modal-remote-xl">
                                <i class="fas fa-fw fa-info-circle"></i> Detalhes do Envio
                            </a>
                        </li>
                        @if(((!empty(Auth::user()->agencies) && in_array($shipment->agency_id, Auth::user()->agencies)) || empty(Auth::user()->agencies) || ($shipment->type == \App\Models\Shipment::TYPE_DEVOLUTION && in_array($shipment->sender_agency_id, Auth::user()->agencies))))
                            @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'edit_shipments') && Auth::user()->showPrices())
                                <li>
                                    <a href="{{ route('admin.change-log.show', ['Shipment', $shipment->id]) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote-lg">
                                        <i class="fas fa-fw fa-history"></i> Histórico de Edições
                                    </a>
                                </li>
                            @endif
                        @endif
                        <li>
                            <a href="{{ route('admin.printer.shipments.transport-guide', $shipment->id) }}" target="_blank" class="text-purple">
                                <i class="fas fa-fw fa-print"></i> Guia de Transporte
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.printer.shipments.labels', $shipment->id) }}" target="_blank" class="text-purple">
                                <i class="fas fa-fw fa-print"></i> Etiquetas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.printer.shipments.value-statement', $shipment->id) }}" target="_blank">
                                <i class="fas fa-fw fa-print"></i> Declaração de Valores
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
            </td>
            <td style="vertical-align: middle">
                {{ Form::open(['route' => 'admin.picking.management.store', 'method' => 'POST']) }}
                <button type="button" class="btn btn-sm btn-default btn-sv-tr" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i>">
                    <i class="fas fa-save"></i>
                </button>
                {{ Form::hidden('id', $shipment->id) }}
                {{ Form::close() }}
            </td>
        </tr>
    @else
        <tr data-id="{{ $shipment->tracking_code }}" class="text-red" style="background: #ff000036">
            <td></td>
            <td>
                <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="bold" data-toggle="modal" data-target="#modal-remote-xl">
                    {{ $shipment->tracking_code }}
                </a>
            </td>
            <td colspan="8">
                <i class="fas fa-exclamation-circle"></i> O estado do envio não permite a sua alteração
            </td>
        </tr>
    @endif
@else
    <tr data-id="{{ $shipment->tracking_code }}" class="text-red" style="background: #ff000036">
        <td></td>
        <td>{{ $shipment->tracking_code }}</td>
        <td colspan="8">
            <i class="fas fa-exclamation-circle"></i> Não foi possível encontrar o envio.
        </td>
    </tr>
@endif