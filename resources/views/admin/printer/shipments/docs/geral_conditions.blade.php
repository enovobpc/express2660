<?php
$conditions = Setting::get('prices_table_general_conditions');
$conditions = str_replace('<strong>', '<b style="font-weight:bold">', str_replace('</strong>', '</b>', $conditions))
?>
<div class="shipping-instructions" style="width: 210mm; padding: 10mm; font-size: 10pt; height: 250mm">
    <div class="guide-content">
        <div class="guide-row" style="padding-top: 15mm;">
            {!! $conditions !!}
        </div>
    </div>
</div>