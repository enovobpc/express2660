<div class="row">
    <div class="col-sm-6 col-customer">
         {{ Form::select('agency_id', (count($userAgencies) > 1) ? $userAgencies : $userAgencies, null, ['class' => 'form-control trigger-price hide']) }}
        <div class="row row-0">
            <div class="col-sm-12 {{ empty($departments) ? '' : 'has-department' }} select-customer">
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('customer_id', 'Cliente', ['class' => 'col-sm-1 control-label']) }}
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
                    <div class="col-sm-12 p-l-0 m-l-3">
                        {{ Form::select('department_id', empty($departments) ? [] : ['' => ''] + $departments, null, ['class' => 'form-control select2', 'data-placeholder' => 'Departamento...']) }}
                    </div>
                </div>
            </div>
        </div>
        @if(Setting::get('shipments_reference3'))
            <div class="form-group form-group-sm grp-ref m-b-5">
                <label class="col-sm-1 control-label p-r-0" for="recipient_attn">Referência</label>
                <div class="col-sm-3 p-r-0">
                    {{ Form::text('reference', null, ['class' => 'form-control input-sm', 'maxlength' => Setting::get('shipment_ref_maxsize', 15)]) }}
                </div>
                <label class="col-sm-1 p-0 control-label label-ref-2" style="width: 8%">Ref#2</label>
                <div class="col-sm-3 p-l-3">
                    {{ Form::text('reference2', null, ['class' => 'form-control input-sm', 'placeholder' => Setting::get('shipments_reference2_name')]) }}
                </div>
                <label class="col-sm-1 p-0 control-label label-ref-3" style="width: 6%">Ref#3</label>
                <div class="col-sm-4 p-l-0 m-l-3" style="width: 24%;">
                    {{ Form::text('reference3', null, ['class' => 'form-control input-sm', 'placeholder' => Setting::get('shipments_reference3_name')]) }}
                </div>
            </div>
        @elseif(Setting::get('shipments_requester_name'))
            <div class="form-group form-group-sm grp-ref m-b-5">
                <label class="col-sm-1 control-label p-r-0" for="recipient_attn"> Referência</label>
                <div class="col-sm-3 p-r-0">
                    {{ Form::text('reference', null, ['class' => 'form-control input-sm', 'maxlength' => Setting::get('shipment_ref_maxsize', 15)]) }}
                </div>
                <label class="col-sm-1 p-0 control-label label-ref-2" style="width: 8%">Ref#2</label>
                <div class="col-sm-3 p-l-3">
                    {{ Form::text('reference2', null, ['class' => 'form-control input-sm']) }}
                </div>
                <label class="col-sm-1 p-0 control-label label-ref-3">Pedido</label>
                <div class="col-sm-4 p-l-0 m-l-3 requestby">
                    {{ Form::text('requester_name', null, ['class' => 'form-control input-sm']) }}
                </div>
            </div>
        @else
            <div class="row row-0">
                <div class="col-sm-6">
                    <div class="form-group form-group-sm grp-ref m-b-5">
                        {{ Form::label('reference', 'Referência', ['class' => 'col-sm-3 control-label p-r-0', 'syle' => '']) }}
                        <div class="col-sm-8 p-l-0 m-l-3" style="padding-right: 15px;">
                            {{ Form::text('reference', null, ['class' => 'form-control input-sm', 'maxlength' => Setting::get('shipment_ref_maxsize', 15)]) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group form-group-sm m-b-5">
                        {{ Form::label('reference2', Setting::get('shipments_reference2_name') ?? 'Referência 2', ['class' => 'col-sm-3 control-label p-r-0 m-l-3 p-l-0 m-r-5']) }}
                        <div class="col-sm-8 p-l-0">
                            {{ Form::text('reference2', null, ['class' => 'form-control input-sm']) }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="col-sm-3">
        <div class="form-group form-group-sm m-b-5">
            {{ Form::label('service_id', 'Serviço', ['class' => 'col-sm-3 control-label p-r-0']) }}
            <div class="col-sm-9">
                {!! Form::selectWithData('service_id', $services, @$shipment->service_id, ['class' => 'form-control select2 trigger-price', 'required']) !!}
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            <label class="col-sm-3 control-label p-0">
                Fornecedor
            </label>
            <div class="col-sm-9">
                {!! Form::selectWithData('provider_id', $providers, $shipment->exists ? null : '', ['class' => 'form-control select2 trigger-price', 'required']) !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group form-group-sm m-b-5">
            {{ Form::label('date', 'Data', ['class' => 'col-sm-2 control-label p-r-0 p-l-0']) }}
            <div class="col-sm-10 p-l-5">
                {{ Form::text('date', empty($shipment->date) ? date('Y-m-d') : null, ['class' => 'form-control datepicker trigger-price']) }}
            </div>
        </div>
        <div class="form-group form-group-sm m-b-10">
            {{ Form::label('recipient_email', 'Hora', ['class' => 'col-sm-2 control-label p-r-0 p-l-0']) }}
            <div class="col-sm-10 p-l-5">
                <div class="input-group input-group-sm">
                    <div style="width: 91px">
                        {{ Form::select('start_hour', ['' => ''] + $hours, null, ['class' => 'form-control select2 trigger-price', 'data-placeholder' => 'Início']) }}
                    </div>
                    <span class="input-group-addon" style="border: none">até</span>
                    <div style="width: 91px">
                        {{ Form::select('end_hour', ['' => ''] + $hours, null, ['class' => 'form-control select2 trigger-price', 'data-placeholder' => 'Fim']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="box box-default box-solid m-b-0">
            <div class="box-header with-border" style="padding: 5px 10px">
                <h4 class="box-title"><i class="fas fa-sign-in-alt"></i>
                    Local Recolha
                </h4>
            </div>
            <div class="box-body p-10" id="box-sender">
                @include('admin.shipments.shipments.partials.edit.sender_block')
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="box box-default box-solid m-b-0">
            <div class="box-header with-border" style="padding: 5px 10px">
                <h4 class="box-title">
                    <h4 class="box-title"><i class="fas fa-sign-in-alt"></i>
                        Local Entrega
                    </h4>
                </h4>
                <ul class="address-tabs">
                    <li data-action="add-addr">
                        <b data-toggle="tooltip"
                           data-placement="right"
                           title="Adicionar endereço de entrega">&nbsp;+&nbsp;</b>
                    </li>
                </ul>
            </div>
            <div class="box-body p-10" id="box-recipient">
                @include('admin.shipments.shipments.partials.edit.recipient_block')
            </div>
        </div>
    </div>
</div>
<div class="sp-10"></div>
<div class="row">
    <div class="col-sm-6">
        <div class="row">

            <div class="col-sm-4 p-r-0">
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
                            {{ Form::text('volumes', null, ['class' => 'form-control trigger-price int vol', 'maxlength' => 6, 'required', 'autocomplete' => 'field-1']) }}
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
                            {{ Form::text('weight', null, ['class' => 'form-control trigger-price decimal kg', 'maxlength' => 8, (($shipment->exists && @$shipment->service->unity == 'm3'))  ? '' : 'required', $shipment->exists && @$shipment->service->unity == 'pallet' ? 'readonly' : '', 'autocomplete' => 'field-1']) }}
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
                            {{ Form::text('ldm', null, ['class' => 'form-control trigger-price decimal', 'maxlength' => 7, $ldmRequired ? 'required' : '']) }}
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
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('volumetric_weight', 'Peso Vol.', ['class' => 'col-sm-4 control-label p-r-0']) }}
                    <div class="col-sm-8">
                        <div class="input-group input-group-money">
                            {{ Form::text('volumetric_weight', null, ['class' => 'form-control decimal vkg', 'maxlength' => 7, 'readonly']) }}
                            <div class="input-group-addon">kg</div>
                        </div>
                        {{ Form::hidden('fator_m3', @$shipment->fator_m3, ['class' => 'trigger-price']) }}
                        {{-- Form::hidden('fator_m3', ($shipment->exists && $shipment->volumetric_weight > 0 && ($shipment->fator_m3 == "" || $shipment->fator_m3 == 0.00)) ? $shipment->volumetric_weight / 167 : null) --}}
                    </div>
                </div>
                <div class="form-group form-group-sm form-group-kms m-b-5">
                    {{ Form::label('kms', 'Kms', ['class' => 'col-sm-4 control-label p-r-0']) }}
                    <div class="col-sm-8">
                        <div class="input-group">
                            {{ Form::text('kms', $shipment->exists && @$shipment->service->unity == 'km' && $shipment->kms ? null : '0', ['class' => 'form-control trigger-price decimal', 'maxlength' => 7, 'autocomplete' => 'field-1']) }}
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
            {{--<div class="col-sm-5">
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('volumes', 'Volumes', ['class' => 'col-sm-4 control-label p-r-0']) }}
                    <div class="col-sm-8">
                        <div class="input-group">
                            {{ Form::text('volumes', ($shipment->exists && $shipment->volumes == 0) ? '' : null, ['class' => 'form-control nospace number', 'maxlength' => 4]) }}
                            <div class="input-group-btn btn-group-sm" data-toggle="tooltip" title="Inserir Dimensões dos Volumes ou Paletes">
                                <button type="button" class="btn btn-default btn-set-dimensions" data-target="#modal-shipment-dimensions" style="display: {{ $shipment->exists && @$shipment->service->unity == 'pallet' ? 'none' : '' }}">
                                    <img src="{{ asset('assets/img/default/shipment_dimensions.svg') }}" style="height: 20px;margin: -3px;"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-5">
                    {{ Form::label('weight', 'Peso', ['class' => 'col-sm-4 control-label p-0']) }}
                    <div class="col-sm-8">
                        <div class="input-group">
                            {{ Form::text('weight', null, ['class' => 'form-control nospace decimal', 'maxlength' => 6]) }}
                            <div class="input-group-addon" style="padding: 0 14px;">Kg</div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-0" style="display:none">
                    {{ Form::label('guide_required', 'Levar Guia', ['class' => 'col-sm-4 control-label p-0']) }}
                    <div class="col-sm-8">
                        {{ Form::select('guide_required', ['' => 'Não', '1' => 'Sim'], null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>--}}
            <div class="col-sm-8">
                <div class="form-group form-group-sm m-b-5">
                    <label class="col-sm-2 control-label p-l-0 p-r-5">Obs. Recolha</label>
                    <div class="col-sm-10 p-l-0">
                        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Observações para recolha', 'style' => 'max-height:45px', 'maxlength' => 250]) }}
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-0">
                    <label class="col-sm-2 control-label p-l-0 p-r-5">Obs.<br/> Entrega</label>
                    <div class="col-sm-10 p-l-0">
                        {{ Form::textarea('obs_delivery', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Observações de entrega', 'style' => 'max-height:45px', 'maxlength' => 250]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="row">
            <div class="col-sm-5">
                <div class="form-group form-group-sm m-b-5" data-toggle="tooltip" title="Valor de adiantamento a pagar no ato da recolha">
                    {{ Form::label('total_price_when_collecting', 'Adiantamento', ['class' => 'col-sm-5 control-label p-r-0']) }}
                    <div class="col-sm-7">
                        <div class="input-group">
                            {{ Form::text('total_price_when_collecting', null, ['class' => 'form-control nospace decimal', 'maxlength' => 7, 'style' => 'border-right: 0;']) }}
                            <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm  m-b-5">
                    {{ Form::label('charge_price', 'Cobrança', ['class' => 'col-sm-5 control-label p-r-0']) }}
                    <div class="col-sm-7">
                        <div class="input-group">
                            {{ Form::text('charge_price', $shipment->charge_price == 0.00 ? '' : null, ['class' => 'form-control nospace decimal', 'maxlength' => 7, 'style' => 'border-right: 0;']) }}
                            <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm m-b-0">
                    {{ Form::label('insurance_price', 'Valor Seguro', ['class' => 'col-sm-5 control-label p-r-0']) }}
                    <div class="col-sm-7">
                        <div class="input-group">
                            {{ Form::text('insurance_price', null, ['class' => 'form-control nospace decimal', 'maxlength' => 7, 'style' => 'border-right: 0;']) }}
                            <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="complementar-services1" style="margin: 0 0 0 -10px; max-height: 93px; overflow-y: auto;">
                    <div class="row row-0">
                        <div class="col-sm-6">
                            <div class="checkbox">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('has_return[]', 'rpack', null, ['class' => 'trigger-price']) }}
                                    Com Retorno
                                </label>
                            </div>
                        </div>
                        @if($complementarServices)
                            <?php $optFields = json_decode($shipment->optional_fields, true); ?>
                            @foreach($complementarServices as $service)
                                <div class="col-sm-6">
                                    <div class="checkbox">
                                        <label style="padding-left: 0">
                                            {{ Form::checkbox('optional_fields['.$service->id.']', $service->value ? $service->value : 1, @$optFields[$service->id], ['data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'qty']) }}
                                            {{ $service->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="row m-t-5">
            <div class="col-sm-4 col-sm-offset-1">
                <div class="form-group form-group-sm m-b-0">
                    {{ Form::label('cost', 'Custo', ['class' => 'col-sm-4 control-label p-r-10']) }}
                    <div class="col-sm-8 p-l-2">
                        <div class="input-group input-group-money">
                            @if(!Auth::user()->can('show_cost_prices'))
                                {{ Form::text('', 'N/A', ['class' => 'form-control', 'readonly']) }}
                                {{ Form::hidden('cost_shipping_price') }}
                                <div class="input-group-addon">{{ $shipment->currency }}</div>
                            @else
                                {{ Form::text('cost_shipping_price', null, ['class' => 'form-control decimal', 'maxlength' => 12, 'required', Auth::user()->allowedAction('edit_prices') ? '' : 'readonly']) }}
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
            <div class="col-sm-7">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group form-group-sm m-b-0">
                            <label class="col-sm-2 control-label m-l-0 p-l-5" for="total_price">Preço</label>
                            <div class="col-sm-10 p-r-0" style="margin-left: 0px; margin-right: -5px;">
                                {{--<div class="input-group">
                                    <span data-toggle="tooltip" >
                                        @if($shipment->invoice_doc_id)
                                            {{ Form::text('shipping_total', null, ['class' => 'form-control', 'readonly']) }}
                                        @else
                                            {{ Form::text('shipping_total', null, ['class' => 'form-control nospace decimal', 'maxlength' => 7, 'required', Auth::user()->allowedAction('edit_prices') ? '' : 'readonly', 'style' => 'border-right: 0;']) }}
                                        @endif
                                    </span>
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-sm btn-default p-t-4 p-b-4 btn-refresh-prices">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="helper-default-price italic text-blue" style="display: none">
                                    <small><i class="fas fa-info-circle"></i> Taxa P.V.P.</small>
                                </div>
                                <div class="helper-zone-not-alowed italic text-red" style="display: none">
                                    <small><i class="fas fa-exclamation-triangle"></i> O serviço selecionado não está disponível para o país do destinatário.</small>
                                </div>--}}
                                <div class="input-group lbl-price">
                                    @if(!$shipment->invoice_id)
                                        <div class="price-lock {{ $shipment->price_fixed ? 'active' : '' }}">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                    @endif
                                    <span>
                                @if($shipment->invoice_id)
                                            {{ Form::text('shipping_price', null, ['class' => 'form-control shpprc decimal', 'readonly']) }}
                                        @else
                                            {{ Form::text('shipping_price', null, ['class' => 'form-control shpprc decimal', 'maxlength' => 10, 'required', Auth::user()->allowedAction('edit_prices') ? '' : 'readonly']) }}
                                        @endif
                                        {{ Form::hidden('base_price') }}
                            </span>
                                    <div class="input-group-addon">{{ $shipment->currency }}</div>
                                    <div class="input-group-btn">
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
                    <div class="col-sm-6">
                        <div class="form-group form-group-sm m-b-0">
                            <label class="col-sm-3 control-label m-l-0 p-l-10" for="total_price" data-toggle="tooltip" title="Preço do Envio após recolha">P.Envio</label>
                            <div class="col-sm-9" style="margin-left: 0; margin-right: -5px;">
                                <div class="input-group">
                                            <span data-toggle="tooltip" >
                                                @if($shipment->invoice_doc_id)
                                                    {{ Form::text('total_price_after_pickup', null, ['class' => 'form-control', 'readonly']) }}
                                                @else
                                                    {{ Form::text('total_price_after_pickup', $shipment->total_price_after_pickup > 0.00 ? null : '', ['class' => 'form-control nospace decimal', 'maxlength' => 7, Auth::user()->allowedAction('edit_prices') ? '' : 'readonly', 'placeholder' => 'Auto', 'style' => 'border-right: 0;']) }}
                                                @endif
                                            </span>
                                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{--@if(!$shipment->exists || ($shipment->exists && $shipment->customer))
                <div class="col-sm-7">

                    <div class="form-group form-group-sm m-b-0">
                        <label class="col-sm-2 control-label m-l-0 p-l-10" for="total_price" data-toggle="tooltip" title="Preço do Envio após recolha">P.Envio</label>
                        <div class="col-sm-10" style="margin-left: 5px; margin-right: -5px;">
                            <div class="input-group">
                                        <span data-toggle="tooltip" >
                                            @if($shipment->invoice_doc_id)
                                                {{ Form::text('total_price_after_pickup', null, ['class' => 'form-control', 'readonly']) }}
                                            @else
                                                {{ Form::text('total_price_after_pickup', null, ['class' => 'form-control nospace decimal', 'maxlength' => 7, Auth::user()->allowedAction('edit_prices') ? '' : 'readonly', 'placeholder' => 'Auto']) }}
                                            @endif
                                        </span>
                                <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif--}}
        </div>
    </div>
</div>
<div class="sp-5"></div>