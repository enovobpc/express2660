@if($row->km > 0.00)
    {{ money($row->km, ' km') }}<br/>
    <i class="text-muted">({{ money($row->total_price / $row->km, Setting::get('app_currency')) }}/km)</i>
@else
    -.-- km
@endif
