@if($row->price && $row->price > 0.00)
    {{ money($row->price, Setting::get('app_currency')) }}
@else
    Personalizável
@endif