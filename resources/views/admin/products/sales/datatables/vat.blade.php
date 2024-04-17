<?php 
$percent = Setting::get('vat_rate_' . $row->vat_rate);
?>
<b>{{ money($row->subtotal + getVat($row->subtotal, $percent), Setting::get('app_currency')) }}</b><br/>
<small class="text-muted"><i>{{ money($percent, '%') }}</i></small>
