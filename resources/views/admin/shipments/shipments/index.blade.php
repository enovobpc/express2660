@section('title')
    @if(app_mode_cargo())
        @trans('Ordens de Carga')
    @else
        @trans('Envios e Serviços')
    @endif
@stop

@section('content-header')
    @if(app_mode_cargo())
        @trans('Ordens de Carga')
    @else
        @trans('Envios e Serviços')
    @endif
@stop

@section('breadcrumb')
<li class="active">
    @if(app_mode_cargo())
        @trans('Ordens de Carga')
    @else
        @trans('Envios e Serviços')
    @endif
</li>
@stop


@section('content')
<div class="row">
    <div class="col-xs-12">
        {{-- <P>
            Assinatura CMR + envio por email<br/>
            Erro ao adicionar referencia envios
        </P> --}}
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.shipments.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-xl">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                    <li>
                        @include('admin.shipments.shipments.partials.tools_button')
                    </li>
                    <li class="fltr-primary w-195px">
                        <strong>@trans('Estado')</strong><br class="visible-xs"/>
                        <div class="w-140px pull-left form-group-sm">
                            {{ Form::selectMultiple('status', $status, fltr_val(Request::all(), 'status'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                        </div>
                    </li>
                    <li>
                        <div class="fltr-trk" style="display: none" data-toggle="tooltip" title="Procura direta pelo número de envio completo ou terminação do número">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-search"></i> TRK
                                </div>
                                {{ Form::text('trk', fltr_val(Request::all(), 'trk'), array('class' => 'form-control input-sm number filter-datatable')) }}
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    @include('admin.shipments.shipments.partials.filters')
                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed table-shipments">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-100px">TRK</th>
                                @if(Setting::get('shipment_list_show_reference'))
                                    <th class="w-50px">@trans('Referência')</th>
                                @endif
                                <th>@trans('Remetente')</th>
                                <th>@trans('Destinatário')</th>
                                <th class="w-1">@trans('Serviço')</th>
                                <th class="w-60px">@trans('Remessa')</th>
                                @if(Setting::get('shipment_list_show_delivery_date'))
                                    <th class="w-120px">@trans('Entrega')</th>
                                @else
                                    <th class="w-75px">@trans('Info')</th>
                                @endif

                                @if(Setting::get('shipment_list_show_vehicle'))
                                    <th class="w-45px">@trans('Viagem')</th>
                                @endif

                                @if(Setting::get('shipment_list_show_obs'))
                                    <th class="w-200px">@trans('Observações')</th>
                                @endif
                                <th class="w-1">@trans('Estado')</th>
                                @if(Setting::get('shipment_list_show_customer_name'))
                                    <th class="w-100px">@trans('Cliente')</th>
                                @endif
                                @if(Setting::get('shipment_list_show_conferred'))
                                <th class="w-1"><i class="fas fa-check-circle" data-toggle="tooltip" title="@trans('Conferir Envio')"></i></th>
                                @endif
                                <th class="w-50px">@trans('Valor')</th>
                                <th class="w-65px">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    <div class="pull-left">
                        <button class="btn btn-sm btn-primary m-l-5" data-toggle="modal" data-target="#modal-edit-history">
                            <i class="fas fa-tasks"></i> @trans('Alterar estado')
                        </button>
                        @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'delivery_management'))
                        {{--<a href="{{ route('admin.shipments.selected.create-manifest') }}"
                           class="btn btn-sm btn-default m-l-5"
                           data-action-url="datatable-action-url"
                           data-toggle="modal"
                           data-target="#modal-remote-lg">
                        </a>--}}
                            <div class="btn-group btn-group-sm dropup m-l-5">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-file-alt"></i>
                                    @if(app_mode_cargo())
                                        @trans('Mapa Viagem')
                                    @else
                                        @trans('Mapa Distribuição')
                                    @endif
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.trips.create') }}"
                                           data-toggle="modal"
                                           data-target="#modal-remote-lg"
                                           data-action-url="datatable-action-url"
                                           target="_blank">
                                            @if(app_mode_cargo())
                                                @trans('Criar nova viagem')
                                            @else
                                                @trans('Criar novo mapa distribuição')
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.trips.shipments.add-selected') }}"
                                           data-toggle="modal"
                                           data-target="#modal-remote-lg"
                                           data-action-url="datatable-action-url">
                                            @if(app_mode_cargo())
                                                @trans('Adicionar a viagem existente')
                                            @else
                                                @trans('Adicionar a um mapa distribuição')
                                            @endif
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                        {{-- <a href="{{ route('admin.export.shipments') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
                            <i class="fas fa-fw fa-file-excel"></i> Exportar
                        </a> --}}
                        <div class="btn-group btn-group-sm dropup m-l-5">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar') <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('admin.export.shipments.alternative') }}" data-toggle="export-selected">
                                        @trans('Listagem simples')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.export.shipments') }}" data-toggle="export-selected">
                                        @trans('Listagem detalhada')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.export.shipments.dimensions') }}" data-toggle="export-selected">
                                        @trans('Listagem mercadoria')
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="btn-group btn-group-sm dropup m-l-5">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-print"></i> @trans('Imprimir') <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('admin.printer.shipments.labels') }}" data-toggle="datatable-action-url" target="_blank">
                                        @trans('Etiquetas em Massa')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.printer.shipments.transport-guide') }}" data-toggle="datatable-action-url" target="_blank">
                                        @trans('Guias de Transporte em Massa')
                                    </a>
                                </li>
                                <li>
                                    <a href="#modal-grouped-guide" data-toggle="modal">
                                        @trans('Guias de Transporte Agrupada')
                                    </a>
                                </li>
                                <li class="divider"></li>

<!--                                <li>
                                    <a href="{{ route('admin.printer.shipments.cargo-manifest', ['0']) }}" data-toggle="datatable-action-url" target="_blank">
                                        @trans('Manifesto Carga')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.printer.shipments.cargo-manifest', ['customers']) }}" data-toggle="datatable-action-url" target="_blank">
                                        @trans('Manifesto Carga (Agrup. Cliente)')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.printer.shipments.cargo-manifest', ['providers']) }}" data-toggle="datatable-action-url" target="_blank">
                                        @trans('Manifesto Carga (Agrup. Fornecedor)')
                                    </a>
                                </li>-->
                                <li>
                                    <a href="{{ route('admin.printer.shipments.goods-manifest') }}" data-toggle="datatable-action-url" target="_blank">
                                        @trans('Resumo de Mercadoria')
                                    </a>
                                </li>
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#modal-print-cargo-manifest">
                                        @trans('Manifesto de Carga')
                                    </a>
                                </li>
                                @if(config('app.source') == 'utiltrans')
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#modal-print-cold-manifest">
                                        @trans('Manifesto de Frio e Humidade')
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a href="{{ route('admin.printer.shipments.delivery-map') }}" data-toggle="datatable-action-url" target="_blank">
                                        @trans('Mapa de Entrega')
                                    </a>
                                </li>
                                @if(Auth::user()->showPrices())
                                <li class="divider"></li>
                                <li>
                                    <a href="{{ route('admin.printer.shipments.selected', 'customer') }}" data-toggle="datatable-action-url" target="_blank">
                                        @trans('Listagem Agrupada Cliente')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.printer.shipments.selected') }}" data-toggle="datatable-action-url" target="_blank">
                                        @trans('Listagem de Envios')
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                        <div class="btn-group btn-group-sm dropup m-l-5">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-pencil-alt"></i> @trans('Editar')... <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'billing'))
                                        <a href="#" data-toggle="modal" data-target="#modal-assign-customer">
                                            @trans('Alterar Cliente')
                                        </a>
                                    @endif
                                </li>
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#modal-assign-service">
                                        @trans('Alterar Tipo de Serviço')
                                    </a>
                                </li>
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#modal-assign-provider">
                                        @trans('Alterar o Fornecedor')
                                    </a>
                                </li>
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#modal-assign-vehicle">
                                        @trans('Editar em Massa')
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="btn-group btn-group-sm dropup m-l-5">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bars"></i> @trans('Ver mais')... <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" class="text-danger" data-toggle="modal" data-target="#modal-mass-destroy">
                                        <i class="fas fa-trash-alt"></i> @trans('Eliminar serviços')
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="{{ route('admin.shipments.selected.notify.edit') }}" data-action-url="datatable-action-url" data-toggle="modal" data-target="#modal-remote">
                                        <i class="fas fa-fw fa-envelope-open-text"></i> @trans('Notificação de serviço')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.shipments.email.edit', [0, 'auction']) }}"  data-action-url="datatable-action-url" data-toggle="modal" data-target="#modal-remote">
                                        <i class="fas fa-fw fa-envelope"></i> @trans('Anunciar/leiloar cargas')
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="{{ route('admin.shipments.selected.grouped.edit') }}"
                                        data-toggle="modal"
                                        data-action-url="datatable-action-url"
                                        data-target="#modal-remote-xs">
                                        <i class="fas fa-share-alt"></i> @trans('Agrupar/Desagrupar serviços')
                                    </a>
                                </li>
                                <li class="divider"></li>
                                @if(!app_mode_cargo())
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#modal-close-shipments">
                                        <i class="fas fa-fw fa-check"></i> @trans('Fechar Envios CTT/Ontime')
                                    </a>
                                </li>
                                @endif
                                @if(Auth::user()->allowedAction('edit_blocked'))
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#modal-mass-block">
                                        <i class="fas fa-fw fa-lock"></i> @trans('Bloquear/Desbloquear Edição')
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#modal-sync-webservice">
                                        <i class="fas fa-fw fa-plug"></i> @trans('Submeter via Webservice')
                                    </a>
                                </li>
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#modal-sync-history">
                                        <i class="fas fa-fw fa-sync-alt"></i> @trans('Forçar atualização estados')
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="selected-rows-totals">
                        <h4>
                            <small>@trans('Total')</small><br/>
                            <span class="dt-sum-total bold"></span><b>€</b>
                        </h4>
                        <h4>
                            <small>@trans('Peso')</small><br/>
                            <span class="dt-sum-kg bold"></span><b>kg</b>
                        </h4>
                        <h4>
                            <small>@trans('Vols')</small><br/>
                            <span class="dt-sum-vol bold"></span>
                        </h4>
                        <h4>
                            <small>M3</small><br/>
                            <span class="dt-sum-m3 bold"></span>
                        </h4>
                        @if(app_mode_cargo())
                        <h4>
                            <small>@trans('LDM')</small><br/>
                            <span class="dt-sum-ldm bold"></span>
                        </h4>
                        @endif
                        <div class="clearfix"></div>
                    </div>
                    @include('admin.shipments.shipments.modals.grouped_guide')
                    @include('admin.shipments.shipments.modals.assign.service')
                    @include('admin.shipments.shipments.modals.assign.mass_edit')
                    @include('admin.shipments.shipments.modals.assign.customer')
                    @include('admin.shipments.shipments.modals.assign.provider')
                    @include('admin.shipments.shipments.modals.print_cold_manifest')
                    @include('admin.shipments.shipments.modals.print_cargo_manifest')
                    @include('admin.shipments.history.mass_edit')

                    @include('admin.shipments.shipments.modals.mass.block')
                    @include('admin.shipments.shipments.modals.mass.destroy')
                    @include('admin.shipments.shipments.modals.mass.close')
                    @include('admin.shipments.shipments.modals.mass.sync')
                    @include('admin.shipments.shipments.modals.mass.sync_history')
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.shipments.shipments.modals.scheduled')

@if(Request::get('fsearch') && Request::get('fsearchmode'))
    <?php
        if(Request::get('fsearchmode') == 'show') {
            $route = route('admin.shipments.show', Request::get('fsearch'));
        } else {
            $route = route('admin.shipments.edit', Request::get('fsearch'));
        }
    ?>
    <a href="{{ @$route }}" class="fsearch" data-toggle="modal" data-target="#modal-remote-xl"></a>
@endif

<style>
    .shown {
        border-left: 1px solid #eca57b;
        border-right: 1px solid #eca57b;
        background: #fbece8 !important;
    }

    .shown > td:first-child{ 
        /* background: #FF5F00 !important; */
    }
    .shown > td{ 
        border-bottom: 1px solid #eca57b42;
        border-top: 1px solid #eca57b !important
    }
    
    .dtl-row {
        border-left: 1px solid #eca57b;
        border-right: 1px solid #eca57b;
       
        /* border-left: 2px solid #ff5f01; */
    }
    .dtl-row > td {
        border-bottom: 1px solid #eca57b;;
        /* background: #fff; */
        background: #fbebe8 !important
    }

    .dtl-row > td .bg-gray {
        background-color: #dabcad !important;
    }

    .dtl-row > div {
        
        padding: 5px 5px 30px 5px
    }
</style>
@stop


@section('scripts')
{{ Html::script(asset('vendor/devbridge-autocomplete/dist/jquery.autocomplete.min.js')) }}
<script src="https://maps.googleapis.com/maps/api/js?key={{ getGoogleMapsApiKey() }}"></script>
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

    var oTable;

    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            dom: Datatables.trkDom(),
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'tracking_code', name: 'tracking_code', visible: false},
                {data: 'id', name: 'id'},
                @if(Setting::get('shipment_list_show_reference'))
                {data: 'reference', name: 'reference'},
                @endif
                {data: 'sender_name', name: 'sender_name'},
                {data: 'recipient_name', name: 'recipient_name'},
                {data: 'service_id', name: 'service_id', searchable: false},
                {data: 'volumes', name: 'volumes', searchable: false},
                @if(Setting::get('shipment_list_show_delivery_date') && !Setting::get('shipment_list_order_shipping_date'))
                {data: 'delivery_date', name: 'delivery_date', searchable: false},
                @elseif(Setting::get('shipment_list_show_delivery_date') && Setting::get('shipment_list_order_shipping_date'))
                {data: 'delivery_date', name: 'shipping_date', searchable: false},
                @else
                {data: 'shipping_date', name: 'shipping_date', searchable: false},
                @endif
                @if(Setting::get('shipment_list_show_vehicle'))
                {data: 'vehicle', name: 'vehicle'},
                @endif
                
                @if(Setting::get('shipment_list_show_obs'))
                {data: 'obs', name: 'obs', class: 'text-center'},
                @endif
                {data: 'status_id', name: 'status_id', searchable: false},
                @if(Setting::get('shipment_list_show_customer_name'))
                {data: 'customer_id', name: 'customer_id', orderable: false, searchable: false},
                @endif
                @if(Setting::get('shipment_list_show_conferred'))
                {data: 'customer_conferred', name: 'customer_conferred', class: 'text-center'},
                @endif

                {data: 'total_price', name: 'total_price', searchable: false, class: 'text-center nowrap'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                /*{data: 'sender_address', name: 'sender_address', visible: false},*/
                /*{data: 'sender_zip_code', name: 'sender_zip_code', visible: false},*/
                {data: 'sender_city', name: 'sender_city', visible: false},
               /* {data: 'recipient_address', name: 'recipient_address', visible: false},*/
                /*{data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},*/
                {data: 'recipient_city', name: 'recipient_city', visible: false},
                {data: 'provider_tracking_code', name: 'provider_tracking_code', visible: false},
                {data: 'reference', name: 'reference', visible: false},
                {data: 'reference2', name: 'reference2', visible: false},
                {data: 'reference3', name: 'reference3', visible: false},
                @if(!Setting::get('shipment_list_show_obs'))
                {data: 'obs', name: 'obs', visible: false},
                @endif
                /*{data: 'obs', name: 'obs', visible: false},
                {data: 'obs2', name: 'obs2', visible: false},*/
                {data: 'parent_tracking_code', name: 'parent_tracking_code', visible: false},
                {data: 'children_tracking_code', name: 'children_tracking_code', visible: false},
                {data: 'sender_phone', name: 'sender_phone', visible: false},
                {data: 'recipient_phone', name: 'recipient_phone', visible: false},
                {data: 'recipient_email', name: 'recipient_email', visible: false},
                {data: 'at_guide_codeat', name: 'at_guide_codeat', visible: false},
                {data: 'trip_code', name: 'trip_code', visible: false},
                {data: 'keywords', name: 'keywords', visible: false},
                @if(config('app.source') == 'ontimeservices')
                {data: 'vehicle', name: 'vehicle', visible: false}
                @endif
            ],
            order: [[2, "desc"]],
            ajax: {
                url: "{{ route('admin.shipments.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.trk                = $('input[name=trk]').val();
                    d.type               = $('select[name=shp_type]').val();
                    d.zone               = $('select[name=zone]').val();
                    d.status             = $('select[name=status]').val();
                    d.source             = $('select[name=source]').val();
                    d.service            = $('select[name=service]').val();
                    d.provider           = $('select[name=provider]').val();
                    d.agency             = $('select[name=agency]').val();
                    d.charge             = $('select[name=charge]').val();
                    d.operator           = $('select[name=operator]').val();
                    d.operator_pickup    = $('select[name=operator_pickup]').val();
                    d.dispatcher         = $('select[name=dispatcher]').val();
                    d.vehicle            = $('select[name=vehicle]').val();
                    d.trailer            = $('select[name=trailer]').val();
                    d.route              = $('select[name=route]').val();
                    d.pickup_route       = $('select[name=pickup_route]').val();
                    d.customer           = $('select[name=dt_customer]').val();
                    d.seller             = $('select[name=seller]').val();
                    d.date_min           = $('input[name=date_min]').val();
                    d.date_max           = $('input[name=date_max]').val();
                    d.date_unity         = $('select[name=date_unity]').val();
                    d.volumes_min        = $('input[name=volumes_min]').val();
                    d.volumes_max        = $('input[name=volumes_max]').val();
                    d.weight_min         = $('input[name=weight_min]').val();
                    d.weight_max         = $('input[name=weight_max]').val();
                    d.deleted            = $('input[name=deleted]:checked').length;
                    d.limit_search       = $('input[name=limit_search]:checked').length;
                    d.invoice            = $('select[name=invoice]').val();
                    d.ignore_billing     = $('select[name=ignore_billing]').val();
                    d.expenses           = $('select[name=expenses]').val();
                    d.expense_type       = $('select[name=expense_type]').val();
                    d.blocked            = $('select[name=blocked]').val();
                    d.printed            = $('select[name=printed]').val();
                    d.cod                = $('select[name=cod]').val();
                    d.hide_final_status  = $('input[name=hide_final_status]:checked').length;
                    d.hide_scheduled     = $('input[name=hide_scheduled]:checked').length;
                    d.sender_agency      = $('select[name=sender_agency]').val();
                    d.recipient_agency   = $('select[name=recipient_agency]').val();
                    d.sender_country     = $('select[name=fltr_sender_country]').val();
                    d.recipient_country  = $('select[name=fltr_recipient_country]').val();
                    d.recipient_district = $('select[name=fltr_recipient_district]').val();
                    d.recipient_county   = $('select[name=fltr_recipient_county]').val();
                    d.recipient_zip_code = $('input[name=fltr_recipient_zp]').val();
                    d.workgroups         = $('select[name=workgroups]').val();
                    d.period             = $('select[name=period]').val();
                    d.customer_conferred = $('select[name=customer_conferred]').val();
                    d.customer_type      = $('select[name=customer_type]').val();
                    d.trips              = $('select[name=trips]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); Datatables.trkSearch() },
                //error: function () { Datatables.error(); }
            }
        });

        //enable option to search with enter press
        Datatables.searchOnEnter(oTable);

        $('.filter-datatable').on('change', function (e) {
            e.preventDefault();
            oTable.draw();

            $('[data-toggle="export-url"]').each(function() {
                var exportUrl = Url.removeQueryString($(this).attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())

                $('.datatable-filters-area-extended [type="checkbox"]').each(function(){ //add checkbox filters
                    checkStatus = $(this).is(':checked') ? 1 : 0;
                    varName = $(this).attr('name');
                    exportUrl+= '&'+varName+'=' + checkStatus;
                })

                $(this).attr('href', exportUrl);
            })
        });
    });

    //show line details
    $(document).on('click', 'tr .details-control', function () {

        var mainTr = $(this).closest('tr');
        var row = oTable.row(mainTr);
        var id  = mainTr.find('.row-select').val();

        if (row.child.isShown()) {
            row.child.hide();
            mainTr.removeClass('shown');
        } else {
            //https://datatables.net/blog/2017-03-31

            row.child('<div style="padding: 30px"><i class="fas fa-spin fa-circle-notch"></i> Aguarde...</div>').show();
            mainTr.addClass('shown');
            
            $.get("{{ route('admin.shipments.show', ['id' => 'expand-row']) }}", {id: id}, function(html){
                mainTr.next().find('td').html(html);
                mainTr.next().addClass('dtl-row');
            }).fail(function(){
                row.child.hide();
                mainTr.removeClass('shown');
                Growl.error('Falha ao obter detalhes. Não é possível expandir a visualização.');
            })
        }
    });

    $(document).on('change', '[name="print_cargo_manifest"]', function(){
        var href   = $(this).attr('data-href');
        var querystring = Url.getQueryString($(this).closest('.modal').find('.btn-primary').attr('href'));
        $(this).closest('.modal').find('.btn-primary').attr('href', href + '?' + querystring);
    })

    $(document).on('click', '#modal-print-cargo-manifest .btn-primary', function(){
        $('#modal-print-cargo-manifest').modal('hide');
    })

    //show concluded shipments
    $(document).on('change', '[name="hide_final_status"], [name="hide_scheduled"], [name="deleted"], [name="limit_search"]', function (e) {
        oTable.draw();
        e.preventDefault();

        var name = $(this).attr('name');
        var value = $(this).is(':checked');
        value = value == false ? 0 : 1;

        newUrl = Url.updateParameter(Url.current(), name, value)
        Url.change(newUrl);

        var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
        exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
        $('[data-toggle="export-url"]').attr('href', exportUrl);
    });

    //export selected
    $(document).on('change', '.row-select',function(){
        var queryString = '';
        $('input[name=row-select]:checked').each(function(i, selected){
            queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
        });

        $('[data-toggle="export-selected"]').each(function(){
            var exportUrl = Url.removeQueryString($(this).attr('href'));
            $(this).attr('href', exportUrl + '?' + queryString);
        })
    });

    $(document).on('change', '#modal-export-selected [name=provider]', function(){
        var exportUrl = Url.updateParameter($('[data-toggle="export-selected"]').attr('href'), 'provider', $(this).val());
        $('[data-toggle="export-selected"]').attr('href', exportUrl);
    })

    $(document).on('click', '#modal-print-cold-manifest a', function (e) {
        e.preventDefault();
        var temp = $('#modal-print-cold-manifest [name="temperature"]').val();
        var hum  = $('#modal-print-cold-manifest [name="humidity"]').val();

        if(temp == '' || hum == '') {
            Growl.error('É obrigatório indicar a temperatura e humidade.')
        } else {
            var url = $(this).attr('href');
            url = Url.updateParameter(url, 'temperature', temp);
            url = Url.updateParameter(url, 'humidity', hum);
            window.open(url, '_blank');

            $('#modal-print-cold-manifest').modal('hide');
        }
    })

    $('#modal-export-selected').on('hidden.bs.modal', function (e) {
        $('#modal-export-selected [name=provider]').val('').trigger('change');
    })

    $(document).on('click', '[data-batatas="export-selected"]', function(){
        $('#modal-export-selected').modal('hide');
    })

    $(document).on('change', '[name=fltr_recipient_district]', function(){
        var district = $(this).val();
        $('.load-county').show();
        $.post('{{ route('admin.shipments.get.counties') }}', {district:district}, function (data) {
            var select = $('[name=fltr_recipient_county]');
            select.empty();
            $.each(data, function(index, county) {
                select.append($('<option>', {
                    value: county.id,
                    text: county.text
                }));
            });
            select.trigger('change');
        }).always(function () {
            $('.load-county').hide();
        });
    });


    $("select[name=dt_customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
    });

    $('#modal-assign-customer').on('shown.bs.modal', function(){
        $(".modal select[name=assign_customer_id]").select2({
            minimumInputLength: 2,
            allowClear: true,
            ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
        });
    })

    $('#modal-assign-customer').on('hidden.bs.modal', function(){
        $(".modal select[name=assign_customer_id]").select2('destroy')
    })


    /**
     * EVENT WHEN CHANGE CUSTOMER
     * ajax method
     */
    $('[name=assign_customer_id]').on('select2:select', function (e) {
        var data  = e.params.data;

        if(data.departments !== null) {
            $('.assign-department').removeClass('hide');
            $('.assign-customer').removeClass('col-sm-12').addClass('col-sm-7');
            $('[name=assign_department_id]').html('').select2('destroy');
            $('[name=assign_department_id]').select2({ data: data.departments });
        } else {
            $('.assign-department').addClass('hide');
            $('.assign-customer').removeClass('col-sm-7').addClass('col-sm-12');
            $('[name=assign_department_id]').select2('data', null);
        }
    })

    $('.btn-webservice-sync').on('click', function(e){
        e.preventDefault();
        $('.selected-rows-action').removeClass('hide');
        $('#modal-sync-history').modal('show');
    })

    $('.webservice-sync-history').on('submit', function(e){
        e.preventDefault();

        var $loadingBtn = $(this).find('[type="submit"]');
        $loadingBtn.button('loading')

        $.post($(this).attr('action'), $(this).serialize(), function(data){
            oTable.draw();
            if(data.result) {
                Growl.success('Sincronização Concluída.');
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function(){
            Growl.error500();
        }).always(function(){
            $loadingBtn.button('reset')
            $('#modal-sync-history').modal('hide');
        })
    })

    $('form.webservice-sync-date').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        $submitBtn.button('loading')

        var webservice = $form.find('[name="webservice"]').val();
        var customer   = $('[name="webservice_sync_customer"]').val();
        var minDate    = $form.find('[name="start_date"]').val();
        var maxDate    = $form.find('[name="end_date"]').val();

        $.post($form.attr('action'), {webservice:webservice, start_date:minDate, end_date:maxDate, customer:customer}, function(data){
            oTable.draw();
            $('#modal-webservice-sync').modal('hide');
            Growl.success('Sincronização Concluída.');
        }).fail(function(){
            Growl.error500();
        }).always(function(){
            $submitBtn.button('reset');
        })
    })

    $('.webservice-sync-customer-reset').on('click', function(){
        $('[name="webservice_sync_customer"]').html('<option value="">Todos</option>').trigger('change');
    })

    /**
     * Close shipments
     */
    $('form.close-shipments').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        $submitBtn.button('loading')

        $('.close-shipments .message').hide();
        $('.close-shipments .loading').show();

        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw();
                Growl.success(data.feedback)
                if(data.filepath) {
                    window.open(data.filepath, '_blank');
                }
            } else {
                Growl.error(data.feedback)
            }
        }).fail(function(){
            Growl.error500()
        }).always(function(){
            $('.close-shipments .message').show();
            $('.close-shipments .loading').hide();
            $('#modal-close-shipments').modal('hide');
            $submitBtn.button('reset');
        })
    })

    /**
     * Mass change customers
     */
    $(document).on('click', '[data-toogle="mass-select-button"]', function(){
        var id = $(this).data('id');

        $('[data-toogle="mass-select-button"]').removeClass('btn-success').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-success');
        $('[name=assign_status_id] option[value="'+id+'"]').prop('selected', true);
        $('[name=assign_status_id]').trigger('change')
    })

    $('[name=assign_status_id]').on('change', function () {
        var status = $(this).val();

        if (status == '5') { //entregue
            $('#mass-status-delivery').show();
        } else {
            $('#mass-status-return-form').addClass('hide');
            $('#mass-status-return-form').find('input').prop('required', false);
            $('#mass-status-return-form').find('.form-group').removeClass('is-required');
            $('#mass-status-return-form').find('p').show();
            $('#mass-status-delivery').hide();
            $('#mass-status-delivery').find('input[name="receiver"]').val('')
        }


        if (status == '9') { //incidencia
            $('#mass-status-incidence').removeClass('hide');
            $('#mass-status-incidence').find('select').prop('required', true);
        } else {
            $('#mass-status-incidence').addClass('hide');
            $('#mass-status-incidence').find('select').prop('required', false);
        }

        if (status == '7') { //devolution
            $('#mass-status-devolution').removeClass('hide');
            $('input[name=devolution]').prop('checked', true);
        } else {
            $('#mass-status-devolution').addClass('hide');
            $('input[name=devolution]').prop('checked', false);
        }

        if (status == '4' || status == '3') { //transporte ou distribiuicao
            $('.trip').show();
            $('.trip input[name=trips]').prop('checked', true);
        } else {
            $('.trip').hide();
            $('.trip input[name=trips]').prop('checked', false);
        }
    })

    $('.btn-print-grouped').on('click', function (e) {
        e.preventDefault();
        var $modal = $(this).closest('.modal');
        var packing = $modal.find('[name=packing_type]').val();
        var vehicle = $modal.find('[name=vehicle]').val();
        var description = $modal.find('[name=description]').val();

        var url = $('#url-grouped-transport-guide').attr('href');

        url = Url.updateParameter(url, 'grouped', '1');
        url = Url.updateParameter(url, 'packing', packing);
        url = Url.updateParameter(url, 'vehicle', vehicle);
        url = Url.updateParameter(url, 'description', description);
        $('#url-grouped-transport-guide').attr('href', url);

        document.getElementById('url-grouped-transport-guide').click();
        $(this).closest('.modal').modal('hide');
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

    $(document).on('click', '[data-s-filter]', function(e){
        e.preventDefault();
        var fltr = $(this).data('s-filter');
        oTable.search(fltr).draw();
    })  

    var oTableScheduled;
    $(document).on('click', '[data-target="#modal-shipments-scheduled"]', function() {
        var $tab = $(this);

        if($tab.data('empty') == '1') {
            $tab.data('empty', 0);
            oTableScheduled = $('#datatable-shipments-scheduled').DataTable({
                columns: [
                    {data: 'id', name: 'id', visible: false},
                    {data: 'finished', name: 'finished', searchable: false},
                    {data: 'sender_name', name: 'sender_name'},
                    {data: 'recipient_name', name: 'recipient_name'},
                    {data: 'service_id', name: 'service_id', searchable: false},
                    {data: 'volumes', name: 'volumes', searchable: false},
                    {data: 'schedule', name: 'schedule', orderable: false, searchable: false},
                    {data: 'schedule_end', name: 'schedule_end', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    {data: 'sender_zip_code', name: 'sender_zip_code', visible: false},
                    {data: 'sender_city', name: 'sender_city', visible: false},
                    {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                    {data: 'recipient_city', name: 'recipient_city', visible: false},
                ],
                ajax: {
                    url: "{{ route('admin.shipments.scheduled.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.status = $('select[name=status]').val()
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableScheduled) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('#modal-shipments-scheduled .filter-datatable').on('change', function (e) {
                oTableScheduled.draw();
                e.preventDefault();
            })
        }
    });

    /**
     * Confirm shipment
     */
    $(document).on('click', '.btn-conferred', function(){
        var $parent  = $(this).parent();
        var id       = $parent.data('id');
        var lastHtml = $parent.html();
        $parent.html('<i class="fas fa-spin fa-circle-notch"></i>');

        $.post("{{ route('admin.shipments.confirm') }}", {ids:id}, function(data){
            if(data.result) {
                Growl.success(data.feedback)
                $parent.replaceWith(data.html);
            } else {
                Growl.error(data.feedback)
                $parent.html(lastHtml);
            }

        }).fail(function(){
            Growl.error500();
            $parent.html(lastHtml);
        })
    })

   /* $(document).on('click', '.btn-submit-status', function (e) {
        e.preventDefault()

        var $form    = $(this).closest('form');
        var status   = $form.find('[name="status_id"]').val();
        var operator = $form.find('[name="operator_id"]').val();

       /!* if(operator == '-1' && (status == '37' || status == '38')) {
            Growl.error('É obrigatório escolher um operador da lista.')
            return;
        } else {*!/
            $form.submit();
       /!* }*!/
    })*/

    $('.fltr-trk input').on('keyup', function(e){
        if(e.keyCode == 13) {
            $(this).trigger('change');
        }
    })

    $('.fltr-sall').on('click', function(){
        $(this).closest('li').find('select option').prop('selected', true).trigger('change');
    })

    @if(Request::get('fsearch') && Request::get('fsearchmode'))
    $(document).ready(function(){
        var url = Url.current();
        url = Url.removeParameter(url, 'fsearch');
        url = Url.removeParameter(url, 'fsearchmode');
        Url.change(url);

        $('.fsearch').trigger('click');
    })
    @endif
</script>
@stop