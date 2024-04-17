@section('title')
    @if(app_mode_cargo())
    @trans('Mapas de Viagem')
    @else
    @trans('Mapas de Distribuição')
    @endif
@stop

@section('content-header')
    @if(app_mode_cargo())
    @trans('Mapas de Viagem')
    @else
    @trans('Mapas de Distribuição')
    @endif
@stop

@section('breadcrumb')
    <li class="active">
        <a href="{{ route('admin.trips.index') }}">
            @if(app_mode_cargo())
            @trans('Mapas de Viagem')
            @else
            @trans('Mapas de Distribuição')
            @endif
        </a>
    </li>
    <li class="active">@trans('Mapa') #{{ $trip->code }}</li>
@stop

@section('content')
    {{-- <p>
        Na lista de selecao aparecer claramente o numero de metros estrado<br/>
        Ao editar oculta o campo LDM<br/>
        Ao adicionar nova carga da lista ou do mapa, avisar limite de peso e LDM<br/>
        Na lista de viagens mostrar o numero de peso e metros estado disponíveis<br/>
    </p> --}}
    <div class="row">
        <div class="col-md-12">
            <div class="box no-border m-b-15" style="{{ $trip->type == 'R' ? 'background: #22b4fe4d;' : ''}}">
                <div class="box-body p-5">
                    <div class="row">
                        <div class="col-xs-12 col-md-2">
                            <div class="pull-left w-85">
                                <h3 class="pull-left bold" style="margin: -6px 10px; line-height: 26px;">
                                    @if($cargoAppMode)
                                    <small>@trans('Viagem')</small>
                                    @else
                                    <small>@trans('Distribuição')</small>
                                    @endif
                                    <br/>{{ $trip->code }}
                                </h3>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-8">
                            <div class="row">
                                <div class="col-sm-2">
                                    <h5 class="m-0">
                                        <small>@trans('Motorista')</small><br/>
                                        <b>
                                            <div class="opphoto">
                                                <img src="{{ @$trip->operator->filepath ? asset(@$trip->operator->getCroppa(200,200)) : asset('assets/img/default/avatar.png') }}" class="image-preview" data-img="{{ @$trip->operator->filepath ? asset(@$trip->operator->filepath) : asset('assets/img/default/avatar.png') }}"/>
                                            </div>
                                            {{ @$trip->operator->name ?? 'N/A' }}</b>
                                        <br/>
                                        <small class="text-black">
                                            @if(!empty($trip->assistants))
                                                {{ implode(', ', $trip->assistants()->pluck('name')->toArray()) }}
                                            @endif
                                        </small>
                                    </h5>
                                </div>
                                <div class="col-sm-10">
                                    <ul class="list-inline w-100 m-0 pull-right">
                                        <li class="w-140px">
                                            <div class="pull-left m-0 m-t-5" style="line-height: 18px; margin-bottom: -10px;     overflow: hidden;
                                            text-overflow: ellipsis;
                                            white-space: nowrap;
                                            width: 135px;">
                                                <i class="flag-icon flag-icon-{{ $trip->start_country }}"></i> {{ $trip->start_location }}
                                                <br/>
                                                <i class="flag-icon flag-icon-{{ $trip->end_country }}"></i> {{ $trip->end_location }}
                                            </p>
                                        </li>
                                        <li class="w-100px" style="border-left: 1px solid #999">
                                            <h5 class="m-0 p-l-5">
                                                <small>@trans('Viatura')</small><br/>
                                                <b>{{ @$trip->vehicle ? @$trip->vehicle : 'N/A' }}</b>
                                                <br/>
                                                <b class="text-black">{{ @$trip->trailer }}&nbsp;</b>
                                            </h5>
                                        </li>
                                        <li class="w-100px">
                                            <h5 class="m-b-5 m-t-0 lh-1p3">
                                                <small>@trans('Data Início')</small><br/>
                                                <b>{{ $trip->start_date }}</b>
                                                <br/>
                                                <small class="text-black text-overflow fs-12">{{ $trip->start_hour }}</small>
                                            </h5>
                                        </li>
                                        <li class="w-100px">
                                            <h5 class="m-0">
                                                <small>@trans('Data Fim')</small><br/>
                                                <b>{{ @$trip->end_date ? @$trip->end_date : '--' }}</b>
                                                <br/>
                                                <small class="text-black text-overflow">{{ $trip->end_hour ?? '--' }}</small>
                                            </h5>
                                        </li>
                                        <li style="border-left: 1px solid #999">
                                            <h5 class="m-0">
                                                <small>@trans('Rota')</small><br/>
                                                <b>{{ number($trip->kms, 0) }}km</b>
                                                <br/>
                                                <small class="text-black">{{ @$trip->delivery_route->name ? @$trip->delivery_route->name : 'N/A' }}</small>
                                            </h5>
                                        </li>
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-2">
                            <a href="{{ route('admin.trips.edit', $trip->id) }}"
                               data-toggle="modal"
                               data-target="#modal-remote-lg"
                               class="btn btn-sm btn-default pull-right m-r-5 m-t-9 pull-right">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <div class="btn-group btn-group-sm pull-right m-r-5 m-t-9" role="group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-print"></i> @trans('Imprimir') <i class="fas fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.trips.print', [$trip->id, 'summary']) }}" target="_blank">
                                            @if($cargoAppMode)
                                            @trans('Folha de Viagem')
                                            @else
                                            @trans('Mapa Distribuição')
                                            @endif
                                        </a>
                                    </li>
                                    @if(hasPermission('billing'))
                                    <li>
                                        <a href="{{ route('admin.trips.print', [$trip->id, 'summary', 'prices' => 1]) }}" target="_blank">
                                            @trans('Balancete Viagem')
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        <a href="{{ route('admin.trips.activity-declatation.edit', [$trip->id]) }}"
                                        data-toggle="modal"
                                        data-target="#modal-remote">
                                            @trans('Declaração Atividade')
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="{{ route('admin.trips.print', [$trip->id, 'labels']) }}" target="_blank">
                                            @trans('Etiquetas')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.trips.print', [$trip->id, 'transport-guide']) }}" target="_blank">
                                            @trans('Guias Transporte')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.trips.print', [$trip->id, 'goods']) }}" target="_blank">
                                            @trans(' Manifesto Mercadoria')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.trips.print', [$trip->id, 'delivery']) }}" target="_blank">
                                            @trans('Manifesto Entrega')
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="{{ route('admin.trips.print', [$trip->id, 'shipments']) }}" target="_blank">
                                            @trans('Resumo Faturação')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-md-3 col-lg-2">
            <div class="box box-solid box-sidebar">
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="active">
                            <a href="#tab-shipments" data-toggle="tab">
                                <i class="fas fa-fw fa-truck"></i> @trans('Serviços a transportar')
                                <div class="badge pull-right">{{ @$trip->shipments->count() }}</div>
                            </a>
                        </li>
                        <li>
                            <a href="#tab-goods" data-toggle="tab">
                                <i class="fas fa-fw fa-pallet"></i> @trans('Mercadoria')
                                <div class="badge pull-right">{{ $dimensions->count() }}</div>
                            </a>
                        </li>
                        <li>
                            <a href="#tab-map" data-toggle="tab">
                                <i class="fas fa-fw fa-map"></i> @trans('Rota Entrega')
                            </a>
                        </li>
                        <li>
                            <a href="#tab-expenses" data-toggle="tab">
                                <i class="fas fa-fw fa-euro-sign"></i> @trans('Custos de Viagem')
                                <div class="badge pull-right">{{ $trip->expenses->count() }}</div>
                            </a>
                        </li>
                        <li>
                            <a href="#tab-history" data-toggle="tab">
                                <i class="fas fa-fw fa-stream"></i> @trans('Histórico da Viagem')
                            </a>
                        </li>
                        <li>
                            <a href="#tab-attachments" data-toggle="tab">
                                <i class="fas fa-fw fa-file"></i> @trans('Documentos')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="box box-solid box-sidebar">
                <div class="box-body">
                    @if(hasPermission('billing'))
                    <div class="balance-panel">
                        <div class="m-b-5 text-center">
                            <h3 class="m-0 bold">
                                <small>@trans('Balanço Viagem')</small><br/>
                                @if($trip->balance >= 0.00)
                                    <span class="text-green">
                                        <i class="fas fa-caret-up"></i> {{ money($trip->balance, Setting::get('app_currency')) }}
                                    </span>
                                @else
                                    <span class="text-red">
                                        <i class="fas fa-caret-down"></i> {{ money($trip->balance, Setting::get('app_currency')) }}
                                    </span>
                                @endif
                            </h3>
                        </div>
                        <div class="row text-center">
                            <div class="col-sm-6">
                                <div class="m-b-15">
                                    <h4 class="m-0">
                                        <small>@trans('Ganho')</small><br/>
                                        {{ money($trip->billing_subtotal, Setting::get('app_currency')) }}
                                    </h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="m-b-15">
                                    <h4 class="m-0">
                                        <small>@trans('Custos')</small><br/>
                                        {{ money($trip->cost_billing_subtotal, Setting::get('app_currency')) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-sm-12">
                                <div style="border: 1px solid; margin: 0 5px 15px 5px; border-radius: 3px; border-color: #ddd; background: #f2f2f2">
                                    <div class="row row-5">
                                        <div class="col-sm-4">
                                            <h5 class="m-t-0 m-b-5">
                                                <small>@trans('Ganho/KM')</small><br/>
                                                {{ money($trip->gain_km, Setting::get('app_currency')) }}
                                            </h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <h5 class="m-t-0 m-b-5">
                                                <small>@trans('Custo/KM')</small><br/>
                                                {{ money($trip->cost_km, Setting::get('app_currency')) }}
                                            </h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <h5 class="m-t-0 m-b-5">
                                                <small>@trans('Emisões CO2')</small><br/>
                                                {{ money($trip->co2_emissions) }}gr
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="m-b-15">
                        <label>@trans('Conclusão')</label>
                        <table style="width: 100%">
                            <tr>
                                <td style="width: {{ @$stats['conclusion']['percent'] }}%; background: {{ @$stats['conclusion']['color'] }}; height: 5px"></td>
                                <td style="width: {{ 100-@$stats['conclusion']['percent'] }}%; background: #ddd"></td>
                            </tr>
                        </table>
                        <small>
                            {{ money(@$stats['conclusion']['percent']) }}%
                            <span class="pull-right">({{ @$stats['conclusion']['delivered'] }}/{{ @$stats['conclusion']['total'] }})</span>
                        </small>
                    </div>
                    <div class="m-b-15">
                        <span class="pull-right bold"><small>{{ (int) @$stats['weight']['max'] }}Kg</small></span>
                        <label>@trans('Peso Veículo')</label>
                        <table style="width: 100%">
                            <tr>
                                <td style="width: {{ @$stats['weight']['percent'] }}%; background: {{ @$stats['weight']['color'] }}; height: 5px"></td>
                                <td style="width: {{ 100 - @$stats['weight']['percent'] }}%; background: #ddd"></td>
                            </tr>
                        </table>
                        <small>
                            {{ money(@$stats['weight']['percent']) }}% / {{ @$stats['weight']['total'] }}  kg
                            <span class="pull-right">Livre {{ @$stats['weight']['max'] - @$stats['weight']['total'] }} Kg</span>
                        </small>
                    </div>
                    {{-- @if(@$stats['ldm']['percent'] > 0.00) --}}
                        <div class="m-b-15">
                            <span class="pull-right bold"><small>{{ (int) @$stats['ldm']['max'] }}Mt</small></span>
                            <label>LDM</label>
                            <table style="width: 100%">
                                <tr>
                                    <td style="width: {{ @$stats['ldm']['percent'] }}%; background: {{ @$stats['ldm']['color'] }}; height: 5px"></td>
                                    <td style="width: {{ 100 - @$stats['ldm']['percent'] }}%; background: #ddd"></td>
                                </tr>
                            </table>
                            <small>
                                {{ money(@$stats['ldm']['percent']) }}% / {{ @$stats['ldm']['total'] }}m
                                <span class="pull-right">Livre {{ @$stats['ldm']['max'] - @$stats['ldm']['total'] }}m</span>
                            </small>
                        </div>
                    {{-- @endif --}}
                </div>
            </div>
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="nav-tabs-custom trip-content">
                <div class="tab-content">
                    {{--<div class="tab-pane" id="tab-edit">
                        @include('admin.trips.tabs.edit')
                    </div>--}}
                    <div class="tab-pane active" id="tab-shipments">
                        @include('admin.trips.tabs.shipments')
                    </div>
                    <div class="tab-pane" id="tab-goods">
                        @include('admin.trips.tabs.goods')
                    </div>
                    <div class="tab-pane" id="tab-map" style="position: relative; margin: -10px">
                        @include('admin.trips.tabs.map')
                    </div>
                    <div class="tab-pane" id="tab-expenses">
                        @include('admin.trips.tabs.expenses')
                    </div>
                    <div class="tab-pane" id="tab-attachments">
                        @include('admin.trips.tabs.attachments')
                    </div>
                    <div class="tab-pane" id="tab-history">
                        @include('admin.trips.tabs.history')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.trips.modals.shipments')
    <style>
        .panel-left {
            width: 21%;
        }

        .panel-right {
            width: 79%;
            padding-right: 0 !important;
        }

        .table-shipments tr {
            cursor: move;
        }

        .border-divisor td{
            border-top: 1px solid #333 !important;
        }

        .nav-tabs-custom {
            margin-bottom: 0px;
        }

        .opphoto {
            margin-left: -45px;
        }

        .opphoto img {
            float: left;
            height: 40px;
            padding-right: 4px;
            margin-top: -10px;
        }

        .trip-content .map-trace-details {
            position: absolute;
            background: #fff;
            border-radius: 2px;
            box-shadow: 0px 1px 1px rgba(0,0,0,0.3);
            margin: 60px 10px 10px;
            padding: 10px;
            width: 173px;
            z-index: 1;
        }

        .trip-content .map-trace-details h4 {
            margin-bottom: 5px
        }
    </style>
@stop

@section('scripts')
    {{ Html::script('/vendor/html.sortable/dist/html.sortable.min.js')}}
    <script type="text/javascript">

        sortRows();

        //atualiza listagem de envios
        function tripRefreshShipmentsList() {
            $.get("{{ route('admin.trips.show', $trip->id) }}", {'action': 'refresh-table'}, function (data){
                if(data) {
                    $('.shipments-table').html(data)
                    sortRows()
                }
            }).fail(function () {
                Growl.error500()
            }).always(function () {
            });
        }

        //ativa plugin de ordenação de linhas
        function sortRows() {
            $('.sortable').sortable({
                forcePlaceholderSize: true,
                placeholder: '<tr><td colspan="12"><div class="h-30px"></div></td></tr>'
            }).bind('sortupdate', function (e, ui) {
                updateShipments()
            });
        }

        //ordena os envios selecionaods
        function updateShipments() {

            $('.alert-optimize-route').show();

            var dataList = $(document).find(".table .sortable > tr").map(function () {
                return $(this).data("id");
            }).get();

            $.post("{{ route('admin.trips.shipments.sort', $trip->id) }}", {'ids[]': dataList}, function (data){
                if(data.result) {} else {
                    Growl.error(data.message)
                }
            }).fail(function () {
                Growl.error500()
            }).always(function () {
            });
        }


        $('[name="period_id"]').on('change', function() {
            var start = $(this).find('option:selected').data('start');
            var end   = $(this).find('option:selected').data('end');

            $('[name="start_hour"]').val(start).trigger('change');
            $('[name="end_hour"]').val(end).trigger('change');
        });



        $(document).on('click', '[data-target="#modal-select-shipments"]', function() {

            var $tab = $(this);

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);
                var oTable = $('#datatable-shipments').DataTable({
                    columns: [
                        {data: 'tracking_code', name: 'tracking_code', visible: false},
                        {data: 'id', name: 'id'},
                        {data: 'reference', name: 'reference'},
                        {data: 'sender_name', name: 'sender_name'},
                        {data: 'recipient_name', name: 'recipient_name'},
                        {data: 'service_id', name: 'service_id', searchable: false},
                        {data: 'date', name: 'date'},
                        {data: 'volumes', name: 'volumes', searchable: false},
                        {data: 'trip_code', name: 'trip_code', searchable: false},
                        {data: 'status_id', name: 'status_id', searchable: false},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},

                        {data: 'sender_zip_code', name: 'sender_zip_code', visible: false},
                        {data: 'sender_city', name: 'sender_city', visible: false},
                        {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                        {data: 'recipient_city', name: 'recipient_city', visible: false},
                        {data: 'reference2', name: 'reference2', visible: false},
                        {data: 'reference3', name: 'reference3', visible: false},
                    ],
                    ajax: {
                        url: "{{ route('admin.trips.shipments.datatable', $trip->id) }}",
                        type: "POST",
                        data: function (d) {
                            d.transport_type = $('select[name=transport_type]').val()
                            d.sender_agency  = $('select[name=sender_agency]').val()
                            d.provider       = $('select[name=provider]').val()
                            d.operator       = $('select[name=operator]').val()
                            d.status         = $('select[name=status]').val()
                            d.delivery_route = $('select[name=delivery_route]').val()
                            d.pickup_route   = $('select[name=pickup_route]').val()
                            d.date_min       = $('input[name=date_min]').val()
                            d.date_max       = $('input[name=date_max]').val()
                            d.date_unity     = $('select[name=date_unity]').val()
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                        complete: function () { Datatables.complete(); }
                    }
                });

                $('.filter-datatable').on('change', function (e) {
                    oTable.draw();
                    e.preventDefault();
                });
            }
        });

        $(document).on('click', '.shipment-read', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var shipmentId = $btn.data('id');

            $.post("{{ route('admin.trips.shipments.add-single', $trip->id) }}", {'shipment':shipmentId}, function(data){
                if(data.result && data.html) {
                    $('.shipments-table').html(data.html)
                    $btn.prop('disabled', true);
                    sortRows()
                } else {
                    Growl.error(data.feedback);
                }
            }).fail(function () {
                Growl.error500()
            })
        })

        /**
         * CHANGE STATUS
         */
        $(document).on('click', '.form-update-history [data-toogle="select-button"]', function(){
            var id = $(this).data('id');

            $('.form-update-history [data-toogle="select-button"]').removeClass('btn-success').addClass('btn-default');
            $(this).removeClass('btn-default').addClass('btn-success');
            $('.form-update-history [name=status_id] option[value="'+id+'"]').prop('selected', true);
            $('.form-update-history [name=status_id]').trigger('change')
        })


        $(document).on('change', '.form-update-history [name=status_id]', function () {
            var status = $(this).val();

            if (status == '5') { //entregue
                $('.form-update-history .form-delivery').show();
            } else {
                $('.form-update-history .form-delivery').hide();
                $('.form-update-history .form-delivery').find('input[name="receiver"]').val('')
            }

            if (status == '9') { //incidencia
                $('.form-update-history .form-incidence').removeClass('hide');
                $('.form-update-history .form-incidence').find('select').prop('required', true);
            } else {
                $('.form-update-history .form-incidence').addClass('hide');
                $('.form-update-history .form-incidence').find('select').prop('required', false);
            }

            if (status == '7') { //devolution
                $('.form-update-history .form-devolution').removeClass('hide');
                $('input[name=devolution]').prop('checked', true);

            } else {
                $('.form-update-history .form-devolution').addClass('hide');
                $('input[name=devolution]').prop('checked', false);
            }

            if (status == '4' || status == '3') { //transporte ou distribiuicao
                $('.form-update-history .trip').show();
                $('.form-update-history .trip input[name=create_manifest]').prop('checked', true);
            } else {
                $('.form-update-history .trip').hide();
                $('.form-update-history .trip input[name=create_manifest]').prop('checked', false);
            }
        })
    </script>


    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ getGoogleMapsApiKey() }}"></script>
    <script src="{{ asset('assets/admin/js/maps.js') }}"></script>




    @if(1)
    <script type="text/javascript">
        var shpmap, shpProps, infowindow, directionsDisplay, geocoder, directionsDisplay;
        var shpMarkers = [];
        geocoder = new google.maps.Geocoder();
        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer;
        shpProps = {
            center: {lat: 40.404874, lng: -7.874651},
            zoom: 14,
            zoomControl: true,
            mapTypeControl: true,
            navigationControl: false,
            streetViewControl: true,
            scrollwheel: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map, geocoder, infowindow, directionsService, directionsDisplay, flightPath;
        var markers  = [];
        var markerVehicles = [];
        var markerCluster = null;

        const ROUTE_DETAILS_URL = "{!! route('admin.shipments.route-details', [
            'start_date' => $trip->start_date,
            'start_hour' => $trip->start_hour,
            'time'       => '_time',
            'return_type' => '_type_'
        ]) !!}";

        $(document).ready(function(){

            if($('.map-trace-details').is(':visible')) {
                initDeliveryMap();
                setDeliveryMapMarkers();
            }

            $('[href="#tab-map"]').on('click', function(){
                initDeliveryMap();
                setDeliveryMapMarkers();
            })

            $('.trace-delivery-route').on('click', function(){
                initDeliveryDirections();
            })
        })

        /**
         * INIT MAP
         */
        function initDeliveryMap() {

            var props, bounds
            props = {
                center: {lat: 40.404874, lng: -7.874651},
                zoom: 8,
                zoomControl: true,
                mapTypeControl: true,
                navigationControl: false,
                streetViewControl: true,
                scrollwheel: true,
                mapTypeControl: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById('deliveryMap'), props);
        }

        
        /**
         * Set marker
         * @param lat
         * @param lng
         * @param options
         * @returns {google.maps.Marker}
         */
        function setMarker(lat, lng, options) {
            var options   = typeof options !== 'undefined' ? options : {};
            var draggable = typeof options.draggable !== 'undefined' ? options.draggable : false;
            var html      = typeof options.html !== 'undefined' ? options.html : null;
            var zoom      = typeof options.zoom !== 'undefined' ? options.zoom : null;
            var centerMap = typeof options.centerMap !== 'undefined' ? options.centerMap : false;
            var icon      = typeof options.icon !== 'undefined' ? options.icon : '';
            var id        = typeof options.id !== 'undefined' ? options.id : '';

            var positionObj = new google.maps.LatLng(lat,lng);
            var marker, infowindow;

            //set map zoom
            if(zoom) {
                map.setZoom(zoom);
            }

            //center map on marker
            if(centerMap) {
                map.setCenter(positionObj);
            }

            infowindow = new google.maps.InfoWindow();
            infowindow.setContent(html);

            //add marker
            marker = new google.maps.Marker({
                position: positionObj,
                draggable: draggable,
                icon:icon,
                map: map,
                infowindow: infowindow
            });

            if(id != "") {
                marker.set("id", id)
            }

            //add info window
            if(html) {

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    if (infowindow) {
                        infowindow.close();
                    }

                    return function () {
                        infowindow.open(map, marker);
                    }
                })(marker));
            }

            return marker;
        };

        /**
         * Open infowindow by marker id
         */
        function openInfoWindowById(id, zoom) {

            zoom = typeof zoom == 'undefined' ? 16 : zoom;

            for (var i = 0; i < markers.length; i++) {
                if (markers[i].id == id) {
                    marker = markers[i];
                    infowindow = marker.infowindow
                    infowindow.open(map, marker);
                    map.setZoom(zoom);

                    map.setCenter({lat: marker.position.lat(), lng: marker.position.lng()});
                    return;
                }
            }
        }

        /**
         * Clear all map routes
         */
        function clearRoutes() {
            directionsDisplay.setMap(null);
        }

        /**
         * Clear flight path
         */
        function clearFlightPath() {
            flightPath.setMap(null);
        }

        /**
         * Clear all map markers
         */
        function clearMarkers() {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }

            if(markerCluster) {
                markerCluster.setMap(null);
            }
        }

        /**
         * Clear Marker by Id
         * @param id
         */
        function clearMarkerById(id) {
            //Find and remove the marker from the Array
            for (var i = 0; i < markers.length; i++) {
                if (markers[i].id == id) {
                    //Remove the marker from Map
                    markers[i].setMap(null);

                    //Remove the marker from array.
                    markers.splice(i, 1);
                    return;
                }
            }
        };

        function getDeliveryCoordinatesFromAddress(address) {
            return new Promise(function(resolve, reject) {
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({ 'address': address }, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        var latitude  = results[0].geometry.location.lat();
                        var longitude = results[0].geometry.location.lng();
                        resolve({ latitude: latitude, longitude: longitude });
                    } else {
                        reject("Geocode was not successful for the following reason: " + status);
                    }
                });
            });
        }

        
        function setDeliveryMapMarkers(){
            //clearMarkers();

            $('.shipments-table .table-shipments tbody tr').each(function () {
    
                var $target = $(this);
                var id   = $target.data('id');
                var html = $target.data('html');
                var lat  = $target.data('lat');
                var lng  = $target.data('lng');
                var addr = $target.data('addr');

                if(lat == '') {
                    getDeliveryCoordinatesFromAddress(addr).then(function(result) {
                        var latitude  = result.latitude;
                        var longitude = result.longitude;
                        
                        $target.data('lat', latitude);
                        $target.data('lng', longitude);
                        
                        if(latitude != "" && longitude != "") {
                            options = { 'html' : html, 'id' : id }
                            marker = setMarker(latitude, longitude, options)
                            markers.push(marker);
                        }


                        $.post("{{ route('admin.shipments.update.fields') }}", {
                            'id': id,
                            'sender_latitude': '',
                            'sender_longitude': '',
                            'recipient_latitude': latitude,
                            'recipient_longitude': longitude
                        }, function(){

                        })
                        
                    }).catch(function(error) {
                        Growl.error(error);
                    });

                } else {
                    lat = typeof lat != 'undefined' ? lat : '';
                    lng = typeof lng != 'undefined' ? lng : '';

                    if(lat != "" && lng != "") {
                        options = { 'html' : html, 'id' : id }
                        marker = setMarker(lat, lng, options)
                        markers.push(marker);
                    }
                }
            })

            initDeliveryDirections();
        }

        /**
         * Trace best route to deliveries
         */
         function initDeliveryDirections(){
            //Documentation: https://developers.google.com/maps/documentation/javascript/directions
            directionsDisplay.setMap(map);
            var originAddress      = "{{ $trip->start_location ? $trip->start_location : Setting::get('address_1').', '. Setting::get('city_1') }}"
            var destinationAddress = "{{ $trip->end_location ? $trip->end_location : Setting::get('address_1').', '. Setting::get('city_1') }}"
            var avoidHighways      = $('.deliveries-route-options [name="avoid_highways"]').is(':checked');
            var avoidTolls         = $('.deliveries-route-options [name="avoid_tolls"]').is(':checked');

            var waypts    = [];
            var locations = []

            $('.shipments-table .table-shipments tbody tr').each(function () {
                lat = $(this).data('lat');
                lng = $(this).data('lng');
                id  = $(this).data('id');

                if(lat!="" && lng != "") {
                    locations.push(['title', lat,lng])
                }
            })


            for (i = 0; i < locations.length; i++) {

                if (!waypts) {
                    waypts = [];
                }

                waypts.push({
                    location: new google.maps.LatLng(locations[i][1], locations[i][2]),
                    stopover: true
                });
            }

            directionsService.route({
                origin: originAddress,
                destination: destinationAddress,
                waypoints: waypts,
                optimizeWaypoints: false, //reorganiza para melhor rota
                avoidHighways: avoidHighways,
                avoidTolls: avoidTolls,
                travelMode: 'DRIVING'
            }, function (response, status) {

                if (status === 'OK') {
                    directionsDisplay.setDirections(response);

                    var pricePerLiter   = {{ (float) Setting::get('guides_fuel_price') }};
                    var fuelConsumption = {{ (float) $trip->fuel_consumption }};
                    var pricePerHour    = {{ (float) @$trip->operator->salary_value_hour }};
                    var fuel   = 0;
                    var salary = 0;
                    var totalDistance = 0;
                    var totalDuration = 0;
                    var deliveryList = '';
                    var legs = response.routes[0].legs;
                    for (var i = 0; i < legs.length; ++i) {
                        deliveryList+='<li>'+legs[i].end_address+'</li>';
                        totalDistance += legs[i].distance.value;
                        totalDuration += legs[i].duration.value;
                    }
                    
                    totalDistance = totalDistance / 1000;

                    fuel   = ((pricePerLiter * totalDistance) / 100) * fuelConsumption;
                    salary = pricePerHour * (totalDuration / 3600);

                    $.ajax({
                        url: ROUTE_DETAILS_URL.replace('_time', totalDuration).replace('_type_', 'totalTime'),
                        type: 'GET',
                        success: function(res) {
                            var timeSeconds = res * 60;
                            $('.total-time').html('<i class="far  fa-clock"></i> ' + secondsTimeSpanToHMS(timeSeconds.toFixed(2)))
                        }
                    });

                    $('.route-details').attr('href', ROUTE_DETAILS_URL.replace('_time', totalDuration).replace('_type_', 'modal'));
                    $('.route-details').show();

                    $('.map-top').show();
                    $('.ordered-route-list').html('<ul>' + deliveryList + '</ul>')

                    $('.total-distance').html('<i class="fas fa-road"></i> ' + totalDistance.toFixed(2) + ' km')
                    // $('.total-time').html('<i class="far  fa-clock"></i> ' + secondsTimeSpanToHMS(totalDuration.toFixed(2)))
                    $('.total-fuel').html(fuel.toFixed(2)+'€');
                    $('.total-salary').html(salary.toFixed(2)+'€');
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }

        function secondsTimeSpanToHMS(s) {
            var h = Math.floor(s/3600); //Get whole hours
            s -= h*3600;
            var m = Math.floor(s/60); //Get remaining minutes
            s -= m*60;
            return h+"h"+(m < 10 ? '0'+m : m); //zero padding on minutes and seconds
        }
    </script>
    @endif
@stop