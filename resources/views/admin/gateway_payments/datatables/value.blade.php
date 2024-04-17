@if($row->sense == 'debit')
    <b class="text-red">-{{ money($row->value, Setting::get('app_currency')) }}</b>
@else
    <b class="text-green">+{{ money($row->value, Setting::get('app_currency')) }}</b>
@endif