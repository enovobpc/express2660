@if($row->sense == 'debit')
    <span class="text-red">
        -{{ money($row->value, Setting::get('app_currency')) }}
    </span>
@else
    <span class="text-green">
        +{{ money($row->value, Setting::get('app_currency')) }}
    </span>
@endif