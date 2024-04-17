<div data-id="{{ $row->id }}" class="text-center">
    @if($row->is_active)
        <i class="fas fa-check-circle text-green btn-confirm" ></i>
    @else
        <i class="fas fa-times-circle text-muted btn-confirm"></i>
    @endif
</div>