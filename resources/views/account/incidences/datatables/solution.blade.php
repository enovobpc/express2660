<?php
    if($row->resolutions) {
        $solution = $row->resolutions->filter(function($item) use($row) {
            return $item->shipment_history_id == $row->history_id;
        })->last();
    }
?>
<b>{{ @$resolutionsTypes[@$solution->resolution_type_id] }}</b>
<br/>
<small>
    @if(@$solution->obs)
        {{ @$solution->obs }}
    @endif
</small>