@section('title')
    Faturação a Clientes
@stop

@section('content-header')
    Faturação a Clientes
@stop

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.billing.customers.index', ['month' => $month, 'year' => $year, 'period' => $period]) }}">
            Faturação a Clientes
        </a>
    </li>
    <li class="active">
        {{ str_limit($customer->name, 50) }} - {{ trans('datetime.month.'.$month) }} de {{ $year }}
    </li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12 billing-header">
            @include('admin.billing.customers.partials.header')
        </div>
    </div>

    @if(!$reminders->isEmpty())
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    @foreach($reminders as $reminder)
                    <p class="m-t-0 m-b-0" style="margin-bottom: 5px">
                        <i class="fas fa-info-circle"></i> <b>{{ $reminder->title }}</b> <a href="{{ route('admin.calendar.events.conclude', $reminder->id) }}" class="btn btn-xs btn-default event-mark-concluded" style="color: #111; text-decoration: none; font-size: 10px; padding: 1px 2px !important; line-height: 11px;">Terminar lembrete</a>
                        @if($reminder->description)
                            <br/>
                            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $reminder->description }}</span>
                        @endif
                    </p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if($customer->has_billing_warnings)
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <h4 class="m-t-0" style="margin-bottom: 5px">
                    <i class="fas fa-exclamation-triangle"></i> <b>ATENÇÃO! Existem envios que requerem atenção.</b><br/>
                </h4>
                <ul class="fs-14">
                    @if($customer->billing_warnings['empty_prices'])
                    <li>
                        {{ $customer->billing_warnings['empty_prices'] }} envios não têm preço.
                        <a class="btn btn-xs btn-default btn-filter-price" style="color: #111; text-decoration: none; font-size: 10px; padding: 1px 2px !important; line-height: 11px;">Ver Envios</a>
                    </li>
                    @endif

                    @if($customer->billing_warnings['empty_countries'])
                        <li>
                            {{ $customer->billing_warnings['empty_countries'] }} envios ou recolhas sem país de origem ou destino associado.
                            <button class="btn btn-xs btn-default btn-filter-empty-country" style="color: #111; text-decoration: none; font-size: 10px; padding: 1px 2px !important; line-height: 11px;">Ver Envios</button>
                        </li>
                    @endif

                    @if($customer->billing_warnings['empty_services'])
                        <li>
                            {{ $customer->billing_warnings['empty_services'] }} envios ou recolhas sem serviço associado.
                            <button class="btn btn-xs btn-default btn-filter-empty-services" style="font-size: 10px; padding: 1px 2px !important; line-height: 11px;">Ver Envios</button>
                        </li>
                    @endif

                    @if($customer->billing_warnings['empty_prices_pickups'])
                        <li>
                            {{ $customer->billing_warnings['empty_prices_pickups'] }} taxas de recolha sem preço.
                            <a href="{{ route('admin.billing.customers.show', [$customer->id, 'year' => $year, 'month' => $month, 'period' => '30d', 'tab' => 'pickups', 'price' => 1, 'filter' => 1]) }}"
                               class="btn btn-xs btn-default text-black"
                               style="font-size: 10px; padding: 1px 2px !important; line-height: 11px; text-decoration: none">
                                Ver Pedidos de Recolha
                            </a>
                        </li>
                    @endif

                    @if($customer->billing_warnings['empty_pickup_assigned_shipment'])
                        <li>
                            {{ $customer->billing_warnings['empty_pickup_assigned_shipment'] }} pedidos de recolha sem envio gerado. Estas recolhas não serão faturadas.
                            <a href="{{ route('admin.billing.customers.show', [$customer->id, 'year' => $year, 'month' => $month, 'period' => '30d', 'tab' => 'pickups', 'filter' => 1]) }}"
                               class="btn btn-xs btn-default text-black"
                               style="font-size: 10px; padding: 1px 2px !important; line-height: 11px; text-decoration: none">
                                Ver Pedidos de Recolha
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="row row-5">
        <div class="col-md-3 col-lg-2 billing-sidebar">
            @include('admin.billing.customers.partials.sidebar')
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="tab-content">
                <div class="{{ Request::get('tab') == 'stats' ? '' : 'active' }} tab-pane" id="tab-shipments">
                    @include('admin.billing.customers.partials.shipments')
                </div>
                <div class="tab-pane" id="tab-pickups" data-empty="1">
                    @include('admin.billing.customers.partials.pickups')
                </div>
                <div class="tab-pane" id="tab-cod" data-empty="1">
                    @include('admin.billing.customers.partials.cod')
                </div>
                <div class="tab-pane" id="tab-billing-expenses" data-empty="1">
                    @include('admin.billing.customers.partials.expenses')
                </div>

                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'products'))
                <div class="tab-pane" id="tab-products" data-empty="1">
                    @include('admin.billing.customers.partials.products')
                </div>
                @endif

                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'customer_covenants'))
                <div class="tab-pane" id="tab-covenants" data-empty="1">
                    @include('admin.billing.customers.partials.covenants')
                </div>
                @endif

                @if(Request::has('tab') && Request::get('tab') == 'stats')
                <div class="active tab-pane" id="tab-stats">
                    @include('admin.billing.customers.partials.stats')
                </div>
                @endif
            </div>
        </div>
    </div>
    <style>
        .datepicker {
            z-index: 999;
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

        $(document).ready(function(){
            $('a[href="#tab-{{ Request::get("tab") }}"]').trigger('click');
        })


        //billing selected
        $(document).on('change', '.row-select',function(){
            var queryString = '';
            $('input[name=row-select]:checked').each(function(i, selected){
                queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
            });

            var tab = $(this).closest('table').attr('id');
            tab = tab.replace('datatable-', '');

            var targetUrl = Url.removeQueryString($('[data-url-target="billing-selected"]').attr('href'));
            $('[data-url-target="billing-selected"]').attr('href', targetUrl + '?' + queryString+ '&month={{ $month }}&year={{ $year }}&period={{ $period }}&tab=' + tab);
        });

        var oTable, oTablePickups;

        $(document).on('change', '[data-target="#datatable-shipments"] .filter-datatable', function (e) {
            e.preventDefault();
            oTable.draw();

            $('[data-export-url]').each(function() {
                var exportUrl = Url.removeQueryString($(this).attr('href'));

                console.log(Url.getQueryString(Url.current()));
                exportUrl = exportUrl + '?billlist=1&' + Url.getQueryString(Url.current())

                //console.log(exportUrl);
                /*$('.datatable-filters-area-extended [type="checkbox"]').each(function(){ //add checkbox filters
                    checkStatus = $(this).is(':checked') ? 1 : 0;
                    varName = $(this).attr('name');
                    exportUrl+= '&'+varName+'=' + checkStatus;
                })
*/
                $(this).attr('href', exportUrl);
            })
        });

        $('[data-target="#datatable-pickups"] .filter-datatable').on('change', function (e) {
            e.preventDefault();
            oTablePickups.draw();
        });


        $(document).ready(function () {

            oTable = $('#datatable-shipments').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'tracking_code', name: 'tracking_code'},
                    {data: 'reference', name: 'reference'},
                    {data: 'sender_name', name: 'sender_name'},
                    {data: 'recipient_name', name: 'recipient_name'},
                    {data: 'service_id', name: 'service_id', searchable: false},
                    {data: 'volumes', name: 'volumes'},
                    @if(Setting::get('shipment_list_show_delivery_date'))
                    {data: 'delivery_date', name: 'delivery_date'},
                    @else
                    {data: 'date', name: 'date'},
                    @endif
                    @if(Setting::get('app_mode') == 'cargo')
                    {data: 'vehicle', name: 'vehicle'},
                    @endif
                    {data: 'status_id', name: 'status_id', searchable: false},
                    @if(config('app.source') == 'horasambulantes')
                     {data: 'charge_price', name: 'charge_price', class:'text-center'},
                    @endif
                    {data: 'total_price', name: 'total_price', class:'text-center'},
                    {data: 'customer_conferred', name: 'customer_conferred', class:'text-center', orderable: false},
                    {data: 'invoice_id', name: 'invoice_id', class: 'text-center'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                    {data: 'recipient_city', name: 'recipient_city', visible: false},
                    {data: 'recipient_phone', name: 'recipient_phone', visible: false},
                    {data: 'reference', name: 'reference', visible: false},
                    {data: 'reference2', name: 'reference2', visible: false},
                ],
                ajax: {
                    url: "{{ route('admin.billing.customers.shipments.datatable', [$customer->id]) }}",
                    type: "POST",
                    data: function (d) {
                        d.month     = "{{ $month }}";
                        d.year      = "{{ $year }}";
                        d.period    = "{{ $period }}";
                        d.zone      = $('[data-target="#datatable-shipments"] select[name=ship_zone]').val();
                        d.service   = $('[data-target="#datatable-shipments"] select[name=service]').val();
                        d.provider  = $('[data-target="#datatable-shipments"] select[name=provider]').val();
                        d.agency    = $('[data-target="#datatable-shipments"] select[name=agency]').val();
                        d.operator  = $('[data-target="#datatable-shipments"] select[name=operator]').val();
                        d.date_min  = $('[data-target="#datatable-shipments"] input[name=date_min]').val();
                        d.date_max  = $('[data-target="#datatable-shipments"] input[name=date_max]').val();
                        d.status    = $('[data-target="#datatable-shipments"] select[name=status]').val();
                        d.conferred = $('[data-target="#datatable-shipments"] select[name=conferred]').val();
                        d.billed    = $('[data-target="#datatable-shipments"] select[name=billed]').val();
                        d.price     = $('[data-target="#datatable-shipments"] select[name=price]').val();
                        d.invoice   = $('[data-target="#datatable-shipments"] select[name=invoice]').val();
                        d.charge    = $('[data-target="#datatable-shipments"] select[name=charge_price]').val();
                        d.expenses  = $('[data-target="#datatable-shipments"] select[name=expenses]').val();
                        d.price_fixed       = $('[data-target="#datatable-shipments"] select[name=price_fixed]').val();
                        d.recipient_agency  = $('[data-target="#datatable-shipments"] select[name=recipient_agency]').val();
                        d.sender_country    = $('[data-target="#datatable-shipments"] select[name=sender_country]').val();
                        d.recipient_country = $('[data-target="#datatable-shipments"] select[name=recipient_country]').val();
                        d.empty_country     = $('[data-target="#datatable-shipments"] select[name=empty_country]').val();
                        d.department        = $('[data-target="#datatable-shipments"] select[name=department]').val();
                        d.expense_type      = $('select[name=expense_type]').val();
                        d.return            = $('select[name=return]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); }
                }
            });
        });

        $(document).on('click', 'a[href="#tab-pickups"]', function () {
            $tab = $('#tab-pickups');

            if ($tab.data('empty') == '1') {
                $tab.data('empty', 0);


                oTablePickups = $('#datatable-pickups').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'tracking_code', name: 'tracking_code'},
                        {data: 'sender_name', name: 'sender_name'},
                        {data: 'recipient_name', name: 'recipient_name'},
                        {data: 'service_id', name: 'service_id', searchable: false},
                        /*{data: 'volumes', name: 'volumes'},*/
                        {data: 'date', name: 'date'},
                        {data: 'status_id', name: 'status_id', searchable: false},
                        {data: 'children_tracking_code', name: 'children_tracking_code', class: 'text-center'},
                        {data: 'total_price', name: 'total_price', class:'text-center'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                        {data: 'sender_zip_code', name: 'sender_zip_code', visible: false},
                        {data: 'sender_city', name: 'sender_city', visible: false},
                        {data: 'sender_phone', name: 'sender_phone', visible: false},
                        {data: 'reference', name: 'reference', visible: false},
                        {data: 'reference2', name: 'reference2', visible: false},
                    ],
                    ajax: {
                        url: "{{ route('admin.billing.customers.shipments.datatable', [$customer->id]) }}",
                        type: "POST",
                        data: function (d) {
                            d.pickup    = 1,
                            d.month     = "{{ $month }}";
                            d.year      = "{{ $year }}";
                            d.period    = "{{ $period }}";
                            d.zone      = $('[data-target="#datatable-pickups"] select[name=ship_zone]').val();
                            d.service   = $('[data-target="#datatable-pickups"] select[name=service]').val();
                            d.provider  = $('[data-target="#datatable-pickups"] select[name=provider]').val();
                            d.agency    = $('[data-target="#datatable-pickups"] select[name=agency]').val();
                            d.operator  = $('[data-target="#datatable-pickups"] select[name=operator]').val();
                            d.date_min  = $('[data-target="#datatable-pickups"] input[name=date_min]').val();
                            d.date_max  = $('[data-target="#datatable-pickups"] input[name=date_max]').val();
                            d.status    = $('[data-target="#datatable-pickups"] select[name=status]').val();
                            d.conferred = $('[data-target="#datatable-pickups"] select[name=conferred]').val();
                            d.billed    = $('[data-target="#datatable-pickups"] select[name=billed]').val();
                            d.price     = $('[data-target="#datatable-pickups"] select[name=price]').val();
                            d.invoice   = $('[data-target="#datatable-pickups"] select[name=invoice]').val();
                            d.charge    = $('[data-target="#datatable-shipments"] select[name=charge_price]').val();
                            d.recipient_agency  = $('[data-target="#datatable-pickups"] select[name=recipient_agency]').val();
                            d.sender_country    = $('[data-target="#datatable-pickups"] select[name=sender_country]').val();
                            d.recipient_country = $('[data-target="#datatable-pickups"] select[name=recipient_country]').val();
                            d.empty_children    = $('[data-target="#datatable-pickups"] select[name=empty_children]').val();
                            d.department        = $('[data-target="#datatable-pickups"] select[name=department]').val();
                            d.return            = $('select[name=return]').val();
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });
            }
        });

        $(document).on('click', 'a[href="#tab-cod"]', function () {
            $tab = $('#tab-cod');

            if ($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTable = $('#datatable-tab-cod').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'tracking_code', name: 'tracking_code'},
                        {data: 'sender_name', name: 'sender_name'},
                        {data: 'recipient_name', name: 'recipient_name'},
                        {data: 'service_id', name: 'service_id', searchable: false},
                        {data: 'volumes', name: 'volumes'},
                        {data: 'date', name: 'date'},
                        {data: 'status_id', name: 'status_id', searchable: false},
                        {data: 'total_price', name: 'total_price'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                        {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                        {data: 'recipient_city', name: 'recipient_city', visible: false},
                        {data: 'recipient_phone', name: 'recipient_phone', visible: false},
                    ],
                    ajax: {
                        url: "{{ route('admin.billing.customers.shipments.datatable', [$customer->id]) }}",
                        type: "POST",
                        data: function (d) {
                            d.cod    = 1,
                            d.month  = "{{ $month }}";
                            d.year   = "{{ $year }}";
                            d.period = "{{ $period }}"
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });
            };
        });


        /**
         * Tab expenses
         * @returns {undefined}
         */
        $(document).on('click', 'a[href="#tab-billing-expenses"]', function () {
            $tab = $('#tab-billing-expenses');

            if ($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                var oTable = $('#datatable-billing-expenses').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'date', name: 'date'},
                        {data: 'tracking_code', name: 'tracking_code', orderable: false, searchable: false},
                        {data: 'name', name: 'name'},
                        {data: 'price', name: 'price', orderable: false, searchable: false},
                        {data: 'qty', name: 'qty'},
                        {data: 'subtotal', name: 'subtotal'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false}
                    ],
                    ajax: {
                        url: "{{ route('admin.billing.customers.expenses.datatable', [$customer->id]) }}",
                        type: "POST",
                        data: function (d) {
                            d.month   = "{{ $month }}";
                            d.year    = "{{ $year }}";
                            d.period  = "{{ $period }}";
                            d.expense = $('[data-target="#datatable-billing-expenses"] select[name=expenseid]').val();
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('.filter-datatable').on('change', function (e) {
                    oTable.draw();
                    e.preventDefault();
                });
            }
        })

        /**
         * Tab covenants
         * @returns {undefined}
         */
        $(document).on('click', 'a[href="#tab-covenants"]', function () {
            $tab = $('#tab-covenants');

            if ($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                var oTable = $('#datatable-covenants').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'type', name: 'type'},
                        {data: 'description', name: 'description'},
                        {data: 'max_shipments', name: 'max_shipments'},
                        {data: 'service', name: 'service', orderable: false, searchable: false},
                        {data: 'amount', name: 'amount'},
                        {data: 'start_date', name: 'start_date'},
                        {data: 'end_date', name: 'end_date'},
                        {data: 'invoice_id', name: 'invoice_id', class: 'text-center', orderable: false, searchable: false},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    ajax: {
                        url: "{{ route('admin.customers.covenants.datatable', [$customer->id, 'month' => $month, 'year' => $year]) }}",
                        type: "POST",
                        data: function (d) {
                            d.source = "billing",
                            d.month  = "{{ $month }}",
                            d.year   = "{{ $year }}",
                            d.period = "{{ $period }}"
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('.filter-datatable').on('change', function (e) {
                    oTable.draw();
                    e.preventDefault();
                });
            }
        })

        /**
         * Tab products
         * @returns {undefined}
         */
        $(document).on('click', 'a[href="#tab-products"]', function () {
            $tab = $('#tab-products');

            if ($tab.data('empty') == '1') {
                $tab.data('empty', 0);


                var oTable = $('#datatable-products').DataTable({
                    columns: [
                        {data: 'created_at', name: 'created_at'},
                        {data: 'name', name: 'product.name'},
                        {data: 'price', name: 'price'},
                        {data: 'qty', name: 'qty'},
                        {data: 'subtotal', name: 'subtotal'},
                        @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'products_sales'))
                        {data: 'actions', name: 'actions', orderable: false, searchable: false}
                        @endif
                    ],
                    ajax: {
                        url: "{{ route('admin.billing.customers.products.datatable', [$customer->id, 'month' => $month, 'year' => $year]) }}",
                        type: "POST",
                        data: function (d) {
                            d.period = "{{ $period }}"
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('.filter-datatable').on('change', function (e) {
                    oTable.draw();
                    e.preventDefault();
                });
            }
        })

        $(document).on('click', 'a[href="#tab-stats"]', function () {
            $tab = $('#tab-stats');

            if ($tab.data('empty') == '1') {
                $tab.data('empty', 0);
            };
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
         * Confirm shipment
         */
        $(document).on('click', '.btn-confirm', function(){
            var $parent = $(this).parent();
            var id = $parent.data('id');
            var lastHtml = $parent.html();
            $parent.html('<i class="fas fa-spin fa-circle-notch"></i>');

            $.post("{{ route('admin.billing.customers.shipments.confirm') }}", {ids:id}, function(data){
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

        /**
         * Sync balance
         */
       {{-- @if(hasModule('invoices'))
        $.post('{{ route('admin.customers-balance.sync', $customer->id) }}', function(data){});
        @endif--}}

        $('.btn-filter-price').on('click', function(){
            $(document).find('.btn-filter-datatable').trigger('click')
            $(document).find('[name="price"]').val('1').trigger('change');
            $(document).find('[name="service"]').val('').trigger('change');
            $(document).find('[name="empty_country"]').val('').trigger('change');
        })

        $('.btn-filter-empty-country').on('click', function(){
            $(document).find('.btn-filter-datatable').trigger('click');
            $(document).find('[name="price"]').val('').trigger('change');
            $(document).find('[name="service"]').val('').trigger('change');
            $(document).find('[name="empty_country"]').val('1').trigger('change');
        })

        $('.btn-filter-empty-services').on('click', function(){
            $(document).find('.btn-filter-datatable').trigger('click')
            $(document).find('[name="price"]').val('').trigger('change');
            $(document).find('[name="service"]').val('-1').trigger('change');
            $(document).find('[name="empty_country"]').val('').trigger('change');
        })

        $('.event-mark-concluded').on('click', function(){
            $(this).closest('p').slideUp(function(){
                if($('.alert-info').find('p:visible').length == 0) {
                    $('.alert-info').slideUp()
                }
            })


        })
    </script>
@stop