<?php
$price = $row->total_price;
?>
<div class="text-right">
    @if($price > 0.00)
        <div class="bold">
            {{ money($price, Setting::get('app_currency')) }}
        </div>
    @else
        <div class="bold" style="opacity: 0.3">
            {{ money(0, Setting::get('app_currency')) }}<br/>
        </div>
    @endif

    @if($row->balance > 0.00)
        <small class="text-green" data-toggle="tooltip" title="Margem lucro">
            <i class="fas fa-caret-up"></i> {{ money($row->balance, '%', 0) }}
        </small>
    @elseif($row->balance < 0.00)
        <small class="text-red" data-toggle="tooltip" title="Margem lucro">
            <i class="fas fa-caret-down"></i> {{ money($row->balance, '%', 0) }}
        </small>
    @else
        <small class="text-muted" data-toggle="tooltip" title="Margem lucro" style="opacity: 0.3">
            0.00%
        </small>
    @endif
</div>