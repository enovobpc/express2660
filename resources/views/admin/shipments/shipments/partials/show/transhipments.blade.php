@if(hasModule('transhipments') || in_array(Setting::get('app_mode'), ['cargo', 'freight']))
    <div>
        @if($transhipments->isEmpty())
            <div class="text-center text-muted p-5 m-t-40">
                <h4><i class="fas fa-info-circle"></i> Este serviço não tem transbordos ou dobragens.</h4>
                {{--<a href="{{ route('admin.shipments.transhipment.create', $transhipment->id) }}"
                   class="btn btn-default btn-sm m-t-20 m-b-40"
                   data-toggle="modal"
                   data-target="#modal-remote-lg">
                    <i class="fas fa-plus"></i> Adicionar Transbordo
                </a>--}}
                <a href="{{ route('admin.shipments.create', ['transhipment' => $shipment->id]) }}"
                   class="btn btn-default btn-sm m-t-20 m-b-40"
                   data-toggle="modal"
                   data-target="#modal-remote-xlg">
                    <i class="fas fa-plus"></i> Adicionar Transbordo
                </a>
            </div>
        @else
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
                @foreach($transhipments as $transhipment)
                    <tr>
                        <td>
                            <a href="{{ route('admin.shipments.show', [$shipment->id]) }}"
                               data-toggle="modal"
                               data-target="#modal-remote-xlg">
                                {{ $transhipment->tracking_code }}
                            </a>
                            <span class="label" style="background: #9135ff">
                                <i class="fas fa-random"></i> Transbordo
                            </span>
                        </td>
                        <td>
                            {{ $transhipment->sender_name }}<br/>
                            <small class="text-muted italic">
                                {{ $transhipment->sender_zip_code }} {{ $transhipment->sender_city }}
                            </small>
                        </td>
                        <td>
                            {{ $transhipment->recipient_name }}<br/>
                            <small class="text-muted italic">
                                {{ $transhipment->recipient_zip_code }} {{ $transhipment->recipient_city }}
                            </small>
                        </td>
                        <td>
                        <span class="label" style="background: {{ @$transhipment->provider->color }}">
                            {{ @$transhipment->provider->name }}
                        </span>
                        </td>
                        <td>
                            <?php $row = $transhipment?>
                            @include('admin.shipments.shipments.datatables.volumes')
                        </td>
                        <td>
                            {{ $transhipment->vehicle }}<br/><small>{{ $transhipment->trailer }}</small>
                        </td>
                        <td>
                            {{ substr($transhipment->shipping_date, 0, 10) }}<br/>
                            {{ substr($transhipment->delivery_date, 0 ,10) }}
                        </td>
                        <td class="text-center">
                            {{ money($transhipment->cost_price, Setting::get('app_currency')) }}
                            @if($transhipment->total_expenses_cost > 0.00)
                                <span class="label label-success">+{{ money($transhipment->total_expenses_cost, Setting::get('app_currency')) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-table-actions">
                                <a href="{{ route('admin.shipments.edit', [$transhipment->id]) }}"
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
                                        <a href="{{ route('admin.printer.shipments.transport-guide', $transhipment->id) }}" target="_blank">
                                            <i class="fas fa-fw fa-print"></i> Guia de Transporte
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.printer.shipments.labels', $transhipment->id) }}" target="_blank">
                                            <i class="fas fa-fw fa-print"></i> Etiquetas
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.printer.shipments.cmr', $transhipment->id) }}" target="_blank">
                                            <i class="fas fa-fw fa-print"></i> CMR Internacional
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.printer.shipments.shipping-instructions', $transhipment->id) }}" target="_blank">
                                            <i class="fas fa-fw fa-print"></i> Instruções de Carga
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="{{ route('admin.shipments.email.edit', [$transhipment->id, 'provider']) }}"
                                            data-toggle="modal"
                                            data-target="#modal-remote-lg">
                                            <i class="fas fa-fw fa-envelope"></i> Confirmação/Instruções Carga
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="{{ route('admin.shipments.destroy', [$transhipment->id]) }}"
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
        <hr style="margin: 0 0 5px"/>
            <a href="{{ route('admin.shipments.create', ['transhipment' => $shipment->id]) }}"
               class="btn btn-success btn-xs"
               data-toggle="modal"
               data-target="#modal-remote-xlg">
                <i class="fas fa-plus"></i> Adicionar Transbordo
            </a>
        @endif
    </div>
@endif