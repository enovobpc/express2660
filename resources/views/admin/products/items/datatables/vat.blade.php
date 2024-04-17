<?php 
$percent = Setting::get('vat_rate_' . $row->vat_rate);
?>
@if($row->promo_price)
<b>{{ money($row->promo_price + getVat($row->promo_price, $percent), Setting::get('app_currency')) }}</b><br/>
<i>{{ money($percent, '%') }}</i>
@else
<b>{{ money($row->price + getVat($row->price, $percent), Setting::get('app_currency')) }}</b><br/>
<i>{{ money($percent, '%') }}</i>
@endif
