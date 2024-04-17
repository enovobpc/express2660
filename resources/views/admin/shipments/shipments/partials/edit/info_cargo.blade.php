@include('admin.shipments.shipments.partials.edit.schedule_block')
@include('admin.shipments.shipments.partials.edit.shipper_modal')
<div class="row">
    <div class="col-sm-6 col-customer">
        @if($shipment->is_back_shipment)
            {{ Form::select('agency_id', (count($userAgencies) > 1) ? $userAgencies : $userAgencies, null, ['class' => 'form-control trigger-price hide']) }}
            <div class="row row-0">
                <div class="col-sm-12 {{ empty($departments) ? '' : 'has-department' }} select-customer">
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('customer_id', 'Cliente', ['class' => 'col-sm-1 control-label']) }}
                       {{-- <div class="col-sm-10 p-l-0">
                            <div class="input-group p-r-2">
                                {!! Form::select('customer_id', $shipment->exists ? [$shipment->customer_id => @$shipment->customer->code.' - '.@$shipment->customer->name] : [], null, ['class' => 'form-control trigger-price select2', 'required']) !!}
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-sm btn-default" data-target="#modal-create-customer">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>--}}
                        <div class="col-sm-10 p-l-0">
                            <div class="input-group p-r-2">
                                {!! Form::select('customer_id', $shipment->exists ? [$shipment->customer_id => @$shipment->customer->code.' - '.@$shipment->customer->name] : [], null, ['class' => 'form-control trigger-price select2', 'required']) !!}
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-sm btn-default" data-target="#modal-create-customer">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 select-department {{ empty($departments) ? 'hide' : '' }}">
                    <div class="form-group form-group-sm m-b-5">
                        {{--{{ Form::label('department_id', 'Depart.', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-7 p-l-0 m-l-3">
                            {{ Form::select('department_id', empty($departments) ? [] : ['' => ''] + $departments, null, ['class' => 'form-control select2']) }}
                        </div>--}}
                        <div class="col-sm-12 p-l-0 m-l-3">
                            {{ Form::select('department_id', empty($departments) ? [] : ['' => ''] + $departments, null, ['class' => 'form-control select2', 'data-placeholder' => 'Departamento...']) }}
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{ Form::hidden('agency_id', $shipment->agency_id) }}
            <div class="row row-0">
                <div class="col-sm-12 {{ empty($departments) ? '' : 'has-department' }} select-customer">
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('customer_id', 'Cliente', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-10 p-l-0">
                            {{ Form::hidden('customer_id') }}
                            {{ Form::text('', @$shipment->customer->code.' - '.@$shipment->customer->name, ['class' => 'form-control', 'readonly']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 select-department {{ empty($departments) ? 'hide' : '' }}">
                    <div class="form-group form-group-sm m-b-5">
                       {{-- {{ Form::label('department_id', 'Depart.', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-7 p-l-0 m-l-3">
                            {{ Form::text('', @$shipment->customer->name, ['class' => 'form-control', 'readonly']) }}
                        </div>--}}
                        <div class="col-sm-12 p-l-0 m-l-3">
                            {{ Form::text('', @$shipment->customer->name, ['class' => 'form-control', 'readonly']) }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(Setting::get('shipments_reference3'))
            <div class="form-group form-group-sm grp-ref m-b-5 refs-panel">
                <label class="col-sm-1 control-label p-r-0" for="recipient_attn"> Referência</label>
                <div class="col-sm-3 p-r-0">
                    {{ Form::text('reference', null, ['class' => 'form-control input-sm ref', 'modal-addr' => '.input-ref', 'maxlength' => Setting::get('shipment_ref_maxsize', 15)]) }}
                </div>
                <label class="col-sm-1 p-0 control-label label-ref-2" style="width: 8%">Ref#2</label>
                <div class="col-sm-3 p-l-3">
                    {{ Form::text('reference2', null, ['class' => 'form-control input-sm ref2', 'modal-addr' => '.input-ref2', 'placeholder' => Setting::get('shipments_reference2_name')]) }}
                </div>
                <label class="col-sm-1 p-0 control-label label-ref-3" style="width: 6%">Ref#3</label>
                <div class="col-sm-4 p-l-0 m-l-3" style="width: 24%;">
                    {{ Form::text('reference3', null, ['class' => 'form-control input-sm ref3', 'modal-addr' => '.input-ref3', 'placeholder' => Setting::get('shipments_reference3_name')]) }}
                </div>
            </div>
        @elseif(Setting::get('shipments_requester_name'))
            <div class="form-group form-group-sm grp-ref m-b-5 refs-panel">
                <label class="col-sm-1 control-label p-r-0" for="recipient_attn"> Referência</label>
                <div class="col-sm-3 p-r-0">
                    {{ Form::text('reference', null, ['class' => 'form-control input-sm ref', 'modal-addr' => '.input-ref', 'maxlength' => Setting::get('shipment_ref_maxsize', 15)]) }}
                </div>
                <label class="col-sm-1 p-0 control-label label-ref-2" style="width: 8%">Ref#2</label>
                <div class="col-sm-3 p-l-3">
                    {{ Form::text('reference2', null, ['class' => 'form-control input-sm ref2', 'modal-addr' => '.input-ref2']) }}
                </div>
                <label class="col-sm-1 p-0 control-label label-ref-3">Pedido</label>
                <div class="col-sm-4 p-l-0 m-l-3 requestby">
                    {{ Form::text('requester_name', null, ['class' => 'form-control input-sm']) }}
                </div>
            </div>
        @else
            <div class="row row-0 refs-panel">
                <div class="col-sm-6">
                    <div class="form-group form-group-sm grp-ref m-b-5">
                        {{ Form::label('reference', 'Referência', ['class' => 'col-sm-3 control-label p-r-0', 'syle' => '']) }}
                        <div class="col-sm-8 p-l-0 m-l-3" style="padding-right: 15px;">
                            {{ Form::text('reference', null, ['class' => 'form-control input-sm ref', 'modal-addr' => '.input-ref', 'maxlength' => Setting::get('shipment_ref_maxsize', 15)]) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('reference2', Setting::get('shipments_reference2_name') ?? 'Referência 2', ['class' => 'col-sm-3 control-label p-r-0 m-l-3 p-l-0 m-r-5']) }}
                        <div class="col-sm-8 p-l-0">
                            {{ Form::text('reference2', null, ['class' => 'form-control input-sm ref2', 'modal-addr' => '.input-ref2']) }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
    <div class="grp-srv col-sm-2 p-r-0" style="width: 20%">
        <div class="form-group form-group-sm m-b-5">
            {{ Form::label('service_id', 'Serviço', ['class' => 'col-sm-3 control-label p-r-0']) }}
            <div class="col-sm-9 p-l-5">
                {!! Form::selectWithData('service_id', $services, @$shipment->service_id, ['class' => 'form-control trigger-price select2', 'required']) !!}
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            <label class="col-sm-3 control-label lbl-provider">
                Fornecedor
            </label>
            <div class="col-sm-9 p-l-5">
                {!! Form::selectWithData('provider_id', $providers, @$shipment->provider_id, ['class' => 'form-control trigger-price select2', 'required']) !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3 col-xs-12 cln-dt">
        <div class="row">
            <div class="col-xs-4 p-r-0 p-l-7">
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-2 control-label p-r-0 p-l-0 btn-dates" style="margin-left: 3px; margin-right: -4px;" data-toggle="tooltip" title="Data Recolha/Expedição">
                        <i class="far fa-calendar-alt fs-15 m-t-2 hidden-xs"></i>
                        <span class="visible-xs"><i class="fas fa-calendar-alt"></i> Recolha</span>
                    </label>
                    <div class="col-sm-10 p-l-7">
                        {{ Form::text('date', empty($shipment->date) ? date('Y-m-d') : null, ['class' => 'form-control trigger-price datepicker', 'required']) }}
                    </div>
                </div>
            </div>
            <div class="col-xs-2 p-0 input-hours">
                <div class="form-group form-group-sm m-0">
                    <div class="col-sm-11 p-l-9">
                        <div>
                           {{ Form::select('start_hour', ['' => '&#45;&#45;:&#45;&#45;'] + $hours, null, ['class' => 'form-control trigger-price select2']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-6 p-r-0">
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-1 control-label p-r-0 p-l-0" data-toggle="tooltip" title="Gestor Trafego">
                        <i class="fas fa-headset m-t-2 hidden-xs"></i>
                        <span class="visible-xs"><i class="fas fa-headset"></i> Gestor Trafego</span>
                    </label>
                    <div class="col-xs-10 p-l-5 p-r-7">
                        {!! Form::selectWithData('dispatcher_id', $users, null, ['class' => 'form-control select2']) !!}
                    </div>
                </div>
            
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4 p-r-0 p-l-15">
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-2 control-label p-r-3 p-l-0" data-toggle="tooltip" title="Data Prevista Entrega">
                        <i class="far fa-calendar-alt fs-15 m-t-2 hidden-xs"></i>
                        <span class="visible-xs"><i class="fas fa-calendar-alt"></i> Entrega</span>
                    </label>
                    <div class="col-sm-10 p-l-0">
                        {{ Form::text('delivery_date', $shipment->delivery_date ? $shipment->delivery_date->format('Y-m-d') :  date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                    </div>
                </div>
            </div>
            <div class="col-xs-2 p-0 input-hours">
                <div class="form-group form-group-sm m-0">
                    <div class="col-sm-11 p-l-9">
                        <div>
                            {{ Form::select('end_hour', ['' => '&#45;&#45;:&#45;&#45;'] + $hours, $shipment->delivery_date ? $shipment->delivery_date->format('H:i') : '', ['class' => 'form-control trigger-price select2']) }}
                        </div>
                    </div>
                </div>
            </div>
            @if(@$transportTypes)
            <div class="col-xs-6 p-r-0">
               <div class="form-group form-group-sm m-b-0">
                    <label class="col-sm-1 control-label p-r-0 p-l-0" data-toggle="tooltip" title="Tipo Transporte">
                        <i class="fas fa-tags m-t-2 hidden-xs"></i>
                        <span class="visible-xs"><i class="fas fa-tags"></i> Tipo Transporte</span>
                    </label>
                    <div class="col-xs-10 p-l-5 p-r-7">
                        {{ Form::select('transport_type_id', ['' => ''] + @$transportTypes, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if(Setting::get('shipment_adicional_addr_mode') == 'pro_fixed')
    <div class="row row-destinations-advanced">
        @include('admin.shipments.shipments.partials.edit.multiple_addr')
    </div>
@else
    <div class="row row-destinations-default" style="{{ $shipment->multiple_addresses ? 'display:none' : ''}}">
        <div class="col-sm-6">
            <div class="box box-default box-solid m-b-0">
                <div class="box-header with-border" style="padding: 5px 10px">
                    <h4 class="box-title"><i class="fas fa-sign-in-alt"></i>
                        Local Carga
                    </h4>
                    <i class="fas fa-exchange-alt toggle-sender" data-toggle="tooltip" data-placement="left" title="Trocar Local de Carga com o Local de Descarga"></i>
                    <i class="fas fa-eraser reset-sender p-t-5 fs-11 pull-right" data-toggle="tooltip" data-placement="left" title="Limpar dados do remetente"></i>
                    <ul class="address-tabs address-left">
                        <li data-action="add-addr">
                            <b data-toggle="tooltip"
                            data-placement="right"
                            title="Criar várias recolhas no mesmo pedido">&nbsp;+&nbsp;</b>
                        </li>
                    </ul>
                </div>
                <div class="box-body p-10" id="box-sender">
                    <div class="form-group form-group-sm form-group-shipper">
                        <i class="fas fa-pencil-alt btn-edit-shipper"></i>
                        <label class="col-sm-2 control-label p-r-0">Expedidor</label>
                        <div class="col-sm-10">
                            {{ Form::text('', $shipment->exists && $shipment->shipper_name ? $shipment->shipper_name : @$shipment->customer->name, ['class' => 'form-control shipper-name', 'readonly']) }}
                        </div>
                    </div>
                    @include('admin.shipments.shipments.partials.edit.sender_block')
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box box-default box-solid m-b-0">
                <div class="box-header with-border" style="padding: 5px 10px">
                    <h4 class="box-title"><i class="fas fa-sign-out-alt"></i>
                        Local Descarga
                    </h4>
                    <ul class="address-tabs address-right">
                        <li data-action="add-addr">
                            <b data-toggle="tooltip"
                            data-placement="right"
                            title="Criar serviço agrupado (Entrega destinos diferentes)">&nbsp;+&nbsp;</b>
                        </li>
                    </ul>
                </div>
                <div class="box-body p-10" id="box-recipient">
                    <div class="form-group form-group-sm form-group-shipper">
                        <i class="fas fa-pencil-alt btn-edit-shipper"></i>
                        <label class="col-sm-2 control-label p-r-0">Destinatário</label>
                        <div class="col-sm-10">
                            {{ Form::text('', $shipment->receiver_name ?? $shipment->recipient_name, ['class' => 'form-control receiver-name', 'readonly']) }}
                        </div>
                    </div>
                    @include('admin.shipments.shipments.partials.edit.recipient_block')
                </div>
            </div>
        </div>
    </div>
    @if(Setting::get('shipment_adicional_addr_mode') == 'pro')
    <div class="row row-destinations-advanced" style="{{ $shipment->multiple_addresses ? '' : 'display:none'}}">
        @include('admin.shipments.shipments.partials.edit.multiple_addr')
    </div>
    @endif
@endif
<div class="h-10px"></div>
<div class="row">
    <div class="col-sm-8 p-r-20">
        <div class="row">
            <div class="col-sm-4">
                <div class="vols-panel {{ Setting::get('shipment_adicional_addr_mode') == 'pro_fixed' || $shipment->multiple_addresses ? 'vols-panel-addr' : ''}}">
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('volumes', 'Volumes', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        @if(@$shipment->conferred_volumes)
                            <span style="position: absolute;left: 31px;margin-top: 17px;font-size: 12px;font-style: italic;color: #0094ff;"
                                data-toggle="tooltip"
                                title="Valor conferido pelo motorista">Conferido
                            </span>
                        @endif
                        <div class="col-sm-8">
                            <div class="input-group {{ @$shipment->conferred_volumes ? 'bold' : '' }}">
                                {{ Form::text('volumes', null, ['class' => 'form-control trigger-price int vol', 'maxlength' => 6, 'required', 'autocomplete' => 'field-1', 'modal-addr' => '.input-vol']) }}
                                <div class="input-group-btn btn-group-sm" data-toggle="tooltip" title="Detalhar mercadoria">
                                    <button type="button" class="btn btn-default btn-set-dimensions" data-target="#modal-shipment-dimensions">
                                        <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE2LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHdpZHRoPSI1MTFweCIgaGVpZ2h0PSI1MTFweCIgdmlld0JveD0iMCAwIDUxMSA1MTEiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDUxMSA1MTEiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8cGF0aCBkPSJNNDIyLjQzMiwyNDcuMDQ2Yy0yLjYyNywwLTUuMiwxLjA1Ny03LjA2OCwyLjkyNGMtMS44NTUsMS44NTYtMi45MTYsNC40MjEtMi45MTYsNy4wNTdjMCwyLjYyOCwxLjA2MSw1LjIsMi45MTYsNy4wNTcKCWMxLjg2OCwxLjg1OSw0LjQzNCwyLjkyOCw3LjA2OCwyLjkyOGMyLjYyNCwwLDUuMTg5LTEuMDY4LDcuMDU3LTIuOTI4YzEuODU1LTEuODU2LDIuOTI0LTQuNDI5LDIuOTI0LTcuMDU3CgljMC0yLjYyNC0xLjA2OC01LjIwMS0yLjkyNC03LjA1N0M0MjcuNjIxLDI0OC4xMDMsNDI1LjA1NiwyNDcuMDQ2LDQyMi40MzIsMjQ3LjA0NnoiLz4KPHBhdGggZD0iTTQyLjE4NywyMzguNjI5YzEuODU2LTEuODU2LDIuOTI0LTQuNDI5LDIuOTI0LTcuMDU3YzAtMi42MjQtMS4wNjgtNS4yMDEtMi45MjQtNy4wNTcKCWMtMS44NjctMS44NTYtNC40MzMtMi45MjQtNy4wNTctMi45MjRjLTIuNjM1LDAtNS4yMDEsMS4wNjgtNy4wNjgsMi45MjRjLTEuODU2LDEuODU1LTIuOTE3LDQuNDMzLTIuOTE3LDcuMDU3CgljMCwyLjYyNywxLjA2MSw1LjIwMSwyLjkxNyw3LjA1N2MxLjg2NywxLjg1OSw0LjQzMywyLjkyOCw3LjA2OCwyLjkyOEMzNy43NTQsMjQxLjU1Nyw0MC4zMiwyNDAuNDg4LDQyLjE4NywyMzguNjI5eiIvPgo8cGF0aCBkPSJNNDk2LjEyNCwzMDMuODM3VjE1MC4yN2MxLjUxNywwLjg1NCwzLjIsMS4yOTEsNC44OTMsMS4yOTFjMi41NTgsMCw1LjExMS0wLjk3NSw3LjA2MS0yLjkyNAoJYzMuODk4LTMuODk4LDMuODk4LTEwLjIxOCwwLTE0LjExN0w0OTMuMiwxMTkuNjQzYy0zLjg5OS0zLjg5NS0xMC4yMTktMy44OTUtMTQuMTE3LDBsLTE0Ljg3NywxNC44NzcKCWMtMy44OTksMy44OTktMy44OTksMTAuMjE5LDAsMTQuMTE3YzMuMjM5LDMuMjQsOC4xNDcsMy43NzgsMTEuOTUzLDEuNjMzdjE1My41NjdjLTMuODA2LTIuMTQ3LTguNzE0LTEuNjEtMTEuOTUzLDEuNjMKCWMtMy44OTksMy44OTgtMy44OTksMTAuMjIzLDAsMTQuMTE3bDE0Ljg3NywxNC44NzdjMS45NDksMS45NDksNC41MDMsMi45MjQsNy4wNTcsMi45MjRjMi41NTgsMCw1LjExLTAuOTc1LDcuMDYxLTIuOTI0CglsMTQuODc3LTE0Ljg3N2MzLjg5NS0zLjg5NSwzLjg5NS0xMC4yMTksMC0xNC4xMTdDNTA0LjgzNywzMDIuMjMsNDk5LjkyOSwzMDEuNjkzLDQ5Ni4xMjQsMzAzLjgzN3oiLz4KPHBhdGggZD0iTTQ1MC45MjMsMzY4LjQyNmwtMTkuOTc3LTYuNTkzYy01LjIzNS0xLjczMS0xMC44ODEsMS4xMTUtMTIuNjA4LDYuMzUxYy0xLjU0Myw0LjY3MSwwLjU2Miw5LjY2MSw0Ljc1MywxMS45MWwtMTQ1LjY5Niw4MAoJYy0wLjAyNy00LjI2NS0yLjgwMy04LjE5NC03LjA5OS05LjQ4OWMtNS4yNzgtMS41OTUtMTAuODUxLDEuMzk2LTEyLjQ0LDYuNjcxbC02LjA3OCwyMC4xNDUKCWMtMS41OTEsNS4yNzgsMS4zOTYsMTAuODQ2LDYuNjc0LDEyLjQzN2wyMC4xNDEsNi4wNzhjMC45NjMsMC4yOTIsMS45MzQsMC40MjksMi44ODksMC40MjljNC4yODksMCw4LjI1LTIuNzg0LDkuNTUzLTcuMQoJYzEuMzQ4LTQuNDc2LTAuNjAyLTkuMTU0LTQuNDQxLTExLjQ0MmwxNDQuNjQ3LTc5LjQyN2MwLjIwNiwzLjk5MiwyLjgwNyw3LjYzOCw2LjgzNCw4Ljk2N2MxLjAzNywwLjM0MywyLjA5NCwwLjUwNywzLjEzMSwwLjUwNwoJYzQuMTksMCw4LjA5My0yLjY1OSw5LjQ3OC02Ljg1N2w2LjU5Mi0xOS45NzhjMC44MzEtMi41MTQsMC42MjgtNS4yNTUtMC41NjEtNy42MTdDNDU1LjUyLDM3MS4wNDksNDUzLjQzOCwzNjkuMjU2LDQ1MC45MjMsMzY4LjQyNgoJeiIvPgo8cGF0aCBkPSJNMTk5LjkxOCw0NTcuMjc1Yy0xLjU5NS01LjI3OS03LjE2Ni04LjI2Ni0xMi40NDEtNi42NzVjLTQuMjk2LDEuMjk5LTcuMDcyLDUuMjI5LTcuMDk5LDkuNDkzbC0xNDUuNjkyLTgwCgljNC4xODctMi4yNDksNi4yOTItNy4yMzksNC43NDktMTEuOTFjLTEuNzI3LTUuMjM1LTcuMzc2LTguMDc4LTEyLjYwNC02LjM1MWwtMTkuOTgsNi41OTNjLTAuMDU1LDAuMDItMC4wOSwwLjA0My0wLjEzMywwLjA2NgoJYy0yLjI1NywwLjc3OS00LjI0NiwyLjM1OC01LjQ4OSw0LjYxNWMtMS4zOCwyLjUxNi0xLjU1Niw1LjM1Ny0wLjczNyw3Ljg4M2MwLjAwNCwwLjAxNiwwLjAwNCwwLjAyNywwLjAwOCwwLjA0M2w2LjU5MywxOS45NzgKCWMxLjM4OCw0LjE5OCw1LjI4Niw2Ljg1Nyw5LjQ3OCw2Ljg1N2MxLjAzNywwLDIuMDkzLTAuMTY0LDMuMTMtMC41MDdjNC4wMjctMS4zMjksNi42MjgtNC45NzUsNi44MzQtOC45NjdsMTQ0LjY0Nyw3OS40MjcKCWMtMy44NCwyLjI5Mi01Ljc5LDYuOTY3LTQuNDQxLDExLjQ0MmMxLjMwMiw0LjMxNSw1LjI2Nyw3LjEsOS41NTIsNy4xYzAuOTU1LDAsMS45MjYtMC4xMzcsMi44ODktMC40MjlsMTkuODQ4LTUuOTg4CgljMC4wMTItMC4wMDQsMC4wMjMtMC4wMDgsMC4wMzUtMC4wMTJsMC4yNTctMC4wNzhjMC4wMjMtMC4wMDQsMC4wMzktMC4wMiwwLjA1OS0wLjAyN2MyLjM5NC0wLjczNyw0LjUxNS0yLjM1NCw1LjgxNi00LjcyMgoJYzEuMzU3LTIuNDY4LDEuNTUyLTUuMjU5LDAuNzgtNy43NTRMMTk5LjkxOCw0NTcuMjc1eiIvPgo8cGF0aCBkPSJNNDMyLjQxMiwxMjcuMDU0aC0wLjAwNHYtOS43NTRjMC0zLjY0MS0xLjk4NC02Ljk5NC01LjE3Ny04Ljc0OUwyMzMuNTc5LDIuMjE2Yy0yLjk5LTEuNjQxLTYuNjE2LTEuNjQxLTkuNjEsMAoJTDMwLjMyLDEwOC41NTFjLTMuMTkzLDEuNzU0LTUuMTc4LDUuMTA3LTUuMTc4LDguNzQ5djguMDM1djY3LjQ3djc4LjUyNnY1NC4wMDN2NC42MzdjMCwzLjY0MiwxLjk4NCw2Ljk5NCw1LjE3OCw4Ljc0OQoJbDE5My42NDgsMTA2LjMzNGMxLjQ5NywwLjgyMywzLjE1NCwxLjIzMiw0LjgwNywxLjIzMnMzLjMwNi0wLjQwOSw0LjgwMy0xLjIzMmwxOTMuNjUzLTEwNi4zMzQKCWMzLjE5Mi0xLjc1NSw1LjE3Ny01LjEwNyw1LjE3Ny04Ljc0OXYtMi45MTdoMC4wMDRWMTI3LjA1NHogTTMwNC41OTIsNjMuOTg2bDI0LjEwMiwxMy41OThsLTE3Mi40OTksOTQuODExbC0xOS41MjEtMTAuNzIxCglsLTguMTI1LTQuNTg1bDE3Mi43MjEtOTQuOTMyTDMwNC41OTIsNjMuOTg2eiBNMTQ1Ljg4MywxODkuNzk0djQ0LjU0MmwtMjcuODY0LTE1LjUyOHYtNDQuNjA0bDguOTQsNC45MTNMMTQ1Ljg4MywxODkuNzk0egoJIE0yMjguNzc1LDIyLjM1M2w1MS43NTgsMjguNDIxbC0xNzIuODMsOTQuOTk0bC01MS44NC0yOC40NjhMMjI4Ljc3NSwyMi4zNTN6IE0yMTguNzkxLDQxOS40MzZMNDUuMTA3LDMyNC4wNjJWMjcxLjMzdi03OC41MjYKCXYtNTguNjM1bDUyLjk0NywyOS4wNzZ2NjEuNDI3YzAsMy42MjIsMS45NjEsNi45NTksNS4xMjMsOC43MjFsNDcuODI4LDI2LjY1NWMxLjUxMywwLjg0MiwzLjE4NiwxLjI2Myw0Ljg1OCwxLjI2MwoJYzEuNzUsMCwzLjQ5Ny0wLjQ2LDUuMDU3LTEuMzc2YzMuMDUyLTEuNzk0LDQuOTI4LTUuMDY4LDQuOTI4LTguNjA0di01MC44NjFsNTIuOTQzLDI5LjA3MlY0MTkuNDM2eiBNMjI4Ljc3NSwyMTIuMjQ3CglsLTUxLjg0NC0yOC40NjhsMTcyLjgzLTk0Ljk5NGw1MS45MjYsMjguNTE0TDIyOC43NzUsMjEyLjI0N3ogTTQxMi40NDMsMzI0LjA2MmwtMTczLjY4OCw5NS4zNzNWMjI5LjU0MWwxNzMuNjg4LTk1LjM3MnY4NS45MjIKCWMwLDAuMDI2LDAuMDA0LDAuMDUxLDAuMDA0LDAuMDc3djc1LjcwOWMwLDAuMDI2LTAuMDA0LDAuMDUxLTAuMDA0LDAuMDc3VjMyNC4wNjJ6Ii8+Cjwvc3ZnPgo=" style="height: 20px;margin: -3px"/>
                                    </button>
                                    <button type="button" class="btn btn-default btn-set-pallets" data-target="#modal-shipment-pallets" style="display: {{ $shipment->exists && @$shipment->service->unity == 'pallet' ? '' : 'none' }}">
                                        <i class="fas fa-external-link-square-alt"></i> Paletes
                                    </button>
                                </div>
                            </div>
                            <div class="helper-max-volumes italic text-red line-height-1p0" style="display: none">
                                <small><i class="fas fa-info-circle"></i> Máximo <b class="lbl-total-vol">1</b> Volume</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-sm form-group-weight m-b-5" style="display: {{ $shipment->exists && @$shipment->service->unity == 'm3' ? 'none' : '' }}">
                        {{ Form::label('weight', 'Peso Total', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        @if(@$shipment->conferred_weight)
                            <span style="position: absolute;left: 31px;margin-top: 17px;font-size: 12px;font-style: italic;color: #0094ff;"
                                data-toggle="tooltip"
                                title="Valor conferido pelo motorista">Conferido</span>
                        @endif
                        <div class="col-sm-8">
                            <div class="input-group input-group-money {{ $shipment->conferred_weight ? 'bold' : '' }}">
                                {{ Form::text('weight', null, ['class' => 'form-control trigger-price decimal kg', 'maxlength' => 8, (($shipment->exists && @$shipment->service->unity == 'm3'))  ? '' : 'required', $shipment->exists && @$shipment->service->unity == 'pallet' ? 'readonly' : '', 'autocomplete' => 'field-1', 'modal-addr' => '.input-kg']) }}
                                <div class="input-group-addon">kg</div>
                            </div>
                            <div class="helper-max-weight italic text-red line-height-1p0" style="display: none">
                                <small><i class="fas fa-info-circle"></i> Máximo <b class="lbl-total-kg">1</b>kg</small>
                            </div>
                        </div>
                    </div>
                    @if(Setting::get('shipments_custom_provider_weight') && @$shipment->service->unity != 'pallet')
                        <div class="form-group form-group-sm form-group-weight m-b-5 provider-weight" style="display: none">
                            {{ Form::label('provider_weight', 'Peso Etiq.', ['class' => 'col-sm-4 control-label p-0']) }}
                            <div class="col-sm-8">
                                <div class="input-group input-group-money">
                                    {{ Form::text('provider_weight', null, ['class' => 'form-control decimal', 'maxlength' => 8, 'autocomplete' => 'field-1']) }}
                                    <div class="input-group-addon">kg</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="form-group form-group-sm form-group-ldm m-b-5" style="display: {{ $showLdm || $ldmRequired ? '' : 'none' }}">
                        {{ Form::label('ldm', 'LDM', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            <div class="input-group input-group-money">
                                {{ Form::text('ldm', null, ['class' => 'form-control trigger-price decimal', 'maxlength' => 7, $ldmRequired ? 'required' : '', 'modal-addr' => '.input-ldm']) }}
                                <div class="input-group-addon">mt</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-sm form-group-hours m-b-5" style="{{ ($shipment->exists && @$shipment->service->unity == 'hours') ? '' : 'display: none' }}">
                        {{ Form::label('hours', 'Nº Horas', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            <div class="input-group input-group-money">
                                {{ Form::text('hours', $shipment->exists && @$shipment->service->unity == 'hours' && $shipment->hours ? null : 0, ['class' => 'form-control trigger-price decimal', 'maxlength' => 5, $shipment->exists && @$shipment->service->unity == 'hours' ? 'required' : '', 'autocomplete' => 'field-1']) }}
                                <div class="input-group-addon">h</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-sm form-group-volume-m3 m-b-5" style="display: {{ $shipment->exists && @$shipment->service->unity == 'm3' ? '' : 'none' }}">
                        {{ Form::label('volume_m3', 'Volume', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            <div class="input-group input-group-money">
                                {{ Form::text('volume_m3', null, ['class' => 'form-control trigger-price decimal', 'maxlength' => 7, $shipment->exists && @$shipment->service->unity == 'm3' ? 'required' : '']) }}
                                <div class="input-group-addon">m<sup>3</sup></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-sm m-b-5 hid">
                        <label for="fator_m3" class="col-sm-4 control-label p-r-0">Volume M<sup>3</sup></label>
                        <div class="col-sm-5 p-r-4">
                            <div class="input-group input-group-money">
                                {{ Form::text('fator_m3', @$shipment->fator_m3, ['class' => 'form-control decimal fm3 trigger-price', 'modal-addr' => '.input-fm3']) }}
                                <div class="input-group-addon" style="background: #fff;margin: 1px 0;padding: 5px 2px;">m<sup>3</sup></div>
                            </div>
                        </div>
                        <div class="col-sm-3 p-0 p-t-6">
                            <b class="text-blue"><span class="lbl-vkg">{{ money($shipment->volumetric_weight) }}</span>kg</b>
                            {{ Form::hidden('volumetric_weight', null, ['class' => 'form-control decimal vkg', 'maxlength' => 7, 'readonly']) }}
                        </div>
                    </div>
                    {{-- <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('volumetric_weight', 'Peso Vol.', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            <div class="input-group input-group-money">
                                {{ Form::text('volumetric_weight', null, ['class' => 'form-control decimal vkg', 'maxlength' => 7, 'readonly']) }}
                                <div class="input-group-addon">kg</div>
                            </div>
                        </div>
                    </div> --}}

                    <div class="form-group form-group-sm form-group-kms m-b-5">
                        {{ Form::label('kms', 'Kms', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                {{ Form::text('kms', $shipment->exists && @$shipment->service->unity == 'km' && $shipment->kms ? null : '0', ['class' => 'form-control trigger-price decimal', 'maxlength' => 7, 'autocomplete' => 'field-1', 'modal-addr' => '.input-kms']) }}
                                <div class="input-group-btn">
                                    @if(hasModule('calc_auto_km'))
                                        <button type="button" class="btn btn-sm btn-default p-t-4 p-b-4 btn-auto-km"  data-toggle="tooltip" title="Calcular KM automáticamente">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-default p-t-4 p-b-4"  data-toggle="tooltip" title="Calcular KM automáticamente. Módulo Inativo.">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    @endif
                                </div>
                                <div class="input-group-addon" style="padding: 0 7px 0 8px;">
                                    <i class="fas fa-warehouse" style="vertical-align: middle" data-toggle="tooltip" title="Calcular distância desde a sede"></i>
                                    {{ Form::checkbox('km_agency', 1) }}
                                </div>
                                {{----}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                @if(Setting::get('app_mode_containers')) 
                <div class="container-fields">
                    <div class="form-group form-group-sm form-group-ldm m-b-5" style="display: ">
                        {{ Form::label('ship_code', 'Navio', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            {{ Form::text('ship_code', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="form-group form-group-sm form-group-ldm m-b-5" style="display: ">
                        {{ Form::label('container_type', 'Tipo Contentor', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            {{ Form::select('container_type', ['' => ''] + trans('admin/shipments.containers-types'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="form-group form-group-sm form-group-ldm m-b-5" style="display: ">
                        {{ Form::label('container_code', 'Nº Contentor', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            {{ Form::text('container_code', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    <div class="form-group form-group-sm form-group-ldm m-b-5" style="display: ">
                        {{ Form::label('container_seal', 'Nº Selo', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            {{ Form::text('container_seal', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
                @elseif (Setting::get('app_mode_adr'))
                <div class="container-fields">
                    <div class="form-group form-group-sm form-group-ldm m-b-5" style="display: ">
                        {{ Form::label('adr_class', 'Classe ADR', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            {{ Form::select('adr_class', ['' => ''], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="form-group form-group-sm form-group-ldm m-b-5" style="display: ">
                        {{ Form::label('adr_onu', 'Nº ONU', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            {{ Form::text('adr_onu', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
                @else
                <div class="opt-flds-grp {{ $complementarServices->count() >= 4 ? 'vsb' : '' }}">
                    @if($complementarServices->count() < 4)
                        @if(Setting::get('customer_shipment_without_pickup'))
                            <div class="checkbox" >
                                <label>
                                    {{ Form::checkbox('without_pickup', '1') }}
                                    Sem recolha (leva à agência)
                                </label>
                            </div>
                        @endif
                        @if(Setting::get('app_rpack'))
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('has_return[]', 'rpack', null, ['class' => 'trigger-price']) }}
                                    Retorno Encomenda
                                </label>
                            </div>
                        @endif

                        <div class="checkbox {{ Setting::get('shipments_show_assembly') ? : 'hide' }}">
                            <label>
                                {{ Form::checkbox('has_assembly', '1') }}
                                Serviço Montagem
                            </label>
                        </div>

                    @endif

                    @if($complementarServices)
                        <table>
                            @if($complementarServices->count() >= 4)
                                @if(Setting::get('customer_shipment_without_pickup'))
                                    <tr class="tdinpt">
                                        <td>
                                            <label for="optional_fields[]">Sem recolha {!! tip('CLIENTE LEVA AO ARMAZÉM. Assinale esta opção se optar por entregar os volumes diretamente no armazém') !!}</label>
                                        </td>
                                        <td>{{ Form::checkbox('without_pickup', '1') }}</td>
                                    </tr>
                                @endif

                                @if(Setting::get('app_rpack'))
                                    <tr class="tdinpt">
                                        <td><label for="optional_fields[]">Retorno Encomenda</label></td>
                                        <td>{{ Form::checkbox('has_return[]', 'rpack', null, ['class' => 'trigger-price']) }}</td>
                                    </tr>
                                @endif

                                <tr class="tdinpt {{ Setting::get('shipments_show_assembly') ? : 'hide' }}">
                                    <td><label for="optional_fields[]">Serviço Montagem</label></td>
                                    <td>{{ Form::checkbox('has_assembly', '1') }}</td>
                                </tr>
                            @endif
                            @if($complementarServices)
                                @include('admin.shipments.shipments.partials.edit.complementar_services')
                            @endif
                        </table>
                    @endif
                </div>
                @endif
            </div>
            <div class="col-sm-3">
                @if(Setting::get('shipments_show_charge_price'))
                    <div class="form-group form-group-sm m-b-5" style="{{ $nonEU ? 'display:none' : '' }}">
                        {{ Form::label('charge_price', 'Cobrança', ['class' => 'col-sm-4 control-label p-0']) }}
                        <div class="col-sm-8 p-l-5">
                            <div class="input-group input-group-money">
                                {{ Form::text('charge_price', $shipment->charge_price == 0.00 ? '' : null, ['class' => 'form-control trigger-price decimal', 'maxlength' => 7, 'autocomplete' => 'field-1', 'data-type' => 'charge']) }}
                                <div class="input-group-addon">{{ $shipment->currency }}</div>
                            </div>
                        </div>
                    </div>
                @else
                    {{ Form::hidden('charge_price', '') }}
                @endif


                <div class="form-group form-group-sm m-b-5 incoterms" style="{{ $nonEU ? '' : 'display: none' }}">
                    {{ Form::label('incoterm', 'Incoterm', ['class' => 'col-sm-4 control-label p-0']) }}
                    <div class="col-sm-8 p-l-5">
                        {{ Form::select('incoterm', [''=>'']+trans('admin/shipments.incoterms'), null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5 goods-price" style="{{ $nonEU || @$shipment->goods_price > 0.00 || @$shipment->service->unity == 'advalue' ? '' : 'display: none' }}">
                    {{ Form::label('goods_price', 'Valor Bens', ['class' => 'col-sm-4 control-label p-0']) }}
                    <div class="col-sm-8 p-l-5">
                        <div class="input-group input-group-money">
                            {{ Form::text('goods_price', $shipment->goods_price == 0.00 ? '' : null, ['class' => 'form-control trigger-price decimal', 'maxlength' => 7, 'autocomplete' => 'field-1']) }}
                            <div class="input-group-addon">{{ $shipment->currency }}</div>
                        </div>
                    </div>
                </div>
                @if(Setting::get('shipments_show_delivery_map_id'))
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-4 control-label p-r-0 p-l-0">Viagem</label>
                    <div class="col-sm-8 p-l-5">
                        {!! Form::select('delivery_map_id', [], null, ['class' => 'form-control select2']) !!}
                    </div>
                </div>
                @endif
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-4 control-label p-r-0 p-l-0">Motorista</label>
                    <div class="col-sm-8 p-l-5">
                        {!! Form::selectWithData('operator_id', $operators, null, ['class' => 'form-control select2']) !!}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-4 control-label p-r-0">Viatura</label>
                    <div class="col-sm-8 p-l-5">
                        @if(Setting::get('shipments_vehicles_field_input'))
                            {{ Form::text('vehicle', null, ['class' => 'form-control']) }}
                        @else
                            {{ Form::select('vehicle', ['' => ''] + $vehicles, $shipment->exists && $shipment->vehicle ? $shipment->vehicle : @$shipment->operator->vehicle, ['class' => 'form-control select2']) }}
                        @endif
                    </div>
                </div>

                @if(!empty($trailers))
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-4 control-label p-r-0">Reboque</label>
                    <div class="col-sm-8 p-l-5">
                        @if(Setting::get('shipments_vehicles_field_input'))
                            {{ Form::text('trailer', null, ['class' => 'form-control']) }}
                        @else
                            {{ Form::select('trailer', [''=>''] + $trailers, null, ['class' => 'form-control select2']) }}
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-4 obs-grp">
        @if(Setting::get('shipments_obs_sender_recipient'))
            <div class="form-group form-group-sm m-b-5">
                <label class="col-sm-2 control-label lh-1-0 p-4">
                    Notas Internas
                </label>
                <div class="col-sm-10 lbl-cost">
                    {{ Form::textarea('obs_internal', null, ['class' => 'form-control', 'rows' => '2', 'maxlength' => 255, 'style' => 'height: 60px;']) }}
                </div>
            </div>
        @else
            <div class="form-group form-group-sm m-b-5 p-l-20 p-r-15">
                <div class="nav-obs {{ Setting::get('shipment_adicional_addr_mode') == 'pro_fixed' || $shipment->multiple_addresses ? 'nav-obs-addr' : '' }}">
                    <ul class="nav nav-tabs" role="tablist">
                        @if($appMode == 'cargo')
                            <li class="active">
                                <a href="#" data-toggle="tabobs" data-target="#tbobs">Obs. Carga</a>
                            </li>
                            <li>
                                <a href="#" data-toggle="tabobs" data-target="#tbobsdesc">Obs. Descarga</a>
                            </li>
                        @else
                            <li class="active">
                                <a href="#" data-toggle="tabobs" data-target="#tbobs">
                                    Obs. Entrega
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="#" data-toggle="tabobs" data-target="#tbobsint">Notas Internas
                                @if($shipment->obs_internal)
                                    <small><small><i class="fas fa-fw fa-circle text-yellow"></i></small></small>
                                @endif
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-obs active" id="tbobs">
                            {{ Form::textarea('obs', null, ['class' => 'form-control obs', 'rows' => '2', 'maxlength' => 150, 'modal-addr' => '.input-obs']) }}
                        </div>
                        @if($appMode == 'cargo')
                            <div role="tabpanel" class="tab-obs" id="tbobsdesc">
                                {{ Form::textarea('obs_delivery', null, ['class' => 'form-control obs-dlvr', 'rows' => '2', 'maxlength' => 150, 'modal-addr' => '.input-obs-dlvr']) }}
                            </div>
                        @endif
                        <div role="tabpanel" class="tab-obs" id="tbobsint">
                            {{ Form::textarea('obs_internal', null, ['class' => 'form-control obs-internal', 'rows' => '2', 'maxlength' => 255]) }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-sm-6" style="padding-left: 17px;padding-right: 11px;margin-left: 8px;">
                @if(Setting::get('shipments_show_price_per_ton'))
                    <div class="form-group form-group-sm m-b-5">
                        <label class="col-sm-3 control-label m-l-0 p-0" style="margin-left: -6px; margin-right: 6px">Preço/Kg</label>
                        <div class="col-sm-4 p-l-5 p-r-0">
                            <div class="input-group">
                                {{ Form::text('price_kg', null, ['class' => 'form-control decimal']) }}
                            </div>
                        </div>
                        <div class="col-sm-5 p-l-0 eurkg">
                            {{ Form::select('x', ['' => '€/Ton', 'kg' => '€/Kg'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                @else
                    <div class="form-group form-group-sm m-b-5">
                        <label class="col-sm-3 control-label p-r-0">Portes</label>
                        <div class="col-sm-9 p-l-5">
                            @if(!Auth::user()->allowedAction('edit_prices'))
                                <div style="background: rgba(0,0,0,0.08); position: absolute;left:5px;right:16px;top:0;bottom:0;z-index:10;"></div>
                            @endif
                            {{ Form::select('cod', ['' => 'Cliente', 'D' => 'Destino', 'S' => 'Remetente', 'P' => 'Pagos (excluir faturação)'], null, ['class' => 'form-control select2 trigger-price']) }}
                        </div>
                    </div>
                @endif
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-3 control-label lbl-expenses">
                        Taxas <i class="fas fa-spin fa-circle-notch hide loading-expenses" style="position: absolute; margin-top: 4px;"></i>
                    </label>
                    <div class="col-sm-9 p-l-5">
                        @if(!Auth::user()->allowedAction('edit_prices'))
                            <div style="background: rgba(0,0,0,0.08); position: absolute;left:5px;right:16px;top:0;bottom:0;z-index:10;"></div>
                        @endif
                        <div class="input-group input-group-money">
                            @if(!hasPermission('show_prices'))
                                {{ Form::text('', 'N/A', ['class' => 'form-control', 'readonly', 'style' => 'background:white;' ]) }}
                                {{ Form::hidden('expenses_price') }}
                            @else
                                {{ Form::text('expenses_price', null, ['class' => 'form-control', 'readonly', 'style' => 'background:white;' ]) }}
                            @endif
                            <div class="input-group-addon" style="margin-right: 32px;">{{ $shipment->currency }}</div>
                            <div class="input-group-btn btn-group-sm">
                                @if($shipment->invoice_doc_id)
                                    <button type="button" class="btn btn-default" disabled data-toggle="tooltip">
                                        <i class="fas fa-plus" style="font-size: 10px;padding: 4px 1px;"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-default btn-expenses-enabled" data-target="#modal-shipment-expenses" data-toggle="tooltip" title="Inserir ou Editar Encargos Adicionais do Envio">
                                        <i class="fas fa-plus" style="font-size: 10px;padding: 4px 1px;"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-sm-6" style="margin-left: -8px;">
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-3 control-label p-0">Custo</label>
                    <div class="col-sm-9 p-l-0">
                        <div class="input-group lbl-cost">
                            <div class="input-group input-group-money p-l-5">
                                @if(!hasPermission('show_cost_prices'))
                                    {{ Form::text('', 'N/A', ['class' => 'form-control', 'readonly']) }}
                                    {{ Form::hidden('cost_shipping_price') }}
                                    <div class="input-group-addon">{{ $shipment->currency }}</div>
                                @else
                                    @if(!Auth::user()->allowedAction('edit_prices'))
                                        <div style="background: rgba(0,0,0,0.08); position: absolute;left: 5px;right: 33px;top: 0;bottom: 0;z-index: 10;"></div>
                                    @endif
                                    {{ Form::text('cost_shipping_price', $shipment->cost_shipping_price ?? '0.00', ['class' => 'form-control decimal', 'maxlength' => 12, 'required']) }}
                                    <div class="input-group-addon" style="padding: 6px 10px 5px 0; right: 33px;">{{ $shipment->currency }}</div>
                                    <div class="input-group-btn">
                                        <button type="button"
                                                class="btn btn-sm btn-default p-t-4 p-b-4 btn-compare-prices"
                                                data-toggle="tooltip" title="Ver comparação de custos">
                                            <i class="fas fa-coins"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-0">
                    <label class="col-sm-3 control-label p-0">Frete</label>
                    <div class="col-sm-9 p-l-5">
                        <div class="input-group lbl-price">
                            @if(!$shipment->invoice_id)
                                <div class="price-lock {{ $shipment->price_fixed ? 'active' : '' }} {{ hasPermission('show_prices') ? '' : 'hide' }}">
                                    <i class="fas fa-lock"></i>
                                </div>
                            @endif
                            <span>
                                @if(!hasPermission('show_prices'))
                                    {{ Form::text('', 'N/A', ['class' => 'form-control', 'readonly', 'style' => 'border-right: 0']) }}
                                    {{ Form::hidden('shipping_price', null, ['class' => 'shpprc']) }}
                                @else
                                    @if(!Auth::user()->allowedAction('edit_prices'))
                                        <div style="background: rgba(0,0,0,0.08); position: absolute;left: 0;right: 0;top: 0;bottom: 0;z-index: 10;"></div>
                                    @endif
                                    @if($shipment->invoice_id)
                                        {{ Form::text('shipping_price', null, ['class' => 'form-control shpprc decimal', 'readonly']) }}
                                    @else
                                        {{ Form::text('shipping_price', $shipment->shipping_price ?? '0.00', ['class' => 'form-control shpprc decimal', 'maxlength' => 10, 'required']) }}
                                    @endif
                                @endif
                                {{ Form::hidden('base_price') }}
                            </span>
                            <div class="input-group-addon" style="{{ hasPermission('show_prices') ? '' : 'background: #eeeeee' }}">{{ $shipment->currency }}</div>
                            <div class="input-group-btn {{ hasPermission('show_prices') ? '' : 'hide' }}">
                                @if($shipment->invoice_id)
                                    <button type="button" class="btn btn-sm btn-default p-t-4 p-b-4" disabled>
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-default p-t-4 p-b-4 btn-refresh-prices">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-obs textarea {
        height: 45px !important;
    }

    .form-group-shipper {
        border-bottom: 1px solid #ccc;
        padding: 2px;
        margin: -10px -10px 5px -10px !important;
        background: #eee;
        cursor: pointer
    }

    .form-group-shipper :hover input {
        color: #0094ff;
    }
    .form-group-shipper label {
        margin: 2px -6px;
    }
    .form-group-shipper input {
        border: none;
        background: transparent;
        cursor: pointer;
    }

    .form-group-shipper .btn-edit-shipper {
        position: absolute;
        right: 8px;
        font-size: 11px;
        padding: 9px 8px;
        z-index: 1;
    }
</style>