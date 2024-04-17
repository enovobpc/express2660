<?php
    $days = floor(($row->balance_time %2592000)/86400);
?>
@if($row->balance_km)
    {{ number_format(@$row->balance_km, 0, ',', '.') }} @trans('km')
    <br/>
    <small>{{ $days }} @trans('dias')</small>
@endif