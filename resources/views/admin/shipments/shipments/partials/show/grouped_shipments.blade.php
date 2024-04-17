<div>
    @if(!$groupedShipments->isEmpty())
        <table class="table table-condensed table-dashed table-hover m-b-0">
            <tr>
                <th class="w-50px bg-gray-light" style="border-top: none">TRK</th>
                <th class="bg-gray-light" style="border-top: none">Carga</th>
                <th class="bg-gray-light" style="border-top: none">Descarga</th>
                <th class="w-80px bg-gray-light" style="border-top: none">Fornecedor</th>
                <th class="w-120px bg-gray-light" style="border-top: none">Remessa</th>
                <th class="w-80px bg-gray-light" style="border-top: none">Viatura</th>
                <th class="w-80px bg-gray-light" style="border-top: none">Datas</th>
                <th class="w-60px bg-gray-light" style="border-top: none">Custo</th>
                <th class="w-1 bg-gray-light" style="border-top: none">Ações</th>
            </tr>
            <tbody>
            @foreach($groupedShipments as $groupedShipment)
                <tr>
                    <td>
                        <a href="{{ route('admin.shipments.show', [$groupedShipment->id]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xlg">
                            {{ $groupedShipment->tracking_code }}
                        </a>

                    </td>
                    <td>
                        {{ $groupedShipment->sender_name }}<br/>
                        <small class="text-muted italic">
                            {{ $groupedShipment->sender_zip_code }} {{ $groupedShipment->sender_city }}
                        </small>
                    </td>
                    <td>
                        {{ $groupedShipment->recipient_name }}<br/>
                        <small class="text-muted italic">
                            {{ $groupedShipment->recipient_zip_code }} {{ $groupedShipment->recipient_city }}
                        </small>
                    </td>
                    <td>
                        <span class="label" style="background: {{ @$groupedShipment->provider->color }}">
                            {{ @$groupedShipment->provider->name }}
                        </span>
                    </td>
                    <td>
                        <?php $row = $groupedShipment?>
                        @include('admin.shipments.shipments.datatables.volumes')
                    </td>
                    <td>
                        {{ $groupedShipment->vehicle }}<br/><small>{{ $groupedShipment->trailer }}</small>
                    </td>
                    <td>
                        {{ substr($groupedShipment->shipping_date, 0, 10) }}<br/>
                        {{ substr($groupedShipment->delivery_date, 0 ,10) }}
                    </td>
                    <td class="text-center">
                        {{ money($groupedShipment->cost_price, Setting::get('app_currency')) }}
                        @if($groupedShipment->total_expenses_cost > 0.00)
                            <span class="label label-success">+{{ money($groupedShipment->total_expenses_cost, Setting::get('app_currency')) }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-table-actions">
                            <a href="{{ route('admin.shipments.edit', [$groupedShipment->id]) }}"
                               class="btn btn-sm btn-default"
                               data-toggle="modal"
                               data-target="#modal-remote-xlg">
                                Editar
                            </a>
                            <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Opções Extra</span>
                            </button>
                            <ul class="dropdown-menu pull-right">
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
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{--<hr style="margin: 0 0 5px"/>
        <a href="{{ route('admin.shipments.create', ['transhipment' => $shipment->id]) }}"
           class="btn btn-success btn-xs"
           data-toggle="modal"
           data-target="#modal-remote-xlg">
            <i class="fas fa-plus"></i> Adicionar Transbordo
        </a>--}}
    @endif
</div>