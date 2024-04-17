<?php
    $operator = @$operatorsList[$row->operator_id][0];
?>
{{ $row->created_at->format('Y-m-d') }}<br/>
<small>{{ $row->created_at->format('H:i') }}

@if($row->operator_id)
    <br/>
    <span class="text-muted" data-toggle="tooltip" title="{{ @$operator['name'] }}">
        <i class="fas fa-user"></i> {{ @$operator['code_abbrv'] ? @$operator['code_abbrv'] : @$operator['code'] }}
    </span>
@endif
</small>