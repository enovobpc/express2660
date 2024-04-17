@section('title')
    Documentos de Venda
@stop

@section('content-header')
    Documentos de Venda
@stop

@section('breadcrumb')
    <li class="active">Faturação</li>
    <li class="active">Documentos de Venda</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom nav-types">
                <ul class="nav nav-tabs tabs-filter">
                    <li><a href="#tab-balance" data-toggle="tab">Pendente</a></li>
                    <li><a href="#tab-invoices" data-type="invoice" data-toggle="tab">Faturas</a></li>
                    @if(in_array(Setting::get('app_country'), ['pt', 'ptmd', 'ptac', 'ao']))
                    <li><a href="#tab-invoices-receipt" data-type="invoice-receipt" data-toggle="tab">Faturas-Recibo</a></li>
                    <li><a href="#tab-simplified-invoices" data-type="simplified-invoice" data-toggle="tab">Fat. Simp.</a></li>
                    @endif
                    <li><a href="#tab-credit-notes" data-type="credit-note" data-toggle="tab">Notas Crédito</a></li>
                    <li><a href="#tab-receipts" data-type="receipt" data-toggle="tab">Recibos</a></li>
                    <li><a href="#tab-credit-notes" data-type="debit-note" data-toggle="tab">Notas Débito</a></li>
                    @if(hasModule('invoices-advanced'))
                        <li><a href="#tab-proforma" data-type="proforma-invoice" data-toggle="tab">Proformas</a></li>
                        <li><a href="#tab-document" data-type="internal-doc" data-toggle="tab">Docs Internos</a></li>
                        <li><a href="#tab-regularization" data-type="regularization" data-toggle="tab">Regularização</a></li>
                        <li><a href="#tab-nodoc" data-type="nodoc" data-toggle="tab">Sem Doc.</a></li>
                        <li><a href="#tab-scheduled" data-type="scheduled" data-toggle="tab"><i class="fas fa-clock"></i> Avenças</a></li>
                        <li class="active"><a href="#tab-all" data-type="all" data-toggle="tab">Todos</a></li>
                    @else
                        <li><a href="#tab-nodoc" data-type="nodoc" data-toggle="tab">Sem Doc.</a></li>
                        <li class="active"><a href="#tab-all" data-type="all" data-toggle="tab">Todos</a></li>
                        <li><a href="#" style="opacity: 0.3" data-toggle="tooltip" title="Este módulo não está incluido no seu plano contratado.">Proformas</a></li>
                        <li><a href="#" style="opacity: 0.3" data-toggle="tooltip" title="Este módulo não está incluido no seu plano contratado.">Docs Internos</a></li>
                        <li><a href="#" style="opacity: 0.3" data-toggle="tooltip" title="Este módulo não está incluido no seu plano contratado."><i class="fas fa-clock"></i> Programado</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tab-balance">
                        @include('admin.invoices.sales.tabs.balance')
                    </div>
                    <div class="tab-pane active" id="tab-all">
                        @include('admin.invoices.sales.tabs.invoices')
                    </div>
                    <div class="tab-pane" id="tab-scheduled">
                        @include('admin.invoices.sales.tabs.scheduled')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .brdlft {
            border-left: 2px solid #333 !important;
        }

        .nav-types .nav>li>a {
            padding: 10px 10px;
        }
    </style>
    @include('admin.invoices.sales.modals.operator_balance')
    @include('admin.invoices.sales.modals.map_customer_sales')
    @include('admin.invoices.sales.modals.map_annual_sales')
    @include('admin.invoices.sales.modals.map_vat_summary')
    @include('admin.invoices.sales.modals.map_unpaid_invoices')
    @include('admin.invoices.sales.modals.mass_invoices')

    @if(Request::get('invoice'))
        <a href="{{ route('admin.invoices.edit', Request::get('invoice')) }}" class="mopen" data-toggle="modal" data-target="#modal-remote-xl"></a>
    @endif
@stop

