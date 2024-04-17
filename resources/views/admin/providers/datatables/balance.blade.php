@if(hasModule('purchase_invoices'))
    @if(!empty($row->balance_total_unpaid) && $row->balance_total_unpaid > 0.00)
        <span class="text-red">{{ money($row->balance_total_unpaid * -1, Setting::get('app_currency')) }}</span>
    @else
        <span class="text-green">{{ money($row->balance_total_unpaid * -1, Setting::get('app_currency')) }}</span>
    @endif
@else
    <span data-toggle="tooltip" title="Módulo de despesas a fornecedores não incluído na sua licença">@trans('N/A')</span>
@endif

@if($row->payment_method)
    <br/>
    <small class="text-muted">{{ @$row->paymentCondition->name }}</small>
@endif