<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.sepa-transfers.edit', $row->id) }}"
           class="btn btn-sm btn-default"
            data-toggle="modal"
            data-target="#modal-remote-xl">
            @if($row->status != 'editing')
                <i class="fa fa-search"></i> Ver
            @else
                <i class="fa fa-pencil"></i> Editar
            @endif
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.printer.sepa-transfers.payment.summary', $row->id) }}" target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir Resumo
                </a>
            </li>
            <li class="divider"></li>
            @if($row->status != 'editing')
                <li>
                    <a href="{{ route('admin.sepa-transfers.xml', $row->id) }}" target="_blank">
                        <i class="fas fa-fw fa-file-alt"></i>  Download XML
                    </a>
                </li>
                @if($row->type == 'dd')
                    <li>
                        <a href="{{ route('admin.sepa-transfers.return.edit', $row->id) }}"
                        data-toggle="modal"
                        data-target="#modal-remote">
                            <i class="fas fa-fw fa-upload"></i> Importar Ficheiro Retorno
                        </a>
                    </li>
                @endif

                @if($row->has_errors)
                    <li>
                        <a href="{{ route('admin.sepa-transfers.notify.errors', $row->id) }}"
                            data-method="post"
                            data-confirm="Confirma a notificação das transações falhadas? <br/>Os clientes irão receber um e-mail com o detalhe da transação."
                            data-confirm-title="Notificar transações falhadas"
                            data-confirm-label="Notificar"
                            data-confirm-class="btn-success">
                            <i class="fas fa-fw fa-envelope"></i> Notificar Transações Falhadas
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('admin.sepa-transfers.invoices.edit', $row->id) }}"
                       data-toggle="modal"
                       data-target="#modal-remote">
                        <i class="fas fa-fw fa-euro-sign"></i> Gerar faturas ou recibos
                    </a>
                </li>

            @else
                <li>
                    <a href="#" style="opacity: 0.5" data-toggle="tooltip" title="Download possível após finalizar.">
                        <i class="fas fa-fw fa-file-alt"></i>  Download XML
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="{{ route('admin.sepa-transfers.destroy', $row->id) }}" data-method="delete"
                       data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                        <i class="fas fa-fw fa-trash-alt"></i> Eliminar
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>