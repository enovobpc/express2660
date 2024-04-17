<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.express-services.edit', $row->id) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote-lg">
            Editar
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            @if($row->invoice_id)
                @if($row->invoice_draft)
                    <li>
                        <a href="{{ route('admin.express-services.invoice.destroy', $row->id) }}" class="text-red"
                           data-method="post" data-confirm="Confirma a anulação do rascunho criado?" data-confirm-title="Confirmar anulação do rascunho." data-confirm-label="Anular Rascunho" data-confirm-class="btn-danger">
                            <i class="fas fa-fw fa-trash-alt"></i> Anular Rascunho
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.express-services.invoice.convert', $row->id) }}" class="text-green"
                           data-method="post" data-confirm="Confirma a conversão do rascunho criado em fatura?" data-confirm-title="Confirmar conversão de rascunho." data-confirm-label="Converter"  data-confirm-class="btn-success">
                            <i class="fas fa-fw fa-exchange"></i> Converter em Fatura
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('admin.express-services.email.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-fw fa-envelope"></i> Enviar por E-mail
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.express-services.invoice.download', $row->id) }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir Fatura
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.express-services.invoice.destroy', $row->id) }}"
                           data-method="post" data-confirm="Confirma a anulação do documento de venda emitido?<br/>O documento será eliminado do software de faturação." data-confirm-title="Confirmar anulação de documento de venda." data-confirm-label="Anular Documento" data-confirm-class="btn-danger">
                            <i class="fas fa-fw fa-trash-alt"></i> Anular Fatura
                        </a>
                    </li>

                @endif
            @else
            <li>
                <a href="{{ route('admin.express-services.selected.billing', ['id[]' => $row->id]) }}" class="text-blue" data-toggle="modal" data-target="#modal-remote-xl">
                    <i class="fas fa-fw fa-file-alt"></i> Emitir Fatura
                </a>
            </li>
            @endif
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.express-services.destroy', $row->id) }}" data-method="delete"
                   data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                    <i class="fas fa-fw fa-trash-alt"></i> Eliminar
                </a>
            </li>
        </ul>
    </div>
</div>