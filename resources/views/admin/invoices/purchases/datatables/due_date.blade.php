<?php
$limitDt = $row->payment_until ? $row->payment_until : $row->due_date;
$today = new Date();
$date  = new Date($limitDt);
?>
{{ $row->due_date }}
@if($row->payment_until && $row->received_date)
<div class="text-muted italic" data-toggle="tooltip" title="Data limite de pagamento até {{ $row->payment_until }}.">
    <small>{{ $row->payment_until }}</small>
</div>
@endif
@if(!$row->is_settle && $date < $today)
    <div class="text-red">
        <small><i class="fas fa-exclamation-triangle"></i> {{ $date->diffInDays($today) }} dias atraso</small>
    </div>
@endif