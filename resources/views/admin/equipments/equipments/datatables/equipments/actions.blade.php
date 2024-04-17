<div class="text-center">
    <div class="btn-group">
        <a href="{{ route('admin.equipments.show', $row->id) }}"
           class="btn btn-sm btn-default"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            Detalhe
        </a>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('admin.equipments.edit', $row->id) }}"
                   data-toggle="modal"
                   data-target="#modal-remote-lg">
                    <i class="fas fa-fw fa-pencil-alt"></i> Editar
                </a>
            </li>
            <li>
                <a href="{{ route('admin.equipments.printer.labels', ['id' => $row->id]) }}" target="_blank">
                    <i class="fas fa-fw fa-print"></i> Imprimir Etiqueta
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ route('admin.equipments.destroy', $row->id) }}" data-method="delete"
                   data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                    <i class="fas fa-fw fa-trash-alt"></i> Eliminar
                </a>
            </li>
        </ul>
    </div>
</div>