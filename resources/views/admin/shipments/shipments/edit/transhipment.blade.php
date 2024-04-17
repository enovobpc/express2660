{{ Form::model($shipment, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-6 col-customer">
            <div class="row row-0">
                <div class="col-sm-12 {{ empty($departments) ? '' : 'has-department' }} select-customer">
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('customer_id', 'Cliente', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-10 p-l-0">
                            {!! Form::select('customer_id', $shipment->exists ? [$shipment->customer_id => @$shipment->customer->code.' - '.@$shipment->customer->name] : [], null, ['class' => 'form-control select2', 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 select-department {{ empty($departments) ? 'hide' : '' }}">
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('department_id', 'Depart.', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-7 p-l-0 m-l-3">
                            {{ Form::select('department_id', empty($departments) ? [] : $departments, null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-0">
                <div class="col-sm-6">
                    <div class="form-group form-group-sm grp-ref m-b-5">
                        {{ Form::label('reference', 'Referência', ['class' => 'col-sm-3 control-label p-r-0', 'syle' => '']) }}
                        <div class="col-sm-8 p-l-0 m-l-3" style="padding-right: 15px;">
                            {{ Form::text('reference', null, ['class' => 'form-control input-sm', 'maxlength' => 15]) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('reference2', Setting::get('shipments_reference2_name'), ['class' => 'col-sm-3 control-label p-r-5 m-l-7 p-l-0']) }}
                        <div class="col-sm-8 p-l-0">
                            {{ Form::text('reference2', null, ['class' => 'form-control input-sm', 'placeholder' => Setting::get('shipments_reference2_name')]) }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="grp-srv {{ Setting::get('app_mode') == 'cargo' ? 'col-sm-2 p-r-0' : 'col-sm-3' }}" style="{{ in_array(Setting::get('app_mode'),['cargo','freight']) ? 'width: 20%' : '' }}">
            <div class="form-group form-group-sm m-b-5">
                {{ Form::label('service_id', 'Serviço', ['class' => 'col-sm-3 control-label p-r-0']) }}
                <div class="col-sm-9 p-l-5">
                    {!! Form::select('service_id', $services, @$shipment->service_id, ['class' => 'form-control select2', 'required']) !!}
                </div>
            </div>
            <div class="form-group form-group-sm m-b-10">
                <label class="col-sm-3 control-label lbl-provider">
                    Fornecedor
                </label>
                <div class="col-sm-9 p-l-5">
                    {!! Form::select('provider_id', [''=>''] + $providers, @$shipment->provider_id, ['class' => 'form-control select2', 'required']) !!}
                </div>
            </div>
        </div>

        <div class="col-sm-4 col-xs-12 cln-dt" style="width: 30%">
                <div class="row">
                    <div class="col-xs-7 p-r-0 input-hours">
                        <div class="form-group form-group-sm m-b-5">
                            <label class="col-sm-2 control-label p-r-5 p-l-0"  data-toggle="tooltip" title="Data de Expedição">
                                <i class="far fa-calendar-alt fs-15 m-t-2 hidden-xs"></i>
                                <span class="visible-xs"><i class="fas fa-calendar-alt"></i> Carga</span>
                            </label>
                            <div class="col-sm-6 p-l-0">
                                {{ Form::text('date', empty($shipment->date) ? date('Y-m-d') : null, ['class' => 'form-control datepicker']) }}
                            </div>
                            <div class="col-sm-2 p-r-0 p-l-5" style="width: 62px;margin-left: -27px;">
                                {{ Form::select('start_hour', ['' => '--:--'] + $hours, null, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                        <div class="form-group form-group-sm m-b-5">
                            <label class="col-sm-2 control-label p-r-5 p-l-0" data-toggle="tooltip" title="Data de Entrega">
                                <i class="far fa-calendar-alt fs-15 m-t-2 hidden-xs"></i>
                                <span class="visible-xs"><i class="fas fa-calendar-alt"></i> Descarga</span>
                            </label>
                            <div class="col-sm-6 p-l-0">
                                {{ Form::text('delivery_date', empty($shipment->delivery_date) ? date('Y-m-d') : @$shipment->delivery_date->format('Y-m-d'), ['class' => 'form-control datepicker']) }}
                            </div>
                            <div class="col-sm-2 p-r-0 p-l-5" style="width: 62px;margin-left: -27px;">
                                {{ Form::select('end_hour', ['' => '--:--'] + $hours, empty($shipment->delivery_date) ? null : @$shipment->delivery_date->format('H:i'), ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-5" style="width: 44%; margin-left: -10px;">
                        <div class="form-group form-group-sm m-b-10">
                            <label class="col-sm-2 control-label p-r-0 p-l-0" data-toggle="tooltip" title="Viatura">
                                <i class="fas fa-truck m-t-2 hidden-xs"></i>
                                <span class="visible-xs"><i class="fas fa-truck"></i> Viatura</span>
                            </label>
                            <div class="col-sm-10 p-l-5">
                                @if(Setting::get('shipments_vehicles_field_input'))
                                    {{ Form::text('vehicle', null, ['class' => 'form-control']) }}
                                @else
                                    {{ Form::select('vehicle', ['' => ''] + $vehicles, $shipment->exists && $shipment->vehicle ? $shipment->vehicle : @$shipment->operator->vehicle, ['class' => 'form-control select2']) }}
                                @endif
                            </div>
                        </div>

                        <div class="form-group form-group-sm m-b-10">
                            <label class="col-sm-1 control-label p-r-0 p-l-0" data-toggle="tooltip" title="Motorista">
                                <i class="fas fa-user hidden-xs"></i>
                                <span class="visible-xs"><i class="fas fa-user"></i> Reboque</span>
                            </label>
                            <div class="col-sm-11 p-l-5">
                                @if(Setting::get('shipments_vehicles_field_input'))
                                    {{ Form::text('trailer', null, ['class' => 'form-control']) }}
                                @else
                                    {{ Form::select('trailer', ['' => ''] + $trailers, null, ['class' => 'form-control select2']) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    </div>
    <div class="row row-5">
        <div class="col-sm-6">
            <div class="box box-default box-solid m-b-0">
                <div class="box-header with-border" style="padding: 5px 10px">
                    <h4 class="box-title"><i class="fas fa-sign-in-alt"></i> Local Carga</h4>
                </div>
                <div class="box-body p-10" id="box-sender">
                    <div class="box-sender-content main-addr">
                        <div class="form-group form-group-sm m-b-5">
                            <label class="col-sm-2 control-label p-r-0" for="recipient_attn">P. Contacto</label>
                            <div class="col-sm-5">
                                {{ Form::text('sender_attn', null, ['class' => 'form-control input-sm']) }}
                            </div>
                            <label class="col-sm-1 control-label label-ref-2">NIF</label>
                            <div class="col-sm-4">
                                {{ Form::text('sender_vat', null, ['class' => 'form-control input-sm']) }}
                            </div>
                        </div>
                        <div class="form-group form-group-sm m-b-5">
                            <label class="col-sm-2 control-label p-r-0" for="recipient_attn">
                                Remetente
                            </label>
                            <div class="col-sm-10">
                                {{ Form::text('sender_name', null, ['class' => 'form-control input-sm search-sender uppercase', 'autocomplete' => 'field-1']) }}
                                {{ Form::hidden('sender_id') }}
                            </div>
                        </div>
                        <div class="form-group form-group-sm m-b-5">
                            <label class="col-sm-2 control-label p-r-0" for="sender_address">
                                Morada <i class="fas fa-spin fa-circle-notch hide"></i>
                            </label>
                            <div class="col-sm-10">
                                {{ Form::text('sender_address', null, ['class' => 'form-control uppercase', 'required', 'autocomplete' => 'field-1']) }}
                            </div>
                        </div>

                        <div class="form-group form-group-sm m-b-5">
                            {{ Form::label('sender_zip_code', 'Cód. Postal', ['class' => 'col-sm-2 control-label p-r-0']) }}
                            <div class="col-sm-3 p-r-0">
                                {{ Form::text('sender_zip_code', null, ['class' => 'form-control uppercase', 'data-zp-country' => '#sender_country', 'required', 'autocomplete' => 'field-1']) }}
                            </div>
                            {{ Form::label('sender_city', 'Localidade', ['class' => 'col-sm-2 control-label p-r-0']) }}
                            <div class="col-sm-5">
                                {{ Form::text('sender_city', null, ['class' => 'form-control uppercase', 'required', 'autocomplete' => 'field-1']) }}
                            </div>
                        </div>

                        <div class="form-group form-group-sm m-b-5">
                            {{ Form::label('sender_country', 'País', ['class' => 'col-sm-2 control-label p-r-0']) }}
                            <div class="col-sm-3 p-r-0">
                                {{ Form::select('sender_country', ['' => ''] + trans('country'), $shipment->exists ? $shipment->sender_country : Setting::get('app_country'), ['class' => 'form-control select2-country', 'data-zp-zip-code' => '#sender_zip_code', 'required']) }}
                            </div>
                            {{ Form::label('sender_phone', 'Telefone', ['class' => 'col-sm-2 control-label p-r-0']) }}
                            <div class="col-sm-5">
                                {{ Form::text('sender_phone', null, ['class' => 'form-control phone', Setting::get('shipments_phone_required') ? 'required' : '']) }}
                            </div>
                        </div>
                        <div class="form-group form-group-sm m-t-5 m-b-5">
                            <label class="col-sm-2 control-label p-r-0">
                                Observações
                            </label>
                            <div class="col-sm-10">
                                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 2, 'maxlength' => 150, 'style' => 'height:40px']) }}
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="col-sm-6">
            <h4>Local Descarga</h4>
            <div class="box-recipient-content main-addr">
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-2 control-label p-r-0">P. Contacto</label>
                    <div class="col-sm-5">
                        {{ Form::text('recipient_attn', null, ['class' => 'form-control input-sm']) }}
                    </div>
                    <label class="col-sm-1 control-label label-ref-2">NIF</label>
                    <div class="col-sm-4">
                        {{ Form::text('recipient_vat', null, ['class' => 'form-control input-sm']) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-2 control-label p-r-0">
                        Destinatário
                    </label>
                    <div class="col-sm-10">
                        {{ Form::text('recipient_name', null, ['class' => 'form-control input-sm search-recipient uppercase', 'autocomplete' => 'field-1']) }}
                        {{ Form::hidden('recipient_id') }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-2 control-label p-r-0" for="recipient_address">
                        Morada <i class="fas fa-spin fa-circle-notch hide"></i>
                    </label>
                    <div class="col-sm-10">
                        {{ Form::text('recipient_address', null, ['class' => 'form-control input-sm uppercase', 'required', 'autocomplete' => 'field-1']) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('recipient__zip_code', 'Cód. Postal', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-3 p-r-0">
                        {{ Form::text('recipient_zip_code', null, ['class' => 'form-control input-sm uppercase zip-code', 'required', 'autocomplete' => 'field-1']) }}
                    </div>
                    {{ Form::label('recipient_city', 'Localidade', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-5">
                        {{ Form::text('recipient_city', null, ['class' => 'form-control input-sm uppercase', 'required', 'autocomplete' => 'field-1']) }}
                    </div>
                </div>

                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('recipient_country', 'País', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-3 p-r-0">
                        {{ Form::select('recipient_country', ['' => ''] + trans('country'), ($shipment->exists || $shipment->collection_tracking_code) ? $shipment->recipient_country : Setting::get('app_country'), ['class' => 'form-control select2-country', 'required']) }}
                    </div>
                    {{ Form::label('recipient_phone', 'Telefone', ['class' => 'col-sm-2 control-label p-r-0']) }}
                    <div class="col-sm-5">
                        {{ Form::text('recipient_phone', null, ['class' => 'form-control phone', Setting::get('shipments_phone_required') ? 'required' : '']) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-t-5 m-b-5">
                    <label class="col-sm-2 control-label p-r-0">
                        Observações
                    </label>
                    <div class="col-sm-10">
                        {{ Form::textarea('obs_delivery', null, ['class' => 'form-control', 'rows' => 2, 'maxlength' => 150, 'style' => 'height:40px']) }}
                    </div>
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
    $('.modal .select2').select2(Init.select2());

    $(document).ready(function () {
        Growl.error('Erro fatal ao inicializar módulo #!<TRANSHIPMENT!#>')
    })
</script>
