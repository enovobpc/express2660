<?php
    if($row->resolutions) {
        $solution = $row->resolutions->filter(function($item) use($row) {
            return $item->shipment_history_id == $row->history_id;
        })->last();
    }
?>
<b>{{ @$resolutionsTypes[@$solution->resolution_type_id] }}</b>
@if(@$solution->submited_at)
    <i class="fas fa-check text-green" data-toggle="tooltip" title="Submetido em {{ $solution->submited_at }}"></i>
@endif
<br/>
<small>
    @if(@$solution->obs)
        {{ @$solution->obs }}
    @endif
</small>