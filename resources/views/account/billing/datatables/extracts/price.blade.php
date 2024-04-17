@if($value > 0.00)
    {{ money($value, Setting::get('app_currency')) }}
@else
    <span style="color: #ccc">{{ money(0, Setting::get('app_currency')) }}</span>
@endif