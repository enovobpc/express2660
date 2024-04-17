@section('title')
    Pedidos de Recolha
@stop

@section('content-header')
    Pedidos de Recolha
@stop

@section('breadcrumb')
    <li class="active">Pedidos de Recolha</li>
@stop


@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                        <li>
                            <a href="{{ route('admin.pickups.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-xl">
                                <i class="fas fa-plus"></i> Novo
                            </a>
                        </li>
                        <li>
                            @include('admin.shipments.shipments.partials.tools_button')
                        </li>
                        <li class="fltr-primary w-215px">
                            <strong>Estado</strong><br class="visible-xs"/>
                            <div class="w-150px pull-left form-group-sm">
                                {{ Form::selectMultiple('status', $status, fltr_val(Request::all(), 'status'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                            </div>
                        </li>
                        <li class="fltr-primary w-200px">
                            <strong>Serviço</strong><br class="visible-xs"/>
                            <div class="w-120px pull-left form-group-sm">
                                {{ Form::selectMultiple('service', $services, fltr_val(Request::all(), 'service'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
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
                                <th class="w-100px">Recolha</th>
                                @if(Setting::get('shipment_list_show_reference'))
                                    <th class="w-50px">Referência</th>
                                @endif
                                <th>Local Recolha</th>
                                <th>Local Descarga</th>
                                <th class="w-1">Serviço</th>
                                <th class="w-75px">Info</th>
                                <th class="w-90px">Estado Pedido</th>
                                <th class="w-85px">Envio Gerado</th>
                                <th class="w-50px">Taxa</th>
                                <th class="w-65px">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="selected-rows-action hide">
                        <div>
                            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal-mass-destroy">
                                <i class="fas fa-trash-alt"></i> Apagar
                            </button>
                            <a href="{{ route('admin.export.shipments') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
                                <i class="fas fa-fw fa-file-excel"></i> Exportar
                            </a>
                            <button class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-edit-history">
                                <i class="fas fa-tasks"></i> Alterar estado
                            </button>
                            <div class="btn-group btn-group-sm dropup m-l-5">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-print"></i> Imprimir <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.printer.pickups.selected.manifest') }}" data-toggle="datatable-action-url" target="_blank">
                                            Manifestos em Massa
                                        </a>
                                    </li>
                                    @if(Auth::user()->showPrices())
                                        <li class="divider"></li>
                                        <li>
                                            <a href="{{ route('admin.printer.shipments.selected') }}" data-toggle="datatable-action-url" target="_blank">
                                                Listagem de Pedidos
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="btn-group btn-group-sm dropup m-l-5">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-pencil-alt"></i> Alterar... <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'billing'))
                                            <a href="#" data-toggle="modal" data-target="#modal-assign-customer">
                                                Alterar Cliente
                                            </a>
                                        @endif
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#modal-assign-service">
                                            Alterar Tipo de Serviço
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#modal-assign-provider">
                                            Alterar o Fornecedor
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="btn-group btn-group-sm dropup m-l-5">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-plus"></i> Mais... <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {{--@if(Auth::user()->showPrices())
                                        <li>
                                            <a href="{{ route('admin.shipments.selected.assign-expenses') }}" data-action-url="datatable-action-url" data-toggle="modal" data-target="#modal-remote-lg">
                                                <i class="fas fa-fw fa-euro-sign"></i> Adicionar Encargo
                                            </a>
                                        </li>
                                    @endif--}}
                                    @if(Auth::user()->allowedAction('edit_blocked'))
                                        <li>
                                            <a href="#" data-toggle="modal" data-target="#modal-mass-block">
                                                <i class="fas fa-fw fa-lock"></i> Bloquear/Desbloquear Recolhas
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#modal-submit-webservice">
                                            <i class="fas fa-fw fa-plug"></i> Submeter via Webservice
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#modal-sync-history">
                                            <i class="fas fa-fw fa-sync-alt"></i> Forçar atualização estados
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @include('admin.shipments.shipments.modals.grouped_guide')
                        @include('admin.shipments.shipments.modals.assign.service')
                        @include('admin.shipments.shipments.modals.assign.customer')
                        @include('admin.shipments.shipments.modals.assign.provider')
                        @include('admin.shipments.history.mass_edit')

                        @include('admin.shipments.shipments.modals.mass.block')
                        @include('admin.shipments.shipments.modals.mass.destroy')
                        @include('admin.shipments.shipments.modals.mass.close')
                        @include('admin.shipments.shipments.modals.mass.sync')
                        @include('admin.shipments.shipments.modals.mass.sync_history')
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
                </div>
            </div>
        </div>
    </div>
    @include('admin.shipments.shipments.modals.scheduled')
    <style>
        .opnm {
            color: #777;
            font-size: 11px;
            line-height: 10px;
            margin-top: 3px;
            margin-bottom: -3px;
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
                    {data: 'shipping_date', name: 'shipping_date', searchable: false},
                    {data: 'status_id', name: 'status_id', searchable: false},
                    {data: 'children_tracking_code', name: 'children_tracking_code', class: 'text-center'},
                    {data: 'total_price', name: 'total_price', searchable: false, class: 'text-center'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    {data: 'sender_address', name: 'sender_address', visible: false},
                    {data: 'sender_zip_code', name: 'sender_zip_code', visible: false},
                    {data: 'sender_city', name: 'sender_city', visible: false},
                    {data: 'recipient_address', name: 'recipient_address', visible: false},
                    {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                    {data: 'recipient_city', name: 'recipient_city', visible: false},
                    {data: 'provider_tracking_code', name: 'provider_tracking_code', visible: false},
                    {data: 'reference', name: 'reference', visible: false},
                    {data: 'reference2', name: 'reference2', visible: false},
                    {data: 'parent_tracking_code', name: 'parent_tracking_code', visible: false},
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.shipments.datatable', ['pickup' => true]) }}",
                    type: "POST",
                    data: function (d) {
                        d.type              = $('select[name=type]').val();
                        d.zone              = $('select[name=zone]').val();
                        d.status            = $('select[name="status"]').val();
                        d.source            = $('select[name=source]').val();
                        d.service           = $('select[name=service]').val();
                        d.provider          = $('select[name=provider]').val();
                        d.agency            = $('select[name=agency]').val();
                        d.charge            = $('select[name=charge]').val();
                        d.operator          = $('select[name=operator]').val();
                        d.dispatcher        = $('select[name=dispatcher]').val();
                        d.vehicle           = $('select[name=vehicle]').val();
                        d.route             = $('select[name=route]').val();
                        d.customer          = $('select[name=dt_customer]').val();
                        d.seller             = $('select[name=seller]').val();
                        d.date_min          = $('input[name=date_min]').val();
                        d.date_max          = $('input[name=date_max]').val();
                        d.date_unity        = $('select[name=date_unity]').val();
                        d.deleted           = $('input[name=deleted]:checked').length;
                        d.limit_search      = $('input[name=limit_search]:checked').length;
                        d.invoice           = $('select[name=invoice]').val();
                        d.blocked           = $('select[name=blocked]').val();
                        d.payment_recipient = $('select[name=payment_recipient]').val();
                        d.hide_final_status = $('input[name=hide_final_status]:checked').length;
                        d.hide_scheduled    = $('input[name=hide_scheduled]:checked').length;
                        d.sender_agency     = $('select[name=sender_agency]').val();
                        d.recipient_agency  = $('select[name=recipient_agency]').val();
                        d.sender_country    = $('select[name=fltr_sender_country]').val();
                        d.recipient_country = $('select[name=fltr_recipient_country]').val();
                        d.customer_type      = $('select[name=customer_type]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    //error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                e.preventDefault();
                oTable.draw();

                var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('[data-toggle="export-url"]').attr('href', exportUrl);
            });

            //enable option to search with enter press
            Datatables.searchOnEnter(oTable);
        });

        //show concluded shipments
        $(document).on('change', '[name="hide_final_status"], [name="hide_scheduled"], [name="deleted"], [name="limit_search"]', function (e) {
            oTable.draw();
            e.preventDefault();

            var name = $(this).attr('name');
            var value = $(this).is(':checked');
            value = value == false ? 0 : 1;

            newUrl = Url.updateParameter(Url.current(), name, value)
            Url.change(newUrl);

        });

        $(document).on('click', '[data-s-filter]', function(e){
            e.preventDefault();
            var fltr = $(this).data('s-filter');
            oTable.search(fltr).draw();
        })

        //export selected
        $(document).on('change', '.row-select',function(){
            var queryString = '';
            $('input[name=row-select]:checked').each(function(i, selected){
                queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
            });

            var exportUrl = Url.removeQueryString($('[data-toggle="export-selected"]').attr('href'));
            $('[data-toggle="export-selected"]').attr('href', exportUrl + '?' + queryString);
        });

        $(document).on('change', '#modal-export-selected [name=provider]', function(){
            var exportUrl = Url.updateParameter($('[data-toggle="export-selected"]').attr('href'), 'provider', $(this).val());
            $('[data-toggle="export-selected"]').attr('href', exportUrl);
        })

        $('#modal-export-selected').on('hidden.bs.modal', function (e) {
            $('#modal-export-selected [name=provider]').val('').trigger('change');
        })

        $(document).on('click', '[data-toggle="export-selected"]', function(){
            $('#modal-export-selected').modal('hide');
        })


        //show concluded shipments
        $(document).on('change', '[name="hide_final_status"], [name="hide_scheduled"]', function (e) {
            oTable.draw();

            var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);
        });

        //show deleted shipments
        $(document).on('change', '[name="deleted"]', function (e) {
            oTable.draw();
        });

        $("select[name=assign_customer_id], select[name=dt_customer]").select2({
            minimumInputLength: 2,
            allowClear: true,
            ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
        });

        $('#modal-trip [name=operator], #modal-trip [name=manifest_date]').on('change', function(){
            var url         = "{{ route('admin.printer.shipments.delivery-map', '') }}";
            var date        = $('#modal-trip [name=manifest_date]').val();
            var operator    = $('#modal-trip [name=operator]').val();
            var finalStatus = $('#modal-trip [name=final_status]').is(':checked');

            if($(this).val() == '') {
                $('[data-toggle="print-manifest-url"]').attr('disabled', true);
            } else {
                $('[data-toggle="print-manifest-url"]').attr('disabled', false);
                $('[data-toggle="print-manifest-url"]').attr('href', url + '/' + operator + '?date='+date+'&final_status='+finalStatus);
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
        })

        var oTableShipmentsScheduled;
        $(document).on('click', '[data-target="#modal-shipments-scheduled"]', function() {
            var $tab = $(this);

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);
                oTableShipmentsScheduled = $('#datatable-shipments-scheduled').DataTable({
                    columns: [
                        {data: 'id', name: 'id', visible: false},
                        {data: 'finished', name: 'finished'},
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
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('#modal-shipments-scheduled .filter-datatable').on('change', function (e) {
                    oTable.draw();
                    e.preventDefault();
                })
            }
        });
    </script>
@stop