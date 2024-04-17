<?php $currency = Setting::get('app_currency')?>
<table class="table m-b-0">
    <tr>
        <th class="w-110px bg-gray-light" style="border-top: none">Data Faturação</th>
        <th class="bg-gray-light" style="border-top: none">Encargo</th>
        <th class="bg-gray-light w-90px text-right" style="border-top: none">Preço Un.</th>
        <th class="bg-gray-light w-60px text-right" style="border-top: none">Qtd</th>
        <th class="bg-gray-light w-90px text-right" style="border-top: none">Subtotal</th>
        <th class="bg-gray-light w-70px text-right" style="border-top: none">IVA</th>
        <th class="bg-gray-light w-70px text-right" style="border-top: none">Total</th>
        @if(Auth::user()->showPrices())
        <th class="bg-gray-light w-80px text-right" style="border-top: none">Custo</th>
        @endif
        {{-- @if(empty($shipment->invoice_id))
        <th class="bg-gray-light w-70px text-right" style="border-top: none">Ações</th>
        @endif --}}
    </tr>
    <tr>
        <td>{{ @$shipment->billing_date ? $shipment->billing_date : '' }}</td>
        <td>Expedição: {{ @$shipment->service->name }} {{ strtoupper(\App\Models\Shipment::getBillingCountry($shipment->sender_country, $shipment->recipient_country, $shipment->is_import)) }} ({{ $shipment->volumes }} Volume, {{ money($shipment->weight > $shipment->volumetric_weight ? $shipment->weight : $shipment->volumetric_weight, 'kg') }} {{ $shipment->kms ? ', '.$shipment->kms.'km' : '' }})</td>
        <td class="text-right">{{ money($shipment->total_price, $currency) }}</td>
        <td class="text-right">1</td>
        <td class="text-right">{{ money($shipment->total_price, $currency) }}</td>
        <td class="text-right">{{ money($shipment->vat_rate, '%') }}</td>
        <td class="text-right bold">{{ money($shipment->total_price * (1+($shipment->vat_rate/100)), $currency) }}</td>
        @if(Auth::user()->showPrices())
            <td class="text-right">{{ money($shipment->cost_price, $currency) }}</td>
        @endif
        {{-- @if(empty($shipment->invoice_id))
        <td></td>
        @endif --}}
    </tr>
    <tr>
        <td>{{ @$shipment->billing_date ? $shipment->billing_date : '' }}</td>
        <td>Taxa Combustível ({{ money(@$shipment->fuel_tax, '%') }})</td>
        <td class="text-right">{{ money($shipment->fuel_price, $currency) }}</td>
        <td class="text-right">1</td>
        <td class="text-right">{{ money($shipment->fuel_price, $currency) }}</td>
        <td class="text-right">{{ money($shipment->vat_rate, '%') }}</td>
        <td class="text-right bold">{{ money($shipment->fuel_price * (1+($shipment->vat_rate/100)), $currency ) }}</td>
        @if(Auth::user()->showPrices())
            <td class="text-right">{{ money($shipment->cost_fuel_price, $currency) }}</td>
        @endif
        {{-- @if(empty($shipment->invoice_id))
            <td></td>
        @endif --}}
    </tr>
    <?php
    $price = $total = $shipment->total_price + $shipment->fuel_price;
    $totalCost = $shipment->cost_price + $shipment->cost_fuel_price;
    ?>
    @foreach($shipment->expenses as $expense)
        <?php
        $price+= @$expense->pivot->price;
        $total+= @$expense->pivot->subtotal;
        $totalCost+= @$expense->pivot->cost_price;
        ?>
        <tr>
            <td>{{ @$expense->pivot->date }}</td>
            <td>{{ $expense->name }}</td>
            <td class="text-right">{{ money($expense->pivot->price, $currency) }}</td>
            <td class="text-right">{{ $expense->pivot->qty }}</td>
            <td class="text-right">{{ money($expense->pivot->subtotal, $currency) }}</td>
            <td class="text-right">{{ money($expense->pivot->vat_rate, '%') }}</td>
            <td class="text-right bold">{{ money($expense->pivot->total, $currency) }}</td>
            @if(Auth::user()->showPrices())
                <td class="text-right">{{ money($expense->pivot->cost_price, $currency) }}</td>
            @endif
            {{-- @if(empty($shipment->invoice_id))
            <td class="text-right">
                <div class="action-buttons">
                    <a href="{{ route('admin.shipments.expenses.edit', [$expense->pivot->shipment_id, $expense->pivot->id]) }}"
                       data-toggle="modal"
                       data-target="#modal-remote"
                       class="text-green">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <a href="{{ route('admin.shipments.expenses.destroy', [$expense->pivot->shipment_id, $expense->pivot->id]) }}"
                       data-method="delete"
                       data-confirm="Confirma a remoção do encargo selecionado?"
                       class="text-red">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>
            </td>
            @endif --}}
        </tr>
    @endforeach
    <tr>
        <td colspan="3" class="vertical-align-middle">
            {{--@if(empty($shipment->invoice_id))
            <p class="text-muted m-b-0 fs-12 text-info">
                <i class="fas fa-info-circle"></i> Os preços apresentados são líquidos. No ato de emissão da fatura o sistema detectará a taxa de IVA a cobrar.
            </p>
            @endif--}}
        </td>
        <td class="text-right vertical-align-middle">{{ trans('account/global.word.total') }}</td>
        <td class="text-right fs-16">{{ money($shipment->billing_subtotal, $currency) }}</td>
        <td class="text-right fs-16">{{ money($shipment->billing_vat, $currency) }}</td>
        <td class="text-right bold fs-16">{{ money($shipment->billing_total, $currency) }}</td>
        <td class="text-right fs-16">
            <div>{{ money($totalCost, $currency) }}</div>
        </td>
        @if(empty($shipment->invoice_id))
        {{-- <td></td> --}}
        @endif
    </tr>
</table>
{{--
@if(empty($shipment->invoice_id))
<a href="{{ route('admin.shipments.expenses.create', $shipment->id) }}" class="btn btn-xs btn-success" data-toggle="modal" data-target="#modal-remote"><i class="fas fa-plus"></i> Adicionar Encargo</a>
@endif--}}
