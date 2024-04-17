@if($row->balance_total_unpaid > 0.00)
    <b class="text-red">{{ money($row->balance_total_unpaid, Setting::get('app_currency')) }}</b>
@else
    <b class="text-green">Regularizado</b>
@endif