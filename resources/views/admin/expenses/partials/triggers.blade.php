{{--<div class="row row-5">
    <div class="col-sm-12 trigger_services">
        <div class="form-group">
            {{ Form::label('trigger_services[]', 'Aplicar taxa apenas aos serviços') }}
            {!! tip('Atenção: o disparo automático por serviço não se aplica a todos os tipos de encargos.') !!}
            {{ Form::select('trigger_services[]', $servicesList, null, ['class' => 'form-control select2', 'multiple']) }}
        </div>
    </div>
</div>--}}
{{--<div class="row row-5">
    <div class="col-sm-3 trigger_value" style="{{ in_array($shippingExpense->type, ['weight']) ?: 'display: none' }}">
        <div class="form-group">
            {{ Form::label('trigger_value', 'Peso de acima') }} {!! tip('ativa o encargo após o valor máximo definido.') !!}
            <div class="input-group">
                {{ Form::text('trigger_value', null, ['class' => 'form-control decimal']) }}
                <div class="input-group-addon">kg</div>
            </div>
        </div>
    </div>
</div>--}}

<div class="row">
    <div class="col-sm-6">
        <h5 class=" text-blue text-uppercase m-t-0">Ativar esta taxa automáticamente quando...</h5>
        <table id="table-custom" class="table table-condensed m-b-0">
            <tbody>
            @foreach($shippingExpense->trigger_fields as $key => $value)
                <tr>
                    <td class="input-sm w-220px">
                        {{ Form::select('trigger_fields[]', ['' => ''] + $triggerVariables,  @$shippingExpense->trigger_fields[$key], ['class' => 'form-control select2 trigger-fields']) }}
                    </td>
                    <td class="input-sm w-80px">
                        {{ Form::select('trigger_operators[]', ['' => ''] + $triggerOperators, @$shippingExpense->trigger_operators[$key], ['class' => 'form-control select2 trigger-operators w-150px']) }}
                    </td>
                    <td class="input-sm trigger-values">
                        @if(@$shippingExpense->trigger_fields[$key] == 'weekday')
                            @include('admin.expenses.partials.input_weekdays')
                        @elseif(@$shippingExpense->trigger_fields[$key] == 'start_hour' || @$shippingExpense->trigger_fields[$key] == 'end_hour')
                            @include('admin.expenses.partials.input_hours')
                        @elseif(@$shippingExpense->trigger_fields[$key] == 'service_id')
                            @include('admin.expenses.partials.input_services')
                        @elseif(@$shippingExpense->trigger_fields[$key] == 'status_id')
                            @include('admin.expenses.partials.input_status')
                        @elseif(@$shippingExpense->trigger_fields[$key] == 'cod')
                            @include('admin.expenses.partials.input_cod')
                        @elseif(@$shippingExpense->trigger_fields[$key] == 'zone' || @$shippingExpense->trigger_fields[$key] == 'origin_zone')
                            @include('admin.expenses.partials.input_zones')
                        @elseif(@$shippingExpense->trigger_fields[$key] == 'remote_zone')
                            @include('admin.expenses.partials.input_remote_zones')
                        @else
                            @include('admin.expenses.partials.input_decimal')
                        @endif
                    </td>
                    <td class="input-sm trigger-join">
                        {{ Form::select('trigger_joins[]', [''=>'','and' => 'e', 'or' => 'ou'], @$shippingExpense->trigger_joins[$key], ['class' => 'form-control select2']) }}
                    </td>
                </tr>
            @endforeach
            @for($i = 0 ; $i <= 4 ; $i++)
                <tr>
                    <td class="input-sm w-220px">
                        {{ Form::select('trigger_fields[]', ['' => ''] + $triggerVariables,  '', ['class' => 'form-control select2 trigger-fields']) }}
                    </td>
                    <td class="input-sm w-80px">
                        {{ Form::select('trigger_operators[]', ['' => ''] + $triggerOperators, '', ['class' => 'form-control select2 trigger-operators w-150px']) }}
                    </td>
                    <td class="input-sm trigger-values">
                        {{ Form::text('trigger_values[]', '', ['class' => 'form-control input-sm']) }}
                    </td>
                    <td class="input-sm trigger-join" style="display: none">
                        {{ Form::select('trigger_joins[]', [''=>'', 'and' => 'e', 'or' => 'ou'], '', ['class' => 'form-control select2']) }}
                    </td>
                </tr>
            @endfor
            </tbody>
        </table>
    </div>
    <div class="col-sm-6">
        <div class="row row-5">
            <div class="col-sm-4">
                <div class="form-group">
                    {{ Form::label('short_name', 'Designação Curta') }}
                    {{ Form::text('short_name', substr(@$shippingExpense->short_name, 0, 15), ['class' => 'form-control', 'maxlength' => 15]) }}
                </div>
            </div>
            <div class="col-sm-8">
                <div class="form-group">
                    {{ Form::label('billing_item_id', 'Artigo Faturação') }}
                    {{ Form::select('billing_item_id', ['' => ''] + $billingItems, null, ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="col-sm-12">
                <label>Configuar menus de escolha rápida</label>
                <table id="table-custom" class="table table-condensed m-0">
                    <tr>
                        <th class="bg-gray"></th>
                        <th class="bg-gray">Ativo</th>
                        <th class="w-150px bg-gray">Tipo caixa</th>
                        <th class="w-80px bg-gray">Texto Info</th>
                    </tr>
                    <tr>
                        <td class="vertical-align-middle">
                            Janela Envios
                        </td>
                        <td class="input-sm">
                            <label style="padding-left: 5px; padding-top: 5px">
                                {{ Form::checkbox('complementar_service', 1) }}
                            </label>
                        </td>
                        <td class="input-sm">
                            {{ Form::select('form_type_shipments', $formTypes, null, ['class' => 'form-control select2']) }}
                        </td>
                        <td>
                            {{ Form::text('addon_text', $shippingExpense->exists ? substr($shippingExpense->addon_text, 0, 6) : null, ['class' => 'form-control input-sm', 'placeholder' => 'Ex: min', 'maxlength' => 6]) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="vertical-align-middle">
                            Janela Recolhas
                        </td>
                        <td class="input-sm">
                            <label style="padding-left: 5px; padding-top: 5px">
                                {{ Form::checkbox('collection_complementar_service', 1) }}
                            </label>
                        </td>
                        <td class="input-sm">
                            {{ Form::select('form_type_pickups', $formTypes, null, ['class' => 'form-control select2']) }}
                        </td>
                        <td>
                            {{ Form::text('addon_text_pickups', $shippingExpense->exists ? substr($shippingExpense->addon_text_pickups, 0, 6) : null, ['class' => 'form-control input-sm', 'placeholder' => 'Ex: min', 'maxlength' => 6]) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="vertical-align-middle">
                            Área Cliente
                        </td>
                        <td class="input-sm">
                            <label style="padding-left: 5px; padding-top: 5px">
                                {{ Form::checkbox('account_complementar_service', 1) }}
                            </label>
                        </td>
                        <td class="input-sm">
                            {{ Form::select('form_type_account', $formTypes, null, ['class' => 'form-control select2']) }}
                        </td>
                        <td>
                            {{ Form::text('addon_text_account', $shippingExpense->exists ? substr($shippingExpense->addon_text_account, 0, 6) : null, ['class' => 'form-control input-sm', 'placeholder' => 'Ex: min', 'maxlength' => 6]) }}
                        </td>
                    </tr>
                </table>
                <div class="checkbox m-b-0" style="border-top: 1px solid #ccc; padding-top: 10px">
                    <label style="padding-left: 5px">
                        {{ Form::checkbox('customer_customization', 1) }}
                        Permitir personalizar o preço para cada cliente
                    </label>
                    {!! tip('Se ativar esta opção, será possível personalizar o preço da taxa na ficha de cada cliente.') !!}
                </div>
            </div>
        </div>
    </div>
</div>