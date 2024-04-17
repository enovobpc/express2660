@if($row->balance_total > 0.00)
    <b class="text-red">{{ money($row->balance_total, $currency) }}</b><br/>
    {{--@if($row->balance_count_unpaid)
    <small class="text-red"><i class="fas fa-exclamation-triangle"></i> {{ $row->balance_count_unpaid }} vencidos</small>
    @endif--}}
@elseif($row->balance_total < 0.00)
    <b class="text-green">{{ money($row->balance_total * -1, $currency) }}</b>
@else
    <b class="text-muted">{{ money(0, $currency) }}</b>
@endif