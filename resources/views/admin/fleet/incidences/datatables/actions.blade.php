<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.fleet.incidences.edit', [$row->id, 'vehicle' => $row->vehicle_id]) }}"
           class="btn btn-sm btn-default"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            <i class="fas fa-pencil-alt"></i> @trans('Editar')
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">@trans('Opções Extra')</span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.fleet.incidences.fix.edit', [$row->id, 'vehicle' => $row->vehicle_id]) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-xs"
                   class="text-green">
                    <i class="fas fa-check"></i> @trans('Resolver')
                </a>
            </li>
            <li>
                <a href="{{ route('admin.fleet.incidences.destroy', [$row->id, 'vehicle' => $row->vehicle_id]) }}" data-method="delete"
                   data-confirm="@trans('Confirma a remoção do registo selecionado?')" class="text-red">
                    <i class="fas fa-trash-alt"></i> @trans('Eliminar')
                </a>
            </li>
        </ul>
    </div>
</div>