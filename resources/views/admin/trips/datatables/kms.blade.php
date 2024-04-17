<div class="text-right">{{ $row->kms ? round($row->kms) : '--' }}<small> km</small></div>
@if($row->pickup_date)
    <div class="text-right text-muted italic"><small>{{ $row->duration_days }} @trans('dias')</small></div>
@endif