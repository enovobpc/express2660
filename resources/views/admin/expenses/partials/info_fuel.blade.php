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
            <div class="col-sm-6">
                <div class="form-group is-required">
                    {{ Form::label('start_at', 'Início') }}
                    <div class="input-group">
                        {{ Form::text('start_at', null, ['class' => 'form-control datepicker', 'required']) }}
                        <div class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group is-required">
                    {{ Form::label('end_at', 'Fim') }}
                    <div class="input-group">
                        {{ Form::text('end_at', null, ['class' => 'form-control datepicker', 'required']) }}
                        <div class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-5">
    <div class="col-xs-12">
        <h5 class=" text-blue text-uppercase m-t-15">Regras de cálculo de preço</h5>
        <table id="table-custom" class="table table-striped table-dashed table-hover table-condensed table-expense-prices m-b-0">
            <thead>
                <tr>
                    <th class="w-140px bg-gray">Se o Serviço é</th>
                    <th class="w-140px bg-gray">e Zona Faturação é</th>
                    <th class="w-110px bg-gray">o preço é</th>
                    <th class="bg-gray">aplica cálculo sobre</th>
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
                    <th class="w-75px bg-gray">
                        <span data-toggle="tooltip" title="Força a taxa de IVA para esta regra.">
                            Taxa IVA
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
            @foreach($shippingExpense->zones_arr as $key => $value)
                <tr>
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
                                    {{ Form::select('unity_arr[]', ['percent' => '%'], @$shippingExpense->unity_arr[$key], ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="input-sm">
                        {{ Form::select('trigger_arr[]', ['total_price' => 'Preço total', 'base_price' => 'Preço envio'],  @$shippingExpense->trigger_arr[$key], ['class' => 'form-control select2']) }}
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
            @for($i = 0 ; $i <= (4 - count($shippingExpense->zones_arr)) ; $i++)
                <tr>
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
                                    {{ Form::select('unity_arr[]', ['percent' => '%'], 'euro', ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="input-sm">
                        {{ Form::select('trigger_arr[]', ['total_price' => 'Preço total', 'base_price' => 'Preço envio'], '', ['class' => 'form-control select2']) }}
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