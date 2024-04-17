@if($row->total_price_for_recipient)
{{ money($row->total_price_for_recipient, Setting::get('app_currency')) }}
@endif

