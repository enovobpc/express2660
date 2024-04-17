@if($row->status)
    <span class="text-green">
        <i class="fas fa-check-circle text-green"></i>
        @trans('Check OK')
    </span>

@else
    <span class="text-red">
        <i class="fas fa-exclamation-circle text-red"></i>
        @trans('Anomalias')
    </span>
@endif