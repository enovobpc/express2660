<?php $date = new Date($row->due_date); ?>

@if(!$row->is_settle && $date < $today)
    <span class="text-red">
        <i class="fas fa-exclamation-triangle"></i> {{ $date->format('Y-m-d') }}<br/>
        <small>{{ trans('account/billing.word.expired-days', ['days' => $date->diffInDays($today)]) }}</small>
    </span>
@else
    {{ $date->format('Y-m-d') }}
@endif
