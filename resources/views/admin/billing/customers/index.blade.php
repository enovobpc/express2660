@section('title')
    Faturação a Clientes
@stop

@section('content-header')
    Faturação a Clientes
@stop

@section('breadcrumb')
    <li class="active">Faturação a Clientes</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    @include('admin.billing.customers.partials.filters')
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                        <li class="fltr-primary w-180px">
                            <strong>Estado</strong><br class="visible-xs"/>
                            <div class="w-120px pull-left form-group-sm">
                                {{ Form::select('billed', ['all' => 'Todos', '1' => 'Faturado', '0' => 'Não faturado'], Request::has('billed') ? Request::get('billed') : '0', array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    </ul>
                    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                        <ul class="list-inline pull-left">
                            @if(count($agencies) > 1)
                                <li style="margin-bottom: 5px;" class="col-xs-12">
                                    <strong>Agência</strong><br/>
                                    <div class="w-200px">
                                        {{ Form::selectMultiple('agency', $agencies, fltr_val(Request::all(), 'agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                    </div>
                                </li>
                            @endif
                            @if(count(@$sellers) > 1)
                                <li style="margin-bottom: 5px;" class="col-xs-12">
                                    <strong>Comercial</strong><br/>
                                    <div class="w-150px">
                                        {{ Form::selectMultiple('seller', $sellers, fltr_val(Request::all(), 'seller'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                    </div>
                                </li>
                            @endif
                            @if(count(@$routes) > 1)
                                <li style="margin-bottom: 5px;" class="col-xs-12">
                                    <strong>Rota</strong><br/>
                                    <div class="w-120px">
                                        {{ Form::selectMultiple('route', $routes, fltr_val(Request::all(), 'seller'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                    </div>
                                </li>
                            @endif
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>Tipo de Cliente</strong><br/>
                                <div class="w-160px">
                                    {{ Form::selectMultiple('type', $types, fltr_val(Request::all(), 'type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>Condição Pgto</strong><br/>
                                <div class="w-160px">
                                    {{ Form::selectMultiple('payment_condition', $paymentConditions, fltr_val(Request::all(), 'payment_condition'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>Com Depart.</strong><br/>
                                <div class="w-90px">
                                    {{ Form::select('department', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], fltr_val(Request::all(), 'department'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                            <thead>
                            <tr>
                                <th></th>
                                <th class="w-45px">Período</th>
                                <th>Cliente</th>
                                <th class="w-1">Doc</th>
                                <th class="w-1">{{ Setting::get('app_mode') == 'cargo' ? 'Cargas' : 'Envios'}}</th>
                                <th class="w-1">Transportes</th>
                                <th class="w-1">Avenças</th>
                                <th class="w-1">Outros</th>
                                <th class="w-70px">Por faturar</th>
                                <th class="w-1">Fatura</th>
                                <th class="w-45px">Total</th>
                                <th class="w-45px">Custos</th>
                                <th class="w-45px">Saldo</th>
                                <th class="w-65px">Ações</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.billing.customers.modals.mass_update_prices')
    @include('admin.billing.customers.modals.export_sap')
    @include('admin.billing.customers.modals.mass_print')
    @include('admin.billing.customers.modals.mass_proforma')
@stop

@section('scripts')
    <script type="text/javascript">
        var oTable;
        $(document).ready(function () {

            oTable = $('#datatable').DataTable({
                columns: [
                    {data: 'id', name: 'id', visible: false},
                    {data: 'month', name: 'month', searchable: false},
                    {data: 'name', name: 'customers.name'},
                    {data: 'default_invoice_type', name: 'default_invoice_type', searchable: false, class: 'text-center'},
                    {data: 'count_shipments', name: 'count_shipments', orderable: false, searchable: false, class: 'text-center'},
                    {data: 'total_shipments', name: 'total_shipments', orderable: false, searchable: false, class: 'text-right'},
                    {data: 'total_covenants', name: 'total_covenants', orderable: false, searchable: false, class: 'text-right'},
                    {data: 'total_products', name: 'total_products', orderable: false, searchable: false, class: 'text-right'},
                    {data: 'total_billing', name: 'total_billing', orderable: false, searchable: false, class: 'text-center'},
                    {data: 'invoice', name: 'invoice', orderable: false, searchable: false},
                    {data: 'total_month', name: 'total_month', orderable: false,searchable: false, class: 'text-right'},
                    {data: 'total_cost', name: 'total_cost', orderable: false, searchable: false, class: 'text-right'},
                    {data: 'profit', name: 'profit', orderable: false, searchable: false, class: 'text-right'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    {data: 'code', name: 'customers.code', visible: false},
                ],
                ajax: {
                    url: "{{ route('admin.billing.customers.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.month      = $('select[name=month]').val();
                        d.year       = $('select[name=year]').val();
                        d.period     = $('select[name=period]').val();
                        d.agency     = $('select[name=agency]').val();
                        d.billed     = $('select[name=billed]').val();
                        d.type       = $('select[name=type]').val();
                        d.payment_condition = $('select[name=payment_condition]').val();
                        d.seller     = $('select[name=seller]').val();
                        d.route      = $('select[name=route]').val();
                        d.department = $('select[name=department]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete() },
                    //error: function () { Datatables.error(); }
                }
            });

            $(document).on('change', '.filter-datatable', function (e) {
                oTable.draw();
                e.preventDefault();

                var exportUrl;
                $('[data-toggle="export-url"]').each(function() {
                    exportUrl = Url.removeQueryString($(this).attr('href'));
                    exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                    $(this).attr('href', exportUrl);
                })
            });

            //enable option to search with enter press
            Datatables.searchOnEnter(oTable);
        });

        /**
         * Change month or year
         */
        $(document).on('change', '[name="month"], [name="year"]',function(){
            $('.filter-loading').show();

            var url    = Url.current();
            var year   = $('.datatable-filters [name="year"]').val();
            var month  = $('.datatable-filters [name="month"]').val();
            var period = typeof $('.datatable-filters [name="period"]').val() == 'undefined' ? '30d' : $('.datatable-filters [name="period"]').val();

            url = Url.updateParameter(url,'month', month);
            url = Url.updateParameter(url,'year', year);
            url = Url.updateParameter(url,'period', period);
            Url.change(url)

            $.post('{{ route('admin.billing.customers.update.filters') }}', {year:year, month:month, period:period}, function(data){
                $('.datatable-filters:first-child').replaceWith(data.html);
                $('.datatable-filters:first-child').removeClass('hide');
                $('.datatable-filters:first-child .select2').select2(Init.select2());

                url = Url.updateParameter(url,'month', data.month);
                url = Url.updateParameter(url,'year', data.year);
                url = Url.updateParameter(url,'period', data.period);
                Url.change(url)

                if(data.month != month) {
                    oTable.draw();
                }
            }).fail(function(){
                $('.filter-loading').hide();
                Growl.error500();
            })
        });

        $(document).on('change', '[name="month"], [name="year"], [name="period"]',function(){
            var month     = $('[name="month"]').val();
            var monthName = $('[name="month"] option:selected').text();
            var year      = $('[name="year"]').val();
            var period    = $('[name="year"]').val();
            var urlPricesForm     = $('.update-prices-form').attr('action');
            var urlPrintForm      = $('.mass-print-form').attr('action');
            var urlBillingSummary = $('#print-billing-summary').attr('href');
            var urlMassBilling    = $('.btn-mass-billing').attr('href');

            $('.month').html(monthName);
            $('.year').html(year);

            urlPricesForm = Url.updateParameter(urlPricesForm, 'month', month);
            urlPricesForm = Url.updateParameter(urlPricesForm, 'year', year);
            urlPricesForm = Url.updateParameter(urlPricesForm, 'period', period);

            urlPrintForm = Url.updateParameter(urlPrintForm, 'month', month);
            urlPrintForm = Url.updateParameter(urlPrintForm, 'year', year);
            urlPrintForm = Url.updateParameter(urlPrintForm, 'period', period);

            urlBillingSummary = Url.updateParameter(urlBillingSummary, 'month', month);
            urlBillingSummary = Url.updateParameter(urlBillingSummary, 'year', year);
            urlBillingSummary = Url.updateParameter(urlBillingSummary, 'period', period);

            urlMassBilling = Url.updateParameter(urlMassBilling, 'month', month);
            urlMassBilling = Url.updateParameter(urlMassBilling, 'year', year);
            urlMassBilling = Url.updateParameter(urlMassBilling, 'period', period);

            $('.update-prices-form').attr('action', urlPricesForm);
            $('.mass-print-form').attr('action', urlPrintForm);
            $('.mass-print-form').attr('href', urlMassBilling);
            $('.btn-mass-billing').attr('href', urlBillingSummary)
        })

        /**
         * Mass update prices
         */
        $('form.update-prices-form').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type=submit]');
            $submitBtn.button('loading');

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                success: function(data) {
                    if(data.result) {
                        Growl.success(data.feedback);
                    } else {
                        Growl.error(data.feedback);
                    }
                }
            }).fail(function () {
                Growl.error500();
            }).always(function () {
                $('#modal-update-prices').modal('hide');
                $submitBtn.button('reset');
            });
        });

        /**
         * Mass print
         */
        $('form.mass-print-form').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type=submit]');
            $submitBtn.button('loading');

            $.ajax({
                url: $form.attr('action'),
                type: 'GET',
                success: function(data) {
                    if(data.result) {
                        Growl.success(data.feedback);
                    } else {
                        Growl.error(data.feedback);
                    }
                }
            }).fail(function () {
                Growl.error500();
            }).always(function () {
                $('#modal-mass-print').modal('hide');
                $submitBtn.button('reset');
            });
        });


        $('#modal-export-sap .btn-submit').on('click', function(e) {
            $(this).closest('form').submit();
            $('#modal-export-sap').modal('hide');
        })
    </script>
@stop