<b data-toggle="tooltip" title="Valor total de faturação no período selecionado">
    @if($total == 0.00)
        <span class="text-red">
            <i class="fas fa-exclamation-triangle"></i> {{ money($total, Setting::get('app_currency')) }}
        </span>
    @else
        {{ money($total, Setting::get('app_currency')) }}
    @endif
</b>
<br/>
<small data-toggle="tooltip" title="Valor total com fatura">{{ money($row->billing->sum('total_month'), Setting::get('app_currency')) }}</small>