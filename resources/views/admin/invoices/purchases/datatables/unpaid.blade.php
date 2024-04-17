@if($row->total_unpaid > $row->total)
@elseif($row->total_unpaid == $row->total)
    <b class="text-red">{{ money($row->total_unpaid, $row->currency) }}</b>
@elseif($row->total_unpaid > 0.00)
    <b class="text-yellow">{{ money($row->total_unpaid, $row->currency) }}</b>
@endif