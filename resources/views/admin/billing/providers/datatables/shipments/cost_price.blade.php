<div class="bold">
    @if($row->cost_shipping_price == 0.00 || empty($row->cost_shipping_price))
        <span class="text-red"><i class="fa fa-exclamation-triangle"></i> {{ money($row->cost_shipping_price, Setting::get('app_currency')) }}</span>
    @else
        {{ money($row->cost_shipping_price, Setting::get('app_currency')) }}
    @endif
</div>
@if($row->cost_expenses_price > 0.00)
<span class="label label-success">+{{ money($row->cost_expenses_price, Setting::get('app_currency')) }}</span>
@endif

@if ($row->conferred_original_cost)
<div>
    <small data-toggle="tooltip" title="Custo inicial antes da conferência" class="text-blue"><i class="fas fa-info-circle"></i> {{ money($row->conferred_original_cost, Setting::get('app_currency'))}}</small>
</div>
@endif
