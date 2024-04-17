@section('title')
    {{ trans('account/shipments.title') }} -
@stop

@section('account-content')
    @include('account.partials.alert_unpaid_invoices')
    <ul class="datatable-filters list-inline hide pull-left m-0" data-target="#datatable">
        <li>
            @if ($isShippingBlocked)
                <button class="btn btn-black btn-sm" disabled>
                    <i class="fas fa-plus"></i> {{ trans('account/global.word.new') }}
                </button>
            </li>
        @else

            @if(!isset($auth->customer_id) && !@$auth->settings['hide_btn_shipments'] || isset($auth->customer_id) && empty($auth->hide_btn_shipments))
                <li>
                    <a href="{{ route('account.shipments.create') }}" class="btn btn-black btn-sm" data-toggle="modal"
                        data-target="#modal-remote-xl">
                        <i class="fas fa-plus"></i> {{ trans('account/global.word.new') }}
                    </a>
                </li>

                @if ($hasEcommerceGateways)
                <li>
                    <a href="{{ route('account.ecommerce-gateway.orders') }}" class="btn btn-default btn-sm" data-toggle="modal"
                        data-target="#modal-remote-lg">
                        <i class="fas fa-plug"></i> {{ trans('account/global.word.orders') }}
                    </a>
                </li>
                @endif
            @endif
        
        @endif
        @if ($auth->show_billing && !empty($auth->enabled_services) && !in_array(config('app.source'), ['baltrans']))
            <li>
                <a href="{{ route('account.budgeter.preview-prices.index') }}" class="btn btn-default btn-sm" data-toggle="modal"
                    data-target="#modal-remote-lg">
                    <i class="fas fa-calculator"></i> {{ trans('account/global.word.quote') }}
                </a>
            </li>
        @endif

        @if (!(config('app.source') == 'utiltrans' && $auth->id == '6'))
            <li>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        {{ trans('account/global.word.tools') }} <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="{{ route('account.shipments.close.edit') }}" data-toggle="modal" data-target="#modal-remote">
                                <i class="fas fa-fw fa-tasks"></i> Fechar expedição
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('account.shipments.close.show') }}" data-toggle="modal" data-target="#modal-remote">
                                <i class="fas fa-fw fa-copy"></i> Mapas de fecho
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="" data-toggle="modal" data-target="#modal-print-shipments">
                                <i class="fas fa-fw fa-print"></i> {{ trans('account/global.word.print-list') }}
                            </a>
                        </li>
                        @if ($customerCtt || in_array(config('app.source'), ['rlrexpress', 'entregaki', 'tartarugaveloz', 'ship2u', 'trilhosdinamicos']))
                            <li>
                                <a href="{{ route('account.shipments.ctt-delivery-manifest') }}" data-toggle="modal"
                                    data-target="#modal-remote">
                                    <i class="fas fa-fw fa-print"></i> Certificados CTT
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('account.export.shipments') }}" data-toggle="export-url">
                                <i class="fas fa-fw fa-file-excel"></i> {{ trans('account/global.word.export-list') }}
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('account.importer.index') }}">
                                <i class="fas fa-fw fa-upload"></i> {{ trans('account/global.word.import') }}
                            </a>
                        </li>
                        @if (hasModule('webservices_ecommerce'))
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('account.ecommerce-gateway.index') }}">
                                <i class="fas fa-fw fa-plug"></i> {{ trans('account/global.menu.ecommerce-gateways') }}
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
        @endif
        <li>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> {{ trans('account/global.word.filter') }} <span
                    class="caret"></span>
            </button>
        </li>
    </ul>
    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : '' }}"
        data-target="#datatable">
        <ul class="list-inline pull-left">
            <li style="" class="input-sm">
                <strong>{{ trans('account/global.word.filter') . ' ' . trans('account/global.word.date') }}</strong><br />
                <div class="input-group input-group-sm">
                    {{ Form::select('date_unity', ['shipping_date' => 'Expedição', 'delivery_date' => 'Previsão Entrega', 'created_at' => 'Data Registo'], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
            <li style="width: 250px" class="input-sm">
                <strong>{{ trans('account/global.word.date') }}</strong><br />
                <div class="input-group input-group-sm">
                    {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable','placeholder' => 'Início']) }}
                    <span class="input-group-addon">{{ trans('account/global.word.to') }}</span>
                    {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable']) }}
                </div>
            </li>
            @if ($services)
                <li style="width: 120px" class="input-sm">
                    <strong>{{ trans('account/global.word.service') }}</strong><br />
                    {{ Form::select('service',['' => trans('account/global.word.all')] + $services,Request::has('service') ? Request::get('service') : null,['class' => 'form-control filter-datatable select2']) }}
                </li>
            @endif
            <li style="width: 120px" class="input-sm">
                <strong>{{ trans('account/global.word.status') }}</strong><br />
                {{ Form::select('status',['' => trans('account/global.word.all')] + $status,Request::has('status') ? Request::get('status') : null,['class' => 'form-control filter-datatable select2']) }}
            </li>
            @if (@$providers)
                <li class="input-sm w-100px">
                    <strong>Fornecedor</strong><br />
                    {{ Form::select('provider',['' => trans('account/global.word.all')] + $providers,Request::has('provider') ? Request::get('provider') : null,['class' => 'form-control filter-datatable select2']) }}
                </li>
            @endif
            <li class="input-sm">
                <strong>Fechado</strong><br />
                {{ Form::select('closed',['' => 'Todos', '1' => 'Fechado', '0' => 'Por Fechar'],Request::has('closed') ? Request::get('closed') : null,['class' => 'form-control filter-datatable select2']) }}
            </li>
            <li class="input-sm">
                <strong>{{ trans('account/global.word.charge') }}</strong><br />
                {{ Form::select('charge',trans('account/shipments.filters.charge'),Request::has('charge') ? Request::get('charge') : null,['class' => 'form-control filter-datatable select2']) }}
            </li>
            <li class="input-sm">
                <strong>{{ trans('account/global.word.label') }}</strong><br />
                {{ Form::select('label',trans('account/shipments.filters.label'),Request::has('label') ? Request::get('label') : null,['class' => 'form-control filter-datatable select2']) }}
            </li>
            <li class="input-sm w-130px">
                <strong class="m-t-5">{{ trans('account/global.word.sender-country') }}</strong><br />
                {{ Form::select('filter_sender_country',['' => trans('account/global.word.all')] + trans('country'),Request::has('filter_sender_country') ? Request::get('filter_sender_country') : null,['class' => 'form-control filter-datatable select2']) }}
            </li>
            <li class="input-sm w-130px" style="margin-top: 5px">
                <strong>{{ trans('account/global.word.recipient-country') }}</strong><br />
                {{ Form::select('filter_recipient_country',['' => trans('account/global.word.all')] + trans('country'),Request::has('filter_recipient_country') ? Request::get('filter_recipient_country') : null,['class' => 'form-control filter-datatable select2']) }}
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="table-responsive w-100">
        <table id="datatable" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th class="w-1" style="padding-right: 0;">{{ Form::checkbox('select-all', '') }}</th>
                    <th class="w-1">Tracking</th>
                    @if ($auth->show_reference_column)
                        <th class="w-20px">{{ trans('account/global.word.reference') }}</th>
                    @endif
                    <th>{{ Setting::get('app_mode' == 'cargo')? trans('account/global.word.cargo'): trans('account/global.word.sender') }}</th>
                    <th>{{ Setting::get('app_mode' == 'cargo')? trans('account/global.word.discharge'): trans('account/global.word.recipient') }}</th>
                    <th>{{ trans('account/global.word.service') }}</th>
                    <th class="w-85px">
                        @if (Setting::get('customers_show_delivery_date'))
                            {{ trans('account/global.word.delivery') }}
                        @else
                            {{ trans('account/global.word.details') }}
                        @endif
                    </th>
                    @if (($auth->id == '1443' || $auth->customer_id == '1443') && config('app.source') == 'corridadotempo')
                        <th>{{ trans('account/global.word.references') }}</th>
                    @else
                        <th>{{ trans('account/global.word.remittance') }}</th>
                    @endif

                    @if ($auth->show_billing)
                        <th class="w-40px">{{ trans('account/global.word.price') }}</th>
                    @endif
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="selected-rows-action hide">
        <div>
            <a href="{{ route('account.export.shipments') }}" data-toggle="datatable-action-url"
                class="btn btn-sm btn-default m-l-0">
                <i class="fas fa-file-excel"></i> {{ trans('account/global.word.export') }}
            </a>
            <div class="btn-group btn-group-sm dropup m-l-5">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="fas fa-print"></i> {{ trans('account/shipments.selected.print-label-guides') }} <span
                        class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('account.shipments.selected.print.labels') }}"
                            data-toggle="datatable-action-url" target="_blank">
                            {{ trans('account/shipments.selected.print-labels') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('account.shipments.selected.print.guide') }}"
                            data-toggle="datatable-action-url" target="_blank">
                            {{ trans('account/shipments.selected.print-guides') }}
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-grouped-guide">
                            {{ trans('account/shipments.selected.print-grouped-guide') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="btn-group btn-group-sm dropup m-l-5">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="fas fa-print"></i> {{ trans('account/shipments.selected.print-list') }} <span
                        class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    @if ($auth->show_billing)
                        <li>
                            <a href="{{ route('account.shipments.selected.print', ['print-type' => 'billing']) }}"
                                data-toggle="datatable-action-url" target="_blank">
                                {{ trans('account/shipments.selected.print-summary') }}
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('account.shipments.selected.print', ['print-type' => 'confirmation']) }}"
                            data-toggle="datatable-action-url" target="_blank">
                            {{ trans('account/shipments.selected.print-manifest') }}
                        </a>
                    </li>
                    @if (config('app.source') == 'utiltrans')
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modal-print-cold-manifest">
                                Manifesto de Frio e Humidade
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <a href="{{ route('account.shipments.selected.close') }}" class="btn btn-sm btn-default m-l-5"
               data-toggle="modal"
               data-target="#modal-remote"
               data-action-url="datatable-action-url">
                <i class="fas fa-check"></i> Fechar Expedição
            </a>

            @if ($customerCtt || in_array(config('app.source'), ['rlrexpress', 'entregaki', 'tartarugaveloz', 'ship2u', 'trilhosdinamicos','camposlogistica']))
                <button class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-close-shipments">
                    <i class="fas fa-check"></i> Fechar Envios
                </button>
                @include('account.shipments.modals.close_ctt_shipments')
            @endif
            @include('account.shipments.modals.grouped_guide')
        </div>
    </div>
    @include('account.shipments.modals.print')
    @include('account.shipments.modals.signature')
    @include('account.shipments.modals.print_cold_manifest')
    @include('account.shipments.modals.payment')

    @if (@$messagesPopup)
        @foreach ($messagesPopup as $messagePopup)
            @include('account.partials.message_modal')
        @endforeach
    @endif

    @if(Request::get('shpTrk') && Request::get('shpMode'))
        <?php
        if(Request::get('shpMode') == 'show') {
            $route = route('admin.shipments.show', Request::get('shpTrk'));
        } else {
            $route = route('admin.shipments.edit', Request::get('shpTrk'));
        }
        ?>
        <a href="{{ @$route }}" class="fsearch" data-toggle="modal" data-target="#modal-remote-xl"></a>
    @endif
@stop

@section('styles')
    <style>
        #modal-remote {
            z-index: 1055 !important;
        }

    </style>
@stop

{{ Html::script(asset('vendor/devbridge-autocomplete/dist/jquery.autocomplete.min.js')) }}
@section('scripts')
    <script type="text/javascript">
        var ROUTE_SHIPMENT_PAY =  "{!! route('account.shipments.payment.store', [':id']) !!}";
        var ROUTE_SET_PAYMENT       = "{{ route('account.shipments.set.payment') }}";


        var oTable;

        $(document).ready(function() {
            oTable = $('#datatable').DataTable({
                columns: [
                    {data: 'tracking_code', name: 'tracking_code', visible: false},
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    @if ($auth->show_reference_column)
                        {data: 'reference_show', name: 'reference'},
                    @endif
                    {data: 'sender_name', name: 'sender_name'},
                    {data: 'recipient_name', name: 'recipient_name'},
                    {data: 'service_id', name: 'service_id', class: 'text-center', orderable: false, searchable: false},
                    {data: 'shipping_date', name: 'shipping_date', searchable: false},
                    {data: 'volumes', name: 'volumes', searchable: false, orderable: false},
                    @if ($auth->show_billing)
                        {data: 'total_price', name: 'total_price', class: 'text-center', searchable: false, orderable: false},
                    @endif
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    {data: 'sender_zip_code', name: 'sender_zip_code', visible: false},
                    {data: 'sender_city', name: 'sender_city', visible: false},
                    {data: 'sender_phone', name: 'sender_phone', visible: false},
                    {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                    {data: 'recipient_city', name: 'recipient_city', visible: false},
                    {data: 'recipient_phone', name: 'recipient_phone', visible: false},
                    {data: 'reference2', name: 'reference2', visible: false},
                    {data: 'reference', name: 'reference', visible: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'obs', name: 'obs', visible: false},
                ],
                order: [
                    [2, "desc"]
                ],
                ajax: {
                    url: "{{ route('account.shipments.datatable') }}",
                    type: "POST",
                    data: function(d) {
                        d.date_unity = $('select[name=date_unity]').val();
                        d.date_min = $('input[name=date_min]').val();
                        d.date_max = $('input[name=date_max]').val();
                        d.service = $('select[name=service]').val();
                        d.status = $('select[name=status]').val();
                        d.provider = $('select[name=provider]').val();
                        d.charge = $('select[name=charge]').val();
                        d.label = $('select[name=label]').val();
                        d.closed = $('select[name=closed]').val();
                        d.sender_country = $('select[name=filter_sender_country]').val();
                        d.recipient_country = $('select[name=filter_recipient_country]').val();
                    },
                    beforeSend: function() {
                        Datatables.cancelDatatableRequest(oTable)
                    },
                    complete: function() {
                        Datatables.complete()
                    }
                }
            });

            $('.filter-datatable').on('change', function(e) {
                oTable.draw();
                e.preventDefault();

                var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('[data-toggle="export-url"]').attr('href', exportUrl);
            });
        });

        $(document).on('change', '[name="print_type"]', function() {
            updatePrintUrl();
        })

        $(document).on('change', '[name="print_min_date"], [name="print_max_date"]', function(e) {
            e.preventDefault()
            updatePrintUrl();
        })

        function updatePrintUrl() {
            var minDate = $('[name="print_min_date"]').val();
            var maxDate = $('[name="print_max_date"]').val();
            var printType = $('[name="print_type"]:checked').val();
            var $printBtn = $('.btn-print');
            var url = $printBtn.attr('href');

            url = Url.updateParameter(url, 'min-date', minDate);
            url = Url.updateParameter(url, 'max-date', maxDate);
            url = Url.updateParameter(url, 'print-type', printType);

            $printBtn.attr('href', url);
        }

        $(document).on('click', '.btn-print', function(e) {
            $('#modal-print-shipments').modal('hide');
        })

        $(document).on('change', '[name="mass_print_type"]', function() {
            var url = $('#mass-print-url').attr('href')
            var type = $(this).val();
            url = Url.updateParameter(url, 'print-type', type);

            $('#mass-print-url').attr('href', url);
        })

        /**
         * Close shipments
         */
        $('form.close-shipments').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"]');
            $submitBtn.button('loading')
            $('.close-shipments .message').hide();
            $('.close-shipments .loading').show();

            $.post($form.attr('action'), $form.serialize(), function(data) {

                try {
                    if (data.result) {
                        oTable.draw();
                        if (data.filepath) {
                            window.open(data.filepath, '_blank');
                        }
                        Growl.success(data.feedback);
                    } else {
                        Growl.error(data.feedback);
                    }
                } catch (e) {}

            }).error(function() {
                Growl.error500();
            }).always(function() {
                $('#modal-close-shipments').modal('hide');
                $('.close-shipments .message').show();
                $('.close-shipments .loading').hide();
                $submitBtn.button('reset');
            })
        })

        $('.btn-print-grouped').on('click', function(e) {
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

        $(document).on('click', '#modal-print-cold-manifest a', function(e) {
            e.preventDefault();
            var temp = $('#modal-print-cold-manifest [name="temperature"]').val();
            var hum = $('#modal-print-cold-manifest [name="humidity"]').val();

            if (temp == '' || hum == '') {
                Growl.error('É obrigatório indicar a temperatura e humidade.')
            } else {
                var url = $(this).attr('href');
                url = Url.updateParameter(url, 'temperature', temp);
                url = Url.updateParameter(url, 'humidity', hum);
                window.open(url, '_blank');

                $('#modal-print-cold-manifest').modal('hide');
            }
        })

        @if ($messagesPopup)
            @foreach ($messagesPopup as $messagePopup)
                $(document).ready(function(){
                $('#modal-message-{{ $messagePopup->id }}').modal('show');
                })
            @endforeach

            $(document).on('change', '[name="is_read"]',function(){

                if($(this).is(':checked')) {
                    var action = $(this).closest('form').attr('action');
                    $.post(action, {is_read:1}, function(){});
                } else {
                    var action = $(this).closest('form').attr('action');
                    $.post(action, {is_read:0}, function(){});
                }

            })
        @endif

        $('#datatable').on('click', '.pay-shipment', function(e) {
            var $this           = $(this);
            var trkId           = $this.data('trkid');
            var billingSubtotal = $this.data('subtotal');
            var billingVat      = $this.data('vat');
            var total           = $this.data('total');

            modalShipmentPayment.show(trkId, billingSubtotal, billingVat, total, function () {
                // SUCCESS
                $('.modal').hide();

                if (typeof oTable !== "undefined") {
                    oTable.draw(false);
                }
            }, function () {
                // CANCEL (PAY LATER)
                $('.modal').hide();
            });
        });

        $('#btn-close').on('click', function () {
            modalShipmentPayment.close(function () {
                $('#modal-remote-xl').modal('hide');
            })
        });

    </script>
@stop
