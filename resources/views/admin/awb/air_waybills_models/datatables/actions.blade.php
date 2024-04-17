<div class="action-buttons text-center">
    <a href="{{ route('admin.air-waybills.models.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-xl" class="text-green">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a href="{{ route('admin.air-waybills.models.destroy', $row->id) }}" data-method="delete" data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
        <i class="fas fa-trash-alt"></i>
    </a>
</div>