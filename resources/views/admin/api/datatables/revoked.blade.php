<div class="text-center">
    @if(!$row->revoked)
        <i class="fas fa-check-circle text-green"></i>
    @else
        <i class="fas fa-times-circle text-muted"></i>
    @endif
</div>