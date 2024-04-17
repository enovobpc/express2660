<?php
    $status = @$statusList[$row->status_id][0];
    $mode   = Setting::get('app_mode');
    $cargoMode = ($mode == 'cargo' || $mode == 'freight') ? true : false;
?>
<div class="btn-group btn-table-actions">
    @if($row->deleted_at)
    <a href="{{ route('admin.shipments.show', $row->id) }}"
       class="btn btn-sm btn-default"
       data-toggle="modal"
       data-target="#modal-remote-xl">
        <i class="fas fa-search"></i> @trans('Ver')
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
        @if(((!empty(Auth::user()->agencies) && in_array($row->agency_id, Auth::user()->agencies)) || empty(Auth::user()->agencies) || ($row->type == \App\Models\Shipment::TYPE_DEVOLUTION && in_array($row->sender_agency_id, Auth::user()->agencies))))
            @if($canEditShipments)
                <li>
                    <a href="{{ route('admin.change-log.show', ['Shipment', $row->id]) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-lg">
                        <i class="fas fa-fw fa-history"></i> @trans('Histórico de Edições')
                    </a>
                </li>
            @endif
        @endif
        <li class="divider"></li>
        <li>
            <a href="{{ route('admin.shipments.restore', $row->id) }}"
               data-method="post"
               data-confirm="@trans('Pretende restaurar este serviço?')"
               data-confirm-title="@trans('Restaurar serviço')"
               data-confirm-label="@trans('Restaurar')"
               data-confirm-class="btn-success"
               class="text-green">
                <i class="fas fa-fw fa-trash-restore-alt"></i> @trans('Restaurar')
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
                    <span data-toggle="tooltip" title="@trans('Serviço Bloqueado. Apenas permite Consulta.')">
                        <i class="fas fa-lock"></i> @trans('Ver')
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
                <a href="{{ route('admin.shipments.edit', $row->id) }}" 
                    class="btn btn-sm btn-default"
                    data-toggle="modal"
                    data-target="#modal-remote-xl">
                    @trans('Editar')
                </a>
            @else
                <a href="{{ route('admin.shipments.show', $row->id) }}" 
                    class="btn btn-sm btn-default"
                    data-toggle="modal"
                    data-target="#modal-remote-xl">
                    <i class="fas fa-search"></i> @trans('Ver')
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
                    <i class="fas fa-fw fa-info-circle"></i> @trans('Detalhes do Serviço')
                </a>
            </li>
            @if(((!empty(Auth::user()->agencies) && in_array($row->agency_id, Auth::user()->agencies)) || empty(Auth::user()->agencies) || ($row->type == \App\Models\Shipment::TYPE_DEVOLUTION && in_array($row->sender_agency_id, Auth::user()->agencies))))
                @if($canEditShipments)
                    <li>
                        <a href="{{ route('admin.change-log.show', ['Shipment', $row->id]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-lg">
                            <i class="fas fa-fw fa-history"></i> @trans('Histórico de Edições')
                        </a>
                    </li>

                @endif
            @endif
            <li role="separator" class="divider"></li>
                {{---------------------------------}}
                {{---------EXECUTE ACTION----------}}
                {{---------------------------------}}
            <li class="dropdown-submenu pull-left">
                <a tabindex="-1" href="#" class="text-blue">
                    <i class="fas fa-fw fa-cog"></i> @trans('Executar Ação')...
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('admin.shipments.replicate.create', $row->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                           class="text-purple">
                            <i class="fas fa-fw fa-copy"></i> @trans('Duplicar Serviço')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shipments.create.return', $row->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xl"
                           class="text-green">
                            <i class="fas fa-fw fa-undo"></i> @trans('Criar e editar retorno')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shipments.create.return.direct', $row->id) }}"
                           data-method="post"
                           data-confirm="@trans('Pretende criar a um retorno direto para este envio?<br/><small>O retorno direto irá manter a mesma informação de volumes, peso e km que o envio original.</small>')"
                           data-confirm-title="@trans('Criar retorno direto')"
                           data-confirm-label="@trans('Criar retorno direto')"
                           data-confirm-class="btn-success"
                           class="text-green">
                            <i class="fas fa-fw fa-undo"></i> @trans('Gerar retorno direto')
                        </a>
                    </li>


                    {{--@if($isAdmin || !$row->isMyShipment())
                    <li>
                        <a href="{{ route('admin.shipments.create.recanalize', $row->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xl"
                           class="text-orange">
                            <i class="fas fa-fw fa-random"></i> Recanalizar
                        </a>
                    </li>
                    @endif--}}

                    @if($row->type != \App\Models\Shipment::TYPE_DEVOLUTION)
                        <li>
                            <a href="{{ route('admin.shipments.create.devolution', $row->id) }}"
                               data-method="post"
                               data-confirm="@trans('Pretende criar a devolução para esta expedição?')"
                               data-confirm-title="@trans('Criar devolução')"
                               data-confirm-label="@trans('Criar devolução')"
                               data-confirm-class="btn-success"
                               class="text-yellow">
                                <i class="fas fa-fw fa-arrow-left"></i> @trans('Criar devolução')
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ route('admin.shipments.edit', [$row->id, 'schedule' => true]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xl"
                           class="text-light-blue">
                            <i class="fas fa-fw fa-calendar-alt"></i> @trans('Agendar Periódico')
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.shipments.interventions.create', [$row->id]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                           class="text-light-black">
                            <i class="fas fa-fw fa-headphones"></i> @trans('Registar Intervenção')
                        </a>
                    </li>

                        {{---------------------------------}}
                        {{------------WEBSERVICE-----------}}
                        {{---------------------------------}}
                        <li role="separator" class="divider"></li>
                        @if(empty($row->webservice_method))
                            <li>
                                <a href="{{ route('admin.shipments.sync.manual', $row->id) }}"
                                   data-toggle="modal"
                                   data-target="#modal-remote-xs">
                                    <i class="fas fa-fw fa-plug"></i> @trans('Ligar webservice')
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('admin.shipments.sync.force', $row->id) }}"
                                   data-method="post"
                                   data-confirm="Pretende submeter este envio via webservice?"
                                   data-confirm-title="Submeter"
                                   data-confirm-label="Submeter via webservice"
                                   data-confirm-class="btn-success">
                                    <i class="fas fa-fw fa-sync-alt"></i> @trans('Submeter webservice')
                                </a>
                            </li>
                        @else
                            @if($row->hasSync())
                                <li>
                                    <a href="{{ route('admin.shipments.sync.reset', $row->id) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote"
                                       class="text-red">
                                        <i class="fas fa-fw fa-unlink"></i> @trans('Anular webservice')
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
                                        <i class="fas fa-fw fa-sync-alt"></i> @trans('Tentar submissão novamente')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.shipments.sync.reset', $row->id) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote"
                                       class="text-red">
                                        <i class="fas fa-fw fa-times"></i> @trans('Anular erro de conexão')
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('admin.shipments.sync.manual', $row->id) }}"
                                   data-toggle="modal"
                                   data-target="#modal-remote-xs">
                                    <i class="fas fa-fw fa-plug"></i> @trans('Editar webservice')
                                </a>
                            </li>
                        @endif
                    @if(!@$status['is_final'])
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('admin.shipments.destroy.confirm', $row->id) }}"
                               data-toggle="modal"
                               data-target="#modal-remote"
                               class="text-red">
                                <i class="fas fa-fw fa-trash-alt"></i> @trans('Eliminar expedição')
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
            {{---------------------------------}}
            {{--------PRINT DOCUMENTS----------}}
            {{---------------------------------}}
            <li role="separator" class="divider"></li>

           {{-- <li>
                <a href="{{ route('admin.shipments.get.property-declaration', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
                    <i class="fas fa-fw fa-print"></i> @trans('Declaração de Valor')
                </a>
            </li>--}}
            @if($cargoMode)
                <li>
                    <a href="{{ route('admin.printer.shipments.cmr', [$row->id]) }}" target="_blank" class="text-purple">
                        <i class="fas fa-fw fa-print"></i> @trans('Imprimir CMR')
                    </a>
                </li>
            @endif
            @if($mode == 'transfers')
                <li>
                    <a href="{{ route('admin.printer.shipments.itenerary', $row->id) }}" target="_blank" class="text-purple">
                        <i class="fas fa-fw fa-print"></i> @trans('Mapa Itenerário')
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.printer.shipments.contract', $row->id) }}" target="_blank" class="text-purple">
                        <i class="fas fa-fw fa-print"></i> @trans('Contrato')
                    </a>
                </li>
            @else
            @if($row->recipient_country == 'pt')
            <li>
                <a href="{{ route('admin.printer.shipments.transport-guide', $row->id) }}" target="_blank" class="text-purple">
                    <i class="fas fa-fw fa-print"></i> @trans('Guia Transporte')
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('admin.printer.shipments.labels', $row->id) }}" target="_blank" class="text-purple">
                    <i class="fas fa-fw fa-print"></i> @trans('Etiquetas')
                </a>
            </li>
            @endif

            <li class="dropdown-submenu pull-left">
                <a tabindex="-1" href="#" class="text-purple">
                    <i class="fas fa-fw fa-print"></i> @trans('Imprimir outros')...
                </a>
                <ul class="dropdown-menu">
                    @if(Setting::get('shipment_label_a4'))
                        <li>
                            <a href="{{ route('admin.shipments.print.labelsA4.edit', ['id[]' => $row->id]) }}"
                            data-toggle="modal"
                            data-target="#modal-remote-xs">
                                <i class="fas fa-fw fa-print"></i> @trans('Etiquetas A4')
                            </a>
                        </li>
                    @endif
                    @if(!$cargoMode)
                    <li>
                        <a href="{{ route('admin.printer.shipments.cmr', $row->id) }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> @trans('Imprimir CMR')
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('admin.printer.shipments.proof', $row->id) }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> @trans('Resumo/Cotação do serviço')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.printer.shipments.shipping-instructions', $row->id) }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> @trans('Adjudicação de Carga')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.printer.shipments.value-statement', $row->id) }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> @trans('Declaração de Valores')
                        </a>
                    </li>
                    @if($row->charge_price && $row->webservice_method == 'ctt')
                        <li>
                            <a href="{{ route('admin.printer.shipments.reimbursement-guide', $row->id) }}" target="_blank">
                                <i class="fas fa-fw fa-print"></i> @trans('Controlo Contra-Reembolso')
                            </a>
                        </li>
                    @endif
                    @if ($row->webservice_method == 'tnt_express')
                        <li>
                            <a href="{{ route('admin.printer.shipments.labels', [$row->id, 'old' => 1]) }}" target="_blank">
                                <i class="fas fa-fw fa-print"></i> Etiquetas Antigas
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            <li role="separator" class="divider"></li>
            <li class="dropdown-submenu pull-left">
                <a tabindex="-1" href="#" class="text-green">
                    <i class="fas fa-fw fa-envelope"></i> @trans('Enviar por e-mail')...
                </a>
                <ul class="dropdown-menu">
                    @if(empty($row->webservice_method))
                        <li>
                            <a href="{{ route('admin.shipments.email.edit', [$row->id, 'provider']) }}"
                               data-toggle="modal"
                               data-target="#modal-remote-lg">
                               <i class="fas fa-fw fa-envelope"></i> @trans('Adjudicação Carga')
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('admin.shipments.email.edit', [$row->id, 'customer']) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-lg">
                           <i class="fas fa-fw fa-envelope"></i> @trans('Cotação/Resumo de serviço')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shipments.email.edit', [$row->id, 'docs']) }}"
                           data-toggle="modal"
                           data-target="#modal-remote">
                           <i class="fas fa-fw fa-envelope"></i> @trans('Guias/CMR ou Documentos')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shipments.email.edit', [$row->id, 'auction']) }}"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            <i class="fas fa-fw fa-envelope"></i> @trans('Anunciar/leiloar carga')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shipments.email.edit', [$row->id, 'provider_request_info']) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-lg">
                            <i class="fas fa-fw fa-envelope"></i> Pedir estado e localização
                        </a>
                    </li>
                </ul>
            </li>


            <li role="separator" class="divider"></li>

            @if($canBilling)

                @if($row->invoice_id)
                    @if($row->invoice_draft)
                    <li class="dropdown-submenu pull-left">
                        <a tabindex="-1" href="#" class="text-blue">
                            <i class="fas fa-fw fa-euro-sign"></i> @trans('Fatura (Rascunho)')
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.invoices.convert', $row->invoice_id) }}" target="_blank" class="text-green"
                                    data-method="post" data-confirm="Confirma a conversão do rascunho criado em fatura?" data-confirm-title="Confirmar conversão de rascunho." data-confirm-label="Converter"  data-confirm-class="btn-success">
                                    <i class="fas fa-fw fa-exchange-alt"></i> @trans('Converter em Fatura')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id, $row->invoice_id, 'key' => $row->invoice_key, 'serie' => $row->invoice_serie]) }}"
                                    data-toggle="modal"
                                    data-target="#modal-remote"
                                    class="text-red">
                                    <i class="fas fa-fw fa-trash-alt"></i> @trans('Anular Rascunho')
                                </a>
                            </li>
                        </ul>
                    </li>
                    @elseif($row->invoice_type != 'nodoc')
                    <li class="dropdown-submenu pull-left">
                        <a tabindex="-1" href="#" class="text-blue">
                            <i class="fas fa-fw fa-euro-sign"></i> @trans('Fatura') {{ trans('admin/billing.types_code.' . $row->invoice_type) }} {{ $row->invoice_doc_id }}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.invoices.download', [$row->customer_id, $row->invoice_doc_id, 'id' => $row->invoice_id, 'type' => $row->invoice_type, 'key' => $row->invoice_key]) }}" target="_blank">
                                    <i class="fas fa-fw fa-print"></i> @trans('Imprimir Fatura')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id, $row->invoice_doc_id, 'id' => $row->invoice_id, 'type' => $row->invoice_type, 'key' => $row->invoice_key, 'serie' => $row->invoice_type]) }}"
                                    data-toggle="modal"
                                    data-target="#modal-remote"
                                    class="text-red">
                                    <i class="fas fa-fw fa-trash-alt"></i> @trans('Anular Documento')
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                @else
                    <li>
                        <a href="{{ route('admin.shipments.invoices.create', $row->id) }}"
                            data-toggle="modal"
                            data-target="#modal-remote-xl"
                            class="text-blue">
                            <i class="fas fa-fw fa-euro-sign"></i> @trans('Emitir fatura')
                        </a>
                    </li>
                @endif

                @if($row->at_guide_codeat)
                    <li>
                        <a href="{{ route('admin.invoices.download', [$row->customer_id, $row->at_guide_doc_id, 'type' => 'transport-guide', 'key' => $row->at_guide_key, 'serie' => $row->at_guide_serie]) }}" target="_blank" class="text-purple">
                            <i class="fas fa-fw fa-print"></i> @trans('Imprimir Guia AT')
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('admin.shipments.invoices.create', [$row->id, 'doc-type' => 'transport-guide']) }}"
                            data-toggle="modal"
                            data-target="#modal-remote-lg"
                            class="text-blue">
                            <i class="fas fa-fw fa-file-alt"></i> @trans('Emitir Guia AT')
                        </a>
                    </li>
                @endif
            @endif

            @if(Setting::get('app_country') == 'pt' || Setting::get('app_country') == 'ptmd' || Setting::get('app_country') == 'ptac')
            <li>
                <a href="{{ route('admin.gateway.payments.create', ['shipment[]' => $row->id]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xs"
                   class="text-blue">
                    <img src="{{ asset('assets/img/default/mb-icon.svg') }}" style="width: 13px;"/> &nbsp;@trans('Pagamento MB')
                </a>
            </li>
            @endif
        </ul>
    </div>
@endif
</div>