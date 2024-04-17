<?php $currency = Setting::get('app_currency'); ?>
<div class="budget-totals">
    <div class="row row-0">
        <div class="col-sm-4">
            @if(@$prices['billing']['is_particular'])
            <h5 class="budget-subtotal">
                <small>Subtotal</small><br/>
                <b>{{ money(@$prices['billing']['subtotal'], $currency) }}</b>
            </h5>
            @endif
            <h5 class="budget-subtotal">
                <small>IVA ({{ (float) @$prices['billing']['vat_rate'] }}%)</small><br/>
                <b>{{ money(@$prices['billing']['vat'], $currency) }}</b>
            </h5>
            @if(!@$prices['billing']['is_particular'])
                <h5 class="budget-subtotal">
                    <small>Total c/IVA</small><br/>
                    <b>{{ money(@$prices['billing']['total'], $currency) }}</b>
                </h5>
            @endif
        </div>
        <div class="col-sm-8">
            <h1 class="m-0">
                @if(@$prices['billing']['is_particular'])
                    <small>Total a Pagar<sup>*</sup></small>
                    <span class="budget-total">{{ money(@$prices['billing']['total'], $currency) }}</span>
                @else
                    <small>Subtotal<sup>*</sup></small>
                    <span class="budget-total">{{ money(@$prices['billing']['subtotal'], $currency) }}</span>
                @endif
            </h1>
        </div>
    </div>
</div>
<div class="sp-15"></div>
<div class="budget-details" style="margin: 5px -10px;">
    @if(@$prices['errors'])
        <div class="budget-errors text-blue text-center p-20">
            <i class="fas fa-info-circle fs-22"></i>
            @foreach(@$prices['errors'] as $key => $error)
                <div>
                    @if($key)
                        <hr style="margin: 3px 0"/>
                    @endif
                    {!! $error !!}
                </div>
            @endforeach
        </div>
    @elseif(@$prices['shipment'])
        <table class="table-condensed w-100 fs-13">
            <tr>
                <th>Encargo</th>
                <th class="text-right bold w-1">Subtotal</th>
                <th class="text-right bold w-1">Total</th>
            </tr>
            <tr>
                <td>
                    Envio - <small class="italic">{{ @$prices['shipment']['volumes'] ? @$prices['shipment']['volumes'] : '0' }} Vol. &bull;
                        {{ @$prices['parcels']['taxable_weight'] ? @$prices['parcels']['taxable_weight'] : '0.00' }}KG
                        @if(@$prices['shipment']['kms'] > 0.00)
                        &bull;{{ @$prices['shipment']['kms'] }}km
                        @endif
                    </small>
                </td>
                <td class="text-right">{{ money(@$prices['prices_details']['shipping']['subtotal'], $currency) }}</td>
                <td class="text-right bold">{{ money(@$prices['prices_details']['shipping']['total'], $currency) }}</td>
            </tr>
            @if(@$prices['prices_details']['fuel'])
            <tr>
                <td>{{ trans('account/global.word.fuel-tax') }} <small class="italic">({{ (float) @$prices['prices']['fuel_tax'] }}%)</small></td>
                <td class="text-right">{{ money(@$prices['prices_details']['fuel']['subtotal'], $currency) }}</td>
                <td class="text-right bold">{{ money(@$prices['prices_details']['fuel']['total'], $currency) }}</td>
            </tr>
            @endif
            @if(@$prices['expenses'])
                @foreach(@$prices['expenses'] as $expense)
                    <tr>
                        <td>{{ @$expense['name'] }}</td>
                        <td class="text-right">{{ money(@$expense['subtotal'], $currency) }}</td>
                        <td class="text-right bold">{{ money(@$expense['total'], $currency) }}</td>
                    </tr>
                @endforeach
            @endif
        </table>
        <span>
            <small class="fs-10 lh-1-1 m-t-5 text-muted italic" style="display: block">
                <span>*Valor previsto. {{ trans('account/shipments.budget.exceptions-info') }}</span>
            </small>
        </span>
    @endif


</div>
