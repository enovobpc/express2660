@if($row->sell_price && $row->sell_price > 0.00)
    {{ money($row->sell_price, Setting::get('app_currency')) }}
@else
    Personalizável
@endif