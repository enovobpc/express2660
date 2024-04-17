<div class="row m-b-15">
    <div class="col-sm-3">
        <h4 style="margin: 0 0 15px;color: #004897;">@trans('Opções Faturação')</h4>
        <div class="form-group form-group-sm m-b-10">
            <label class="col-sm-5 control-label p-r-0">
                @trans('Data Faturação')
                {!! tip(__('Data/Mês em que o envio será faturado.')) !!}
            </label>
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('billing_date', null, ['class' => 'form-control input-sm datepicker']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('fuel_tax', __('Taxa Combustivel'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('fuel_tax', null, ['class' => 'form-control trigger-price input-sm decimal']) }}
                    <div class="input-group-addon">%</div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('cost_fuel_tax', __('Taxa Combustivel (Custo)'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('cost_fuel_tax', null, ['class' => 'form-control trigger-price input-sm decimal']) }}
                    <div class="input-group-addon">%</div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('vat_rate_id', __('Taxa IVA'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                {{ Form::select('vat_rate_id', ['' => 'Automático'] + $vatTaxes, null, ['class' => 'form-control select2 trigger-price']) }}
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('payment_method', __('Cond. Pagamento'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                {{ Form::select('payment_method', ['' => __('Automático')] + $paymentConditions, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('billing_item', __('Razão IVA'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                {{ Form::text('billing_item', null, ['class' => 'form-control', 'readonly']) }}
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('billing_item_id', __('Artigo Faturação'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                {{ Form::select('billing_item_id', ['' =>__('Automático')], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div style="display:none">
            {{ Form::text('vat_rate') }}
            {{ Form::text('billing_pickup_zone') }}
            {{ Form::text('billing_zone') }}
            {{ Form::text('billing_subtotal') }}
            {{ Form::text('billing_vat') }}
            {{ Form::text('billing_total') }}
            {{ Form::text('cost_shipping_base_price') }}
            {{ Form::text('cost_expenses_price') }}
            {{ Form::text('cost_billing_subtotal') }}
            {{ Form::text('cost_billing_vat') }}
            {{ Form::text('cost_billing_total') }}
            {{ Form::text('cost_billing_zone') }}
            {{ Form::text('extra_weight') }}
            {{ Form::text('taxable_weight') }}
            {{ Form::text('provider_taxable_weight') }}
            {{ Form::text('has_sku') }}
        </div>
    </div>
    <div class="col-sm-3" style="border-left: 1px solid #ccc; border-right: 1px solid #ccc;">
        <h4 style="margin: 0 0 15px;color: #004897;">@trans('Preços Venda')</h4>
        <div class="form-group form-group-sm m-b-10">
            <label class="col-sm-5 control-label p-r-0">
                @trans('Zona Faturação')
                {!! tip(__('Zona de faturação utilizada para cálculo do preço')) !!}
            </label>
            <div class="col-sm-6 p-l-0 m-l-3">
                {{ Form::text('zone', null, ['class' => 'form-control input-sm text-uppercase', 'disabled']) }}
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            <label class="col-sm-5 control-label p-r-0">
                @trans('Preço Base')
                {!! tip(__('Preço tabelado')) !!}
            </label>
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('base_price', null, ['class' => 'form-control input-sm decimal', 'disabled']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            <label class="col-sm-5 control-label p-r-0">
                @trans('Preço Uni. Adic')
                {!! tip(__('Preço total dos KG, VOL adicionais ou KM percorridos')) !!}
                <small class="italic" style="font-weight: normal;"><span class="extra-weight">{{ $shipment->extra_weight ? $shipment->extra_weight : 0 }}</span> adicional</small>
            </label>
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('extra_price', number($shipment->shipping_price - $shipment->shipping_base_price), ['class' => 'form-control input-sm decimal', 'disabled']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('prv_shipping_price', __('Total Transporte'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('prv_shipping_price', $shipment->shipping_price, ['class' => 'form-control input-sm decimal bold blue', 'readonly']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
        <hr style="margin: 15px 0"/>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('fuel_price', __('Taxa Combustível'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('fuel_price', null, ['class' => 'form-control input-sm decimal', 'readonly']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('expenses_price', __('Taxas Adicionais'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('expenses_price', null, ['class' => 'form-control input-sm decimal', 'disabled']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('expenses_sum', __('Total Taxas'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('expenses_sum', number($shipment->expenses_price + $shipment->fuel_price), ['class' => 'form-control input-sm decimal bold blue', 'disabled']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <h4 style="margin: 0 0 15px;color: #004897;">@trans('Preços Compra')</h4>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('cost_shipping_price', __('Transporte'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('cost_shipping_price', null, ['class' => 'form-control input-sm decimal', 'disabled']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>

        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('cost_fuel_price', __('Taxa Combustível'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('cost_fuel_price', null, ['class' => 'form-control input-sm decimal', 'readonly']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>

        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('cost_expenses_price', __('Taxas Adicionais'), ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-6 p-l-0 m-l-3">
                <div class="input-group">
                    {{ Form::text('cost_expenses_price', null, ['class' => 'form-control input-sm decimal', 'disabled']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
        <hr/>
        <h4 style="margin: 0 0 15px;color: #004897;">@trans('Comissões')</h4>
        <table class="table table-condensed m-0" style="margin-top: -10px;">
            <tr>
                <td style="border-top: 0">@trans('Motorista')</td>
                <td style="border-top: 0" class="w-100px">@trans('Valor')</td>
            </tr>
            <tr class="input-sm">
                <td>
                    {{ Form::select('x', [''=>''], '', ['class' => 'form-control select2']) }}
                </td>
                <td>
                    <div class="input-group">
                        {{ Form::text('total_comission_1', null, ['class' => 'form-control input-sm decimal']) }}
                        <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                    </div>
                </td>
            </tr>
            <tr class="input-sm">
                <td>
                    {{ Form::select('x', [''=>''], '', ['class' => 'form-control select2']) }}
                </td>
                <td>
                    <div class="input-group">
                        {{ Form::text('total_comission_1', null, ['class' => 'form-control input-sm decimal']) }}
                        <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                    </div>
                </td>
            </tr>
            <tr class="input-sm">
                <td>
                    {{ Form::select('x', [''=>''], '', ['class' => 'form-control select2']) }}
                </td>
                <td>
                    <div class="input-group">
                        {{ Form::text('total_comission_1', null, ['class' => 'form-control input-sm decimal']) }}
                        <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-sm-3" style="border-left: 1px solid #ccc;">
        <div class="row row-5">
            <h4 style="margin: 0 0 15px;color: #004897;">@trans('Resumo Ganhos')</h4>
            <div class="col-sm-5">
                <h4 class="m-0">
                    <small>@trans('Subtotal')</small><br/>
                    <span class="billing-subtotal">{{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}</span>
                </h4>
                <h4 class="m-0">
                    <small>@trans('IVA') (<span class="billing-vat-rate">{{ $shipment->vat_rate }}</span>%)</small><br/>
                    <span class="billing-vat">{{ money($shipment->billing_vat, Setting::get('app_currency')) }}</span>
                </h4>
            </div>
            <div class="col-sm-7">
                <h2 class="m-0">
                    <small>@trans('Total')</small><br/>
                    <span class="billing-total">{{ money($shipment->billing_total, Setting::get('app_currency')) }}</span>
                </h2>
            </div>
        </div>
        <hr style="margin: 10px 0"/>
        <div class="row row-5">
            <h4 style="margin: 0 0 15px;color: #004897;">@trans('Resumo Custos')</h4>
            <div class="col-sm-5">
                <h4 class="m-0">
                    <small>@trans('Subtotal')</small><br/>
                    <span class="cost-billing-subtotal">{{ money($shipment->cost_billing_subtotal, Setting::get('app_currency')) }}</span>
                </h4>
                <h4 class="m-0">
                    <small>@trans('IVA')</small><br/>
                    <span class="cost-billing-vat">{{ money($shipment->cost_billing_vat, Setting::get('app_currency')) }}</span>
                </h4>
            </div>
            <div class="col-sm-7">
                <h2 class="m-0">
                    <small>@trans('Total')</small><br/>
                    <span class="cost-billing-total">{{ money($shipment->cost_billing_total, Setting::get('app_currency')) }}</span>
                </h2>
            </div>
        </div>
        <hr style="margin: 10px 0"/>
        <div class="row row-5">
            <div class="col-sm-7">
                <h2 class="m-0 text-green" style="margin-top: -10px">
                    <small>@trans('Resumo Ganhos')</small><br/>
                    <span class="billing-balance">{{ money($shipment->gain_money, Setting::get('app_currency')) }}</span>
                </h2>
            </div>
        </div>
    </div>
</div>