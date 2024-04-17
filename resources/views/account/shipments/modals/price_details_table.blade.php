<?php $currency = Setting::get('app_currency') ?>
<div class="row">
    <div class="col-sm-12">
        <table class="table table-condensed m-b-5">
            <tr>
                <th class="bg-gray p-l-15">{{ trans('account/global.word.expense') }}</th>
                <th class="bg-gray text-right w-60px">{{ trans('account/global.word.price') }}</th>
                <th class="bg-gray text-center w-50px">{{ trans('account/global.word.qty') }}</th>
                <th class="bg-gray text-right w-65px">{{ trans('account/global.word.subtotal') }}</th>
                <th class="bg-gray text-right w-65px">{{ trans('account/global.word.vat') }}</th>
                <th class="bg-gray text-right w-70px">{{ trans('account/global.word.total') }}</th>
            </tr>
            <tr>
                <td class="p-l-15">{{ trans('account/global.word.shipment') }}: {{ @$prices['service']['name'] }} {{ strtoupper(@$prices['prices']['zone']) }} <small class="italic">({{ @$prices['shipment']['volumes'] }} Vol, {{ money(@$prices['parcels']['taxable_weight'], 'kg') }} {{ @$prices['shipment']['kms'] ? ', '.@$prices['shipment']['kms'].'km' : '' }})</small></td>
                <td class="text-right">{{ money(@$prices['prices_details']['shipping']['subtotal'], $currency) }}</td>
                <td class="text-center">1</td>
                <td class="text-right">{{ money(@$prices['prices_details']['shipping']['subtotal'], $currency) }}</td>
                <td class="text-right">{{ money(@$prices['prices_details']['shipping']['vat'], $currency) }}</td>
                <td class="text-right bold">{{ money(@$prices['prices_details']['shipping']['total'], $currency) }}</td>
            </tr>
            @if(@$prices['prices']['fuel_tax'])
                <tr>
                    <td>{{ trans('account/global.word.fuel-tax') }} <small class="italic">({{ money(@$prices['prices']['fuel_tax'], '%') }})</small></td>
                    <td class="text-right">{{ money(@$prices['prices_details']['fuel']['subtotal'], $currency) }}</td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ money(@$prices['prices_details']['fuel']['subtotal'], $currency) }}</td>
                    <td class="text-right">{{ money(@$prices['prices_details']['fuel']['vat'], $currency) }}</td>
                    <td class="text-right bold">{{ money(@$prices['prices_details']['fuel']['total'], $currency) }}</td>
                </tr>
            @endif
            @if(@$prices['expenses'])
                @foreach($prices['expenses'] as $expense)
                    <tr>
                        <td class="p-l-15">{{ $expense['name'] }}</td>
                        <td class="text-right">{{ money($expense['price'], $currency) }}</td>
                        <td class="text-center">{{ $expense['qty'] }}</td>
                        <td class="text-right">{{ money($expense['subtotal'], $currency) }}</td>
                        <td class="text-right">{{ money($expense['vat'], $currency) }}</td>
                        <td class="text-right bold">{{ money($expense['total'], $currency) }}</td>
                    </tr>
                @endforeach
            @endif
            {{--<tr>
                <td></td>
                <td class="text-right"></td>
                <td class="text-right"></td>
                <td class="text-right text-blue bold fs-16">{{ money(@$prices['billing']['subtotal'], $currency) }}</td>
                <td class="text-right text-blue bold fs-16">{{ money(@$prices['billing']['vat'], $currency) }}</td>
                <td class="text-right text-blue bold fs-16">{{ money(@$prices['billing']['total'], $currency) }}</td>
            </tr>--}}
        </table>
    </div>
</div>