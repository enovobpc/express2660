@if($row->source)
<div class="btn-group btn-table-actions">
    <a href="{{ route('admin.billing.zones.edit', $row->id) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote-lg">
        Editar
    </a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu pull-right">
        <li>
            <a href="{{ route('admin.billing.zones.replicate', $row->id) }}" 
                data-method="post"
                data-confirm-title="Duplicar registo"
                data-confirm="Confirma a duplicação do registo selecionado?" 
                data-confirm-class="btn-success"
                data-confirm-label="Duplicar"
                class="text-blue">
                <i class="fas fa-copy"></i> Duplicar
            </a>
        </li>
        <li>
            <a href="{{ route('admin.billing.zones.destroy', $row->id) }}" data-method="delete"
               data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
                <i class="fas fa-trash-alt"></i> Eliminar
            </a>
        </li>
    </ul>
</div>
@endif
