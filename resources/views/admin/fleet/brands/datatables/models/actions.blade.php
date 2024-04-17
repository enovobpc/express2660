<div class="action-buttons text-center">
    <a href="{{ route('admin.fleet.brand-models.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote" class="text-green">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a href="{{ route('admin.fleet.brand-models.destroy', $row->id) }}" data-method="delete" data-confirm="@trans('Confirma a remoção do registo selecionado?')" class="text-red">
        <i class="fas fa-trash-alt"></i>
    </a>
</div>