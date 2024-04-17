<div class="btn-group btn-table-actions">
    @if($row->deleted_at)
    <a href="{{ route('admin.shipments.show', $row->id) }}"
       class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote-xl">
        <i class="fas fa-search"></i> Ver
    </a>
    <button type="button"
            class="btn btn-sm btn-default dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Opções</span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.shipments.restore', $row->id) }}"
               data-method="post"
               data-confirm="Pretende restaurar este envio?"
               data-confirm-title="Restaurar envio"
               data-confirm-label="Restaurar"
               data-confirm-class="btn-success"
               class="text-green">
                <i class="fas fa-fw fa-trash-restore-alt"></i> Restaurar
            </a>
        </li>
    </ul>
    @else
        {{-- BLOCKED --}}
        @if($row->is_blocked)
            @if(Auth::user()->allowedAction('edit_blocked'))
                <a href="{{ route('admin.shipments.edit', $row->id) }}" class="btn btn-sm btn-danger"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-lock"></i> Edit
                </a>
                <button type="button" class="btn btn-sm btn-danger dropdown-toggle"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Opções</span>
                </button>
            @else
                <a href="{{ route('admin.shipments.show', $row->id) }}" class="btn btn-sm btn-danger"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <span data-toggle="tooltip" title="Envio Bloqueado. Apenas permite Consulta.">
                        <i class="fas fa-lock"></i> Ver
                    </span>
                </a>
                <button type="button" class="btn btn-sm btn-danger dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Opções Extra</span>
                </button>
            @endif
        @else
            @if($isAdmin || in_array($row->agency_id, Auth::user()->agencies))
                <a href="{{ route('admin.shipments.edit', $row->id) }}" class="btn btn-sm btn-default"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    Editar
                </a>
            @else
                <a href="{{ route('admin.shipments.show', $row->id) }}" class="btn btn-sm btn-default"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-search"></i> Ver
                </a>
            @endif
            <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                <span class="caret"></span>
                <span class="sr-only">Opções</span>
            </button>
        @endif

        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.shipments.show', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-fw fa-info-circle"></i> Detalhes da recolha
                </a>
            </li>
            @if(((!empty(Auth::user()->agencies) && in_array($row->agency_id, Auth::user()->agencies)) || empty(Auth::user()->agencies) || ($row->type == \App\Models\Shipment::TYPE_DEVOLUTION && in_array($row->sender_agency_id, Auth::user()->agencies))))
                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'edit_shipments') && Auth::user()->showPrices())
                    <li>
                        <a href="{{ route('admin.change-log.show', ['Shipment', $row->id]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-lg">
                            <i class="fas fa-fw fa-history"></i> Histórico de Edições
                        </a>
                    </li>

                @endif
            @endif
            <li role="separator" class="divider"></li>
                {{---------------------------------}}
                {{---------EXECUTE ACTION----------}}
                {{---------------------------------}}
            @if(!$row->children_tracking_code)
            <li>
                <a href="{{ route('admin.pickups.create.shipment', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xl"
                   class="text-green">
                    <i class="fas fa-fw fa-shipping-fast"></i> Gerar Envio
                </a>
            </li>
            @endif

            <li class="dropdown-submenu pull-left">
                <a tabindex="-1" href="#" class="text-blue">
                    <i class="fas fa-fw fa-cog"></i> Executar Ação...
                </a>
                <ul class="dropdown-menu">
                    {{--@if(Auth::user()->showPrices())
                    <li>
                        <a href="{{ route('admin.shipments.expenses.create', $row->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-lg"
                           class="text-blue">
                            <i class="fas fa-fw fa-euro-sign"></i> Adicionar Encargos
                        </a>
                    </li>
                    @endif--}}
                    <li>
                        <a href="{{ route('admin.shipments.replicate.create', $row->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                           class="text-purple">
                            <i class="fas fa-fw fa-copy"></i> Duplicar Pedido Recolha
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shipments.edit', [$row->id, 'schedule' => true]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xl"
                           class="text-purple">
                            <i class="fas fa-fw fa-clock"></i> Agendar Recolha Periódica
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pickup.convert', $row->id) }}"
                           data-method="post"
                           data-confirm="Pretende converter este pedido de recolha num envio?<br/><small>Só deve usar esta opção se o pedido de recolha foi criado por engano e diz respeito a um envio.</small>"
                           data-confirm-title="Converter recolha em envio"
                           data-confirm-label="Converter"
                           data-confirm-class="btn-success"
                           class="text-green">
                            <i class="fas fa-fw fa-sync-alt"></i> Transferir para envios
                        </a>
                    </li>
                    @if(!$row->status->is_final)
                        <li>
                            <a href="{{ route('admin.shipments.destroy.confirm', $row->id) }}"
                               data-toggle="modal"
                               data-target="#modal-remote"
                               class="text-red">
                                <i class="fas fa-fw fa-trash-alt"></i> Eliminar Recolha
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
            {{---------------------------------}}
            {{--------PRINT DOCUMENTS----------}}
            {{---------------------------------}}
            <li role="separator" class="divider"></li>
            <li>
                <a href="{{ route('admin.printer.pickups.pickup-manifest', $row->id) }}" target="_blank" class="text-purple">
                    <i class="fas fa-fw fa-print"></i> Manifesto de Recolha
                </a>
            </li>

            {{---------------------------------}}
            {{------------WEBSERVICE-----------}}
            {{---------------------------------}}
            @if(empty($row->webservice_method))
                <li role="separator" class="divider"></li>
                <li>
                    <a href="{{ route('admin.shipments.email.edit', [$row->id, 'provider']) }}"
                        data-toggle="modal"
                        data-target="#modal-remote-lg">
                        <i class="fas fa-fw fa-envelope"></i> Confirmação/Instruções Carga
                     </a>
                </li>
                <li>
                    <a href="{{ route('admin.shipments.sync.manual', $row->id) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-xs">
                        <i class="fas fa-fw fa-plug"></i> Conexão Manual
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.shipments.sync.force', $row->id) }}"
                       data-method="post"
                       data-confirm="Pretende submeter este envio via webservice?"
                       data-confirm-title="Submeter"
                       data-confirm-label="Submeter via webservice"
                       data-confirm-class="btn-success">
                        <i class="fas fa-fw fa-sync-alt"></i> Submeter webservice
                    </a>
                </li>
            @else
                <li role="separator" class="divider"></li>
                @if($row->hasSync())
                    <li>
                        <a href="{{ route('admin.shipments.sync.reset', $row->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                           class="text-red">
                            <i class="fas fa-fw fa-times"></i> Anular conexão
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('admin.shipments.sync.force', $row->id) }}"
                           data-method="post"
                           data-confirm="Pretende submeter este envio via webservice?"
                           data-confirm-title="Submeter"
                           data-confirm-label="Submeter via webservice"
                           data-confirm-class="btn-success"
                           class="text-green">
                            <i class="fas fa-fw fa-sync-alt"></i> Tentar conexão novamente
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shipments.sync.reset', $row->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                           class="text-red">
                            <i class="fas fa-fw fa-times"></i> Anular erro de conexão
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('admin.shipments.sync.manual', $row->id) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-xs">
                        <i class="fas fa-fw fa-plug"></i> Editar conexão
                    </a>
                </li>
            @endif
        </ul>
    </div>
    @endif
</div>