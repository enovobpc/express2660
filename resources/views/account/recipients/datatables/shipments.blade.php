@if($row->shipments)
    {{ $row->shipments }}
@else
    <span class="text-red">
        <i class="fas fa-exclamation-triangle"></i> {{ $row->shipments }}
    </span>
@endif