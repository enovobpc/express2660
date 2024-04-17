@if($row->sense == 'debit')
    <span class="bold text-red">
    -{{ money($row->amount, Setting::get('app_currency')) }}
    </span>
@else
    <span class="bold text-green">
    +{{ money($row->amount, Setting::get('app_currency')) }}
    </span>
@endif
