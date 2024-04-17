{{ money($row->total_price + $row->total_goods_price, Setting::get('app_currency')) }}
{{--
<br/>
@if($row->customer_id)
    <i class="text-muted" data-toggle="tooltip" title="{{ @$row->customer->name }}">{{ @$row->customer->code }}</i>
@endif
--}}
