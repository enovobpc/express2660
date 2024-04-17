@if($row->shipping_price == 0.00 || empty($row->shipping_price))
    <span class="text-red">
        <i class="fa fa-exclamation-triangle"></i>
        {{ money($row->shipping_price, Setting::get('app_currency')) }}
    </span>
@else
    {{ money($row->shipping_price, Setting::get('app_currency')) }}
@endif

@if(($row->expenses_price + $row->fuel_price) > 0.00)
    <br/>
    <span class="label label-success">+{{ money($row->expenses_price + $row->fuel_price, Setting::get('app_currency')) }}</span>
@endif
