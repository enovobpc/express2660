<?php
$row->expenses_price = $row->expenses_price + $row->fuel_price;
$subtotal = $row->shipping_price + $row->expenses_price;

if(Setting::get('shipment_sum_expenses')) {
    $row->shipping_price = $row->shipping_price + $row->expenses_price;
    $subtotal = $row->shipping_price;
}

?>

@if((empty(Auth::user()->agencies) || in_array($row->agency_id, Auth::user()->agencies)) && $row->type != \App\Models\Shipment::TYPE_MASTER)

    @if(!Auth::user()->hasRole([config('permissions.role.admin')]) && Auth::user()->allowedAction('block_prices'))
        N/A
    @else
        @if($row->shipping_price == 0.00 && !in_array($row->cod, ['S','D']))
            <span data-target="tooltip">
                <i class="text-red fas fa-exclamation-triangle"></i>
            </span>
        @endif

        @if(in_array($row->cod, ['S','D']))
            @if($row->ignore_billing)
                <strike class="label bg-orange" data-toggle="tooltip" title="Portes no Remetente/Destino">
                    <i class="fas fa-hand-holding-usd"></i> 
                    {{ money($row->shipping_price ? $row->shipping_price : 0, Setting::get('app_currency')) }}
                </strike>
            @else
                <span class="label bg-orange" data-toggle="tooltip" title="Portes no Remetente/Destino">
                    <i class="fas fa-hand-holding-usd"></i> 
                    {{ money($row->shipping_price ? $row->shipping_price : 0, Setting::get('app_currency')) }}
                </span>
            @endif
        @elseif(!empty($row->requested_by) && $row->requested_by != $row->customer_id)
            <span data-total="{{ $subtotal }}" class="text-orange" data-toggle="tooltip" title="Cliente faturação diferente do cliente solicitou">
                <i class="fas fa-user"></i> {{ money($row->shipping_price, Setting::get('app_currency')) }}
            </span>
        @elseif($row->ignore_billing)
            <strike class="text-muted" data-toggle="tooltip" title="Serviço ignorado da faturação por já se encontrar pago.">
                {{ money($row->shipping_price, Setting::get('app_currency')) }}
            </strike>
        @else
            <span data-total="{{ $subtotal }}">
                {{ money($row->shipping_price, Setting::get('app_currency')) }}
            </span>
        @endif

        @if($row->price_fixed)
        <span data-toggle="tooltip" title="Preço bloqueado. Este valor não será alterado.">
            <i class="text-red fas fa-lock"></i>
        </span>
        @endif

        @if($row->expenses_price && !Setting::get('shipment_sum_expenses'))
            <br/>
            <span class="label bg-green" data-toggle="tooltip" title="Taxas adicionais">
                +{{ money($row->expenses_price, Setting::get('app_currency')) }}
            </span>
        @endif

        @if($row->fuel_price)
            <br/>
            <small class="text-muted" data-toggle="tooltip" title="Combustível: {{ money($row->fuel_price, Setting::get('app_currency')) }}">
                <i class="fas fa-gas-pump"></i> {{ (float) $row->fuel_tax }}%
            </small>
        @endif
    @endif
@elseif(Auth::user()->showPrices() && $row->type == \App\Models\Shipment::TYPE_MASTER)
    @if($row->expenses_price > 0.00 || $row->shipping_price > 0.00)
        <span data-total="{{ $subtotal }}">{{ $row->shipping_price > 0.00 ? money($row->shipping_price, Setting::get('app_currency')) : '-.--' }}</span>
        @if($row->expenses_price > 0.00)
            <br/>
            <span class="label bg-green" data-toggle="tooltip" title="Taxas adicionais">
                +{{ money($row->expenses_price, Setting::get('app_currency')) }}
            </span>
        @endif
    @endif
@endif