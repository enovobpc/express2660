<div class="btn-group btn-table-actions">
    @if ($row->is_draft)
        <a href="{{ route('admin.invoices.edit', [$row->id]) }}" class="btn btn-sm btn-warning" data-toggle="modal"
            data-target="#modal-remote-xl">
            <i class="fas fa-pencil-alt"></i> @trans('Editar')
        </a>
        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">

            @if ($row->target == \App\Models\Invoice::TARGET_CUSTOMER_BILLING)
            <li>
                <a href="{{ route('admin.invoices.summary', [$row->customer_id,$row->doc_id,'id' => $row->id,'type' => $row->doc_type,'serie' => $row->doc_series_id,'key' => $row->api_key]) }}"
                    target="_blank">
                    <i class="fas fa-fw fa-print"></i> @trans('Resumo de serviços')
                </a>
            </li>
            <div class="divider"></div>
            @endif
            <li>
                <a href="{{ route('admin.invoices.convert', $row->id) }}" 
                    data-method="post"
                    data-confirm="@trans('Confirma a emissão do documento?')"
                    data-confirm-title="@trans('Confirmar emissão de documento')" 
                    data-confirm-label="@trans('Emitir')"
                    data-confirm-class="btn-success">
                    <i class="fas fa-fw fa-check"></i> @trans('Emitir documento')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id, 'invoiceId' => $row->invoice_id ? $row->invoice_id : '0',$row->doc_id,'id' => $row->id,'type' => $row->doc_type,'serie' => $row->doc_series_id,'key' => $row->api_key]) }}"
                    data-toggle="modal" 
                    data-target="#modal-remote" 
                    class="text-red">
                    <i class="fas fa-fw fa-trash-alt"></i>
                    @trans('Eliminar')
                </a>
            </li>
            @if (Auth::user()->isAdmin())
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.invoices.edit', [$row->id, 'action' => 'admin']) }}"
                    data-toggle="modal"
                    data-target="#modal-remote">
                    <i class="fas fa-fw fa-pencil-alt"></i> Editar dados
                </a>
            </li>
            @endif
        </ul>

    @else
    
        @if($row->doc_type == \App\Models\Invoice::DOC_TYPE_NODOC 
            || $row->doc_type == \App\Models\Invoice::DOC_TYPE_SIND
            || $row->doc_type == \App\Models\Invoice::DOC_TYPE_SINC)
            <a href="#" class="btn btn-sm btn-default disabled">
                @trans('Imprimir')
            </a>
        @else
            <a href="{{ route('admin.invoices.download.pdf', $row->id) }}" class="btn btn-sm btn-default" target="_blank">
                @trans('Imprimir')
            </a>
        @endif
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">

            {{-- OPCAO PARA EMITIR RECIBO / LIQUIDAÇÃO --}}
            @if(!$row->is_settle && !$row->is_deleted && !$row->is_reversed)
                @if($row->doc_type == \App\Models\Invoice::DOC_TYPE_FT)
                    <li>
                        <a href="{{ route('admin.invoices.receipt.create', ['customer' => $row->customer_id,'type' => $row->doc_type,'id[]' => $row->id,'key' => $row->api_key]) }}"
                            data-toggle="modal" data-target="#modal-remote-xl">
                            <i class="fas fa-fw fa-receipt"></i> @trans('Emitir Recibo')
                        </a>
                    </li>
                    <li class="divider"></li>
                @elseif($row->doc_type == \App\Models\Invoice::DOC_TYPE_NODOC)
                    <li>
                        <a href="{{ route('admin.invoices.nodoc.settle.edit', $row->id) }}" data-toggle="modal"
                            data-target="#modal-remote-xs" class="text-green">
                            <i class="fas fa-fw fa-check"></i> @trans('Marcar como pago')
                        </a>
                    </li>
                    <li class="divider"></li>
                @endif
            @endif

            {{-- LISTA RECIBOS DO DOCUMENTO --}}
            @if (!$row->receipts->isEmpty())
                @foreach ($row->receipts as $receipt)
                    @if (!@$receipt->invoice->is_deleted && !@$receipt->invoice->is_reversed)
                        <li class="dropdown-submenu pull-left">
                            <a tabindex="-1" href="#" class="text-blue">
                                <i class="fas fa-fw fa-file-alt"></i> @trans('Recibo') {{ @$receipt->invoice->doc_series }}
                                {{ @$receipt->invoice->doc_id }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('admin.invoices.download.pdf', @$receipt->invoice_id) }}"
                                        target="_blank">
                                        <i class="fas fa-fw fa-print"></i> @trans('Imprimir Recibo')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.invoices.email.edit', [$row->customer_id,@$receipt->invoice->doc_id,'id' => $receipt->invoice_id]) }}"
                                        data-toggle="modal" data-target="#modal-remote">
                                        <i class="fas fa-fw fa-envelope"></i> @trans('Enviar por e-mail')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id,'invoiceId' => $receipt->invoice_id,@$receipt->invoice->doc_id,'id' => @$receipt->invoice_id,'type' => @$receipt->invoice->doc_type,'serie' => @$receipt->invoice->doc_series_id,'key' => @$receipt->invoice->api_key]) }}"
                                        data-toggle="modal" data-target="#modal-remote" class="text-red">
                                        <i class="fas fa-fw fa-trash-alt"></i> @trans('Anular Recibo')
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endforeach
                <li class="divider"></li>

            {{-- LISTA NOTA CRÉDITO DO DOCUMENTO --}}
            @elseif ($row->credit_note_id)
                <li class="dropdown-submenu pull-left">
                    <a tabindex="-1" href="#" class="text-blue">
                        @if($row->doc_type == \App\Models\Invoice::DOC_TYPE_NC)
                        <i class="fas fa-fw fa-file-alt"></i> @trans('Nota Débito') {{ @$row->credit_note->doc_series }}
                        @else
                        <i class="fas fa-fw fa-file-alt"></i> @trans('Nota Crédito') {{ @$row->credit_note->doc_series }}
                        @endif
                        {{ @$row->credit_note->doc_id }}
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ route('admin.invoices.download.pdf', @$row->credit_note_id) }}"
                                target="_blank">
                                <i class="fas fa-fw fa-print"></i> @trans('Imprimir Documento')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.invoices.email.edit', [$row->customer_id, @$row->credit_note->doc_id,'id' => @$row->credit_note_id]) }}"
                                data-toggle="modal" data-target="#modal-remote">
                                <i class="fas fa-fw fa-envelope"></i> @trans('Enviar por e-mail')
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="divider"></li>
            @endif

            {{-- RESUMO SERVIÇOS --}}
            @if ($row->target == \App\Models\Invoice::TARGET_CUSTOMER_BILLING)
                <li>
                    <a href="{{ route('admin.invoices.summary', [$row->customer_id,$row->doc_id,'id' => $row->id,'type' => $row->doc_type,'serie' => $row->doc_series_id,'key' => $row->api_key]) }}"
                        target="_blank">
                        <i class="fas fa-fw fa-print"></i> @trans('Resumo de Serviços')
                    </a>
                </li>
            @endif

            {{-- ENVIO DE E-MAIL --}}
            @if ($row->doc_type != \App\Models\Invoice::DOC_TYPE_INTERNAL_DOC 
                && $row->doc_type != \App\Models\Invoice::DOC_TYPE_SIND
                && $row->doc_type != \App\Models\Invoice::DOC_TYPE_SINC)
                <li>
                    <a href="{{ route('admin.invoices.email.edit', [$row->customer_id,$row->doc_id,'id' => $row->id,'type' => $row->doc_type,'serie' => $row->doc_series_id,'key' => $row->api_key]) }}"
                        data-toggle="modal" data-target="#modal-remote">
                        <i class="fas fa-fw fa-envelope"></i> @trans('Enviar por e-mail')
                    </a>
                </li>
                <li class="divider"></li>
            @endif
        
            {{-- FATURA PROFORMA --}}
            @if ($row->doc_type == \App\Models\Invoice::DOC_TYPE_FP)
                <li>
                    <a href="{{ route('admin.invoices.replicate', [$row->id, 'type' => 'invoice']) }}" data-method="post"
                        data-confirm="Confirma a criação de fatura a partir da Fatura-Proforma?<br/><small>A fatura ficará em modo de rascunho.</small>"
                        data-confirm-label="Converter" data-confirm-class="btn-success" class="text-purple">
                        <i class="fas fa-fw fa-reply"></i> @trans('Converter em Fatura')
                    </a>
                </li>
                @if ($row->doc_after_payment && !$row->is_settle)
                    <li>
                        <a href="{{ route('admin.invoices.autocreate.edit', $row->id) }}" data-toggle="modal"
                            data-target="#modal-remote" class="text-purple">
                            <i class="fas fa-fw fa-file-alt"></i> Auto
                            {{ trans('admin/billing.types.' . $row->doc_after_payment) }}
                        </a>
                    </li>
                @endif
            @endif

            @if ($row->sepa_payment_id)
                <li>
                    <a href="{{ route('admin.sepa-transfers.edit', [$row->sepa_payment_id, 'type' => 'invoice']) }}"
                        data-toggle="modal" data-target="#modal-remote-xl" class="text-blue">
                        <i class="fas fa-fw fa-file-export"></i> @trans('Ver Transferência SEPA')
                    </a>
                </li>
                <li class="divider"></li>
            @endif

            {{-- DUPLICAR DOCUMENTO --}}
            @if ($row->doc_type != \App\Models\Invoice::DOC_TYPE_RC 
                && $row->doc_type != \App\Models\Invoice::DOC_TYPE_RG 
                && $row->doc_type != \App\Models\Invoice::DOC_TYPE_SIND
                && $row->doc_type != \App\Models\Invoice::DOC_TYPE_SINC)
                <li>
                    <a href="{{ route('admin.invoices.replicate', $row->id) }}" 
                        data-method="post"
                        data-confirm="@trans('Confirma a duplicação da fatura?')<br/><small>@trans('A fatura ficará em modo de rascunho.')</small>"
                        data-confirm-label="@trans('Duplicar')" 
                        data-confirm-class="btn-success" 
                        class="text-purple">
                        <i class="fas fa-fw fa-copy"></i> @trans('Duplicar')
                    </a>
                </li>
            @endif

            <li>
                @if(!$row->is_deleted && !$row->is_reversed && $row->receipts->isEmpty())
                <a href="{{ route('admin.invoices.destroy.edit', [$row->customer_id,'invoiceId' => $row->invoice_id ? $row->invoice_id : '0',$row->doc_id,'id' => $row->id,'type' => $row->doc_type,'serie' => $row->doc_series_id,'key' => $row->api_key]) }}"
                    data-toggle="modal" data-target="#modal-remote" class="text-red">
                    <i class="fas fa-fw fa-trash-alt"></i>
                    @trans('Anular documento')
                </a>
                @endif
            </li>
            
            @if (Auth::user()->isAdmin())
                <li class="divider"></li>
                <li>
                    <a href="{{ route('admin.invoices.edit', [$row->id, 'action' => 'admin']) }}"
                        data-toggle="modal"
                        data-target="#modal-remote">
                        <i class="fas fa-fw fa-pencil-alt"></i> Editar dados
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.invoices.download.pdf', [$row->id, 'cache' => 'force-ignore']) }}"
                        target="_blank">
                        <i class="fas fa-fw fa-print"></i> Limpar Cache
                    </a>
                </li>
            @endif
        </ul>
    @endif
</div>
