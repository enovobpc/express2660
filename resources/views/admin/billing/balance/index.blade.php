@section('title')
    Contas Corrente
@stop

@section('content-header')
    Contas Corrente
@stop

@section('breadcrumb')
    <li class="active">Contas Corrente</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-customers" data-toggle="tab">Clientes</a></li>
                    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'purchase_invoices') || !hasModule('purchase_invoices'))
                        <li><a href="#tab-providers" data-toggle="tab">Fornecedores</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-customers">
                        @include('admin.billing.balance.tabs.customers')
                    </div>
                    <div class="tab-pane" id="tab-providers">
                        @if(hasModule('purchase_invoices') && Auth::user()->ability(Config::get('permissions.role.admin'), 'purchase_invoices'))
                            @include('admin.billing.balance.tabs.providers')
                        @else
                            @include('admin.providers.partials.denied_message')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('admin.billing.balance.modals.import_divergences')
{{--@include('admin.customers.balance.modals.update_balance_status')--}}
    <style>
        .brdlft {
            border-left: 2px solid #333 !important;
        }
    </style>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable, oTableProviders;
    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'vat', name: 'vat'},
                {data: 'payment_method', name: 'payment_method'},
                @if(Auth::user()->isAdmin())
                {data: 'balance_divergence', name: 'balance_divergence', class: 'text-center', searchable: false},
                @endif
                {data: 'balance_expired_count', name: 'balance_expired_count', class: 'text-right brdlft', searchable: false},
                {data: 'balance_total_debit', name: 'balance_total_debitit', 'class': 'text-right', searchable: false},
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
            @if(Auth::user()->isAdmin())
            order: [[10, "desc"]],
            @else
            order: [[9, "desc"]],
            @endif
            ajax: {
                url: "{{ route('admin.billing.balance.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.agency            = $('#tab-customers select[name="agency"]').val(),
                    d.unpaid            = $('#tab-customers select[name="unpaid"]').val(),
                    d.expired           = $('#tab-customers select[name="expired"]').val(),
                    d.seller            = $('#tab-customers select[name="seller"]').val(),
                    d.divergence        = $('#tab-customers select[name="divergence"]').val(),
                    d.payment_condition = $('#tab-customers select[name="payment_condition"]').val(),
                    d.last_shipment     = $('#tab-customers select[name="last_shipment"]').val(),
                    d.last_shipment     = $('#tab-customers select[name="last_shipment"]').val(),
                    d.type              = $('#tab-customers select[name="type"]').val(),
                    d.particular        = $('#tab-customers select[name="particular"]').val(),
                    d.route             = $('#tab-customers select[name="route"]').val(),
                    d.billing_country   = $('#tab-customers select[name="country_billing"]').val(),
                    d.country           = $('#tab-customers select[name="country"]').val(),
                    d.district          = $('#tab-customers select[name="district"]').val(),
                    d.county            = $('#tab-customers select[name="county"]').val(),
                    d.prices            = $('#tab-customers select[name="prices"]').val()
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-customers .filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('#tab-customers [data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('#tab-customers [data-toggle="export-url"]').attr('href', exportUrl);
        });

        /**
         * Providers
         */
        oTableProviders = $('#datatable-providers').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'code', name: 'code'},
                {data: 'company', name: 'company'},
                {data: 'vat', name: 'vat'},
                {data: 'payment_method', name: 'payment_method'},
               /* {data: 'balance_count_expired', name: 'balance_count_expired', searchable: false},*/
                {data: 'balance_count_expired', name: 'balance_count_expired', class: 'text-right brdlft', searchable: false},
                {data: 'debit', name: 'debit', class: 'text-right', searchable: false},
                {data: 'credit', name: 'credit', class: 'text-right', searchable: false},
                {data: 'balance_total_unpaid', name: 'balance_total_unpaid', class: 'text-right', searchable: false},
                {data: 'last_invoice', name: 'last_invoice', class: 'text-right', searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'city', name: 'city', visible: false},
                {data: 'zip_code', name: 'zip_code', visible: false},
                {data: 'vat', name: 'vat', visible: false},
                {data: 'name', name: 'name', visible: false},
            ],
            order: [[9, "desc"]],
            ajax: {
                url: "{{ route('admin.billing.balance.datatable', ['source' => 'providers']) }}",
                type: "POST",
                data: function (d) {
                    d.category       = $('#tab-providers select[name="category"]').val(),
                    d.unpaid         = $('#tab-providers select[name="unpaid"]').val(),
                    d.expired        = $('#tab-providers select[name="expired"]').val(),
                    d.payment_method = $('#tab-providers select[name="payment_method"]').val()
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableProviders) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-providers .filter-datatable').on('change', function (e) {
            oTableProviders.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('#tab-providers [data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('#tab-providers [data-toggle="export-url"]').attr('href', exportUrl);
        });
    });

    /**
     * Update billing payment status
     */
    $('#modal-update-balance-status form').on('submit', function(e){
        e.preventDefault()

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');

        $('#modal-update-balance-status .loading-status').show();
        $('#modal-update-balance-status .loading-status').prev().hide();
        $.post($form.attr('action'), function(data){

            if(data.result) {
                Growl.success(data.feedback);
                oTableBalance.draw();
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error('Ocorreu um erro ao tentar obter os dados do programa de faturação.');
        }).always(function () {
            $('#modal-update-balance-status .loading-status').hide();
            $('#modal-update-balance-status .loading-status').prev().show();
            $('#modal-update-balance-status').modal('hide');
            $submitBtn.button('reset');
        });
    });


    /**
     * Sync balance
     */
    $('#modal-sync-balance form').on('submit', function(e){
        e.preventDefault()

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');

        $('#modal-sync-balance .loading-status').show();
        $('#modal-sync-balance .loading-status').prev().hide();
        $.post($form.attr('action'), function(data){

            if(data.result) {
                Growl.success(data.feedback);
                $('.balance-total-expired').html(data.totalExpired);
                $('.balance-total-unpaid').html(data.totalUnpaid);
                oTableBalance.draw();
            } else {
                Growl.error(data.feedback);
            }
        }).fail(function () {
            Growl.error('Ocorreu um erro ao tentar obter os dados do programa de faturação.');
        }).always(function () {
            $('#modal-sync-balance .loading-status').hide();
            $('#modal-sync-balance .loading-status').prev().show();
            $('#modal-sync-balance').modal('hide');
            $submitBtn.button('reset');
        });
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

</script>
@stop
