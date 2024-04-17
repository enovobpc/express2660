<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.shipments.edit', $row->id) }}"
           class="btn btn-sm btn-default"
           data-toggle="modal"
           data-target="#modal-remote-xl" >
            Editar
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            @if(((!empty(Auth::user()->agencies) && in_array($row->agency_id, Auth::user()->agencies)) || empty(Auth::user()->agencies) || ($row->type == \App\Models\Shipment::TYPE_DEVOLUTION && in_array($row->sender_agency_id, Auth::user()->agencies))))
                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'edit_shipments') && Auth::user()->showPrices())
                    <li>
                        <a href="{{ route('admin.change-log.show', ['Shipment', $row->id]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-lg">
                            <i class="fas fa-fw fa-history"></i> Histórico de Edições
                        </a>
                    </li>
                    <li class="divider"></li>
                @endif
            @endif
           {{-- <li>
                <a href="{{ route('admin.shipments.expenses.create', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote">
                    <i class="fas fa-fw fa-euro-sign"></i> Adicionar Encargo
                </a>
            </li>--}}

            @if(!$row->children_tracking_code && $row->is_collection)
                <li>
                    <a href="{{ route('admin.pickups.create.shipment', $row->id) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-xl"
                       class="text-green">
                        <i class="fas fa-fw fa-shipping-fast"></i> Gerar Envio
                    </a>
                </li>
            @endif

            <li>
                <a href="{{ route('admin.printer.shipments.proof', $row->id) }}" target="_blank">
                    <i class="fas fa-fw fa-print"></i> Resumo do serviço
                </a>
            </li>

            @if(hasModule('invoices') && Auth::user()->ability(Config::get('permissions.role.admin'),'billing'))
                <li role="separator" class="divider"></li>
                @if($row->invoice_doc_id && $row->invoice_draft)
                    <li>
                        <a href="{{ route('admin.invoices.edit', ['0', 'customer' => $row->customer_id, 'invoice-id' => $row->invoice_doc_id, 'key' => $row->invoice_key]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xl">
                            <i class="fas fa-fw fa-pencil-alt"></i> Editar Rascunho
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.invoices.convert', [$row->customer_id, $row->invoice_doc_id, 'key' => $row->invoice_key]) }}"
                           data-method="post"
                           data-confirm="Confirma a conversão do rascunho criado em fatura?"
                           data-confirm-title="Confirmar conversão de rascunho."
                           data-confirm-label="Converter"
                           data-confirm-class="btn-success">
                            <i class="fas fa-fw fa-exchange-alt"></i> Converter em Fatura
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id, $row->invoice_doc_id, 'id' => $row->invoice_id, 'key' => $row->invoice_key, 'serie' => $row->doc_type]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                           class="text-red">
                            <i class="fas fa-fw fa-trash-alt"></i> Anular Rascunho
                        </a>
                    </li>
                @elseif($row->invoice_type == 'nodoc')
                    <li>
                        <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id, $row->invoice_doc_id, 'key' => $row->invoice_key, 'serie' => $row->doc_type]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                           class="text-red">
                            <i class="fas fa-fw fa-trash-alt"></i> Anular como faturado
                        </a>
                    </li>
                @elseif($row->invoice_doc_id && !$row->invoice_draft)
                    <li>
                        <a href="{{ route('admin.invoices.download', [$row->customer_id, $row->invoice_doc_id, 'key' => $row->invoice_key]) }}"
                           target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir Fatura
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.invoices.summary', [$row->customer_id, $row->invoice_doc_id, 'key' => $row->invoice_key]) }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir Resumo
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.invoices.email.edit', [$row->customer_id, $row->invoice_doc_id, 'key' => $row->invoice_key]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            <i class="fas fa-fw fa-envelope"></i> Enviar por E-mail
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id, $row->invoice_doc_id, 'id' => $row->invoice_id, 'key' => $row->invoice_key, 'serie' => $row->doc_type]) }}"
                           data-toggle="modal"
                           data-target="#modal-remote"
                           class="text-red">
                            <i class="fas fa-fw fa-trash-alt"></i> Anular Fatura
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('admin.shipments.invoices.create', $row->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xl"
                           class="text-blue">
                            <i class="fas fa-fw fa-file-alt"></i> Emitir Fatura Individual
                        </a>
                    </li>
                @endif
            @endif
               {{--  @if(empty($row->webservice_method))
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="{{ route('admin.shipments.email.edit', [$row->id, 'provider']) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-lg"
                           class="text-green">
                            <i class="fas fa-fw fa-envelope"></i> Solicitar Transporte
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shipments.sync.manual', $row->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xs">
                            <i class="fas fa-fw fa-plug"></i> Conexão Manual
                        </a>
                    </li>
                @else --}}
                    {{-- <li role="separator" class="divider"></li>
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
                    @endif --}}
                    {{-- <li>
                        <a href="{{ route('admin.shipments.sync.manual', $row->id) }}"
                           data-toggle="modal"
                           data-target="#modal-remote-xs">
                            <i class="fas fa-fw fa-plug"></i> Editar conexão
                        </a>
                    </li>
                @endif --}}
        </ul>
    </div>
</div>