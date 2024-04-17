<div class="action-buttons text-center">
    <a href="#" class="text-green trigger-edit-row">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a href="{{ route('admin.brands.models.destroy', [$row->brand_id, $row->id]) }}"
       data-ajax-method="delete"
       data-ajax-confirm="Confirma a remoção do registo selecionado?"
       class="text-red">
        <i class="fas fa-trash-alt"></i>
    </a>
</div>