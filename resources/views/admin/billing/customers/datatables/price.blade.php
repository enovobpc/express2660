@if($price != 0.00)
    {{ money($price, Setting::get('app_currency')) }}
@else
    <span class="text-light-gray">{{ money(0, Setting::get('app_currency')) }}</span>
@endif