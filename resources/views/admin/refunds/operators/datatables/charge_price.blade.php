@if($row->charge_price)
{{ money($row->charge_price, Setting::get('app_currency')) }}
@endif

