{{ Form::model($model, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-8">
            <div class="form-group form-group-sm m-b-5">
                {{ Form::label('provider_id', 'Designação do Modelo', ['class' => 'col-sm-12 text-left']) }}
                <div class="col-sm-12">
                    {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
            <hr style="margin: 10px 0"/>
            <div class="row row-10">
                <div class="col-sm-5" style="padding-left: 0;">
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('provider_id', 'Transportador', ['class' => 'col-sm-4 control-label p-0']) }}
                        <div class="col-sm-8">
                            {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('reference', 'Referência', ['class' => 'col-sm-4 control-label p-0']) }}
                        <div class="col-sm-8">
                            {{ Form::text('reference', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('agent_id', 'Agente', ['class' => 'col-sm-4 control-label p-0']) }}
                        <div class="col-sm-8">
                            {{ Form::select('agent_id', ['' => ''] + $agents, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3" style="padding-left: 0;">
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('goods_type_id', 'Tipo Carga', ['class' => 'col-sm-5 control-label p-0']) }}
                        <div class="col-sm-7">
                            {{ Form::select('goods_type_id', ['' => ''] + $goodsTypes, null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>

                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('currency', 'Moeda', ['class' => 'col-sm-5 control-label p-r-0']) }}
                        <div class="col-sm-7">
                            {{ Form::select('currency', trans('admin/air_waybills.currency'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('charge_code', 'Cód. Cobr.', ['class' => 'col-sm-5 control-label p-0']) }}
                        <div class="col-sm-7">
                            {{ Form::select('charge_code', ['' => ''] + trans('admin/air_waybills.charge-codes'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4" style="padding-left: 0;">
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('value_insurance', 'Valor Seguro', ['class' => 'col-sm-5 control-label p-r-0']) }}
                        <div class="col-sm-7">
                            <div class="input-group">
                                {{ Form::text('value_insurance', null, ['class' => 'form-control']) }}
                                <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('value_for_customs', 'V. Alfândega', ['class' => 'col-sm-5 control-label p-r-0']) }}
                        <div class="col-sm-7">
                            <div class="input-group">
                                {{ Form::text('value_for_customs', null, ['class' => 'form-control']) }}
                                <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('value_for_carriage', 'V. Transporte', ['class' => 'col-sm-5 control-label p-r-0']) }}
                        <div class="col-sm-7">
                            <div class="input-group">
                                {{ Form::text('value_for_carriage', null, ['class' => 'form-control']) }}
                                <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr style="margin: 10px 0"/>

            <table class="table table-condensed table-sender m-t-10 m-b-0 no-border">
                <tr class="bg-gray-light">
                    <th class="w-65px"></th>
                    <th class="text-uppercase">Expedidor</th>
                    <th class="text-uppercase">Consignatário</th>
                    <th class="text-uppercase">Emissor</th>
                </tr>
                <tr>
                    <th class="text-right">Nome</th>
                    <td>
                        {{ Form::hidden('customer_id') }}
                        {{ Form::text('sender_name', null, ['class' => 'form-control input-sm search-customer', 'autocomplete'=> 'nofill']) }}
                    </td>
                    <td>
                        {{ Form::hidden('consignee_id') }}
                        {{ Form::text('consignee_name', null, ['class' => 'form-control input-sm search-consignee', 'autocomplete'=> 'nofill']) }}
                    </td>
                    <td>
                        {{ Form::text('issuer_name', null, ['class' => 'form-control input-sm', 'autocomplete'=> 'nofill']) }}
                    </td>
                </tr>
                <tr>
                    <th class="text-right">NIF</th>
                    <td>{{ Form::text('sender_vat', null, ['class' => 'form-control input-sm']) }}</td>
                    <td>{{ Form::text('consignee_vat', null, ['class' => 'form-control input-sm']) }}</td>
                    <td rowspan="2">
                        {{ Form::textarea('issuer_address', null, ['class' => 'form-control', 'rows' => 3, 'style' => 'height: 108px;']) }}
                    </td>
                </tr>
                <tr>
                    <th class="text-right">Endereço</th>
                    <td>{{ Form::textarea('sender_address', null, ['class' => 'form-control', 'rows' => 3]) }}</td>
                    <td>{{ Form::textarea('consignee_address', null, ['class' => 'form-control', 'rows' => 3]) }}</td>
                    <td rowspan="2"></td>
                </tr>
            </table>

            <hr/>

            <div class="row">
                <div class="col-sm-7">
                    <h4 class="m-t-0 bold">Aeroportos</h4>
                    <div class="row row-5">
                        <div class="col-sm-6">
                            <div class="form-group form-group-sm m-b-5">
                                {{ Form::label('source_airport', 'Origem', ['class' => 'col-sm-3 control-label p-0']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('source_airport', $model->exists ? [$model->sourceAirport->code => $model->sourceAirport->airport] : ['' => ''], null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-group-sm m-b-5">
                                {{ Form::label('recipient_airport', 'Dest.', ['class' => 'col-sm-3 control-label p-0']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('recipient_airport', $model->exists ? [$model->recipientAirport->code => $model->recipientAirport->airport] : ['' => ''], null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="bold">Pontos de Escala</h4>
                    <table class="table table-condensed m-0 table-flight-scales">
                        <tr class="bg-gray-light">
                            <th class="w-50">Aeroporto</th>
                            <th class="w-50">Transportador</th>
                            <th class="w-1"></th>
                        </tr>
                        @for ($i = 0 ; $i<=5; $i++)
                            <tr style="{{ $i == 0 || isset($model->flight_scales[$i]) ? '' : 'display: none' }}">
                                <td style="padding-left: 0">
                                    <div class="form-group-sm m-0">
                                        {{ Form::select('flight_scales['.$i.'][airport]', ['' => ''] + $scaleAirports, null, ['class' => 'form-control search-airport select2']) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group-sm m-0">
                                        {{ Form::select('flight_scales['.$i.'][provider]', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
                                    </div>
                                </td>
                                <td>
                                    <a href="#" class="text-red remove-flight-scale">
                                        <i class="fas fa-times m-t-8"></i>
                                    </a>
                                </td>
                            </tr>
                        @endfor
                    </table>
                    <button type="button" class="btn btn-xs btn-default btn-add-flight-scale"><i class="fas fa-plus"></i> Adicionar Escala de Vôo</button>
                </div>
                <div class="col-sm-5">
                    <h4 class="m-t-0 bold">Encargos</h4>
                    <table class="table table-condensed m-0 table-expenses">
                        <tr class="bg-gray-light">
                            <th class="w-200px">Encargo</th>
                            <th class="w-130px">Preço</th>
                            <th class="w-1"></th>
                        </tr>
                        <?php
                        $rowsVisible = 3;

                        if($model->exists) {
                            $totalGoods  = count($model->expenses);
                            $rowsVisible = $totalGoods > $rowsVisible ? $totalGoods : $rowsVisible;
                        }
                        ?>
                        @for($i=0 ; $i<20; $i++)
                            <tr style="{{ $i >= $rowsVisible ? 'display:none' : '' }}">
                                <td style="padding-left: 0; width: 200px">
                                    {{ Form::select('expenses['.$i.'][expense]', ['' => ''] + $expenses, null, ['class' => 'form-control input-sm select2', 'style' => 'width:200px !important']) }}
                                </td>
                                <td>
                                    <div class="input-group">
                                        {{ Form::text('expenses['.$i.'][price]', null, ['class' => 'form-control input-sm weight']) }}
                                        <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="#" class="text-red remove-expenses">
                                        <i class="fas fa-times m-t-8"></i>
                                    </a>
                                </td>
                            </tr>
                        @endfor
                    </table>

                    <button type="button" class="btn btn-xs btn-default btn-add-expenses"><i class="fas fa-plus"></i> Adicionar Encargo</button>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group form-group-sm m-b-5" style="margin-left: 0">
                {{ Form::label('customs_status', 'Estatuto Aduaneiro', ['class' => 'p-r-0', 'style' => 'line-height: 14px;']) }}
                <div class="clearfix"></div>
                @foreach(trans('admin/air_waybills.customs_status') as $key => $item)
                    <label style="width: 40px">{{ Form::radio('customs_status', $key, null, ['required']) }} {{ $item }}</label>
                @endforeach
            </div>
            <div class="col-sm-12">
                <div class="form-group m-b-5">
                    {{ Form::label('adicional_info', 'Informações Adicionais', ['class' => 'p-r-0']) }}
                    {{ Form::textarea('adicional_info', null, ['class' => 'form-control', 'rows' => 4]) }}
                </div>
                <div class="form-group m-b-5">
                    {{ Form::label('handling_info', 'Informações de Manuseamento', ['class' => 'p-r-0']) }}
                    {{ Form::textarea('handling_info', null, ['class' => 'form-control', 'rows' => 4]) }}
                </div>
                <div class="form-group m-b-5">
                    {{ Form::label('nature_quantity_info', 'Natureza e Quantidade', ['class' => 'p-r-0']) }}
                    {{ Form::textarea('nature_quantity_info', null, ['class' => 'form-control', 'rows' => 4]) }}
                </div>
                <div class="form-group m-b-5">
                    {{ Form::label('accounting_info', 'Informação para Contabilidade') }}
                    {{ Form::textarea('accounting_info', null, ['class' => 'form-control', 'rows' => 4]) }}
                </div>
                <div class="form-group m-b-5">
                    {{ Form::label('obs', 'Observações') }}
                    {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker());

    $("select[name=source_airport], select[name=recipient_airport], .search-airport").select2({
        ajax: {
            url: "{{ route('admin.air-waybills.search.airport') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=customer_id] option').remove()
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });


    $('.search-customer').autocomplete({
        serviceUrl: '{{ route('admin.air-waybills.search.customer') }}',
        onSearchStart: function () {
            $('[name="customer_id"]').val('');
        },
        onSelect: function (suggestion) {
            $('[name="customer_id"]').val(suggestion.data).trigger('change');
        },
    });

    $('.search-consignee').autocomplete({
        serviceUrl: '{{ route('admin.air-waybills.search.customer') }}',
        onSearchStart: function () {
            $('[name="consignee_id"]').val('');
        },
        onSelect: function (suggestion) {
            $('[name="consignee_id"]').val(suggestion.data).trigger('change');
        },
    });

    /**
     * Change customer
     */
    $('[name=customer_id]').on('change', function () {
        var customerId = $(this).val();

        $('input[name=customer_id]').val(customerId);

        $.post("{{ route('admin.air-waybills.get.customer') }}", {id: customerId}, function (data) {

            $('[name=sender_name]').val(data.name);
            $('[name=sender_address]').val(data.address);
            $('[name=sender_vat]').val(data.vat);

        }).error(function () {
            $('.form-waybill .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function() {
            $('label[for=sender_address] .fa-spin').addClass('hide');
        })
    });

    /**
     * Change consignee
     */
    $('[name=consignee_id]').on('change', function () {
        var consigneeId = $(this).val();

        $('input[name=consignee_id]').val(consigneeId);

        $.post("{{ route('admin.air-waybills.get.customer') }}", {id: consigneeId}, function (data) {

            $('[name=consignee_name]').val(data.name);
            $('[name=consignee_address]').val(data.address);
            $('[name=consignee_vat]').val(data.vat);

        }).error(function () {
            $('.form-waybill .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function() {
            $('label[for=sender_address] .fa-spin').addClass('hide');
        })
    });

    /**
     * Get provider data
     */
    $('[name=provider_id]').on('change', function () {

        $.post("{{ route('admin.air-waybills.get.provider') }}", {id: $(this).val()}, function (data) {
            $('[name=issuer_id]').val(data.id);
            $('[name=issuer_name]').val(data.name);
            $('[name=issuer_address]').val(data.address);
            $('[name="awb[1]"]').val(data.iata_no).trigger('change');

        }).error(function () {
            $('.form-waybill .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function() {
            $('label[for=recipient_address] i').addClass('hide');
        })
    });

    /**
     * Add flight scale
     */
    $('.btn-add-flight-scale').on('click', function(){
        $('.table-flight-scales').find('tr:hidden:first').show();

        if($('.table-flight-scales').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.remove-flight-scale').on('click', function(){

        if($('.table-flight-scales').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.table-flight-scales').append($tr);

            if ($('.table-flight-scales').find("tr:hidden").length == 0) {
                $('.btn-add-flight-scale').hide();
            } else {
                $('.btn-add-flight-scale').show();
            }
        }
    });


    /**
     * Add expenses
     */
    $('.btn-add-expenses').on('click', function(){
        $('.table-expenses').find('tr:hidden:first').show();

        if($('.table-expenses').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.remove-expenses').on('click', function(){

        if($('.table-expenses').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.find('input').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.table-expenses').append($tr);

            if ($('.table-expenses').find("tr:hidden").length == 0) {
                $('.btn-add-expenses').hide();
            } else {
                $('.btn-add-expenses').show();
            }
        }
    });

    $('.rate-charge, .chargeable-weight').on('change', function(){
        $tr = $(this).closest('tr');

        var chargeableWeight = parseFloat($tr.find('.chargeable-weight').val());
        var rateCharge       = parseFloat($tr.find('.rate-charge').val());

        total = chargeableWeight * rateCharge
        $tr.find('.total').val(total.toFixed(2));
    });


</script>

<style>
    .table-sender td {
        padding: 2px !important;
    }
    .select2-container .select2-selection--single {
        padding: 4px 10px;
        height: 30px;
    }

    .table-expenses .select2.select2-container {
        width: 170px !important;
    }
</style>
