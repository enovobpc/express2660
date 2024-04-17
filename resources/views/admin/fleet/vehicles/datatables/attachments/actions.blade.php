<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.fleet.vehicles.attachments.edit', [$row->source_id, $row->id]) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-pencil-alt"></i> @trans('Editar')
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">@trans('Opções Extra')</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.fleet.vehicles.attachments.destroy', [$row->source_id, $row->id]) }}" data-method="delete"
                   data-confirm="@trans('Confirma a remoção do registo selecionado?')" class="text-red">
                    <i class="fas fa-trash-alt"></i> @trans('Eliminar')
                </a>
            </li>
        </ul>
    </div>
</div>