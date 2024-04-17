{{ @$row->operator->name }}
@if($row->operator_price > 0.00)
    <br/>
    @if($row->is_paid)
    <span class="text-muted" data-toggle="tooltip" title="Valor já pago ao motorista">
        <i class="fas fa-check-circle text-green"></i>
    </span>
    @else
    <span class="text-muted" data-toggle="tooltip" title="Valor por pagar ao motorista">
        <i class="fas fa-times-circle text-muted"></i>
    </span>
    @endif

    {{ money($row->operator_price, Setting::get('app_currency')) }}

@endif
