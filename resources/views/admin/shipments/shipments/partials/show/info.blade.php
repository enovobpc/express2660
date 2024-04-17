<div class="spacer-5"></div>
<div class="form-horizontal">
    <div class="row row-5 main-block">
        <div class="col-sm-8">
            <div class="row row-5">
                <div class="col-sm-4">
                    {{--<h4 class="service-preview">
                        <small>{{ trans('account/global.word.service') }}</small><br/>
                        {{ @$shipment->service->name ? @$shipment->service->name : 'Aguarda atribuição' }}
                    </h4>--}}
                    <div class="form-group m-b-8" style="margin-top: -3px">
                        <label class="col-sm-4 control-label" style="padding: 0">Serviço</label>
                        <div class="col-sm-8">
                            <p class="m-0">{{ @$shipment->service->name ? @$shipment->service->name : 'Aguarda atribuição' }}</p>
                        </div>
                    </div>
                    <div class="form-group m-b-8" style="margin-top: -3px">
                        <label class="col-sm-4 control-label" style="padding: 0">Fornecedor</label>
                        <div class="col-sm-8">
                            <p class="m-0">
                                <span class="label" style="background: {{ @$shipment->provider->color }}">
                                    {{ @$shipment->provider->name }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group m-b-0" style="margin-top: -3px">
                        <label class="col-sm-4 control-label" style="padding: 0">Data Envio</label>
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
                </div>
                <div class="col-sm-4">
                    @if(Auth::user()->showPrices() && (!$userAgencies || $userAgencies && in_array($shipment->agency_id, $userAgencies)))
                    <div class="form-group m-b-2">
                        <label class="col-sm-4 control-label" style="padding: 0">Cliente Paga</label>
                        <div class="col-sm-8">
                            <a href="{{ route('admin.customers.edit', $shipment->customer_id) }}" target="_blank" class="m-0 text-uppercase" data-toggle="tooltip" title="{{ @$shipment->customer->code }} - {{ @$shipment->customer->name }}">
                            {{ str_limit(@$shipment->customer->name, 18) }} <i class="fas fa-external-link-square-alt"></i>
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="form-group m-b-2">
                        <label class="col-sm-4 control-label" style="padding: 0">Agência Paga</label>
                        <div class="col-sm-8">
                            <p class="m-0">{{ @$shipment->agency->name }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="form-group m-b-2">
                        <label class="col-sm-4 control-label" style="padding: 0">A. Origem</label>
                        <div class="col-sm-8">
                            <p class="m-0">{{ @$shipment->senderAgency->name }}</p>
                        </div>
                    </div>
                    <div class="form-group m-b-2">
                        <label class="col-sm-4 control-label" style="padding: 0">A. Destino</label>
                        <div class="col-sm-8">
                            <p class="m-0">{{ @$shipment->recipientAgency->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group m-b-2">
                        <label class="col-sm-5 control-label" style="padding: 0">Prev. Entrega</label>
                        <div class="col-sm-7">
                            <p class="m-0">
                                <?php $shipment->delivery_date = new Date($shipment->delivery_date)?>
                                {{ $shipment->delivery_date->format('Y-m-d H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="form-group m-b-2" style="margin-top: -3px">
                        <label class="col-sm-5 control-label" style="padding: 0">Entrege em</label>
                        <div class="col-sm-7">
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
                        <label class="col-sm-5 control-label" style="padding: 0">
                            @if($shipment->is_collection)
                            Tempo Recolha
                            @else
                            Tempo Entrega
                            @endif
                        </label>
                        <div class="col-sm-7">
                            <p class="m-0">
                                {{ $shipment->transit_time }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">

            @if(Auth::user()->showPrices() && (!$userAgencies || $userAgencies && in_array($shipment->agency_id, $userAgencies)))
                @if(hasModule('statistics'))
                <h3 class="price-preview bold pull-right">
                    <small>Ganhos</small>
                    <div class="m-t-3">
                        @if(@$shipment->gain_money > 0.00)
                            <span class="text-green" style="display: block; line-height: 15px">
                                <b>{{ money(@$shipment->gain_money, Setting::get('app_currency')) }}</b>
                            </span>
                        @else
                            <span class="text-red" style="display: block; line-height: 15px">
                                <b>{{ money(@$shipment->gain_money, Setting::get('app_currency')) }}</b>
                            </span>
                        @endif
                    </div>
                    <div><small>{{ money(@$shipment->gain_percent, '%') }}</small></div>
                </h3>
                @endif
                <h3 class="price-preview bold pull-right" style="line-height: 19px">
                    <small>
                        Preço
                        @if($shipment->cod == 'D' || $shipment->cod == 'S')
                            <span class="label label-warning" data-toggle="tooltip" title="Portes no Destino">PGD</span>
                        @endif
                        @if($shipment->ignore_billing || $shipment->invoice_id)
                            <span class="label label-success" data-toggle="tooltip" title="O envio foi marcado como pago.">PAGO</span>
                        @endif
                    </small>
                    <div class="m-t-3">
                        <b>
                            @if($shipment->cod)
                                {{ money($shipment->billing_subtotal, Setting::get('app_currency')) }}
                            @else
                                @if($groupedShipments)
                                    {{ money(@$shipmentTotals['price'], Setting::get('app_currency')) }}
                                @else
                                    {{ money(@$shipmentTotals['price'], Setting::get('app_currency')) }}
                                @endif
                            @endif
                        </b>

                    </div>
                    <div><small>Custo: {{ money($shipment->cost_billing_subtotal, Setting::get('app_currency')) }}</small></div>
                </h3>
            @endif

            @if($shipment->charge_price > 0.00)
            <h4 class="price-preview text-blue pull-right">
                <small class="text-blue">Cobrança</small><br/>
                <i class="fas fa-hand-holding-usd"></i> {{ money($shipment->charge_price, Setting::get('app_currency'))}}
            </h4>
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
                <div class="panel-body p-10 bg-gray-light">
                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">
                            P. Contacto
                        </label>
                        <div class="col-sm-5 p-r-0">
                            <p class="form-control-static bg-white">{{ $shipment->sender_attn }}</p>
                        </div>
                        <label class="col-sm-1 control-label p-0">
                            NIF
                        </label>
                        <div class="col-sm-4">
                            <p class="form-control-static bg-white">{{ $shipment->sender_tin }}</p>
                        </div>
                    </div>

                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">Nome</label>
                        <div class="col-sm-10">
                            <p class="form-control-static bg-white">{{ $shipment->sender_name }}</p>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">Morada</label>
                        <div class="col-sm-10">
                            <p class="form-control-static bg-white">{{ $shipment->sender_address }}</p>
                        </div>
                    </div>

                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">Cód. Postal</label>
                        <div class="col-sm-3 p-r-0">
                            <p class="form-control-static bg-white">{{ $shipment->sender_zip_code }}</p>
                        </div>
                        <label class="col-sm-2 control-label p-r-0">Localidade</label>
                        <div class="col-sm-5">
                            <p class="form-control-static bg-white">{{ $shipment->sender_city }}</p>
                        </div>
                    </div>

                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">País</label>
                        <div class="col-sm-3 p-r-0">
                            <p class="form-control-static bg-white">{{ trans('country.'.$shipment->sender_country) }}</p>
                        </div>
                        <label class="col-sm-2 control-label p-r-0">Telefone</label>
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
                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">
                            P. Contacto
                        </label>
                        <div class="col-sm-5 p-r-0">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_attn }}</p>
                        </div>
                        <label class="col-sm-1 control-label p-0">
                            NIF
                        </label>
                        <div class="col-sm-4">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_tin }}</p>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">Nome</label>
                        <div class="col-sm-10">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_name }}</p>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">Morada</label>
                        <div class="col-sm-10">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_address }}</p>
                        </div>
                    </div>

                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-0">Cod. Postal</label>
                        <div class="col-sm-3 p-r-0">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_zip_code }}</p>
                        </div>
                        <label class="col-sm-2 control-label p-r-0">Localidade</label>
                        <div class="col-sm-5">
                            <p class="form-control-static bg-white">{{ $shipment->recipient_city }}</p>
                        </div>
                    </div>

                    <div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">País</label>
                        <div class="col-sm-3 p-r-0">
                            <p class="form-control-static bg-white">{{ trans('country.'.$shipment->recipient_country) }}</p>
                        </div>
                        <label class="col-sm-2 control-label p-r-0">Telefone</label>
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
                            <div class="form-group form-group-sm m-b-5">
                                <label class="col-sm-5 control-label p-0">Volumes</label>
                                <div class="col-sm-7">
                                    {{ Form::text('', $shipment->volumes, ['class' => 'form-control static', 'readonly']) }}
                                </div>
                            </div>
                            @if(@$shipment->service->unity == 'm3')
                                <div class="form-group form-group-sm m-b-5">
                                    <label class="col-sm-5 control-label p-0"style="margin-top: -5px;">Volume</label>
                                    <div class="col-sm-7">
                                        <div class="input-group static">
                                            {{ Form::text('', $shipment->volume_m3, ['class' => 'form-control', 'readonly']) }}
                                            <div class="input-group-addon">m<sup>3</sup></div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="form-group form-group-sm m-b-5">
                                    <label class="col-sm-5 control-label p-0">Peso Real</label>
                                    <div class="col-sm-7">
                                        <div class="input-group static">
                                            {{ Form::text('', $shipment->weight, ['class' => 'form-control', 'readonly']) }}
                                            <div class="input-group-addon">kg</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group form-group-sm m-b-0">
                                <label class="col-sm-5 control-label p-0">Kms</label>
                                <div class="col-sm-7">
                                    <div class="input-group static">
                                        {{ Form::text('', $shipment->kms, ['class' => 'form-control', 'readonly']) }}
                                        <div class="input-group-addon">km</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-group-sm m-b-5">
                                <label class="col-sm-5 control-label p-0">Cubicagem</label>
                                <div class="col-sm-7" style="padding-right: 0 !important;">
                                    {{ Form::text('', $shipment->fator_m3, ['class' => 'form-control static', 'readonly']) }}
                                </div>
                            </div>
                            <div class="form-group form-group-sm m-b-5">
                                <label class="col-sm-5 control-label p-0">Peso Vol.</label>
                                <div class="col-sm-7" style="padding-right: 0 !important;">
                                    <div class="input-group static">
                                        {{ Form::text('', $shipment->volumetric_weight, ['class' => 'form-control', 'readonly']) }}
                                        <div class="input-group-addon">kg</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-group-sm m-b-0">
                                <label class="col-sm-5 control-label p-0" style="margin-top: -5px;">Peso Taxável</label>
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
                    <div class="form-group form-group-sm m-b-0">
                        <label class="col-sm-4 control-label p-0">Referência</label>
                        <div class="col-sm-8" style="padding-left: 5px !important;">
                            <p class="form-control-static">{{ $shipment->reference }}</p>
                        </div>
                    </div>
                    @if(Setting::get('shipments_reference2_visible'))
                    <div class="form-group form-group-sm m-b-0">
                        <label class="col-sm-4 control-label p-0">{{ Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : 'Ref#2' }}</label>
                        <div class="col-sm-8" style="padding-left: 5px !important;">
                            <p class="form-control-static">{{ $shipment->reference2 }}</p>
                        </div>
                    </div>
                    @endif
                    @if(Setting::get('shipments_reference3_visible'))
                    <div class="form-group form-group-sm m-b-0">
                        <label class="col-sm-4 control-label p-0">{{ Setting::get('shipments_reference3_name') ? Setting::get('shipments_reference3_name') : 'Ref#3' }}</label>
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
                        @if(!empty(@$shipment->operator->name))
                            <div class="form-group m-b-8" style="margin-top: -3px">
                                <label class="col-sm-4 control-label" style="padding: 0">Motorista</label>
                                <div class="col-sm-8">
                                    <p class="m-0">{{ @$shipment->operator->name }}</p>
                                </div>
                            </div>
                        @endif
                        @if(!empty(@$shipment->vehicle))
                            <div class="form-group m-b-8" style="margin-top: -3px">
                                <label class="col-sm-4 control-label" style="padding: 0">Viatura</label>
                                <div class="col-sm-8">
                                    <p class="m-0">{{ @$shipment->vehicle }}</p>
                                </div>
                            </div>
                        @endif
                        @if(!empty(@$shipment->trailer))
                            <div class="form-group m-b-8" style="margin-top: -3px">
                                <label class="col-sm-4 control-label" style="padding: 0">Reboque</label>
                                <div class="col-sm-8">
                                    <p class="m-0">{{ @$shipment->trailer }}</p>
                                </div>
                            </div>
                        @endif
                        <br>
                        @if(($shipment->has_return && in_array('rpack', @$shipment->has_return)) || !empty(@$complementarServices))
                        <p><b>Serviços Adicionais</b></p>
                        @if($shipment->has_return && in_array('rpack', $shipment->has_return))
                            <div class="checkbox-static-label">Retorno Encomenda</div>
                        @endif

                        @foreach($complementarServices as $serviceName)
                            <div class="checkbox-static-label"><i class="fas fa-check"></i> {{ $serviceName }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-sm-8">
                    @if(in_array(Setting::get('app_mode'), ['cargo', 'freight']))
                        <div class="form-group m-b-0 p-r-0">
                            <label class="col-sm-2 control-label p-0">Obs Carga</label>
                            <div class="col-sm-10">
                                <p class="form-control-static" style="min-height: 45px !important; overflow-y: scroll;">
                                    {{ $shipment->obs }}
                                </p>
                            </div>
                        </div>
                        <div class="form-group m-b-0 p-r-0">
                            <label class="col-sm-2 control-label p-0">Obs Descarga</label>
                            <div class="col-sm-10">
                                <p class="form-control-static" style="min-height: 45px !important; overflow-y: scroll;">
                                    {{ $shipment->obs_delivery }}
                                </p>
                            </div>
                        </div>
                    @else
                    <div class="form-group m-b-0 p-r-0">
                        <label class="col-sm-2 control-label p-0">Obs {{ $shipment->is_collection ? 'Obs Recolha' : 'Obs Envio' }}</label>
                        <div class="col-sm-10">
                            <p class="form-control-static" style="min-height: 45px !important; overflow-y: scroll;">
                                {{ $shipment->obs }}
                            </p>
                        </div>
                    </div>
                    <div class="form-group m-b-0 p-r-0">
                        <label class="col-sm-2 control-label p-0">Obs Interna</label>
                        <div class="col-sm-10">
                            <p class="form-control-static" style="min-height: 45px !important; overflow-y: scroll;">
                                {{ $shipment->obs_internal }}
                            </p>
                        </div>
                    </div>
                    @endif
                    {{--<div class="form-group m-b-0">
                        <label class="col-sm-2 control-label p-r-0">{{ trans('account/global.word.email') }}</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $shipment->recipient_email }}</p>
                        </div>
                    </div>--}}
                </div>
            </div>

        </div>
    </div>
</div>