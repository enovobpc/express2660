@if(@$row->route_id)
<div>
    Rota {{ @$row->route->code }}
</div>
@endif
@if(@$row->status->is_final && @$row->last_history)
    {{ $row->last_history->created_at->format('Y-m-d') }}
    @if(config('app.source') == 'rlrexpress' && empty(@$row->refund_control->received_date) && $row->last_history->created_at->diffInDays(\Jenssegers\Date\Date::now()) >= 5)
        <small class="text-red">
        <i class="fas fa-exclamation-triangle"></i> Há {{ $row->last_history->created_at->diffInDays(\Jenssegers\Date\Date::now()) }} dias
        </small>
    @endif
@endif
<div>
    <small class="text-muted" data-toggle="tooltip" title="{{ @$row->operator->name }}">
        {{ @$row->operator->code_abbrv ? @$row->operator->code_abbrv : @$row->operator->code }}
    </small>
</div>