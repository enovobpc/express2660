<div class="row row-10">
    <div class="col-sm-3">
        <div class="form-group form-group-sm m-b-5">
            {{ Form::label('awb_no', 'HAWB Nº', ['class' => 'col-sm-3 control-label p-0']) }}
            <div class="col-sm-9">
                {{ Form::text('awb_no', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="form-group form-group-sm m-b-5">
            {{ Form::label('reference', 'Ref.', ['class' => 'col-sm-3 control-label p-0']) }}
            <div class="col-sm-9">
                {{ Form::text('reference', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group form-group-sm m-b-5">
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
            {{ Form::label('goods_type_id', 'Tipo Carga', ['class' => 'col-sm-5 control-label p-0']) }}
            <div class="col-sm-7">
                {{ Form::select('goods_type_id', ['' => ''] + $goodsTypes, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
    <div class="col-sm-3" style="padding-left: 0;">
        <div class="form-group form-group-sm m-b-5">
            {{ Form::label('charge_code', 'Cód. Cobrança', ['class' => 'col-sm-5 control-label p-0']) }}
            <div class="col-sm-7">
                {{ Form::select('charge_code', trans('admin/air_waybills.charge-codes'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="form-group form-group-sm m-b-5">
            {{ Form::label('value_insurance', 'Valor Seguro', ['class' => 'col-sm-5 control-label p-r-0']) }}
            <div class="col-sm-7">
                <div class="input-group">
                    {{ Form::text('value_insurance', null, ['class' => 'form-control']) }}
                    <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3" style="padding-left: 0;">
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
<table class="table table-condensed table-sender m-t-10 m-b-0 no-border">
    <tr class="bg-gray-light">
        <th class="w-85px"></th>
        <th class="text-uppercase">Expedidor</th>
        <th class="text-uppercase">Consignatário</th>
        <th class="text-uppercase">Emissor</th>
    </tr>
    <tr>
        <th class="text-right">Nome</th>
        <td>
            {{ Form::hidden('customer_id') }}
            {{ Form::text('sender_name', null, ['class' => 'form-control input-sm search-customer', 'required', 'autocomplete'=> 'nofill']) }}
        </td>
        <td>
            {{ Form::hidden('consignee_id') }}
            {{ Form::text('consignee_name', null, ['class' => 'form-control input-sm search-consignee', 'required', 'autocomplete'=> 'nofill']) }}
        </td>
        <td>
            {{ Form::text('issuer_name', @$parentAWB->consignee_name, ['class' => 'form-control input-sm', 'required', 'autocomplete'=> 'nofill']) }}
        </td>
    </tr>
    <tr>
        <th class="text-right">NIF</th>
        <td>{{ Form::text('sender_vat', null, ['class' => 'form-control input-sm']) }}</td>
        <td>{{ Form::text('consignee_vat', null, ['class' => 'form-control input-sm']) }}</td>
        <td rowspan="2">
            {{ Form::textarea('issuer_address', @$parentAWB->consignee_address, ['class' => 'form-control', 'rows' => 3, 'required', 'style' => 'height: 108px;']) }}
        </td>
    </tr>
    <tr>
        <th class="text-right">Endereço</th>
        <td>{{ Form::textarea('sender_address', null, ['class' => 'form-control', 'rows' => 3, 'required']) }}</td>
        <td>{{ Form::textarea('consignee_address', null, ['class' => 'form-control', 'rows' => 3, 'required']) }}</td>
        <td rowspan="2"></td>
    </tr>
</table>
<hr style="margin: 10px 0"/>
<div class="row row-5">
    <div class="col-sm-12">
        <div class="col-sm-6" style="padding: 0 30px 0 15px;">
            <div class="form-group m-b-5">
                {{ Form::label('obs', 'Observações') }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
        <div class="col-sm-6" style="padding: 0 15px 0 30px;">
            <div class="form-group m-b-5">
                {{ Form::label('accounting_info', 'Informação para Contabilidade') }}
                {{ Form::textarea('accounting_info', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
    </div>
</div>