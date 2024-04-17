
@if(!$row->deleted_at)
    <div class="btn-group btn-table-actions">
        <a href="{{ route('admin.invoices.purchase.payment-notes.show', $row->id) }}"
           data-toggle="modal"
           data-target="#modal-remote-lg"
           class="btn btn-sm btn-default">
            Detalhe
        </a>

        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>

        <ul class="dropdown-menu pull-right">
           {{--<li>
                <a href="{{ route('admin.invoices.purchase.payment-notes.edit', ['id[]' => $row->id]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-lg">
                    <i class="fas fa-fw fa-pencil-alt"></i> Editar
                </a>
            </li>--}}
            <li>
                <a href="{{ route('admin.invoices.purchase.payment-notes.download', $row->id) }}"
                   target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir
                </a>
            </li>
            <li>
                <a href="{{ route('admin.invoices.purchase.payment-notes.email.edit', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote">
                    <i class="fas fa-fw fa-envelope"></i> Enviar por E-mail
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.invoices.purchase.payment-notes.destroy', $row->id) }}"
                   data-method="delete"
                   data-confirm="Confirma a anulação do pagamento selecionado?">
                    <i class="fas fa-fw fa-trash-alt"></i> Anular pagamento
                </a>
            </li>
        </ul>
    </div>
@else
    <a href="{{ route('admin.invoices.purchase.payment-notes.download', $row->id) }}"
       class="btn btn-sm btn-block btn-default"
       target="_blank">
        Imprimir
    </a>
@endif