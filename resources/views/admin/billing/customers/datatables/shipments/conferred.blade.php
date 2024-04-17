<div data-id="{{ $row->id }}">
    @if($row->customer_conferred)
        <i class="fas fa-check-circle text-green btn-confirm"></i>
    @else
        <i class="fas fa-times-circle text-muted btn-confirm"></i>
    @endif
</div>