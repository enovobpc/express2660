@if($row->balance_divergence > 0.00)
    <span class="text-red">
        <i class="fas fa-exclamation-triangle"></i> {{ money($row->balance_divergence, Setting::get('app_currency')) }}
        <br/>
        <small>Dif.: {{ money($row->balance_divergence - $row->balance_total_unpaid, Setting::get('app_currency')) }}</small>
    </span>
@endif