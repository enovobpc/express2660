{{ Form::model($shipment, $formOptions) }}
<?php
$hashId = str_random(8);

$appCountry = Setting::get('app_country');

$nonEU  = !empty($shipment->recipient_country) && !in_array($shipment->recipient_country, trans('countries_eu'));

if(!in_array(Setting::get('app_country'), trans('countries_eu'))) {
    $nonEU = true;
}


$recipientEmailRequired = '';
$obsPlaceholder = '';
$obsLabel = trans('account/global.word.obs-abrv');
if(config('app.source') == 'hunterex') {
    $obsLabel    = 'Conteudo Caixa';
    $obsPlaceholder = 'Escreva realmente o material que vai enviar no seu frete.';
    $recipientEmailRequired = 'required';
}

$showDischargeObs = false;
if(Setting::get('app_mode') == 'cargo') {
    $showDischargeObs = true;
    $obsLabel = 'Obs Carga';
}

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title text-white">{{ $action }}</h4>
</div>
<div class="modal-body p-l-15 p-t-15 p-r-15 p-b-10 modal-shipment" id="modal-{{ $hashId }}">
    <div class="row row-5 shipment-top-header">
        @if(!empty($services))
        <div class="col-sm-3 col-md-2">
            <div class="form-group m-b-5 p-0 services-shipments">
                {{ Form::label('service', trans('account/global.word.service'), ['class' => 'col-sm-2 col-md-3 control-label p-0']) }}
                <div class="col-sm-10 col-md-9" style="padding-right: 15px;">
                    {!! Form::selectWithData('services', $services, $shipment->exists ? @$shipment->service_id : (!empty($customer->default_service) ? $customer->default_service : Setting::get('customers_default_service')), ['class' => 'form-control select2 trigger-price', 'required']) !!}
                </div>
            </div>
        </div>
        @endif

        @if(!empty($providers))
            @if(count($providers) == 1)
                <div class="hide">
                    {{ Form::select('provider_id', $providers) }}
                </div>
            @else
                <div class="col-sm-2">
                    <div class="form-group m-b-5 p-0">
                        {{ Form::label('provider_id', trans('account/global.word.provider'), ['class' => 'col-sm-3 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            {{ Form::select('provider_id', ['' => 'Automatico'] + $providers, null, ['class' => 'form-control select2 trigger-price', 'data-toggle'=> 'tooltip', 'title' => trans('account/shipments.modal-shipment.tips.ref')]) }}
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <div class="col-sm-3 col-md-2">
            <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="{{ trans('account/shipments.modal-shipment.tips.ref') }}">
                {{ Form::label('reference', trans('account/global.word.reference'), ['class' => 'col-sm-4 control-label p-r-0']) }}
                <div class="col-sm-8">
                    {{ Form::text('reference', null, ['class' => 'form-control', 'maxlength' => 15]) }}
                </div>
            </div>
        </div>
        <div class="col-sm-1 col-md-2">
            @if(Setting::get('shipments_reference2_visible'))
                <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="{{ Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : '' }}">
                    {{ Form::label('reference2', Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : 'Ref #2', ['class' => 'col-sm-4 control-label p-r-0']) }}
                    <div class="col-sm-8">
                        {{ Form::text('reference2', null, ['class' => 'form-control', 'maxlength' => 15]) }}
                    </div>
                </div>
            @else
                &nbsp;
            @endif
        </div>

        @if(empty($services))
            <div class="hidden-sm col-sm-3 col-md-2"></div>
        @endif

        @if(empty($providers) || count($providers) <= 1)
            <div class="hidden-sm col-md-1 col-lg-2"></div>
        @endif

        @if(!Setting::get('customers_shipment_hours'))
            <div class="col-sm-2 col-md-1 col-lg-2"></div>
        @endif
        <div class="col-sm-2 col-md-2 col-lg-2">
            <div class="form-group m-b-5">
                {{ Form::label('date', trans('account/global.word.date'), ['class' => 'col-lg-3 col-sm-2 control-label p-0']) }}
                <div class="col-sm-10 col-lg-8 p-r-0 p-l-5">
                    <div class="input-group">
                        {{ Form::text('date', $shipment->exists ? null : $shipmentDate, ['class' => 'form-control datepicker trigger-price dynamic-tooltip shipment-date', 'required', 'autocomplete' => 'off']) }}
                        <span class="input-group-addon">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @if(Setting::get('customers_shipment_hours'))
            <div class="col-sm-3 col-md-3 col-lg-2">
                <div class="form-group m-b-5">
                    {{ Form::label('hour', trans('account/global.word.hour'), ['class' => 'col-sm-3 col-lg-2 control-label p-0']) }}
                    <div class="col-sm-9 col-lg-10 p-r-0 p-l-5">
                        <div class="input-group">
                            <div class="dynamic-tooltip shipment-start-hour" style="float: left; width: 64px">
                                {{ Form::select('start_hour_pickup', ['' => ''] + $hours, null, ['class' => 'form-control trigger-price select2', Setting::get('customers_shipment_hours_required') ? 'required' : '']) }}
                            </div>
                            <div style="float: left;width: 23px;height: 32px; font-size: 12px; margin: 0 -1px;padding: 8px 2px;background: #ccc;color: #333;">
                                {{ trans('account/global.word.to') }}
                            </div>
                            <div class="dynamic-tooltip shipment-end-hour" style="float: left; width: 64px">
                                {{ Form::select('end_hour_pickup', ['' => ''] + $hours, null, ['class' => 'form-control trigger-price select2', Setting::get('customers_shipment_hours_required') ? 'required' : '']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="row">
        @if($exceeded)
            <div class="col-xs-12">
                <div class="alert alert-info m-b-15">
                    <i class="fas fa-info-circle"></i> {{ trans('account/shipments.modal-shipment.tips.hour-exceeded', ['date' => $shipmentDate]) }}
                </div>
            </div>
        @else
            <div class="col-xs-12">
                <div class="alert alert-info m-b-15" id="alert-date" style="display: none"></div>
            </div>
        @endif
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-default m-b-0">
                <div class="panel-heading">
                    @if($shipment->type == \App\Models\Shipment::TYPE_RETURN)
                    <h4 class="panel-title">{{ trans('account/shipments.modal-shipment.recipient-block') }}</h4>
                    @else
                    <h4 class="panel-title">{{ trans('account/shipments.modal-shipment.sender-block') }}</h4>
                    @endif
                    @if(Setting::get('account_toggle_sender'))
                    <span class="toggle-sender" data-toggle="tooltip" title="{{ trans('account/shipments.modal-shipment.tips.toggle-sender') }}">
                        <i class="fas fa-exchange-alt"></i>
                    </span>
                    @endif
                </div>
                <div class="box-body panel-body p-10 p-b-3 bg-gray-light" id="box-sender">
                    @if($shipment->exists && $shipment->is_collection)
                    @include('account.shipments.partials.edit.recipient_block')
                    @else
                    @include('account.shipments.partials.edit.sender_block')
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-default m-b-0">
                <div class="panel-heading">
                    @if($shipment->type == \App\Models\Shipment::TYPE_RETURN)
                    <h4 class="panel-title">{{ trans('account/shipments.modal-shipment.sender-block') }}</h4>
                    @else
                    <h4 class="panel-title">{{ trans('account/shipments.modal-shipment.recipient-block') }}</h4>
                    @endif
                </div>
                <div class="box-body panel-body p-10 p-b-3 bg-gray-light" id="box-recipient">
                    @if($shipment->exists && $shipment->is_collection)
                    @include('account.shipments.partials.edit.sender_block')
                    @else
                    @include('account.shipments.partials.edit.recipient_block')
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="m-t-20"></div>
    <div class="row row-10">
        <div class="col-xs-12 col-sm-8">
            <div class="row">
                <div class="col-xs-12 col-sm-4" style="width: 30%;margin-right: 15px; padding: 0">
                    <div class="form-group m-b-5">
                        {{ Form::label('volumes', trans('account/global.word.volumes'), ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                {{ Form::text('volumes', null, ['class' => 'form-control number trigger-price', 'maxlength' => 4,'required', 'data-toggle'=> 'tooltip', 'autocomplete' => 'off']) }}
                                <div class="input-group-btn">
                                    <button type="button"
                                            class="btn btn-default"
                                            data-toggle="tooltip"
                                            title="{{ trans('account/shipments.modal-shipment.tips.dimensions') }}"
                                            data-target="#modal-shipment-dimensions">
                                        <img src="{{ asset('assets/img/default/shipment_dimensions.svg') }}" style="height: 23px;line-height: 2px; margin-top: -4px;margin-left: -4px;margin-right: -4px;"/>
                                    </button>
                                </div>
                            </div>
                            <div class="helper-max-volumes italic text-red line-height-1p0" style="display: none">
                                <small><i class="fas fa-info-circle"></i> {!! trans('account/shipments.modal-shipment.tips.max-volumes') !!}</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('weight', trans('account/global.word.weight'), ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="weight-col {{ !empty($shipment->volumetric_weight) ? 'col-sm-5' : 'col-sm-8' }}">
                            <div class="input-group">
                                {{ Form::text('weight', null, ['class' => 'form-control decimal trigger-price', 'maxlength' => 7, 'required', 'data-toggle' => 'tooltip', 'title' => trans('account/shipments.modal-shipment.tips.weight'), 'autocomplete' => 'off']) }}
                                <div class="input-group-addon">kg</div>
                            </div>
                            {{--<div class="helper-max-weight italic text-red line-height-1p0" style="display: none">
                                <small><i class="fas fa-info-circle"></i> {!! trans('account/shipments.modal-shipment.tips.max-weight') !!}</small>
                            </div>--}}
                        </div>
                        <div class="col-sm-3 helper-volumetric-weight" style="{{ !empty($shipment->volumetric_weight) ? '' : 'display:none;' }} padding: 0; margin-left: -3px; font-size: 12px; color: #0c82ff;">
                            <p class="m-0">
                                <small>{{ trans('account/global.word.volumetric') }}</small>
                                <br/>
                                <b>{{ money($shipment->volumetric_weight) }}</b> kg
                            </p>
                        </div>
                    </div>
                    <?php $ldm = (Setting::get('app_mode') == 'freight' || Setting::get('app_mode') == 'cargo' || ($shipment->exists && @$shipment->service->unity == 'ldm')); ?>
                    <div class="form-group input-ldm m-b-" style="{{ $ldm ? '' : 'display: none' }}">
                        {{ Form::label('ldm', 'LDM', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                {{ Form::text('ldm', null, ['class' => 'form-control decimal trigger-price', 'maxlength' => 7, 'autocomplete' => 'off']) }}
                                <div class="input-group-addon" style="padding: 5px 9px 5px 10px;">mt</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group input-km m-b-0 m-t-5" style="{{ @$shipment->service->unity == 'km' ? 'display: block' : 'display: none'}}">
                        {{ Form::label('_kms', 'Distância', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            <div class="overflow-inpt" style="position: absolute;background: #fff;
                                    opacity: 0;
                                    left: 0;
                                    right: 34px;
                                    top: 0;
                                    bottom: 0;
                                    z-index: 10;
                                    cursor: not-allowed;"></div>
                            <div class="input-group">
                                {{ Form::text('kms', null, ['class' => 'form-control decimal trigger-price', 'maxlength' => 7, 'autocomplete' => 'off', 'style' => 'border-right: 0']) }}
                                <div class="input-group-addon" style="padding: 5px 9px 5px 10px;">km</div>
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-sm btn-default btn-auto-km" data-toggle="tooltip" title="Re-calcular distância">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-5">
                    <div class="opt-flds-grp account-complementar-services {{ ($complementarServices->count() + Setting::get('customer_shipment_without_pickup') +  Setting::get('app_rpack')+Setting::get('shipments_show_assembly')) >= 4 ? 'vsb' : '' }}">
                        @if(in_array(config('app.source'), ['aveirofast', 'velozrotina', 'gigantexpress']))
                            <div class="checkbox required">
                                <label>
                                    <input name="" type="checkbox" required class="custom-control-input form-check-input">
                                    <a href="{{ route('legal.show') }}/condicoes-servico" target="_blank"> Aceito as condições gerais do serviço</a>
                                </label>
                            </div>
                        @endif
                        @if($complementarServices->isEmpty())
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
                        @else
                            @if($allInputAreCheckboxes)
                                {{-- Todos os input são checkboxes --}}
                                <table class="w-100 tbl-all-chkbx">
                                    @if(Setting::get('customer_shipment_without_pickup'))
                                        <tr>
                                            <td class="tdinpt input-sm">{{ Form::checkbox('without_pickup', '1') }}</td>
                                            <td>
                                                <label for="optional_fields[]">Sem recolha {!! tip('CLIENTE LEVA AO ARMAZÉM. Assinale esta opção se optar por entregar os volumes diretamente no armazém') !!}</label>
                                            </td>
                                        </tr>
                                    @endif

                                    @if(Setting::get('app_rpack'))
                                        <tr>
                                            <td class="tdinpt input-sm">{{ Form::checkbox('has_return[]', 'rpack', null, ['class' => 'trigger-price']) }}</td>
                                            <td><label for="has_return[]">Retorno Encomenda</label></td>
                                        </tr>
                                    @endif

                                    <tr class="{{ Setting::get('shipments_show_assembly') ? : 'hide' }}">
                                        <td class="tdinpt input-sm">{{ Form::checkbox('has_assembly', '1') }}</td>
                                        <td><label for="has_assembly">Serviço Montagem</label></td>
                                    </tr>
                                    @include('account.shipments.partials.edit.complementar_services')
                                </table>
                            @else
                                <table class="w-100">
                                    @if(Setting::get('customer_shipment_without_pickup'))
                                        <tr>
                                            <td>
                                                <label for="optional_fields[]">Sem recolha {!! tip('CLIENTE LEVA AO ARMAZÉM. Assinale esta opção se optar por entregar os volumes diretamente no armazém') !!}</label>
                                            </td>
                                            <td class="tdinpt input-sm">{{ Form::checkbox('without_pickup', '1') }}</td>
                                        </tr>
                                    @endif

                                    @if(Setting::get('app_rpack'))
                                        <tr>
                                            <td><label for="optional_fields[]">Retorno Encomenda</label></td>
                                            <td class="tdinpt input-sm">{{ Form::checkbox('has_return[]', 'rpack', null, ['class' => 'trigger-price']) }}</td>
                                        </tr>
                                    @endif

                                    <tr class="{{ Setting::get('shipments_show_assembly') ? : 'hide' }}">
                                        <td><label for="optional_fields[]">Serviço Montagem</label></td>
                                        <td class="tdinpt input-sm">{{ Form::checkbox('has_assembly', '1') }}</td>
                                    </tr>
                                    @include('account.shipments.partials.edit.complementar_services')
                                </table>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3" style="padding: 0">
                    @if(Setting::get('customers_show_charge_price') && Setting::get('shipments_show_charge_price'))
                    <div class="form-group"  style="{{ $nonEU ? 'display:none' : '' }}">
                        {{ Form::label('charge_price', trans('account/global.word.charge'), ['class' => 'col-sm-4 control-label p-r-0 bold']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                {{ Form::text('charge_price', $shipment->charge_price == 0.00 ? '' : null, ['class' => 'form-control decimal trigger-price', 'maxlength' => 8, 'max' => Setting::get('shipment_max_charge_price')]) }}
                                <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="form-group goods-price" style="{{ $nonEU || @$shipment->goods_price > 0.00 || @$shipment->service->unity == 'advalue' ? '' : 'display: none' }}">
                        {{ Form::label('goods_price', trans('account/global.word.goods-price'), ['class' => 'col-sm-4 control-label p-r-0 bold']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                {{ Form::text('goods_price', $shipment->goods_price == 0.00 ? '' : null, ['class' => 'form-control decimal trigger-price', 'maxlength' => 8]) }}
                                <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group goods-price" style="{{ $nonEU || @$shipment->at_code ? '' : 'display: none' }}">
                        {{ Form::label('at_code', trans('account/global.word.commercial-invoice'), ['class' => 'col-sm-4 control-label p-r-0 bold']) }}
                        <div class="col-sm-8">
                            {{ Form::text('at_code', null, ['class' => 'form-control decimal trigger-price', 'maxlength' => 8]) }}
                        </div>
                    </div>
                    @if(Setting::get('app_country') != 'br' && Setting::get('app_country') != 'us')
                    <div class="form-group m-b-0 incoterms" style="{{ $nonEU && !in_array($shipment->recipient_country, ['br', 'us']) ? '' : 'display: none' }}">
                        {{ Form::label('incoterm', 'Incoterm', ['class' => 'col-sm-4 control-label p-0']) }}
                        <div class="col-sm-8">
                            {{ Form::select('incoterm', [''=>'']+trans('admin/shipments.incoterms'), null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    @endif
                    @if(!Setting::get('customers_hide_payment_at_recipient'))
                        {{--<div class="form-group m-b-0">
                            {{ Form::label('cod', 'Portes', ['class' => 'col-sm-4 control-label p-0']) }}
                            <div class="col-sm-8">
                                {{ Form::select('cod', [''=>'', 'D' => 'Entrega', 'S' => 'Recolha'], $customer->always_cod ? 'D' : null), ['class' => 'form-control select2']) }}
                            </div>
                        </div>--}}
                        <div class="form-group m-b-0"  style="{{ $nonEU ? 'display:none' : '' }}">
                            <div class="checkbox pull-left">
                                <label style="margin-top: -5px;display: block; padding-left: 85px">
                                    {{ Form::checkbox('cod', 'D', $shipment->exists ? $shipment->cod == 'D' : $customer->always_cod) }}
                                    {{ trans('account/global.word.cod') }} {!! tip(trans('account/shipments.modal-shipment.tips.cod')) !!}
                                </label>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-4">
            <div class="form-group form-group-sm m-b-8 p-l-20 p-r-15">
                <div class="nav-obs">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active">
                            <a href="#" data-toggle="tabobs" data-target="#tbobs">{{ $obsLabel }}</a>
                        </li>
                        @if($showDischargeObs)
                        <li>
                            <a href="#" data-toggle="tabobs" data-target="#tbobsint"> {{ trans('account/global.word.obs-discharge') }}
                                @if($shipment->obs_delivery)
                                    <small><small><i class="fas fa-fw fa-circle text-yellow"></i></small></small>
                                @endif
                            </a>
                        </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-obs active" id="tbobs">
                            {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => '2', 'maxlength' => 150]) }}
                        </div>
                        @if($showDischargeObs)
                        <div role="tabpanel" class="tab-obs" id="tbobsint">
                            {{ Form::textarea('obs_delivery', null, ['class' => 'form-control', 'rows' => '2', 'maxlength' => 255]) }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options">
        <div class="pull-left">
            <p style="margin: 5px 8px 0 0;"><b>{{ trans('account/global.word.print') }}</b></p>
        </div>
        <div class="checkbox" style="margin-top: 3px">
            <label>
                {{ Form::checkbox('print_guide', 1, $shipment->exists ? false : (($defaultPrint == 'guide' || $defaultPrint == 'all') ? true : false)) }}
                {{ trans('account/shipments.print.guide') }}
            </label>
        </div>
        @if($defaultPrint == 'cmr' || Setting::get('customers_show_cmr') || in_array(Setting::get('app_mode'), ['cargo', 'freight']))
            <div class="checkbox" style="margin-left: -8px; margin-top: 3px;">
                <label>
                    {{ Form::checkbox('print_cmr', 1, $shipment->exists ? false : (($defaultPrint == 'cmr' || $defaultPrint == 'all') ? true : false)) }}
                    {{ trans('account/shipments.print.cmr') }}
                </label>
            </div>
        @endif
        <div class="checkbox" style="margin-left: -8px; margin-top: 3px;">
            <label>
                {{ Form::checkbox('print_label', 1, $shipment->exists ? false : (($defaultPrint == 'labels' || $defaultPrint == 'all') ? true : false)) }}
                {{ trans('account/shipments.print.labels') }}
            </label>
        </div>

        <div class="pull-left" style="margin: 0;border-left: 1px solid #ccc;margin-right: 13px;height: 27px;"></div>
        @if(Setting::get('tracking_email_active'))
        <div class="pull-left">
            <div class="checkbox send-email" style="margin-top: 3px; {{ ($shipment->recipient_email || $recipientEmailRequired || Setting::get('customer_account_email_required') ) ? 'display:none' : '' }}">
                <label>
                    {{ Form::checkbox('active_email', 1) }}
                    <i class="fas fa-envelope"></i> {{ trans('account/global.word.send-email') }}
                </label>
            </div>
            <div class="row row-5 input-group-email" style="float: left; width: 270px; margin-right: 25px; {{ $shipment->recipient_email || $recipientEmailRequired || Setting::get('customer_account_email_required') ? '' : 'display:none' }}">
                <div class="form-group m-b-0 m-t-0">
                    <div class="col-sm-12">
                        <div class="input-group" style="margin-top: -3px">
                            <div class="input-group-addon">
                                @if($recipientEmailRequired || Setting::get('customer_account_email_required'))
                                    <div style="display: none">
                                        {{ Form::checkbox('send_email', 1, true) }}
                                    </div>
                                @else
                                    {{ Form::checkbox('send_email', 1, true) }}&nbsp;
                                @endif
                                <i class="fas fa-envelope" style="vertical-align: middle"></i>
                            </div>

                            @if($recipientEmailRequired || Setting::get('customer_account_email_required'))
                                {{ Form::text('recipient_email', null, ['class' => 'form-control email nospace lowercase', 'required', 'placeholder' => trans('account/shipments.modal-shipment.write-email')]) }}
                            @else
                                {{ Form::text('recipient_email', null, ['class' => 'form-control email nospace lowercase', 'placeholder' => trans('account/shipments.modal-shipment.write-email')]) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(hasModule('sms'))

            @if(!$customer->sms_enabled)
            <div class="pull-left" data-toggle="tooltip" title="{{ trans('account/shipments.modal-shipment.tips.sms') }}">
            @endif
                <div class="checkbox" style="margin-top: 3px">
                    <label>
                        {{ Form::checkbox('send_sms', 1, false, [$customer->sms_enabled ? '' : 'disabled']) }}
                        <i class="fas fa-mobile-alt"></i> {{ trans('account/global.word.send-message') }}
                    </label>
                </div>
            @if(!$customer->sms_enabled)
            </div>
            @endif
        @endif
        <div class="clearfix"></div>
    </div>

    @if($shipment->type == \App\Models\Shipment::TYPE_RETURN)
    {{ Form::hidden('parent_tracking_code') }}
    {{ Form::hidden('type') }}
    @endif
    {{ Form::hidden('customer_id', null) }}
    {{ Form::hidden('service_id', null) }}
    {{ Form::hidden('volumetric_weight') }}
    {{ Form::hidden('fator_m3') }}
    {{ Form::hidden('agency_zp', $customer->distance_from_agency ? @$customer->agency->zip_code : '') }}
    {{ Form::hidden('agency_city', $customer->distance_from_agency ? @$customer->agency->city : '') }}
    {{ Form::hidden('customer_km', $shipment->exists ? @$shipment->customer->distance_km : 0) }}
    {{ Form::hidden('ecommerce_gateway_id') }}
    {{ Form::hidden('ecommerce_gateway_order_code') }}
    <input type="hidden" name="agency_id" value="{{ @$shipment->customer->agency_id }}">
    <input type="hidden" name="tags" value="{{ implode(',', $shipment->tags) }}">
    <input type="hidden" name="waint_ajax" value="0">
    <input type="hidden" name="is_collection" value="0"/>
    <div class="pull-right" style="margin-top: -2px; margin-bottom: -2px;">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
        <button type="submit" class="btn btn-black btn-submit" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> {{ trans('account/global.word.loading') }}...">{{ trans('account/global.word.save') }}</button>
    </div>
    @if($customer->show_billing && Setting::get('customers_preview_shipment_price'))
        <div class="pull-right hidden-xs"
             data-toggle="tooltip"
             title="Clique para detalhe completo"
             style="margin-top: -9px;margin-bottom: -12px;margin-right: 10px;">
            <h4 class="m-0 shp-price text-left btn-refresh-prices" style="cursor: pointer; {{ in_array($shipment->cod, ['D', 'S'])? 'display:none' : '' }}">
                <small style="font-size: 12px; cursor: pointer">Preço Previsto*</small><br/>
                {{--<i class="fas fa-spin fa-circle-notch loading-prices" style="display: none"></i>--}}
                <i class="fas fa-info-circle fs-14 loading-prices"></i>
                <span class="billing-subtotal">{{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}</span>
                @if(in_array(Setting::get('app_country'), ['pt','ptmd','ptac','ao']))
                <small class="shp-prvt" style="font-size: 11px; margin-left: -3px; color: #333;">
                    @if($shipment->vat_rate)
                        +IVA
                    @else
                        IVA 0%
                    @endif
                </small>
                @endif
            </h4>
        </div>
    @endif
    <div class="clearfix"></div>
</div>
@include('account.shipments.partials.edit.dimensions')
@include('account.shipments.modals.confirm_sync_error')
@include('account.shipments.modals.price_details')
@if(hasModule('account_wallet'))
    @include('account.shipments.modals.confirm_payment_error')
@endif
{{ Form::close() }}
{{--@if(hasModule('gateway_payments') && (!$shipment->ignore_billing || !$shipment->invoice_id))--}}
{{--@endif--}}



<style>
</style>

{{ Html::script(asset('vendor/devbridge-autocomplete/dist/jquery.autocomplete.js')) }}
<script>
    $(".modal .select2").select2(Init.select2());
    $('.modal .select2-country').select2(Init.select2Country());
    $('.modal .datepicker').datepicker({
        format: 'yyyy-mm-dd',
        language: 'pt',
        startDate: '{{ $shipmentDate }}'
    });

    var CURRENT_DATE = "{{ date('Y-m-d') }}";
    var NEXT_DATE    = "{{ date('Y-m-') . (date('d') + 1) }}";
    var CURRENT_HOUR = "{{ date('H:i') }}";

    var STR_HASH_ID             = "{{ $hashId }}"
    var EU_COUNTRIES            = {!! json_encode(trans('countries_eu')) !!};
    var APP_SOURCE              = "{{ config('app.source') }}";
    var APP_COUNTRY             = "{{ config('app.country') }}";
    var ROUTE_SEARCH_SENDER     = "{{ route('account.shipments.search.recipient') }}";
    var ROUTE_SEARCH_RECIPIENT  = "{{ route('account.shipments.search.recipient') }}";
    var ROUTE_SEARCH_SKU        = "{{ route('account.shipments.search.sku') }}";
    var ROUTE_GET_AGENCY        = "{{ route('account.shipments.get.agency') }}";
    var ROUTE_GET_PUDOS         = "{{ route('account.shipments.get.pudos') }}";
    var ROUTE_GET_PRICE         = "{{ route('account.shipments.get.price') }}";
    var ROUTE_SET_PAYMENT       = "{{ route('account.shipments.set.payment') }}";
    var ROUTE_GET_DEPARTMENT    = "{{ route('account.shipments.get.department') }}";
    var ROUTE_GET_DISTANCE_KM   = "{{ config('app.core') . '/helper/maps/distance' }}";
    var SHIPMENT_CALC_AUTO_KM   = {{ hasModule('calc_auto_km') && Setting::get('shipments_km_calc_auto') ? 1 : 0 }};
    var SHIPMENT_KM_RETURN_BACK = "{{ Setting::get('shipments_km_return_back') }}";
    var CUSTOMER_SHOW_PRICES    = {{ $customer->show_billing && Setting::get('customers_preview_shipment_price') ? 1 : 0 }};
    var VOLUMES_MESURE_UNITY    = "{{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}"
    var HAS_MODULE_LOGISTIC     = {{ hasModule('logistic') ? 1 : 0 }};

    var ROUTE_GET_DEPARTMENT    = "{{ route('account.shipments.get.department') }}";
    var GLOBAL_EMAIL_REQUIRED   = {{ Setting::get('customer_account_email_required') ? 1 : 0 }};
    var FILL_HOURS              = {{ Setting::get('customers_shipment_hours_fill') ? 1 : 0 }};

    @if(count($services) == 2)
    $(document).ready(function() {
        $('.modal [name="services"] option:last-child').attr('selected', true).trigger('change').trigger('change.select2');
    })
    @endif

    $('.opt-flds-grp label').on('click', function(){
        var $target = $(this).attr('for');
        $('[name="'+$target+'"]').trigger('click');
    })
    /*==============================================*/
    /*=============== OPTIONAL FIELDS ==============*/
    /*==============================================*/
    {!! include(public_path().'/assets/js/shipments.js') !!}

    @if ($shipment->exists && @$shipment->service_id)
        $('.modal [name="services"]').val({{ @$shipment->service_id }}).trigger('change');
    @else
        if ($('.modal [name="services"]').val()) {
            $('.modal [name="services"]').trigger('change');
        }
    @endif
</script>
