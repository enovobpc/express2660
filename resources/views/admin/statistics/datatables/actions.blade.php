@if(Auth::user()->hasRole([config('permissions.role.admin')]) || (!Auth::user()->hasRole([config('permissions.role.admin')]) && in_array($row->id, $myAgencies)))
<div class="action-buttons text-center">
    <a href="{{ route('admin.agencies.edit', $row->id) }}" class="text-green">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a href="{{ route('admin.agencies.destroy', $row->id) }}" data-method="delete" data-confirm="Confirma a remoção do registo selecionado?" class="text-red">
        <i class="fas fa-trash-alt"></i>
    </a>
</div>
@endif