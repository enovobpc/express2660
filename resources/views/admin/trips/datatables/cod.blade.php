<?php $price = @$row->shipments->sum('total_price_for_recipient') + $price = @$row->shipments->sum('charge_price'); ?>
@if($price > 0.00)
    <div class="text-center">
        {{ money($price, Setting::get('app_currency')) }}
    </div>
@else
    <div class="text-center" style="opacity: 0.3">
        {{ money(0, Setting::get('app_currency')) }}
    </div>
@endif