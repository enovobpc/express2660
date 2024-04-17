<div class="action-buttons text-center">
    <a href="{{ route('admin.website.recruitments.show', $row->hash) }}" class="text-blue">
        <i class="fas fa-search-plus"></i>
    </a>
    <a href="{{ route('admin.website.recruitments.destroy', $row->hash) }}" data-method="delete" data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
        <i class="fas fa-trash-alt"></i>
    </a>
</div>