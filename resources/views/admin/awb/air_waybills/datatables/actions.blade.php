<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.air-waybills.edit', $row->id) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote-xl">
        Editar
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.air-waybills.print.pdf', $row->id) }}" target="_blank">
                <i class="fas fa-print"></i> Imprimir Carta Porte
            </a>
        </li>
        @if($row->has_hawb)
            <li>
                <a href="{{ route('admin.air-waybills.print.hawbs', $row->id) }}" target="_blank">
                    <i class="fas fa-print"></i> Imprimir HAWB
                </a>
            </li>
            <li>
                <a href="{{ route('admin.air-waybills.print.manifest', $row->id) }}" target="_blank">
                    <i class="fas fa-print"></i> Imprimir Manifesto
                </a>
            </li>
        @endif
        <li>
            <a href="{{ route('admin.air-waybills.print.labels', $row->id) }}" target="_blank">
                <i class="fas fa-print"></i> Imprimir Etiquetas
            </a>
        </li>
        <li>
            <a href="{{ route('admin.air-waybills.print.summary', $row->id) }}" target="_blank">
                <i class="fas fa-print"></i> Imprimir Resumo
            </a>
        </li>
        @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'air-waybills-invoices'))
            <li role="separator" class="divider"></li>
            @if($row->invoice_id)
                @if($row->invoice_draft)
                    <li>
                        <a href="{{ route('admin.air-waybills.invoice.destroy', $row->id) }}" class="text-red"
                           data-method="post" data-confirm="Confirma a anulação do rascunho criado?" data-confirm-title="Confirmar anulação do rascunho." data-confirm-label="Anular Rascunho" data-confirm-class="btn-danger">
                            <i class="fas fa-fw fa-trash-alt"></i> Anular Rascunho
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.air-waybills.invoice.convert', $row->id) }}" class="text-green"
                           data-method="post" data-confirm="Confirma a conversão do rascunho criado em fatura?" data-confirm-title="Confirmar conversão de rascunho." data-confirm-label="Converter"  data-confirm-class="btn-success">
                            <i class="fas fa-fw fa-exchange"></i> Converter em Fatura
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('admin.air-waybills.email.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-fw fa-envelope"></i> Enviar por E-mail
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.air-waybills.invoice.download', $row->id) }}" target="_blank">
                            <i class="fas fa-fw fa-print"></i> Imprimir Fatura
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.air-waybills.invoice.destroy', $row->id) }}"
                           data-method="post" data-confirm="Confirma a anulação do documento de venda emitido?<br/>O documento será eliminado do software de faturação." data-confirm-title="Confirmar anulação de documento de venda." data-confirm-label="Anular Documento" data-confirm-class="btn-danger">
                            <i class="fas fa-fw fa-trash-alt"></i> Anular Fatura
                        </a>
                    </li>

                @endif
            @else
                <li>
                    <a href="{{ route('admin.air-waybills.selected.billing', ['id[]' => $row->id]) }}" class="text-blue" data-toggle="modal" data-target="#modal-remote-xl">
                        <i class="fas fa-fw fa-file-alt"></i> Emitir Fatura
                    </a>
                </li>
            @endif
        @endif
        <li role="separator" class="divider"></li>
        <li>
            <a href="{{ route('admin.air-waybills.replicate', $row->id) }}" class="text-purple"
               data-method="POST"
               data-confirm="Confirma a duplicação da carta de porte selecionado?"
               data-confirm-title="Duplicar Carta de Porte"
               data-confirm-label="Duplicar"
               data-confirm-class="btn-success">
                <i class="fas fa-copy"></i> Duplicar
            </a>
        </li>
        <li role="separator" class="divider"></li>
        <li>
            <a href="{{ route('admin.air-waybills.destroy', $row->id) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-fw fa-trash-alt"></i> Eliminar
            </a>
        </li>
    </ul>
</div>
