@section('title')
    {{ trans('website.seo.tracking.title') }} |
@stop

@section('metatags')
    <meta name="description" content="{{ trans('website.seo.tracking.description') }}">
    <meta property="og:title" content="{{ trans('website.seo.tracking.title') }}">
    <meta property="og:description" content="{{ trans('website.seo.tracking.description') }}">
    <meta property="og:image" content="{{ trans('website.seo.image.url') }}">
    <meta property="og:image:width" content="{{ trans('website.seo.image.width') }}">
    <meta property="og:image:height" content="{{ trans('website.seo.image.height') }}">
    <meta name="robots" content="index, follow">
@stop

@section('content')
    <section class="header-title">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-7">
                    <h1>{{ trans('website.seo.tracking.title') }}</h1>
                </div>

            </div>
        </div>
    </section>
 <section class="search-tracking" style="padding-bottom: 15px">
        <div class="container account-container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="card card-tracking m-b-0 p-20">
                        <div class="card-body">
                            <form method="GET" action="{{ route('website.tracking.index') }}" accept-charset="UTF-8">
                                @if(!empty($tracking))
                                <div class="row row-5 m-t-10">
                                    <div class="col-sm-3 col-md-3">
                                        <label class="m-t-10 pull-right hidden-xs search-label">
                                            {{ trans('account/tracking.form.label') }}
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-md-5">
                                        <div class="form-group m-b-0">
                                            <div class="input-group">
                                                <div class="input-group-addon" style="background: #fff;border: 1px solid #ccc; border-right: 0;">
                                                    <i class="fas fa-paper-plane"></i>
                                                </div>
                                                <input class="form-control input-lg nospace" value="{{ $tracking }}" placeholder="Ex. 001023900321, 005068105492, ..." name="tracking" type="text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-md-3">
                                        <button type="submit" class="btn btn-primary">
                                            {{ trans('account/tracking.form.button') }}
                                        </button>
                                        <button type="button" class="btn btn-default">
                                            <i class="fas fa-question-circle text-blue"
                                               data-toggle="tooltip"
                                               title="{{ trans('account/tracking.form.tip') }}"></i>
                                        </button>
                                    </div>
                                </div>
                                @else
                                    <div class="row">
                                        <div class="col-xs-12 col-md-offset-3 col-md-6">
                                            <div class="spacer-30"></div>
                                            <div class="text-center">
                                                <img class="h-60px" src="data:image/svg+xml;base64,
PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTEyIDUxMjsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIj48Zz48Zz4KCTxnPgoJCTxwYXRoIGQ9Ik00OTEuNzI5LDExMi45NzFMMjU5LjI2MSwwLjc0NWMtMi4wNjEtMC45OTQtNC40NjEtMC45OTQtNi41MjEsMEwyMC4yNzEsMTEyLjk3MWMtMi41OTIsMS4yNTEtNC4yMzksMy44NzYtNC4yMzksNi43NTQgICAgdjI3Mi41NDljMCwyLjg3OCwxLjY0Nyw1LjUwMyw0LjIzOSw2Ljc1NGwyMzIuNDY4LDExMi4yMjZjMS4wMywwLjQ5NywyLjE0NiwwLjc0NiwzLjI2MSwwLjc0NnMyLjIzLTAuMjQ5LDMuMjYxLTAuNzQ2ICAgIGwyMzIuNDY4LTExMi4yMjZjMi41OTItMS4yNTEsNC4yMzktMy44NzYsNC4yMzktNi43NTRWMTE5LjcyNkM0OTUuOTY4LDExNi44NDYsNDk0LjMyLDExNC4yMjMsNDkxLjcyOSwxMTIuOTcxeiBNMjU2LDE1LjgyOCAgICBsMjE1LjIxNywxMDMuODk3bC02Mi4zODcsMzAuMTE4Yy0wLjM5NS0wLjMwMS0wLjgxMi0wLjU3OS0xLjI3LTAuOEwxOTMuODA1LDQ1Ljg1M0wyNTYsMTUuODI4eiBNMTc2Ljg2Nyw1NC4zMzNsMjE0LjkwNCwxMDMuNzQ2ICAgIGwtNDQuMDE1LDIxLjI0OUwxMzIuOTQxLDc1LjYyNEwxNzYuODY3LDU0LjMzM3ogTTM5Ni43OTksMTcyLjMwN3Y3OC41NDZsLTQxLjExMywxOS44NDh2LTc4LjU0NkwzOTYuNzk5LDE3Mi4zMDd6ICAgICBNNDgwLjk2OCwzODcuNTY4TDI2My41LDQ5Mi41NVYyMzYuNjU4bDUxLjg3My0yNS4wNDJjMy43My0xLjgwMSw1LjI5NC02LjI4NCwzLjQ5My0xMC4wMTUgICAgYy0xLjgwMS0zLjcyOS02LjI4NC01LjI5NS0xMC4wMTUtMy40OTNMMjU2LDIyMy42MjNsLTIwLjc5Ni0xMC4wNGMtMy43MzEtMS44MDMtOC4yMTQtMC4yMzctMTAuMDE1LDMuNDkzICAgIGMtMS44MDEsMy43My0wLjIzNyw4LjIxNCwzLjQ5MywxMC4wMTVsMTkuODE4LDkuNTY3VjQ5Mi41NUwzMS4wMzIsMzg3LjU2NlYxMzEuNjc0bDE2NS42LDc5Ljk0NSAgICBjMS4wNTEsMC41MDgsMi4xNjIsMC43NDgsMy4yNTUsMC43NDhjMi43ODgsMCw1LjQ2Ni0xLjU2Miw2Ljc1OS00LjI0MWMxLjgwMS0zLjczLDAuMjM3LTguMjE0LTMuNDkzLTEwLjAxNWwtMTYyLjM3LTc4LjM4NiAgICBsNzQuNTA1LTM1Ljk2OEwzNDAuNTgyLDE5Mi41MmMwLjAzMywwLjA0NiwwLjA3LDAuMDg3LDAuMTA0LDAuMTMydjg5Ljk5OWMwLDIuNTgxLDEuMzI3LDQuOTgsMy41MTMsNi4zNTMgICAgYzEuMjE0LDAuNzYyLDIuNTk5LDEuMTQ3LDMuOTg4LDEuMTQ3YzEuMTEyLDAsMi4yMjctMC4yNDcsMy4yNi0wLjc0Nmw1Ni4xMTMtMjcuMDg5YzIuNTkyLTEuMjUxLDQuMjM5LTMuODc1LDQuMjM5LTYuNzU0di05MC40OTUgICAgbDY5LjE2OS0zMy4zOTJWMzg3LjU2OHoiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6IzRFNEU0RSIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD4KCTwvZz4KPC9nPjxnPgoJPGc+CgkJPHBhdGggZD0iTTkyLjkyNiwzNTguNDc5TDU4LjgxMSwzNDIuMDFjLTMuNzMyLTEuODAzLTguMjE0LTAuMjM3LTEwLjAxNSwzLjQ5M2MtMS44MDEsMy43My0wLjIzNyw4LjIxNCwzLjQ5MywxMC4wMTUgICAgbDM0LjExNSwxNi40NjljMS4wNTEsMC41MDgsMi4xNjIsMC43NDgsMy4yNTUsMC43NDhjMi43ODgsMCw1LjQ2Ni0xLjU2Miw2Ljc1OS00LjI0MSAgICBDOTguMjIsMzY0Ljc2Myw5Ni42NTYsMzYwLjI4MSw5Mi45MjYsMzU4LjQ3OXoiIGRhdGEtb3JpZ2luYWw9IiMwMDAwMDAiIGNsYXNzPSJhY3RpdmUtcGF0aCIgc3R5bGU9ImZpbGw6IzRFNEU0RSIgZGF0YS1vbGRfY29sb3I9IiMwMDAwMDAiPjwvcGF0aD4KCTwvZz4KPC9nPjxnPgoJPGc+CgkJPHBhdGggZD0iTTEyNC4zMjMsMzM4LjA0MmwtNjUuNDY1LTMxLjYwNGMtMy43MzEtMS44MDEtOC4yMTQtMC4yMzctMTAuMDE1LDMuNDk0Yy0xLjgsMy43My0wLjIzNiw4LjIxNCwzLjQ5NCwxMC4wMTUgICAgbDY1LjQ2NSwzMS42MDRjMS4wNTEsMC41MDcsMi4xNjIsMC43NDgsMy4yNTUsMC43NDhjMi43ODgsMCw1LjQ2Ni0xLjU2Miw2Ljc1OS00LjI0MSAgICBDMTI5LjYxNywzNDQuMzI2LDEyOC4wNTMsMzM5Ljg0MiwxMjQuMzIzLDMzOC4wNDJ6IiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIiBjbGFzcz0iYWN0aXZlLXBhdGgiIHN0eWxlPSJmaWxsOiM0RTRFNEUiIGRhdGEtb2xkX2NvbG9yPSIjMDAwMDAwIj48L3BhdGg+Cgk8L2c+CjwvZz48L2c+IDwvc3ZnPg==" />
                                            </div>
                                            <h4 class="lh-1-4 m-b-0 text-center">
                                                {{ trans('account/tracking.index.title') }}
                                            </h4>
                                            <p class="text-center text-muted m-b-50">{{ trans('account/tracking.index.subtitle') }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 col-sm-offset-3">
                                            <label class="m-t-10 pull-righ fw-400 fs-15">
                                                {{ trans('account/tracking.form.label') }}
                                            </label>
                                            <div class="form-group m-b-0">
                                                <input class="form-control input-lg nospace" value="{{ $tracking }}" placeholder="Ex. 001023900321, 005068105492, ..." name="tracking" type="text">
                                            </div>
                                            <div class="m-t-15 text-center">
                                                <button type="submit" class="btn btn-primary fs-16">
                                                    <i class="fas fa-search"></i> {{ trans('account/tracking.form.button') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="spacer-80 hidden-xs"></div>
                                    <div class="spacer-30 visible-xs"></div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section style="padding-top: 0">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    @foreach($shipmentsResults as $shipment)
                    <?php
                    $stepId     = $shipment['stepId'];
                    $stepStatus = $shipment['stepStatus'];
                    $shipment   = $shipment['shipment'];
                    ?>
                    <div class="card card-tracking m-b-15">
                        <div class="card-body">
                            <div class="row">
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
                                        @if($stepStatus == 'canceled')
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
                                        <li class="{{ $stepStatus == 'incidence' ? 'incidence' : '' }} {{ $stepStatus == 'returned' ? 'returned' : '' }} {{ $stepId >= 5 ? 'active' : '' }}">
                                            <a href="#">
                                                @if($stepStatus == 'incidence')
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
                                        <h4 class="text-center m-b-10 bold">TRK#{{ $shipment->tracking_code }}</h4>
                                        <table class="table table-condensed">
                                            @if($shipment->reference)
                                                <tr>
                                                    <td class="field">{{ trans('account/global.word.reference') }}</td>
                                                    <td>{{ $shipment->reference }}</td>
                                                </tr>
                                            @endif
                                                <tr>
                                                    <td class="field">{{ trans('account/global.word.service') }}</td>
                                                    <td>{{ @$shipment->service->name }}</td>
                                                </tr>
                                            <tr>
                                                <td class="field">{{ trans('account/global.word.delivery-prevision') }}</td>
                                                <td>{{ $shipment->delivery_date ? $shipment->delivery_date : 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                       {{-- <div class="ontime-tracking m-b-30">
                            <table class="table table-history m-0">
                                <tr>
                                    <th class="">
                                        <i class="fas fa-map-marker-alt"></i> Seguimento <i>On Time</i>
                                        <span class="pull-right">Localização Ativa</span>
                                    </th>
                                </tr>
                            </table>
                            <div class="tracking-history">
                                <div class="operator-avatar">
                                    <img src="{{ @$shipment->operator->filepath }}" onerror="this.src='{{ asset('assets/img/default/avatar.png') }}'"/>
                                    <div class="pull-left">
                                        <h4>
                                            <small>Operador</small><br/>
                                            @if(1 && @$shipment->operator->name)
                                            {{ @$shipment->operator->name }}
                                            @else
                                                Nome Indisponível
                                            @endif
                                            <br/>
                                            @if($shipment->vehicle)
                                            <small class="vehicle">
                                                <i class="fas fa-car"></i> {{ $shipment->vehicle }}
                                            </small>
                                            @endif
                                        </h4>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="navegation-status">
                                    <i class=""></i> Em execução. <i class="fas fa-info-circle" data-toggle="tooltip" title="A localização é atualizada a cada 10 minutos."></i>

                                    <h4 class="last-status" style="color: {{ @$shipment->status->color }}">
                                        <small class="status-date">{{ @$shipment->last_history->created_at->format('Y-m-d H:i') }}</small>
                                        <small>Estado</small><br/>
                                        {{ @$shipment->status->name }}
                                    </h4>
                                </div>
                                <div class="navegation-history">
                                    --}}{{--<table>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr><tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr><tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50px">15:15</td>
                                            <td>Rua Maria Casimira</td>
                                        </tr>


                                    </table>--}}{{--
                                    <span class="disabled-history">
                                        <i class="fas fa-location-arrow"></i>
                                        <br/>
                                        Seguimento Indisponível
                                        <br/>
                                        <small>
                                            De momento não é possível
                                            acompanhar a sua entrega
                                            em tempo real.
                                        </small>
                                    </span>
                                </div>
                            </div>
                            <div class="" id="map" style="height: 350px"></div>
                        </div>--}}
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="content-left">
                                        <div class="row">
                                            @if(!$shipment)
                                                <div class="col-sm-12 text-center">
                                                    <h4><i class="fas fa-info-circle"></i> {{ trans('account/tracking.empty.title', ['trk' => $tracking]) }}</h4>
                                                    <p>{{ trans('account/tracking.empty.msg') }}</p>
                                                    <div class="spacer-50"></div>
                                                </div>
                                            @else
                                                <div class="col-sm-12">
                                                    {{--<h4 class="m-b-10">Detalhes do Envio</h4>--}}
                                                    <div class="table-responsive">
                                                        <table class="table table-history">
                                                            <tr>
                                                                <th class="w-95px">{{ trans('account/global.word.date') }}</th>
                                                                <th class="w-60px">{{ trans('account/global.word.hour') }}</th>
                                                                <th class="w-180px">{{ trans('account/global.word.status') }}</th>
                                                                <th class="w-120px">{{ trans('account/global.word.warehouse') }}</th>
                                                                <th class="w-40">{{ trans('account/global.word.details') }}</th>
                                                                <th>{{ trans('account/global.word.obs') }}</th>
                                                            </tr>
                                                            @foreach($shipment->history as $item)
                                                                <tr>
                                                                    <td>{{ $item->created_at->format('Y-m-d') }}</td>
                                                                    <td>{{ $item->created_at->format('H:i') }}</td>
                                                                    <td class="status">
                                                                        {{ $item->status->name }}
                                                                    </td>
                                                                    <td>{{ @$item->status->agency->name }}</td>
                                                                    <td class="details">{{ $item->status->description }}</td>
                                                                    <td>
                                                                        @if($item->status_id == \App\Models\ShippingStatus::DELIVERED_ID && Setting::get('tracktrace_show_signature'))
                                                                            <a href="#" data-toggle="modal" data-target="#modal-signature" class="btn btn-default">{{ trans('account/tracking.word.consult-pod') }}</a>
                                                                            @include('default.modals.signature')
                                                                        @endif
                                                                        {!! $item->obs !!}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
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
                    @endforeach

                </div>
            </div>
        </div>
    </section>
@stop

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}"></script>
    <script>
        var myLatlng = new google.maps.LatLng({{ Setting::get('maps_latitude_1') }},{{ Setting::get('maps_longitude_1') }});
        var mapOptions = {
            zoom: 16,
            center: myLatlng,
            mapTypeControl: false,
            scaleControl: false,
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE
            },
        }
        var map = new google.maps.Map(document.getElementById("map"), mapOptions);

        var marker = new google.maps.Marker({
            position: myLatlng,
            icon: "{{ asset('assets/website/img/map-marker.svg') }}",
            title:"ESTAMOS AQUI"
        });

        // To add the marker to the map, call setMap();
        marker.setMap(map);
    </script>
@stop