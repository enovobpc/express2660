<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.logistic.brands.edit', $row->id) }}"
           class="btn btn-sm btn-default"
            data-toggle="modal"
            data-target="#modal-remote-xs">
            @trans('Editar')
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.logistic.brands.destroy', $row->id) }}" data-method="delete"
                   class="text-red">
                    <i class="fas fa-fw fa-trash-alt"></i> @trans('Eliminar')
                </a>
            </li>
        </ul>
    </div>
</div>