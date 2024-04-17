@if($row->status_id != \App\Models\Logistic\ReceptionOrderStatus::STATUS_REQUESTED)
    @if($row->total_qty_received < $row->total_qty)
        <span class="text-red">
            <small>{{ $row->total_qty_received ? $row->total_qty_received : 0 }}</small> / {{ $row->total_qty }} <i class="fas fa-times"></i>
        </span>
    @else
        <span class="text-green">
            <small>{{ $row->total_qty_received }} /</small> {{ $row->total_qty }} <i class="fas fa-check"></i>
        </span>
    @endif
@else
    <span class="text-muted"><small>0 /</small> {{ $row->total_qty }}</span>
@endif