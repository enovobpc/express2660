@if($row->status_id != \App\Models\Logistic\ShippingOrderStatus::STATUS_PENDING)
    @if($row->qty_satisfied < $row->qty_total)
        <span class="text-red">
            <small>{{ $row->qty_satisfied ? $row->qty_satisfied : 0 }}</small> / {{ $row->qty_total }} <i class="fas fa-times"></i>
        </span>
    @else
        <span class="text-green">
            <small>{{ $row->qty_satisfied }} /</small> {{ $row->qty_total }} <i class="fas fa-check"></i>
        </span>
    @endif
@else
    <span class="text-muted"><small>0 /</small> {{ $row->qty_total }}</span>
@endif