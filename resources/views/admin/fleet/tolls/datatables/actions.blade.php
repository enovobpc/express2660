<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.fleet.tolls.show', [$row->vehicle_id, 'date' => $row->entry_date->format('Y-m-d')]) }}"
           class="btn btn-sm btn-default"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            <i class="fas fa-search"></i> @trans('Detalhes')
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">@trans('Opções Extra')</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.fleet.tolls.destroy', [$row->vehicle_id, 'date' => $row->entry_date->format('Y-m-d')]) }}" data-method="delete"
                   data-confirm="@trans('Confirma a remoção do registo selecionado?')" class="text-red">
                    <i class="fas fa-trash-alt"></i> @trans('Eliminar')
                </a>
            </li>
        </ul>
    </div>
</div>