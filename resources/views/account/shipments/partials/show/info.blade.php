
<div class="spacer-5"></div>
<div class="form-horizontal">
    <div class="row main-block">
        <div class="col-sm-3">
            <h4 class="service-preview">
                <small>{{ trans('account/global.word.service') }}</small><br/>
                {{ @$shipment->service->name ? @$shipment->service->name : 'Aguarda atribuição' }}
            </h4>
        </div>
        <div class="col-sm-3">
            <div class="form-group m-b-0" style="margin-top: -3px">
                <label class="col-sm-4 control-label" style="padding: 0">{{ trans('account/global.word.shipment-date') }}</label>
                <div class="col-sm-8">
                    <p class="m-0">
                        @if($shipment->shipping_date)
                            <?php $shipment->shipping_date = new Date($shipment->shipping_date)?>
                            {{ $shipment->shipping_date->format('Y-m-d') }} {{ $shipment->shipping_date->format('H:i') == '00:00' ? '' : $shipment->shipping_date->format('H:i') }}
                        @else
                            {{ $shipment->date }}
                            @if($shipment->start_hour)
                                <br/>{{ $shipment->start_hour }}
                            @endif
                        @endif
                    </p>
                </div>
            </div>
            <div class="form-group m-b-0">
                <label class="col-sm-4 control-label" style="padding: 0">{{ trans('account/global.word.delivery-prevision') }}</label>
                <div class="col-sm-8">
                    <p class="m-0">
                        <?php $shipment->delivery_date = new Date($shipment->delivery_date)?>
                        {{ $shipment->delivery_date->format('Y-m-d H:i') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group m-b-0" style="margin-top: -3px">
                <label class="col-sm-4 control-label" style="padding: 0">{{ trans('account/global.word.delivery-date') }}</label>
                <div class="col-sm-8">
                    <p class="m-0">
                        @if(@$shipment->lastHistory->status_id == \App\Models\ShippingStatus::DELIVERED_ID)
                            <?php $shipment->shipping_date = new Date($shipment->shipping_date)?>
                                {{ $shipment->lastHistory->created_at->format('Y-m-d H:i') }}
                        @else
                            ---
                        @endif
                    </p>
                </div>
            </div>
            <div class="form-group m-b-0">
                <label class="col-sm-4 control-label" style="padding: 0">
                    @if($shipment->is_collection)
                    {{ trans('account/global.word.pickup-time') }}
                    @else
                    {{ trans('account/global.word.transit-time') }}
                    @endif
                </label>
                <div class="col-sm-8">
                    <p class="m-0">
                        {{ $shipment->transit_time }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            @if($auth->show_billing)
                <h3 class="price-preview bold pull-right">
                    <small>{{ trans('account/global.word.price') }}</small><br/>
                    @if(!$shipment->payment_at_recipient)
                        @if($shipment->status_id == App\Models\ShippingStatus::PENDING_ID)
                            -.-- {{  Setting::get('app_currency') }}
                        @else
                            {{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}
                        @endif
                    @else
                        <span class="label label-warning" data-toggle="tooltip" title="{{ trans('account/global.word.cod') }}">PGD</span>
                    @endif
                </h3>
                @if($shipment->charge_price > 0.00)
                <h4 class="price-preview text-info pull-right">
                    <small class="text-info">{{ trans('account/global.word.charge') }}</small><br/>
                    <i class="fas fa-hand-holding-usd"></i> {{ money($shipment->charge_price, Setting::get('app_currency'))}}
                </h4>
                @endif
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default m-b-0">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        {{ trans('account/shipments.modal-shipment.sender-block') }}
                    </h4>
                </div>
                <div class="panel-body p-10px bg-gray-light">
                    <div class="form-group m-b-5">
                        <label class="col-sm-2 control-label p-r-0">
                            {{ trans('account/global.word.recipient-attn-abrv') }}
                        </label>
                        <div class="col-sm-5 p-r-0">
                            <p class="form-control-static bg-white">{{ $shipment->sender_attn }}</p>
                        </div>
                        <label class="col-sm-1 control-label p-0">
                            {{ trans('account/global.word.tin-abrv.'.Setting::get('app_country')) }}
                        </label>
                        <div class="col-sm-4">
                            <p class="form-control-static bg-white">{{ $shipment->sender_tin }}</p>
                        </div>
                    </div>

                    <div class="form-group m-b-5">
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.sender') }}</label>
                        <div class="col-sm-10">
                            <p class="form-control-static bg-white">{{ $shipment->sender_name }}</p>
                        </div>
                    </div>
                    <div class="form-group m-b-5">
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.address') }}</label>
                        <div class="col-sm-10">
                            <p class="form-control-static bg-white">{{ $shipment->sender_address }}</p>
                        </div>
                    </div>

                    <div class="form-group m-b-5">
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.zip_code') }}</label>
                        <div class="col-sm-3 p-r-0">
                            <p class="form-control-static bg-white">{{ $shipment->sender_zip_code }}</p>
                        </div>
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.city') }}</label>
                        <div class="col-sm-5">
                            <p class="form-control-static bg-white">{{ $shipment->sender_city }}</p>
                        </div>
                    </div>

                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.country') }}</label>
                        <div class="col-sm-3 p-r-0">
                            <p class="form-control-static bg-white">{{ trans('country.'.$shipment->sender_country) }}</p>
                        </div>
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.phone') }}</label>
                        <div class="col-sm-5">
                            <p class="form-control-static bg-white">{{ $shipment->sender_phone }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="panel panel-default m-b-0">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        {{ trans('account/shipments.modal-shipment.recipient-block') }}
                    </h4>
                </div>
                <div class="panel-body p-10 bg-gray-light">
                    <div class="form-group m-b-5">
                        <label class="col-sm-2 control-label p-r-0">
                            {{ trans('account/global.word.recipient-attn-abrv') }}
                        </label>
                        <div class="col-sm-5 p-r-0">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_attn }}</p>
                        </div>
                        <label class="col-sm-1 control-label p-0">
                            {{ trans('account/global.word.tin-abrv.'.Setting::get('app_country')) }}
                        </label>
                        <div class="col-sm-4">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_tin }}</p>
                        </div>
                    </div>
                    <div class="form-group m-b-5">
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.recipient-name') }}</label>
                        <div class="col-sm-10">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_name }}</p>
                        </div>
                    </div>
                    <div class="form-group m-b-5">
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.address') }}</label>
                        <div class="col-sm-10">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_address }}</p>
                        </div>
                    </div>

                    <div class="form-group m-b-5">
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.zip_code') }}</label>
                        <div class="col-sm-3 p-r-0">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_zip_code }}</p>
                        </div>
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.city') }}</label>
                        <div class="col-sm-5">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_city }}</p>
                        </div>
                    </div>

                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.country') }}</label>
                        <div class="col-sm-3 p-r-0">
                            <p class="form-control-static bg-white">{{ trans('country.'.$shipment->recipient_country) }}</p>
                        </div>
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.phone') }}</label>
                        <div class="col-sm-5">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_phone }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="m-t-15"></div>
    <div class="row">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-7">
                    <div class="row row-10">
                        <div class="col-sm-6">
                            <div class="form-group m-b-5">
                                <label class="col-sm-4 control-label p-r-0">{{ trans('account/global.word.volumes') }}</label>
                                <div class="col-sm-8">
                                    {{ Form::text('', $shipment->volumes, ['class' => 'form-control static', 'readonly']) }}
                                </div>
                            </div>
                            @if(@$shipment->service->unity == 'm3')
                                <div class="form-group m-b-5">
                                    <label class="col-sm-4 control-label p-r-0"style="margin-top: -5px;">{{ trans('account/global.word.volume') }}</label>
                                    <div class="col-sm-8">
                                        <div class="input-group static">
                                            {{ Form::text('', $shipment->volume_m3, ['class' => 'form-control', 'readonly']) }}
                                            <div class="input-group-addon">m<sup>3</sup></div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="form-group m-b-5">
                                    <label class="col-sm-4 control-label p-r-0"style="margin-top: -5px;">{{ trans('account/global.word.real-weight') }}</label>
                                    <div class="col-sm-8">
                                        <div class="input-group static">
                                            {{ Form::text('', $shipment->weight, ['class' => 'form-control', 'readonly']) }}
                                            <div class="input-group-addon">kg</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group m-b-0">
                                <label class="col-sm-4 control-label p-r-0">{{ trans('account/global.word.distance') }}</label>
                                <div class="col-sm-8">
                                    <div class="input-group static">
                                        {{ Form::text('', $shipment->kms, ['class' => 'form-control', 'readonly']) }}
                                        <div class="input-group-addon">km</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group m-b-5">
                                <label class="col-sm-5 control-label p-r-0">{{ trans('account/global.word.fator-m3') }}</label>
                                <div class="col-sm-7" style="padding-right: 0 !important;">
                                    {{ Form::text('', $shipment->fator_m3, ['class' => 'form-control static', 'readonly']) }}
                                </div>
                            </div>
                            <div class="form-group m-b-5">
                                <label class="col-sm-5 control-label p-r-0">{{ trans('account/global.word.weight_vol') }}</label>
                                <div class="col-sm-7" style="padding-right: 0 !important;">
                                    <div class="input-group static">
                                        {{ Form::text('', $shipment->volumetric_weight, ['class' => 'form-control', 'readonly']) }}
                                        <div class="input-group-addon">kg</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-b-0">
                                <label class="col-sm-5 control-label p-r-0" style="margin-top: -5px;">{{ trans('account/global.word.taxable-weight') }}</label>
                                <div class="col-sm-7" style="padding-right: 0 !important;">
                                    <div class="input-group static bold">
                                        {{ Form::text('', $shipment->volumetric_weight > $shipment->weight ? $shipment->volumetric_weight : $shipment->weight, ['class' => 'form-control', 'readonly']) }}
                                        <div class="input-group-addon">kg</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group m-b-5">
                        <label class="col-sm-4 control-label p-0">{{ trans('account/global.word.reference') }}</label>
                        <div class="col-sm-8" style="padding-left: 5px !important;">
                            <p class="form-control-static">{{ $shipment->reference }}</p>
                        </div>
                    </div>
                    @if(Setting::get('shipments_reference2_visible'))
                        <div class="form-group m-b-5">
                            <label class="col-sm-4 control-label p-0">{{ Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : 'Ref.2' }}</label>
                            <div class="col-sm-8" style="padding-left: 5px !important;">
                                <p class="form-control-static">{{ $shipment->reference2 }}</p>
                            </div>
                        </div>
                    @endif
                    @if(Setting::get('shipments_reference3_visible'))
                        <div class="form-group m-b-0">
                            <label class="col-sm-4 control-label p-0">{{ Setting::get('shipments_reference3_name') ? Setting::get('shipments_reference3_name') : 'Ref.3' }}</label>
                            <div class="col-sm-8" style="padding-left: 5px !important;">
                                <p class="form-control-static">{{ $shipment->reference3 }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-4">
                    <div class="complementar-services1" style="margin-top: -7px;">
                        <p><b>{{ trans('account/global.word.adicional-services') }}</b></p>
                        @if($shipment->has_return && in_array('rpack', $shipment->has_return))
                            <div class="checkbox-static-label">{{ trans('account/global.word.return-pack') }}</div>
                        @endif

                        @foreach($complementarServices as $service)
                            @if($shipment->complementar_services && in_array($service->id, $shipment->complementar_services))
                                <div class="checkbox-static-label">{{ $service->name }}</div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="col-sm-8">

                    @if(Setting::get('app_mode') == 'cargo')
                        <div class="form-group m-b-5 p-r-0">
                            <label class="col-sm-2 control-label">{{ trans('account/global.word.obs-cargo') }}</label>
                            <div class="col-sm-10">
                                <p class="form-control-static" style="height: 30px !important; overflow-y: scroll;">
                                    {{ $shipment->obs }}
                                </p>
                            </div>
                        </div>
                        <div class="form-group m-b-5 p-r-0">
                            <label class="col-sm-2 control-label">{{ trans('account/global.word.obs-discharge') }}</label>
                            <div class="col-sm-10">
                                <p class="form-control-static" style="height: 30px !important; overflow-y: scroll;">
                                    {{ $shipment->obs_internal }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="form-group m-b-5 p-r-0">
                            <label class="col-sm-2 control-label">{{ trans('account/global.word.obs-abrv') }}</label>
                            <div class="col-sm-10">
                                <p class="form-control-static" style="height: 65px !important; overflow-y: scroll;">
                                    {{ $shipment->obs }}
                                </p>
                            </div>
                        </div>
                    @endif
                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.email') }}</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $shipment->recipient_email }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>