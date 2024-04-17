<?php
$createOptions = [
    'source'                => 'budgeter',
    'intercity'             => @$pickupType['intercity'],
    'service'               => $service->id,
    'date'                  => $service->pickup_date,
    'sender_country'        => $service->sender_country,
    'sender_zip_code'       => $service->sender_zip_code,
    'sender_city'           => $service->sender_city,
    'recipient_country'     => $service->recipient_country,
    'recipient_zip_code'    => $service->recipient_zip_code,
    'recipient_city'        => $service->recipient_city,
    'volumes'               => $service->volumes,
    'weight'                => $service->weight,
    'fator_m3'              => $service->fator_m3,
    'dimensions'            => $service->pack_dimensions,
];
?>


<div class="result-row">
    <div class="row row-0">
        <div class="col-sm-2 result-row-left">
            <div class="service-img">
                @if($service->filepath)
                    <img src="{{ asset($service->filepath) }}"/>
                @else
                    <img src="https://app.baltransurgente.com/assets/img/logo/logo.svg"/>
                @endif
            </div>

            @if($service->is_air)
            <div class="service-vehicle" style="color: #FF5722">
                <i class="fas fa-plane"></i> {{ trans('account/global.word.aerial') }}
            </div>
            @elseif($service->is_maritime)
            <div class="service-vehicle" style="color: #FF5722">
                <i class="fas fa-ship"></i> {{ trans('account/global.word.maritime') }}
            </div>
            @else
            <div class="service-vehicle" style="color: #FF5722">
                <i class="fas fa-truck"></i> {{ trans('account/global.word.terrestrial') }}
            </div>
            @endif
        </div>
        <div class="col-sm-8 result-row-center">
            <div class="service-info">
                <div class="row row-0">
                    <div class="col-sm-4">
                        <h4 class="bold">
                            {{ @$service->name }}
                        </h4>
                        <p class="text-black">
                            @if($service->transit_time <= 72 && (!$service->transit_time_max || $service->transit_time_max <= 72))
                                @if($service->transit_time_max)
                                    Transito {{ (int) ($service->transit_time) }} a {{ (int) ($service->transit_time_max) }} horas
                                @else
                                    Transito {{ (int) $service->transit_time }} horas
                                @endif
                            @else
                                @if($service->transit_time_max)
                                    Transito {{ (int) ($service->transit_time / 24) }} a {{ (int) ($service->transit_time_max / 24) }} dias
                                @else
                                    Transito {{ (int) ($service->transit_time / 24)  }} dias
                                @endif
                            @endif
                        </p>
                    </div>
                    <div class="col-sm-4">
                        <h4 class="bold"><i class="fas fa-calendar-check"></i> {{ trans('account/global.word.pickup') }}</h4>
                        <p>
                            {{ @$service->pickup_date }}
                            @if($service->pickup_hour)
                                <br/>
                                {{ trans('account/budgeter.results.pickup_hour', ['hour' => $service->pickup_hour]) }}
                            @endif
                        </p>
                    </div>
                    <div class="col-sm-4">
                        <h4 class="bold"><i class="fas fa-home"></i> {{ trans('account/global.word.delivery') }}</h4>
                        <div class="delivery-date">
                            @if(in_array(@$service->provider->webservice_method, ['db_schenker', 'fedex', 'ups', 'tnt_express', 'chronopost']))
                                <p class="provider-delivery-date">
                                    <a href="{{ route('account.budgeter.get.transit-time', ['provider' => @$service->provider->webservice_method]) }}"
                                       data-provider="{{ @$service->provider->webservice_method }}"
                                       data-service="{{ @$service->id }}">
                                        <i class="fas fa-spin fa-circle-notch"></i> Verificando fecha
                                    </a>
                                </p>
                            @endif
                            <p class="default-delivery-date" style="{{ in_array(@$service->provider->webservice_method, ['db_schenker', 'fedex', 'ups', 'tnt_express', 'chronopost']) ? 'display:none' : '' }}">
                                {{ @$service->delivery_date }}
                                @if($service->delivery_hour)
                                    <br/>
                                    {{ trans('account/budgeter.results.delivery_hour', ['hour' => $service->delivery_hour]) }}
                                @endif
                            </p>

                            <p class="default-delivery-date-error" style="display:none">
                                Sin Calcular {!! tip('No ha sido posible calcular la fecha de entrega con el transportista, puede probar mas tarde.') !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="service-details">
                <div class="row row-0">
                    <div class="col-sm-3">
                        <p class="text-blue">
                            <i class="fas fa-print"></i> {{ trans('account/budgeter.form.tips.label-required') }}
                        </p>
                    </div>
                    <div class="col-sm-3">
                        <p class="{{ $service->allow_cod ? 'text-blue' : 'text-muted-light' }}">
                            <i class="fas fa-wallet"></i> {{ trans('account/budgeter.form.tips.allowed-cod') }}
                        </p>
                    </div>
                    <div class="col-sm-3">
                        <p class="{{ $service->allow_return ? 'text-blue' : 'text-muted-light' }}">
                            <i class="fas fa-undo"></i> {{ trans('account/budgeter.form.tips.allowed-return') }}
                        </p>
                    </div>
                    <div class="col-sm-3">
                        @if(Auth::check())
                            <a href="#"
                               data-toggle="modal"
                               data-target="#budgeter-details-{{ $service->id }}"
                               class="pull-right text-muted m-l-15">
                                <i class="fas fa-cog"></i>
                            </a>
                        @endif
                        @if($service->description || $service->description2)
                        <a href="#" class="text-muted text-right pull-right btn-service-details">
                            {{ trans('account/global.word.details') }} <i class="fas fa-angle-down"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="service-price">
                @if(@$service->prices['billing_total'] == 0.00)
                    <h1 class="price">
                        Sob Consulta
                    </h1>
                    <a href="" class="btn btn-create-shipment">
                        Pedir Cotación
                    </a>
                @else
                <h1 class="price price-with-vat {{ $vatEnabled ? '' : 'hide' }}">
                    {{ money(@$service->prices['billing_total'], '€') }}<br/>
                    <small>{{ trans('account/global.word.with-vat') }}</small>
                </h1>
                <h1 class="price price-without-vat {{ $vatEnabled ? 'hide' : '' }}">
                    {{ money(@$service->prices['billing_subtotal'], '€') }}<br/>
                    <small>{{ trans('account/global.word.without-vat') }}</small>
                </h1>
                @if ($pickupType['type'] == 'shipment')
                <a href="{{ route('account.shipments.create', $createOptions) }}" data-toggle="modal" data-target="#modal-remote-xl" class="btn btn-create-shipment">
                    {{ trans('account/global.word.create-shipment') }}
                </a>
                @elseif ($pickupType['type'] == 'pickup')
                <a href="{{ route('account.pickups.create', $createOptions) }}" data-toggle="modal" data-target="#modal-remote-xl" class="btn btn-create-shipment">
                    {{ trans('account/global.word.create-pickup') }}
                </a>
                @endif
                @endif
            </div>
        </div>
    </div>
    @if($service->description || $service->description2)
    <div class="row row-0 result-row-details" style="display:none">
        <div class="col-xs-12 col-sm-6">
            <h4>{{ trans('account/budgeter.form.tips.service-info') }}</h4>
            <p>{!! nl2br($service->description) !!}</p>
        </div>
        <div class="col-xs-12 col-sm-6">
            <h4>{{ trans('account/budgeter.form.tips.geral-info') }}</h4>
            <p>{!! nl2br($service->description2) !!}</p>
        </div>
    </div>
    @endif
</div>
@if(Auth::check())
    @include('account.budgeter.cost_details')
@endif

<script>
    $('[data-toggle="tooltip"]').tooltip();
</script>