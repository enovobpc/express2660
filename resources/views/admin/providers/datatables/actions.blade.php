@if(Auth::user()->hasRole(Config::get('permissions.role.admin')) || Auth::user()->id == 312 || !empty($row->source))
<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.providers.edit', [$row->id]) }}"
       class="btn btn-sm btn-default">
       @trans('Editar')
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">@trans('Opções Extra')</span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            @if($row->is_active)
                <a href="{{ route('admin.providers.inactivate', $row->id) }}"
                   data-method="post"
                   data-confirm-title="Inativar Fornecedor"
                   data-confirm-class="btn-success"
                   data-confirm-label="Inativar"
                   data-confirm="Pretende inativar o fornecedor e esconde-lo desta lista? Pode voltar a ativar o fornecedor a qualquer momento.">
                    <i class="fas fa-fw fa-user-times"></i> @trans('Inativar Fornecedor')
                </a>
            @else
                <a href="{{ route('admin.providers.inactivate', $row->id) }}"
                   data-method="post"
                   data-confirm-title="Ativar Fornecedor"
                   data-confirm-class="btn-success"
                   data-confirm-label="Ativar"
                   data-confirm="Pretende voltar a ativar o fornecedor e envia-lo de novo para a lista de fornecedores?">
                    <i class="fas fa-fw fa-user-check"></i> @trans('Ativar Fornecedor')
                </a>
            @endif
        </li>
        <li>
            <a href="{{ route('admin.providers.destroy', [$row->id]) }}"
               data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?"
               class="text-red">
                <i class="fas fa-trash-alt"></i> @trans('Eliminar')
            </a>
        </li>
    </ul>
</div>
@endif