@section('title')
    Documentos de Compra
@stop

@section('content-header')
    Documentos de Compra
@stop

@section('breadcrumb')
    <li class="active">Faturação</li>
    <li class="active">Documentos de Compra</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs tabs-filter">
                    <li><a href="#tab-balance" data-toggle="tab">Pendentes</a></li>
                    {{-- <li><a href="#tab-unpaid" data-paid="0" data-toggle="tab">Por Pagar</a></li>
                    <li><a href="#tab-paid" data-paid="1" data-toggle="tab">Pagos</a></li> --}}
                    <li><a href="#tab-invoices" data-type="provider-invoice" data-toggle="tab">Faturas</a></li>
                    <li><a href="#tab-invoices-receipt" data-type="provider-invoice-receipt"
                            data-toggle="tab">Faturas-Recibo</a></li>
                    <li><a href="#tab-credit-notes" data-type="provider-credit-note" data-toggle="tab">Notas Crédito</a></li>
                    <li><a href="#tab-orders" data-type="provider-order" data-toggle="tab">Encomendas</a></li>
                    <li><a href="#tab-payment-notes" data-toggle="tab">Notas Pagamento</a></li>
                    <li class="active"><a href="#tab-all" data-type="all" data-toggle="tab">Todos</a></li>
                    {{-- <li><a href="#tab-scheduled" data-toggle="tab">Faturas Fixas e Agendadas</a></li> --}}
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tab-balance">
                        @include('admin.invoices.purchases.tabs.balance')
                    </div>
                    <div class="tab-pane active" id="tab-all">
                        @include('admin.invoices.purchases.tabs.list')
                    </div>
                    <div class="tab-pane" id="tab-payment-notes">
                        @include('admin.invoices.purchases.tabs.payment_note')
                    </div>
                    {{-- <div class="tab-pane" id="tab-not-linked">
                        @include('admin.invoices.purchases.tabs.scheduled')
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    @include('admin.invoices.purchases.modals.map_summary_vehicle')
    @include('admin.invoices.purchases.modals.export_yearly_grouped_type')
    @include('admin.invoices.purchases.modals.map_providers_purchase')
    <style>
        .brdlft {
            border-left: 2px solid #333 !important;
        }

    </style>
@stop

@section('scripts')
    <script>
        var oTable, oTablePaymentNotes, oTableProviders;

        $(document).ready(function() {
            oTable = $('#datatable').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id',name: 'id', visible: false},
                    {data: 'doc_date',name: 'doc_date',class: 'text-center'},
                    {data: 'code',name: 'code'},
                    {data: 'reference',name: 'reference'},
                    {data: 'provider_id',name: 'provider_id'},
                    {data: 'description',name: 'description'},
                    {data: 'total',name: 'total',lass: 'text-right'},
                    /* {data: 'vat_total', name: 'vat_total', class:'text-center'},*/
                    {data: 'total_unpaid',name: 'total_unpaid',class: 'text-right'},
                    {data: 'due_date',name: 'due_date',class: 'text-center'},
                    {data: 'payment_date',name: 'payment_date',class: 'text-center',searchable: false},
                    {data: 'assigned_targets',name: 'assigned_targets',class: 'text-center',searchable: false},
                    {data: 'ignore_stats',name: 'ignore_stats',class: 'text-center',searchable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'actions',name: 'actions',class: 'text-center',orderable: false, searchable: false},
                    {data: 'billing_name',name: 'billing_name',visible: false},
                    {data: 'obs',name: 'obs',visible: false},
                    {data: 'vat',name: 'vat',visible: false},
                    {data: 'doc_type',name: 'doc_type', visible: false}
                ],
                order: [
                    [2, "desc"]
                ],
                ajax: {
                    url: "{{ route('admin.invoices.purchase.datatable') }}",
                    type: "POST",
                    data: function(d) {
                        d.paid         = $('select[name=paid]').val();
                        d.agency       = $('select[name=agency]').val();
                        d.expired      = $('select[name=expired]').val();
                        d.type         = $('select[name=type]').val();
                        d.ignore_stats = $('select[name=ignore_stats]').val();
                        d.operator     = $('select[name=operator]').val();
                        d.doc_type     = $('select[name=doc_type]').val();
                        d.doc_id       = $('input[name=doc_id]').val();
                        d.provider     = $('select[name=provider]').val();
                        d.date_unity = $('select[name=date_unity]').val();
                        d.date_min  = $('input[name=date_min]').val();
                        d.date_max  = $('input[name=date_max]').val();
                        d.payment_condition = $('select[name=payment_condition]').val();
                        d.target    = $('select[name=target]').val();
                        d.target_id = $('select[name=target_id]').val();
                        d.deleted   = $('input[name=deleted]:checked').length;
                    },
                    beforeSend: function() { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    //error: function () { Datatables.error(); }
                }
            });

            $('[id="tab-all"] .filter-datatable').on('change', function(e) {
                oTable.draw();
                e.preventDefault();

                var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('[data-toggle="export-url"]').attr('href', exportUrl);

                $('[data-toggle="print-url"]').each(function() {
                    href = $(this).attr('href');
                    printUrl = Url.removeQueryString(href);
                    printUrl = printUrl + '?' + Url.getQueryString(Url.current())
                    $(this).attr('href', printUrl);
                })

            });

            /**
             * Scheduled
             * @type {*|jQuery}
             */
            oTablePaymentNotes = $('#datatable-payment-notes').DataTable({
                columns: [{data: 'select',name: 'select',orderable: false,searchable: false},
                    {data: 'id',name: 'id',class: 'text-center', visible: false},
                    {data: 'doc_date',name: 'doc_date'},
                    {data: 'code', name: 'code'},
                    {data: 'reference', name: 'reference'},
                    {data: 'provider_id', name: 'provider_id'},
                    {data: 'total', name: 'total'},
                    {data: 'count_invoices', name: 'count_invoices', class: 'text-center', orderable: false, searchable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'actions', name: 'actions', class: 'text-center', orderable: false, searchable: false},
                    {data: 'vat', name: 'vat', visible: false},
                    {data: 'billing_name', name: 'billing_name', visible: false},
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.invoices.purchase.payment-notes.datatable') }}",
                    type: "POST",
                    data: function(d) {
                        d.agency = $('select[name=agency]').val();
                        d.provider = $('#tab-payment-notes select[name=provider]').val();
                        d.date_min = $('#tab-payment-notes input[name=date_min]').val();
                        d.date_max = $('#tab-payment-notes input[name=date_max]').val();
                        d.doc_id   = $('#tab-payment-notes select[name=doc_id]').val();
                        d.has_ref  = $('#tab-payment-notes select[name=has_ref]').val();
                        d.deleted  = $('#tab-payment-notes input[name=payment_deleted]:checked').length;
                    },
                    beforeSend: function() { Datatables.cancelDatatableRequest(oTablePaymentNotes) },
                    complete: function () { Datatables.complete(); },
                    //error: function () { Datatables.error(); }
                }
            });

            $('[id="tab-payment-notes"] .filter-datatable').on('change', function(e) {
                oTablePaymentNotes.draw();
                e.preventDefault();
                
                var exportUrl = Url.removeQueryString($('[data-toggle="export-url-payment-notes"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('[data-toggle="export-url-payment-notes"]').attr('href', exportUrl);
                
                $('[data-toggle="print-url-payment-notes"]').each(function() {
                    href = $(this).attr('href');
                    printUrl = Url.removeQueryString(href);
                    printUrl = printUrl + '?' + Url.getQueryString(Url.current())
                    $(this).attr('href', printUrl);
                })
            });

            /**
             * Providers
             */
            oTableProviders = $('#datatable-providers').DataTable({
                columns: [{data: 'select', name: 'select',orderable: false,searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'code', name: 'code'},
                    {data: 'company', name: 'company'},
                    {data: 'vat', name: 'vat'},
                    {data: 'payment_method', name: 'payment_method'},
                    {data: 'balance_count_expired', name: 'balance_count_expired', class: 'text-right brdlft', searchable: false},
                    {data: 'debit', name: 'debit', class: 'text-right', searchable: false},
                    {data: 'credit', name: 'credit', class: 'text-right', searchable: false},
                    {data: 'balance_total_unpaid', class: 'text-right', name: 'balance_total_unpaid', searchable: false},
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
                    data: function(d) {
                        d.category = $('#tab-balance select[name="category"]').val(),
                        d.unpaid = $('#tab-balance select[name="unpaid"]').val(),
                        d.expired = $('#tab-balance select[name="expired"]').val(),
                        d.payment_method = $('#tab-balance select[name="payment_condition"]').val()
                    },
                    beforeSend: function() { Datatables.cancelDatatableRequest(oTableProviders) },
                    complete: function () { Datatables.complete(); },
                    //error: function () { Datatables.error(); }
                }
            });

            $('#tab-balance .filter-datatable').on('change', function(e) {
                oTableProviders.draw();
                e.preventDefault();

                var exportUrl = Url.removeQueryString($('#tab-balance [data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('#tab-balance [data-toggle="export-url"]').attr('href', exportUrl);
            });
        });

        //show concluded shipments
        $(document).on('change', '[name="deleted"], [name="payment_deleted"]', function(e) {
            e.preventDefault();

            var name = $(this).attr('name');
            var value = $(this).is(':checked');
            value = value == false ? 0 : 1;

            newUrl = Url.updateParameter(Url.current(), name, value)
            Url.change(newUrl);

        });

        //show deleted shipments
        $(document).on('change', '[name="deleted"]', function(e) {
            oTable.draw();
        });

        $(document).on('change', '[name="payment_deleted"]', function(e) {
            oTablePaymentNotes.draw();
        });


        //export selected
        $(document).on('change', '.row-select', function() {
            var queryString = '';
            $('input[name=row-select]:checked').each(function(i, selected) {
                queryString += (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
            });

            var exportUrl = Url.removeQueryString($('[data-toggle="export-selected"]').attr('href'));
            $('[data-toggle="export-selected"]').attr('href', exportUrl + '?' + queryString);

            //notas de pagamento
            var exportUrl = Url.removeQueryString($('#tab-payment-notes [data-toggle="export-selected-payments"]').attr('href'));
            $('#tab-payment-notes [data-toggle="export-selected-payments"]').attr('href', exportUrl + '?' + queryString);
        });

        $("select[name=provider]").select2({
            minimumInputLength: 2,
            allowClear: true,
            ajax: Init.select2Ajax("{{ route('admin.invoices.purchase.search.providers.select2') }}")
        });


        $('.modal-filter-dates button[type="submit"]').on('click', function() {
            $(this).prop('disabled', false).removeClass('disabled').html('Imprimir')
            $('.modal-filter-dates').modal('hide')
        })

        $('.tabs-filter a').on('click', function(e) {
            e.preventDefault();
            var type = $(this).data('type');

            $('#tab-all').addClass('active');
            $('#tab-scheduled').removeClass('active');

            if (type == 'all') {
                $('.doc-type-filter').show();
                $('#tab-all [name="doc_type"]').val('').trigger('change');
            } else if (typeof type !== 'undefined') {
                $('.doc-type-filter').hide();
                $('#tab-all [name="doc_type"]').val(type).trigger('change');
                $('.tab-pane').removeClass('active')
                $('#tab-all').addClass('active')
            }
        })

        @if (Request::get('invoice'))
            $(document).ready(function(){
            var url = Url.current();
            url = Url.removeParameter(url, 'invoice');
            Url.change(url);

            $('.mopen').trigger('click');
            })
        @endif

        $('#modal-export-yearly-grouped-type .btn-submit').on('click', function(e) {
            $(this).closest('form').submit();
            $('#modal-export-yearly-grouped-type').modal('hide');
        })
    </script>
@stop
