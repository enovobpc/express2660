<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.agencies.edit', $row->id) }}"
           class="btn btn-sm btn-default"
           data-toggle="modal"
           data-target="#modal-remote">
            Editar
        </a>
        @if(Auth::user()->hasRole([config('permissions.role.admin')]))
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Opções Extra</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.agencies.replicate', $row->id) }}"
                   data-method="post"
                    data-confirm-title="Duplicar agência"
                    data-confirm="Confirma a duplicação do registo selecionado?"
                    data-confirm-label="Duplicar"
                    data-confirm-class="btn-success">
                    <i class="fas fa-copy"></i> Duplicar
                </a>
            </li>
            <li>
                <a href="{{ route('admin.agencies.destroy', $row->id) }}" data-method="delete"
                   data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </a>
            </li>
        </ul>
        @endif
    </div>
</div>