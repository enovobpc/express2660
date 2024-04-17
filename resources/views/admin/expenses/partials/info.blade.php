<div class="row" style="    background: #f9f9f9;
    padding-top: 15px;
    margin-top: -15px;
    border-bottom: 1px solid #ddd;">
    <div class="col-sm-8">
        <div class="row row-5">
            <div class="col-sm-2">
                <div class="form-group is-required">
                    {{ Form::label('code', 'Código') }}
                    {{ Form::text('code', null, ['class' => 'form-control uppercase nospace', 'maxlength' => 5]) }}
                </div>
            </div>
            <div class="col-sm-5">
                <div class="form-group is-required">
                    {{ Form::label('internal_name', 'Designação Interna') }} {!! tip('Esta é a designação que vai ser apresentada no sistema internamente.') !!}
                    {{ Form::text('internal_name', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
            <div class="col-sm-5">
                <div class="form-group is-required">
                    {{ Form::label('name', 'Designação para Clientes') }}  {!! tip('Esta será a designação que os clientes vão visualizar.') !!}
                    {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="row row-5">
            <div class="col-sm-8">
                <div class="form-group is-required">
                    {{ Form::label('type', 'Tipo taxa') }}
                    {{ Form::select('type', ['' => ''] + trans('admin/expenses.types'),null, ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('vat_rate_global', 'Forçar IVA') }}
                    {!! tip('Taxa de IVA global a aplicar nesta taxa adicional. A opção AUTO (recomendado) permite que seja o sistema a decidir a taxa a aplicar.') !!}
                    {{ Form::select('vat_rate_global', $vatRates, null, ['class' => 'form-control select2']) }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-5">
    <div class="col-xs-12">
        {{--<div class="pull-right m-t-8 range-unity" style="{{ $shippingExpense->has_range_prices ? '' : 'display:none' }}">
            <span>
                Unidade {{ Form::select('range_unity', ['kg' => 'Peso (Kg)', 'cm' => 'Medias (cm)', 'km' => 'Distância (Km)'], null, ['class' => 'custom-select']) }}
            </span>
        </div>
        <div class="pull-right m-t-12 m-r-20">
            <label style="font-weight: normal">
                {{ Form::checkbox('has_range_prices', 1, null, ['disabled']) }}
                Ativar preços por escalão {!! tip('Permite-lhe definir os preços da taxa por escalões em vez de um preço único (Por exemplo: preço De 0-10kg, de 10-20kg, etc.). Os escalões podem ser peso, medidas ou outro. ') !!}
            </label>
        </div>--}}
        <h5 class="text-blue text-uppercase m-t-15 pull-left">Regras de cálculo de preço</h5>
        <div class="clearfix"></div>
        <table id="table-custom" class="table table-striped table-dashed table-hover table-condensed table-expense-prices m-b-0">
            <thead>
                <tr>
                    <th class="w-60px bg-gray range-unity" style="{{ $shippingExpense->has_range_prices ? '' : 'display:none' }}">Até (<span class="range-unity-mesure">{{ $shippingExpense->range_unity }}</span>)</th>
                    <th class="w-140px bg-gray">Se o Serviço é</th>
                    <th class="w-140px bg-gray">e Zona Faturação é</th>
                    <th class="w-110px bg-gray">o preço é</th>
                    <th class="bg-gray">aplica cálculo </th>
                    <th class="bg-gray w-80px">a cada</th>
                    <th class="bg-gray w-120px">aplica desconto</th>
                    <th class="w-80px bg-gray">
                        <span data-toggle="tooltip" data-html="true" title="O valor definido será somado ao valor calculado da taxa. <br/>Preço = Preço Base + Preço Calculado">
                            Preço Base
                        </span>
                    </th>
                    <th class="w-75px bg-gray">
                        <span data-toggle="tooltip" title="Se o preço da taxa não atingir o valor minimo, é cobrado o valor aqui definido.">
                            Preço Min
                        </span>
                    </th>
                    <th class="w-75px bg-gray">
                        <span data-toggle="tooltip" title="Se o preço da taxa ultrapassar o valor máximo, é cobrado o valor aqui definido.">
                            Preço Max
                        </span>
                    </th>
                    <th class="w-65px bg-gray">
                        <span data-toggle="tooltip" title="Força a taxa de IVA para esta regra.">
                            IVA
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
            @foreach($shippingExpense->zones_arr as $key => $value)
                <tr>
                    <td class="input-sm range-unity" style="{{ $shippingExpense->has_range_prices ? '' : 'display:none' }}">
                        {{ Form::text('ranges_arr[]', empty(@$shippingExpense->ranges_arr[$key]) ? '' : number(@$shippingExpense->ranges_arr[$key]), ['class' => 'form-control input-sm decimal']) }}
                    </td>
                    <td class="input-sm">
                        {{ Form::select('services_arr[]', ['' => '', 'qq' => 'Qualquer'] + $servicesList, @$shippingExpense->services_arr[$key], ['class' => 'form-control select2']) }}
                    </td>
                    <td class="input-sm">
                        {{ Form::select('zones_arr[]', ['' => '', 'qqz' => 'Qualquer zona'] + $billingZones, @$shippingExpense->zones_arr[$key], ['class' => 'form-control select2']) }}
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            <div class="row row-0">
                                <div class="col-xs-7">
                                    {{ Form::text('values_arr[]', number(@$shippingExpense->values_arr[$key]), ['class' => 'form-control input-sm decimal']) }}
                                </div>
                                <div class="col-xs-3" style="width: 40px">
                                    {{ Form::select('unity_arr[]', ['euro' => Setting::get('app_currency'), 'percent' => '%'], @$shippingExpense->unity_arr[$key], ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="input-sm">
                        {{ Form::select('trigger_arr[]', $trigger,  @$shippingExpense->trigger_arr[$key], ['class' => 'form-control select2']) }}

                        <div class="row row-5 m-t-5" style="display: {{ @$shippingExpense->trigger_arr[$key] != 'every' ? 'none' : 'block' }}">
                            <div class="col-xs-6">
                                {{ Form::text("every_arr[values][{$key}]", @$shippingExpense->every_arr['values'][$key], ['class' => 'form-control number input-sm']) }}
                            </div>
                            <div class="col-xs-6">
                                {{ Form::select('every_arr[fields][]', $everyFields,  @$shippingExpense->every_arr['fields'][$key], ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            {{ Form::text('discount_arr[everies][]', @$shippingExpense->discount_arr['everies'][$key] ? number(@$shippingExpense->discount_arr['everies'][$key]) : '', ['class' => 'form-control input-sm decimal']) }}
                        </div>
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            <div class="row row-0">
                                <div class="col-xs-7">
                                    {{ Form::text('discount_arr[values][]', @$shippingExpense->discount_arr['values'][$key] ? number(@$shippingExpense->discount_arr['values'][$key]) : '', ['class' => 'form-control input-sm decimal']) }}
                                </div>
                                <div class="col-xs-3" style="width: 45px">
                                    {{ Form::select('discount_arr[unities][]', ['euro' => Setting::get('app_currency'), 'percent' => '%'], @$shippingExpense->discount_arr['unities'][$key], ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            {{ Form::text('base_price_arr[]', @$shippingExpense->base_price_arr[$key] ? number(@$shippingExpense->base_price_arr[$key]) : '', ['class' => 'form-control input-sm decimal']) }}
                            <div class="input-group-addon">€</div>
                        </div>
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            {{ Form::text('min_price_arr[]', @$shippingExpense->min_price_arr[$key] ? number(@$shippingExpense->min_price_arr[$key]) : '', ['class' => 'form-control input-sm decimal']) }}
                            <div class="input-group-addon">€</div>
                        </div>
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            {{ Form::text('max_price_arr[]', @$shippingExpense->max_price_arr[$key] ? number(@$shippingExpense->max_price_arr[$key]) : '', ['class' => 'form-control input-sm decimal']) }}
                            <div class="input-group-addon">€</div>
                        </div>
                    </td>
                    <td class="input-sm">
                        {{ Form::select('vat_rate_arr[]', $vatRates, @$shippingExpense->vat_rate_arr[$key] ? @$shippingExpense->vat_rate_arr[$key] : '', ['class' => 'form-control input-sm select2']) }}
                    </td>
                </tr>
            @endforeach

            @if(empty($shippingExpense->zones_arr) || count($shippingExpense->zones_arr) <= 4)
            @for($i = 0; $i <= (4 - count($shippingExpense->zones_arr)); $i++)
                <tr>
                    <td class="range-unity" style="{{ $shippingExpense->has_range_prices ? '' : 'display:none' }}">
                        {{ Form::text('ranges_arr[]', '', ['class' => 'form-control input-sm decimal']) }}
                    </td>
                    <td class="input-sm">
                        {{ Form::select('services_arr[]', ['' => '', 'qq' => 'Qualquer'] + $servicesList, '', ['class' => 'form-control select2']) }}
                    </td>
                    <td class="input-sm">
                        {{ Form::select('zones_arr[]', ['' => '', 'qqz' => 'Qualquer zona'] + $billingZones,  '', ['class' => 'form-control select2']) }}
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            <div class="row row-0">
                                <div class="col-xs-7">
                                    {{ Form::text('values_arr[]', '', ['class' => 'form-control input-sm']) }}
                                </div>
                                <div class="col-xs-3" style="width: 40px">
                                    {{ Form::select('unity_arr[]', ['euro' => Setting::get('app_currency'), 'percent' => '%'], 'euro', ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="input-sm">
                        {{ Form::select('trigger_arr[]', $trigger, '', ['class' => 'form-control select2']) }}

                        <div class="row row-5 m-t-5" style="display: none">
                            <div class="col-xs-6">
                                {{ Form::text('every_arr[values][]', '', ['class' => 'form-control number input-sm']) }}
                            </div>
                            <div class="col-xs-6">
                                {{ Form::select('every_arr[fields][]', $everyFields,  'weight', ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            {{ Form::text('discount_arr[everies][]', '', ['class' => 'form-control input-sm decimal']) }}
                        </div>
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            <div class="row row-0">
                                <div class="col-xs-7">
                                    {{ Form::text('discount_arr[values][]', '', ['class' => 'form-control input-sm decimal']) }}
                                </div>
                                <div class="col-xs-3" style="width: 45px">
                                    {{ Form::select('discount_arr[unities][]', ['euro' => Setting::get('app_currency'), 'percent' => '%'], '', ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            {{ Form::text('base_price_arr[]', '', ['class' => 'form-control input-sm decimal']) }}
                            <div class="input-group-addon">€</div>
                        </div>
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            {{ Form::text('min_price_arr[]', '', ['class' => 'form-control input-sm decimal']) }}
                            <div class="input-group-addon">€</div>
                        </div>
                    </td>
                    <td class="input-sm">
                        <div class="input-group">
                            {{ Form::text('max_price_arr[]', '', ['class' => 'form-control input-sm decimal']) }}
                            <div class="input-group-addon">€</div>
                        </div>
                    </td>
                    <td class="input-sm">
                        {{ Form::select('vat_rate_arr[]', $vatRates, '', ['class' => 'form-control input-sm select2']) }}
                    </td>
                </tr>
            @endfor
            @endif
            </tbody>
        </table>
        <button type="button" class="btn btn-xs btn-default btn-add-line m-b-15"><i class="fas fa-plus"></i> Adicionar linha</button>
    </div>
</div>

<style>
    .custom-select {
        border-radius: 0;
        border: 1px solid #d2d6de;
        padding: 3px;
    }
</style>