@section('scripts')
<script type="text/javascript">
    var oTable, oTableScheduled, oTableBalance, oTableBalance;

    $(document).ready(function () {
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'doc_date', name: 'doc_date'},
                {data: 'doc_id', name: 'doc_id', class: 'text-center'},
                {data: 'customer_id', name: 'customer_id'},
                /*{data: 'doc_type', name: 'doc_type', class: 'text-center'},*/
                {data: 'reference', name: 'reference'},
                {data: 'doc_subtotal', name: 'doc_subtotal', class:'text-right'},
                {data: 'total', name: 'total', class:'text-right'},
                {data: 'doc_total_pending', name: 'doc_total_pending', class:'text-right'},
                {data: 'due_date', name: 'due_date', class: 'text-center'},
                {data: 'is_settle', name: 'is_settle', class: 'text-center'},
                {data: 'payment_date', name: 'payment_date'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'billing_name', name: 'billing_name', visible: false},
                {data: 'billing_code', name: 'billing_code', visible: false},
                {data: 'vat', name: 'vat', visible: false},
                {data: 'doc_subtotal', name: 'doc_subtotal', visible: false},
                {data: 'doc_total', name: 'doc_total', visible: false},
                {data: 'doc_total_pending', name: 'doc_total_pending', visible: false},
            ],
            order: [[12, "desc"]],
            ajax: {
                url: "{{ route('admin.invoices.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.year             = $('#tab-all select[name=year]').val();
                    d.month            = $('#tab-all select[name=month]').val();
                    d.serie            = $('#tab-all select[name=serie]').val();
                    d.doc_type         = $('#tab-all select[name=doc_type]').val();
                    d.doc_id           = $('#tab-all input[name=doc_id]').val();
                    d.customer         = $('#tab-all select[name=customer]').val();
                    d.date_min         = $('#tab-all input[name=date_min]').val();
                    d.date_max         = $('#tab-all input[name=date_max]').val();
                    d.settle           = $('#tab-all select[name=settle]').val();
                    d.expired          = $('#tab-all input[name=expired]').val();
                    d.seller           = $('#tab-all select[name=seller]').val();
                    d.draft            = $('#tab-all select[name=draft]').val();
                    d.target           = $('#tab-all select[name=target]').val();
                    d.payment_method   = $('#tab-all select[name=payment_method]').val();
                    d.payment_condtion = $('#tab-all select[name=payment_condition]').val();
                    d.agency           = $('#tab-all select[name=agency]').val();
                    d.route            = $('#tab-all select[name=route]').val();
                    d.operator         = $('#tab-all select[name=operator]').val();
                    d.deleted          = $('#tab-all input[name=deleted]:checked').length;
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                //error: function () { Datatables.error(); }
            }
        });

        $('#tab-all .filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('#tab-all [data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('#tab-all [data-toggle="export-url"]').attr('href', exportUrl);

            var printUrl = Url.removeQueryString($('[data-toggle="print-url"]').attr('href'));
            printUrl = printUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="print-url"]').attr('href', printUrl);
        });

        /**
         * Table scheduled
         * @type {*|jQuery}
         */
        oTableScheduled = $('#datatable-scheduled').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id'},
                {data: 'doc_type', name: 'doc_type', class: 'text-center'},
                /*{data: 'doc_subtotal', name: 'doc_subtotal', class:'text-right'},*/
                {data: 'total', name: 'total'},
                {data: 'reference', name: 'reference'},
                {data: 'payment_condition', name: 'payment_condition'},
                {data: 'schedule_time', name: 'schedule_time', orderable: false, searchable: false},
                {data: 'schedule_config', name: 'schedule_config', orderable: false, searchable: false},
                {data: 'schedule_end', name: 'schedule_end', orderable: false, searchable: false},
                {data: 'finished', name: 'finished', orderable: false, searchable: false, class: 'text-center'},
                {data: 'actions', name: 'actions', class: 'text-center', orderable: false, searchable: false},
                {data: 'billing_name', name: 'billing_name', visible: false},
                {data: 'billing_code', name: 'billing_code', visible: false},
                {data: 'vat', name: 'vat', visible: false},
                {data: 'doc_subtotal', name: 'doc_subtotal', visible: false},
                {data: 'doc_total', name: 'doc_total', visible: false},
                {data: 'doc_total_pending', name: 'doc_total_pending', visible: false},
            ],
            order: [[1, "desc"]],
            ajax: {
                url: "{{ route('admin.invoices.datatable', ['scheduled' => true]) }}",
                type: "POST",
                data: function (d) {
                    d.scheduled = 1;
                    d.year      = $('#tab-scheduled select[name=year]').val();
                    d.month     = $('#tab-scheduled select[name=month]').val();
                    d.serie     = $('#tab-scheduled select[name=serie]').val();
                    d.type      = $('#tab-scheduled select[name=type]').val();
                    d.customer  = $('#tab-scheduled select[name=customer]').val();
                    d.date_min  = $('#tab-scheduled input[name=date_min]').val();
                    d.date_max  = $('#tab-scheduled input[name=date_max]').val();
                    d.payment_condition = $('#tab-scheduled select[name=payment_condition]').val();
                    d.deleted   = $('#tab-scheduled input[name=deleted]:checked').length;
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableScheduled) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-scheduled .filter-datatable').on('change', function (e) {
            oTableScheduled.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('#tab-scheduled [data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('#tab-scheduled [data-toggle="export-url"]').attr('href', exportUrl);
        });


        oTableBalance = $('#datatable-balance').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'vat', name: 'vat'},
                {data: 'payment_method', name: 'payment_method'},
                {data: 'balance_expired_count', name: 'balance_expired_count', class: 'text-right brdlft', searchable: false},
                {data: 'balance_total_debit', name: 'balance_total_debit', 'class': 'text-right', searchable: false},
                {data: 'balance_total_credit', name: 'balance_total_credit', 'class': 'text-right', searchable: false},
                {data: 'balance_total', 'class': 'text-right', name: 'balance_total', searchable: false},
                {data: 'last_shipment', name: 'last_shipment', searchable: false},
                @if(App\Models\Invoice::getInvoiceSoftware() != App\Models\Invoice::SOFTWARE_ENOVO)
                {data: 'balance_last_update', name: 'balance_last_update', searchable: false},
                @endif
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'city', name: 'city', visible: false},
                {data: 'zip_code', name: 'zip_code', visible: false},
            ],
            order: [[9, "desc"]],
            ajax: {
                url: "{{ route('admin.billing.balance.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.agency            = $('#tab-balance select[name="agency"]').val(),
                    d.unpaid            = $('#tab-balance select[name="unpaid"]').val(),
                    d.expired           = $('#tab-balance select[name="expired"]').val(),
                    d.seller            = $('#tab-balance select[name="seller"]').val(),
                    d.divergence        = $('#tab-balance select[name="divergence"]').val(),
                    d.payment_method    = $('#tab-balance select[name="payment_method"]').val(),
                    d.last_shipment     = $('#tab-balance select[name="last_shipment"]').val(),
                    d.last_shipment     = $('#tab-balance select[name="last_shipment"]').val(),
                    d.type              = $('#tab-balance select[name="type"]').val(),
                    d.particular        = $('#tab-balance select[name="particular"]').val(),
                    d.route             = $('#tab-balance select[name="route"]').val(),
                    d.billing_country   = $('#tab-balance select[name="country_billing"]').val(),
                    d.country           = $('#tab-balance select[name="country"]').val(),
                    d.district          = $('#tab-balance select[name="district"]').val(),
                    d.county            = $('#tab-balance select[name="county"]').val(),
                    d.prices            = $('#tab-balance select[name="prices"]').val()
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableBalance) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-balance .filter-datatable').on('change', function (e) {
            oTableBalance.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('#tab-balance [data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('#tab-balance [data-toggle="export-url"]').attr('href', exportUrl);
        });


        $('.tabs-filter a').on('click', function(e){
            e.preventDefault();
            var type = $(this).data('type');

            $('#tab-all').addClass('active');
            $('#tab-scheduled').removeClass('active');

            if(type == 'all') {
                $('.doc-type-filter').show();
                $('[name="doc_type"]').val('').trigger('change');
            } else if(typeof type !== 'undefined') {
                $('.doc-type-filter').hide();
                $('[name="doc_type"]').val(type).trigger('change');
                $('.tab-pane').removeClass('active')
                $('#tab-all').addClass('active')
            }

            $('.btn-add-invoice, .btn-saft').show();
            $('.btn-add-receipt, .btn-add-regularization, .btn-mass-edit').hide();

            if(type == 'receipt') {
                $('.btn-add-invoice, .btn-saft').hide();
                $('.btn-add-receipt').show();
            }

            if(type == 'regularization') {
                $('.btn-add-invoice, btn-add-receipt, .btn-saft').hide();
                $('.btn-add-regularization').show();
            }

            if(type == 'nodoc') {
                $('.btn-add-invoice, .btn-saft').hide();
                $('.btn-mass-edit').show();
            }

            if(type == 'scheduled') {
                $('.btn-add-invoice, .btn-saft').hide();
                $('.btn-add-receipt').show();
                $('.btn-add-receipt').show();
            }
        })

    });

    //show concluded shipments
    $(document).on('change', '[name="deleted"]', function (e) {
        oTable.draw();
        e.preventDefault();

        var name = $(this).attr('name');
        var value = $(this).is(':checked');
        value = value == false ? 0 : 1;

        newUrl = Url.updateParameter(Url.current(), name, value)
        Url.change(newUrl);

    });

    //export selected
    $(document).on('change', '.row-select',function(){
        var queryString = '';
        $('input[name=row-select]:checked').each(function(i, selected){
            queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
        });

        var exportUrl = Url.removeQueryString($('[data-toggle="export-selected"]').attr('href'));
        $('[data-toggle="export-selected"]').attr('href', exportUrl + '?' + queryString);
    });

    $("select[name=customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
    });

    $('.modal-filter-dates .btn-submit').on('click', function(e) {
        $(this).closest('form').submit();
        $('.modal-filter-dates').modal('hide')
    })

    $('#modal-print-mass-invoices button[type="submit"]').on('click', function(e) {
        e.preventDefault();
        var $btn  = $(this);
        var $form = $(this).closest('form');

        $btn.button('loading')

        $.post($form.attr('action'), $form.serialize(), function (data) {

            if(data.result) {
                html = '<tr><td>' +
                    '<a href="' + data.url + '" target="_blank">' + data.title + '</a>' +
                    '</td></tr>'

                $('.downloads-area').show()
                $('.downloads-area table tbody').append(html);
                Growl.success(data.feedback);
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function() {
            Growl.error500();
        }).always(function(){
            $btn.button('reset')
        })

        $('#modal-print-operator-balance').modal('hide')
    })

    $(document).on('click', '.btn-update-balance', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var $this = $(this);

        $this.button('loading');

        $.post(url, function(data){

            if(data.result) {
                oTable.draw(false);
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $this.button('reset');
        });
    })

    @if(Request::get('invoice'))
    $(document).ready(function(){
        var url = Url.current();
        url = Url.removeParameter(url, 'invoice');
        Url.change(url);

        $('.mopen').trigger('click');
    })
    @endif
</script>
@stop