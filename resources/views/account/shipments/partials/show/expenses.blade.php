<table class="table m-b-0">
    <tr>
        <th class="w-100px bg-gray-light" style="border-top: none">{{ trans('account/global.word.date') }}</th>
        <th class="bg-gray-light w-50px" style="border-top: none">{{ trans('account/global.word.code') }}</th>
        <th class="bg-gray-light" style="border-top: none">{{ trans('account/global.word.expense') }}</th>
        <th class="bg-gray-light w-90px text-right" style="border-top: none">{{ trans('account/global.word.price') }}</th>
        <th class="bg-gray-light w-70px text-right" style="border-top: none">{{ trans('account/global.word.qty') }}</th>
        <th class="bg-gray-light w-90px text-right" style="border-top: none">{{ trans('account/global.word.subtotal') }}</th>
        <th class="bg-gray-light w-90px text-right" style="border-top: none">{{ trans('account/global.word.vat') }}</th>
        <th class="bg-gray-light w-90px text-right" style="border-top: none">{{ trans('account/global.word.total') }}</th>
    </tr>
    <tr>
        <td>{{ $shipment->created_at->format('Y-m-d') }}</td>
        <td>{{ @$shipment->service->display_code }}</td>
        <td>{{ trans('account/global.word.expedition') }}: {{ @$shipment->service->name }} {{ strtoupper($shipment->billing_zone) }} <small class="italic">({{ $shipment->volumes }} Vol, {{ money($shipment->taxable_weight, 'kg') }} {{ $shipment->kms ? ', '.$shipment->kms.'km' : '' }})</small></td>
        <td class="text-right">{{ money($shipment->total_price, Setting::get('app_currency')) }}</td>
        <td class="text-right">1</td>
        <td class="text-right">{{ money($shipment->shipping_price, Setting::get('app_currency')) }}</td>
        <td class="text-right">{{ money($shipment->shipping_price_vat, Setting::get('app_currency')) }}</td>
        <td class="text-right bold">{{ money($shipment->shipping_price_total, Setting::get('app_currency')) }}</td>
    </tr>
    @if($shipment->fuel_price)
        <tr>
            <td>{{ $shipment->created_at->format('Y-m-d') }}</td>
            <td>FUEL</td>
            <td>{{ trans('account/global.word.fuel-tax') }} <small class="italic">({{ money($shipment->fuel_tax, '%') }})</small></td>
            <td class="text-right">{{ money($shipment->fuel_price, Setting::get('app_currency')) }}</td>
            <td class="text-right">1</td>
            <td class="text-right">{{ money($shipment->fuel_price, Setting::get('app_currency')) }}</td>
            <td class="text-right">{{ money($shipment->fuel_price_vat, Setting::get('app_currency')) }}</td>
            <td class="text-right bold">{{ money($shipment->fuel_price_total, Setting::get('app_currency')) }}</td>
        </tr>
    @endif
    <?php $price = $total = $shipment->total_price ?>
    @foreach($shipment->expenses as $expense)
        <?php
            $price+= $expense->pivot->price;
            $total+= $expense->pivot->subtotal;
        ?>
        <tr>
            <td>{{ @$expense->pivot->date }}</td>
            <td>{{ $expense->code }}</td>
            <td>{{ $expense->name }}</td>
            <td class="text-right">{{ money($expense->pivot->price, Setting::get('app_currency')) }}</td>
            <td class="text-right">{{ $expense->pivot->qty }}</td>
            <td class="text-right">{{ money($expense->pivot->subtotal, Setting::get('app_currency')) }}</td>
            <td class="text-right">{{ money($expense->pivot->vat, Setting::get('app_currency')) }}</td>
            <td class="text-right bold">{{ money($expense->pivot->total, Setting::get('app_currency')) }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="4" class="vertical-align-middle">
            <p class="text-muted m-b-0 fs-12 text-info">
                <i class="fas fa-info-circle"></i> {{ trans('account/shipments.modal-show.tips.prices') }}
            </p>
        </td>
        <td class="text-right bold vertical-align-bottom">{{ trans('account/global.word.total') }}</td>
        <td class="text-right bold vertical-align-bottom fs-16">{{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}</td>
        <td class="text-right bold vertical-align-bottom fs-16">{{ money($shipment->billing_vat, Setting::get('app_currency')) }}</td>
        <td class="text-right bold vertical-align-bottom fs-20">{{ money($shipment->billing_total, Setting::get('app_currency')) }}</td>
    </tr>
</table>
