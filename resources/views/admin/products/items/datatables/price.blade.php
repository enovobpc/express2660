@if($row->promo_price)
<b class="text-yellow">{{ money($row->promo_price, Setting::get('app_currency')) }}</b><br/>
<strike>{{ money($row->price, Setting::get('app_currency')) }}</strike>
@else
{{ money($row->price, Setting::get('app_currency')) }}
@endif

@if($row->cost_price > 0.00 && $row->price > 0.00)
    @if($row->price >= $row->cost_price)
    <br/>
    <span class="text-green"><i class="fas fa-caret-up"></i> {{ $row->price ? money((($row->price - $row->cost_price) * 100) / $row->price, '%', 0)  : money(0, '%') }} ({{ money($row->price - $row->cost_price, Setting::get('app_currency')) }})</span>
    @else
    <br/>
    <span class="text-red"><i class="fas fa-caret-down"></i> {{ $row->cost_price ? money((($row->cost_price - $row->price) * 100) / $row->cost_price, '%', 0) : money(0, '%') }} ({{ money($row->price - $row->cost_price, Setting::get('app_currency')) }})</span>
    @endif
@endif