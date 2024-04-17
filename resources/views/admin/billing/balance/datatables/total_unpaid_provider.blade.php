@if($row->balance_total_unpaid > 0.00 && $row->balance_count_unpaid == 0)
    <?php \App\Models\Customer::where('id', $row->customer_id)->update([
        'balance_total_unpaid' => 0,
        'balance_count_expired' => 0
    ])
    ?>
    <b class="text-green">-{{ money(0, Setting::get('app_currency')) }}</b>
@elseif($row->balance_total_unpaid > 0.00)
    <b class="text-red">-{{ money($row->balance_total_unpaid, Setting::get('app_currency')) }}</b><br/>
    {{--<small class="text-red"><i class="fas fa-exclamation-triangle"></i> {{ $row->balance_count_unpaid }} documentos</small>--}}
@elseif($row->balance_total_unpaid < 0.00)
    <b class="text-green">+{{ money($row->balance_total_unpaid * -1, Setting::get('app_currency')) }}</b>
@else
    <b class="text-muted">{{ money(0, Setting::get('app_currency')) }}</b>
@endif

{{--
@if($row->balance_count_expired)
    <small>
        <span class="text-red italic"><i class="fas fa-exclamation-circle"></i> {{ $row->balance_count_expired }} Vencidos</span>
    </small>
@endif--}}
