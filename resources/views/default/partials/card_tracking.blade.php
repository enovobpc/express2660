<?php
$stepId = $shipment['stepId'];
$stepStatus = $shipment['stepStatus'];
$shipment = $shipment['shipment'];
?>
<div class="card card-tracking m-b-15" data-tracking="{{ $shipment['id'] }}">
    <div class="card-body">
        <div class="row align-items-center-sm">
            <div class="col-xs-12 col-sm-9">
                <div class="spacer-25 hidden-xs"></div>
                <ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
                    <li class="active">
                        <a href="#discover">
                            <i class="fas fa-file-alt"></i>
                            <p>{{ trans('account/tracking.progress.pending') }}</p>
                        </a>
                    </li>
                    <li class="{{ $stepId >= 2 ? 'active' : '' }}">
                        <a href="#">
                            <i class="fas fa-clipboard-check"></i>
                            <p>{{ trans('account/tracking.progress.accepted') }}</p>
                        </a>
                    </li>
                    <li class="{{ $stepId >= 3 ? 'active' : '' }}">
                        <a href="#">
                            <i class="fas fa-dolly"></i>
                            <p>{{ trans('account/tracking.progress.pickup') }}</p>
                        </a>
                    </li>
                    @if ($stepStatus == 'canceled')
                        <li class="active incidence">
                            <a href="#">
                                <i class="fas fa-times"></i>
                                <p>{{ trans('account/tracking.progress.canceled') }}</p>
                            </a>
                        </li>
                    @else
                        <li class="{{ $stepId >= 4 ? 'active' : '' }}">
                            <a href="#">
                                <i class="fas fa-shipping-fast"></i>
                                <p>{{ trans('account/tracking.progress.transit') }}</p>
                            </a>
                        </li>
                        <li
                            class="{{ $stepStatus == 'incidence' ? 'incidence' : '' }} {{ $stepStatus == 'returned' ? 'returned' : '' }} {{ $stepId >= 5 ? 'active' : '' }}">
                            <a href="#">
                                @if ($stepStatus == 'incidence')
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <p>{{ trans('account/tracking.progress.incidence') }}</p>
                                @elseif($stepStatus == 'returned')
                                    <i class="fas fa-undo"></i>
                                    <p>{{ trans('account/tracking.progress.returned') }}</p>
                                @else
                                    <i class="fas fa-check"></i>
                                    <p>{{ trans('account/tracking.progress.delivered') }}</p>
                                @endif
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="col-xs-12 col-sm-3">
                <div class="details-box">
                    <h4 class="text-center m-b-10 bold">TRK#{{ @$shipment->tracking_code }}</h4>
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-condensed">
                                {{-- @if (Setting::get('tracking_show_delivery_date'))
                                    @if (@$shipment->last_history->provider_agency_code)
                                    <tr>
                                        <td class="text-muted text-right">Apoio Cliente</td>
                                        <td>
                                            <i class="fas fa-phone"></i> {{ @$shipment->last_history->provider_agency->phone }}
                                        </td>
                                    </tr>
                                    @elseif(@$shipment->last_history->agency_id)
                                        <tr>
                                            <td class="text-muted text-right">Apoio Cliente</td>
                                            <td>
                                                <i class="fas fa-phone"></i> {{ @$shipment->last_history->agency->phone }}
                                            </td>
                                        </tr>
                                    @endif
                                @endif --}}
                                @if ($shipment->provider_tracking_code && Setting::get('tracking_show_provider_trk'))
                                    <tr>
                                        <td class="text-muted text-right bold">Nº {{ @$shipment->provider->name }}</td>
                                        <td class="bold">
                                            <span data-toggle="tooltip"
                                                title="Em alternativa ao código de encomenda, poderá fornecer este número caso lhe seja solicitado pelo apoio ao cliente.">
                                                @if (in_array($shipment->webservice_method, ['envialia', 'tipsa', 'nacex']))
                                                    {{ $shipment->provider_cargo_agency }}&nbsp;
                                                @endif
                                                {{ $shipment->provider_tracking_code }}
                                                <i class="fas fa-info-circle"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                                @if ($shipment->reference)
                                    <tr>
                                        <td class="text-muted text-right">{{ trans('account/global.word.reference') }}</td>
                                        <td>
                                            <span data-toggle="tooltip"
                                                title="Esta é a referência do seu remetente para a encomenda. Use-a caso o remetente a solicite.">
                                                {{ $shipment->reference }}
                                                <i class="fas fa-info-circle"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @endif
        
                                @if ($shipment->delivery_date && Setting::get('tracking_show_delivery_date'))
                                    <?php
                                    $deliveryDate = $shipment->delivery_date ? new Date($shipment->delivery_date) : null;
                                    ?>
                                    <tr>
                                        <td class="w-140px text-muted text-right">
                                            {{ trans('account/global.word.delivery-prevision') }}</td>
                                        <td>{{ $deliveryDate? $deliveryDate->format('Y-m-d') .($deliveryDate->format('H:i') == '00:00' ?: ' até ' . $deliveryDate->format('H:i')): 'N/A' }}
                                        </td>
                                    </tr>
                                @endif
                                @if (Setting::get('change_info_shipment') && $shipment->status_id != \App\Models\ShippingStatus::DELIVERED_ID)
                                    <tr>
                                        <td class="w-140px text-muted text-right"></td>
                                        <td><a href="{{ route('tracking.reschedule.edit', $shipment['id']) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                                            Reagendar Entrega</a>
                                        </td>
                                    </tr>
                                @endif
                                @if (@$shipment->lastHistory->provider_agency && Setting::get('tracking_show_delivery_agency'))
                                    <tr>
                                        <td class="text-muted text-right">Atendimento</td>
                                        <td class="bold">
                                            <i class="fas fa-phone"></i>
                                            {{ @$shipment->lastHistory->provider_agency->phone }}
                                        </td>
                                    </tr>
                                @endif
                                {{-- <tr>
                                    <td class="text-muted text-right">{{ trans('account/global.word.volumes') }}</td>
                                    <td>{{ $shipment->volumes }}</td>
                                </tr> --}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($shipment->live_tracking)
        @include('default.partials.live_tracking')
    @endif
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="content-left" style="margin: -15px;">
                    <div class="row">
                        @if (!$shipment)
                            <div class="col-sm-12 text-center">
                                <h4><i class="fas fa-info-circle"></i>
                                    {{ trans('account/tracking.empty.title', ['trk' => $tracking]) }}</h4>
                                <p>{{ trans('account/tracking.empty.msg') }}</p>
                                <div class="spacer-50"></div>
                            </div>
                        @else
                            <div class="col-sm-12">
                                {{-- <h4 class="m-b-10">Detalhes do Envio</h4> --}}
                                <div class="table-responsive">
                                    <table class="table table-history">
                                        <thead>
                                            <tr>
                                                <th class="w-110px">{{ trans('account/global.word.date') }}
                                                </th>
                                                <th class="w-60px">{{ trans('account/global.word.hour') }}
                                                </th>
                                                <th class="w-120px">{{ trans('account/global.word.status') }}
                                                </th>
                                                <th class="w-170px">
                                                    {{ trans('account/global.word.warehouse') }}</th>
                                                <th class="w-30">{{ trans('account/global.word.details') }}
                                                </th>
                                                <th>{{ trans('account/global.word.obs') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($shipment->history as $item)
                                                <tr>
                                                    <td>{{ $item->created_at->format('Y-m-d') }}</td>
                                                    <td>{{ $item->created_at->format('H:i') }}</td>
                                                    <td>
                                                        <span class="label"
                                                            style="background: {{ @$item->status->color }}">{{ @$item->status->{'name_' . LaravelLocalization::setLocale()} ?? @$item->status->name }}</span>
                                                    </td>
                                                    <td>
                                                        @if ($item->provider_agency_code)
                                                            @if (Setting::get('tracking_show_delivery_agency'))
                                                                <div data-toggle="popover" data-placement="top"
                                                                    data-trigger="hover"
                                                                    title="{{ @$item->provider_agency->code }} - {{ @$item->provider_agency->name }}"
                                                                    data-html="true"
                                                                    data-content="{{ agencyTip($item->provider_agency, true) }}">
                                                                    {{ @$item->provider_agency->name }}
                                                                    <i class="fas fa-phone"
                                                                        style="top: -1px;position: relative;font-size: 11px;"></i>
                                                                </div>
                                                            @else
                                                                {{ @$item->provider_agency->print_name }}
                                                            @endif
                                                        @elseif(@$item->agency_id)
                                                            @if (Setting::get('tracking_show_delivery_agency'))
                                                                <div data-toggle="popover" data-placement="top"
                                                                    data-trigger="hover"
                                                                    title="{{ @$item->agency->name }}"
                                                                    data-html="true"
                                                                    data-content="{{ agencyTip($item->agency, true) }}">
                                                                    {{ @$item->agency->print_name }}
                                                                    <i class="fas fa-phone "
                                                                        style="top: -1px;position: relative;font-size: 11px;"></i>
                                                                </div>
                                                            @else
                                                                {{ @$item->agency->print_name }}
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="fs-13">{{ @$item->status->{'description_' . LaravelLocalization::setLocale()} ?? @$item->status->description }}</td>
                                                    <td>
                                                        @if ($item->status_id == \App\Models\ShippingStatus::DELIVERED_ID && Setting::get('tracktrace_show_signature'))
                                                            <a href="#" data-toggle="modal"
                                                                data-target="#modal-signature"
                                                                class="btn btn-xs btn-default m-b-3">
                                                                <i class="fas fa-signature"></i>
                                                                {{ trans('account/tracking.word.consult-pod') }}
                                                            </a>
                                                            @include(
                                                                'default.modals.signature'
                                                            )
                                                        @endif

                                                        @if (Setting::get('tracking_location_active') && $item->latitude && $item->longitude)
                                                            <div class="clearfix"></div>
                                                            <a href="http://maps.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}"
                                                                target="_blank" class="btn btn-xs btn-default">
                                                                <i class="fas fa-map-marker-alt"></i> Consultar
                                                                localização
                                                            </a>
                                                        @endif

                                                        @if (@$shipment->provider_id == '21' && config('app.source') == '2660express')
                                                            @if(@$item->status_id != 2)
                                                            <?
                                                                $my_str = $item->obs;
                                                                echo str_replace("CTT", "2660 Express", $my_str);
                                                            ?>
                                                            @endif
                                                        @else
                                                            {!! $item->obs !!}
                                                        @endif

                                                        <br>    
                                                         @if(!$item->resolutions->isEmpty())
                                                            <strong> Resolução da Incidência </strong> <br> {{  $item->resolutions[0]->obs }}
                                                        @endif
                                                        
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.modals.remote_md')