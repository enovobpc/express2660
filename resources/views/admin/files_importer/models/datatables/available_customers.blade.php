@if(in_array($row->type, ['shipments', 'shipments_fast']))
    @if($row->available_customers)
        <i class="fas fa-check-circle text-green"></i>
    @else
        <i class="fas fa-times-circle text-muted"></i>
    @endif
@endif