<div class="row">
    <div class="col-sm-4">
        <div class="form-group form-group-sm m-b-5 is-required">
            {{ Form::label('provider_id', 'Transportador', ['class' => 'col-sm-3 control-label p-0']) }}
            <div class="col-sm-9">
                {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="form-group form-group-sm m-b-5 is-required">
            {{ Form::label('name', 'AWB Nº', ['class' => 'col-sm-3 control-label p-r-0']) }}
            <div class="col-sm-9">
                <div class="row row-5">
                    <div class="col-sm-3" style="padding-right: 0">
                        {{ Form::text('awb[1]', null, ['class' => 'form-control', 'required', 'maxlength' => 3]) }}
                    </div>
                    <div class="col-sm-1 text-center" style="padding: 3px 0 0 0;">-</div>
                    <div class="col-sm-4" style="padding-left: 0">
                        {{ Form::text('awb[2]', null, ['class' => 'form-control', 'required', 'maxlength' => 4]) }}
                    </div>
                    <div class="col-sm-4">
                        {{ Form::text('awb[3]', null, ['class' => 'form-control', 'required', 'maxlength' => 4]) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group form-group-sm m-b-5">
            {{ Form::label('title', 'Descrição', ['class' => 'col-sm-3 control-label p-r-0']) }}
            <div class="col-sm-9">
                {{ Form::text('title', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group form-group-sm m-b-5 is-required">
                    {{ Form::label('date', 'Data', ['class' => 'col-sm-3 control-label p-r-0']) }}
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fas fa-calendar"></i>
                            </span>
                            {{ Form::text('date', $waybill->exists ? $waybill->date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('reference', 'Referência', ['class' => 'col-sm-3 control-label p-l-0']) }}
                    <div class="col-sm-9">
                        {{ Form::text('reference', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5 is-required">
                    {{ Form::label('agent_id', 'Agente', ['class' => 'col-sm-3 control-label p-r-0']) }}
                    <div class="col-sm-9">
                        {{ Form::select('agent_id', ['' => ''] + $agents, null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
            </div>
            <div class="col-sm-4" style="padding-left: 0;">
                <div class="form-group form-group-sm m-b-5 is-required">
                    {{ Form::label('goods_type_id', 'Tipo Carga', ['class' => 'col-sm-5 control-label p-0']) }}
                    <div class="col-sm-7">
                        {{ Form::select('goods_type_id', ['' => ''] + $goodsTypes, null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>

                <div class="form-group form-group-sm m-b-5 is-required">
                    {{ Form::label('currency', 'Moeda', ['class' => 'col-sm-5 control-label p-r-0']) }}
                    <div class="col-sm-7">
                        {{ Form::select('currency', trans('admin/air_waybills.currency'), null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5 is-required">
                    {{ Form::label('charge_code', 'Cód. Cobrança', ['class' => 'col-sm-5 control-label p-0']) }}
                    <div class="col-sm-7">
                        {{ Form::select('charge_code', ['' => ''] + trans('admin/air_waybills.charge-codes'), null, ['class' => 'form-control select2', 'required']) }}
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
                    {{ Form::label('value_for_customs', 'Val. Alfândega', ['class' => 'col-sm-5 control-label p-r-0']) }}
                    <div class="col-sm-7">
                        <div class="input-group">
                            {{ Form::text('value_for_customs', null, ['class' => 'form-control']) }}
                            <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('value_for_carriage', 'Val. Transporte', ['class' => 'col-sm-5 control-label p-r-0']) }}
                    <div class="col-sm-7">
                        <div class="input-group">
                            {{ Form::text('value_for_carriage', null, ['class' => 'form-control']) }}
                            <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<table class="table table-condensed table-sender m-t-10 m-b-0 no-border">
    <tr class="bg-gray-light">
        <th class="w-85px"></th>
        <th class="text-uppercase">Expedidor</th>
        <th class="text-uppercase">Consignatário</th>
        <th class="text-uppercase">Emissor</th>
    </tr>
    <tr>
        <th class="text-right">Nome<sup class="text-red">*</sup></th>
        <td>
            {{ Form::hidden('customer_id') }}
            {{ Form::text('sender_name', null, ['class' => 'form-control input-sm search-custome', 'required', 'autocomplete'=> 'nofill']) }}
        </td>
        <td>
            {{ Form::hidden('consignee_id') }}
            {{ Form::text('consignee_name', null, ['class' => 'form-control input-sm search-consignee', 'required', 'autocomplete'=> 'nofill']) }}
        </td>
        <td>
            {{ Form::text('issuer_name', null, ['class' => 'form-control input-sm', 'required', 'autocomplete'=> 'nofill']) }}
        </td>
    </tr>
    <tr>
        <th class="text-right">NIF</th>
        <td>{{ Form::text('sender_vat', null, ['class' => 'form-control input-sm']) }}</td>
        <td>{{ Form::text('consignee_vat', null, ['class' => 'form-control input-sm']) }}</td>
        <td rowspan="2">
            {{ Form::textarea('issuer_address', null, ['class' => 'form-control', 'rows' => 3, 'required', 'style' => 'height: 108px;']) }}
        </td>
    </tr>
    <tr>
        <th class="text-right">Endereço<sup class="text-red">*</sup></th>
        <td>{{ Form::textarea('sender_address', null, ['class' => 'form-control', 'rows' => 3, 'required']) }}</td>
        <td>{{ Form::textarea('consignee_address', null, ['class' => 'form-control', 'rows' => 3, 'required']) }}</td>
        <td rowspan="2"></td>
    </tr>
</table>
<hr/>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group form-group-sm m-b-5 is-required">
            {{ Form::label('source_airport', 'Aeroporto Origem', ['class' => 'col-sm-3 control-label p-r-0']) }}
            <div class="col-sm-9">
                {{ Form::select('source_airport', $waybill->exists || $waybill->prefill ? [$waybill->sourceAirport->code => $waybill->sourceAirport->airport] : ['' => ''], null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="form-group form-group-sm m-b-5 is-required">
            {{ Form::label('recipient_airport', 'Aeroporto Destino', ['class' => 'col-sm-3 control-label p-r-0']) }}
            <div class="col-sm-9">
                {{ Form::select('recipient_airport', $waybill->exists || $waybill->prefill ? [$waybill->recipientAirport->code => $waybill->recipientAirport->airport] : ['' => ''], null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('flight_no_1', 'Número Vôo 1', ['class' => 'col-sm-6 control-label p-r-0']) }}
                    <div class="col-sm-6">
                        {{ Form::text('flight_no_1', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('flight_no_2', 'Número Vôo 2', ['class' => 'col-sm-6 control-label p-r-0']) }}
                    <div class="col-sm-6">
                        {{ Form::text('flight_no_2', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('flight_no_3', 'Número Vôo 3', ['class' => 'col-sm-6 control-label p-r-0']) }}
                    <div class="col-sm-6">
                        {{ Form::text('flight_no_3', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('flight_no_4', 'Número Vôo 4', ['class' => 'col-sm-6 control-label p-r-0']) }}
                    <div class="col-sm-6">
                        {{ Form::text('flight_no_4', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <p class="m-t-0 bold text-uppercase bigger-110"><i class="fas fa-list-ol"></i> Pontos de Escala</p>
        <table class="table table-condensed m-0 table-flight-scales">
            <tr class="bg-gray-light">
                <th class="w-50">Aeroporto</th>
                <th class="w-50">Transportador</th>
                <th class="w-1"></th>
            </tr>
                @for ($i = 0 ; $i<=5; $i++)
                <tr style="{{ $i == 0 || isset($waybill->flight_scales[$i]) ? '' : 'display: none' }}">
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
</div>