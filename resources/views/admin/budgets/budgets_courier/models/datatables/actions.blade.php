<div class="action-buttons text-center">
    <a href="{{ route('admin.budgets.courier.models.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg" class="text-green">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a href="{{ route('admin.budgets.courier.models.destroy', $row->id) }}" data-method="delete" data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
        <i class="fas fa-trash-alt"></i>
    </a>
</div>