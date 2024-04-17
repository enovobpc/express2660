@if($row->is_active)
    <span data-toggle="tooltip" title="Ativo">
        <i class="fas fa-check-circle text-green"></i>
    </span>
@else
    <span data-toggle="tooltip" title="Inativo">
        <i class="fas fa-times-circle text-muted"></i>
    </span>
@endif