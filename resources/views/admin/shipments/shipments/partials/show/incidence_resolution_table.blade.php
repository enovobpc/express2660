<table class="table table-condensed table-incidence-resolutions m-0">
    <tr>
        <th class="w-140px bg-gray-light" style="border-top: none">Data</th>
        <th class="w-180px bg-gray-light w-280px" style="border-top: none">Resolução</th>
        <th class="bg-gray-light" style="border-top: none">Observações</th>
        <th class="bg-gray-light w-200px" style="border-top: none">Registado Por</th>
        <th class="bg-gray-light w-60px" style="border-top: none">Ações</th>
    </tr>
    @if($shipmentIncidencesResolutions->isEmpty())
    @else
        @foreach($shipmentIncidencesResolutions as $resolution)
            <tr>
                <td>{{ $resolution->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ @$resolution->resolution->name }}</td>
                <td>{{ $resolution->obs }}</td>
                <td>
                    @if(@$resolution->operator->name)
                    {{ @$resolution->operator->name }}
                    @else
                        Cliente
                    @endif
                    @if($resolution->is_api)
                        <span class="label bg-blue"><small><i class="fas fa-plug"></i> API</small></span>
                    @endif
                </td>
                <td>
                    <div class="btn-group btn-group-xs">
                        <a href="{{ route('admin.shipments.incidences.edit', [$shipment->id, $resolution->id]) }}"
                           class="btn btn-default"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            Editar
                        </a>
                        <button type="button"
                                class="btn btn-default dropdown-toggle"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Opções</span>
                        </button>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="{{ route('admin.shipments.incidences.destroy', [$shipment->id, $resolution->id]) }}"
                                   data-method="delete"
                                   data-confirm="Confirma a remoção do registo selecionado?"
                                   class="text-red">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
    @endif
</table>