@if($row->doc_type == 'payment-note')
    <div class="btn-group btn-table-actions">
        <a href="{{ route('admin.invoices.purchase.payment-notes.download', [$row->doc_id, 'reference' => $row->reference, 'provider' => $row->provider]) }}"
           class="btn btn-sm btn-default"
           target="_blank">
            Imprimir
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
                <a href="{{ route('admin.invoices.purchase.payment-notes.email.edit', [$row->doc_id, 'reference' => $row->reference, 'provider' => $row->provider]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote">
                    <i class="fas fa-fw fa-envelope"></i> Enviar por E-mail
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.invoices.purchase.payment-notes.destroy', [$row->doc_id, 'reference' => $row->reference, 'provider' => $row->provider]) }}"
                   data-method="delete"
                   data-confirm="Confirma a anulação do pagamento selecionado?">
                    <i class="fas fa-fw fa-trash-alt"></i> Anular pagamento
                </a>
            </li>
        </ul>
    </div>
@else
    @if($row->is_scheduled)
        <div class="btn-group btn-table-actions">
            <a href="{{ route('admin.invoices.purchase.edit', [$row->id]) }}"
               class="btn btn-sm btn-default"
               data-toggle="modal"
               data-target="#modal-remote-xl">
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
                    <a href="{{ route('admin.invoices.purchase.destroy', [$row->id]) }}"
                       data-method="delete"
                       data-confirm="Confirma a anulação da fatura de compra selecionada?"
                       class="text-red">
                        <i class="fas fa-fw fa-trash-alt"></i> Apagar
                    </a>
                </li>
            </ul>
        </div>
    @else
    <div class="btn-group btn-table-actions">
    {{--    @if(empty($row->gateway))--}}
        <a href="{{ route('admin.invoices.purchase.download', [$row->id]) }}"
           class="btn btn-sm btn-default"
           target="_blank">
            Imprimir
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">

            @if($row->doc_type != \App\Models\PurchaseInvoice::DOC_TYPE_SIND
                && $row->doc_type != \App\Models\PurchaseInvoice::DOC_TYPE_SINC)
                <li>
                    <a href="{{ route('admin.invoices.purchase.edit', [$row->id]) }}"
                    data-toggle="modal"
                    data-target="#modal-remote-xl">
                        @if($row->payment_notes->isEmpty())
                            <i class="fas fa-fw fa-pencil-alt"></i> Editar
                        @else
                            <i class="fas fa-fw fa-search"></i> Ver Detalhe
                        @endif
                    </a>
                </li>
                <li class="divider"></li>
            @endif  


            @if(!$row->payment_notes->isEmpty()) 
                @foreach($row->payment_notes as $paymentNote)
                <li class="dropdown-submenu pull-left">
                    <a tabindex="-1" href="#" class="text-blue">
                        <i class="fas fa-fw fa-file-alt"></i> Pagamento {{ $paymentNote->code }}
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ route('admin.invoices.purchase.payment-notes.download', $paymentNote->id) }}"
                            target="_blank">
                                <i class="fas fa-fw fa-print"></i> Imprimir NDL{{ $paymentNote->code }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.invoices.purchase.payment-notes.email.edit', $paymentNote->id) }}"
                            data-toggle="modal"
                                data-target="#modal-remote">
                                <i class="fas fa-fw fa-envelope"></i> Enviar por E-mail
                            </a>
                        </li>
                        <li class="divider"></li>
                        {{--<li>
                            <a href="{{ route('admin.invoices.purchase.payment-notes.edit', ['id[]' => $paymentNote->id]) }}"
                            data-toggle="modal"
                            data-target="#modal-remote-lg">
                                <i class="fas fa-fw fa-pencil-alt"></i> Editar pagamento
                            </a>
                        </li>--}}
                        <li>
                            <a href="{{ route('admin.invoices.purchase.payment-notes.destroy', $paymentNote->id) }}"
                            data-method="delete"
                            data-confirm="Confirma a anulação do pagamento selecionado?">
                                <i class="fas fa-fw fa-trash-alt"></i> Anular pagamento
                            </a>
                        </li>
                    </ul>
                </li>
                @endforeach
            @endif

            @if(!$row->is_settle)
                <li>
                    <a href="{{ route('admin.invoices.purchase.payment-notes.create', ['id[]' => $row->id]) }}"
                       data-toggle="modal"
                       data-target="#modal-remote-lg"
                       class="text-green">
                        <i class="fas fa-fw fa-check"></i> Liquidar
                    </a>
                </li>
            @endif

            @if($row->doc_type != \App\Models\PurchaseInvoice::DOC_TYPE_SIND
                && $row->doc_type != \App\Models\PurchaseInvoice::DOC_TYPE_SINC)
                <li class="divider"></li>
                
                <li>
                    <a href="{{ route('admin.invoices.purchase.replicate', [$row->id]) }}"
                    data-method="POST"
                    data-confirm-label="Duplicar"
                    data-confirm-title="Duplicar Despesa"
                    data-confirm-class="btn-success"
                    data-confirm="Confirma a duplicação?"
                    class="text-purple">
                        <i class="fas fa-fw fa-copy"></i> Duplicar
                    </a>
                </li>

                @if($row->payment_notes->isEmpty() && (!$row->is_settle || ($row->is_settle && $row->doc_type == 'provider-invoice-receipt')))
                    <li>
                        <a href="{{ route('admin.invoices.purchase.destroy', [$row->id]) }}"
                        data-method="delete"
                        data-confirm="Confirma a anulação da fatura de compra selecionada?"
                        class="text-red">
                            <i class="fas fa-fw fa-trash-alt"></i> Anular
                        </a>
                    </li>
                @endif
            @endif
        </ul>
    </div>
    @endif
@endif