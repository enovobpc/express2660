@section('title')
    {{ trans('account/billing.title') }} -
@stop

@section('account-content')
    <style>
        .td-b-left{
            border-left: 1px solid #ddd;
        }
    </style>
    @if(hasModule('invoices'))
    <div class="tabbable-line">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab-invoices" data-toggle="tab">
                    <i class="fa fa-file-text"></i> {{ trans('account/billing.tabs.invoices') }}
                </a>
            </li>
            <li>
                <a href="#tab-extracts" data-toggle="tab">
                    <i class="fa fa-file-text"></i> {{ trans('account/billing.tabs.extracts') }}
                </a>
            </li>
        </ul>
        <div class="tab-content" style="padding-bottom: 0">
            <div class="tab-pane active" id="tab-invoices">
                @if($auth->show_billing)
                @include('account.billing.partials.invoices')
                @else
                    <div class="text-center text-muted" style="margin: 188px;">
                        <i class="fas fa-info-circle fs-30"></i>
                        <h4 class="text-muted">Não possui permissão para consultar faturas</h4>
                    </div>
                @endif
            </div>
            <div class="tab-pane" id="tab-extracts">
                @if($auth->show_billing)
                    @include('account.billing.partials.extracts')
                @else
                    <div class="text-center text-muted" style="margin: 188px;">
                        <i class="fas fa-info-circle fs-30"></i>
                        <h4 class="text-muted">Não possui permissão para consultar extratos mensais</h4>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @else
        @include('account.billing.partials.extracts')
    @endif
@stop

@section('scripts')
<script type="text/javascript">
    var oTableInvoices, oTableExtracts, oTable;
    $(document).ready(function () {

        oTableExtracts = $('#datatable-extracts').DataTable({
            columns: [
                {data: 'yearmonth', name: 'yearmonth', searchable: false},
                {data: 'count_shipments', name: 'count_shipments', class:'text-center', searchable: false, orderable: false},
                {data: 'shipments', name: 'shipments', class:'text-right td-b-left', searchable: false, orderable: false},
                {data: 'covenants', name: 'covenants', class:'text-right', searchable: false, orderable: false},
                {data: 'others', name: 'others', class:'text-right', searchable: false, orderable: false},
                {data: 'weight_avg', name: 'weight_avg', class:'text-right td-b-left', searchable: false, orderable: false},
                {data: 'price_avg', name: 'price_avg', class:'text-right', searchable: false, orderable: false},
                {data: 'total', name: 'total', class:'text-right bold td-b-left', searchable: false, orderable: false},
                {data: 'download', name: 'download', class: 'td-b-left', orderable: false, searchable: false},
            ],
            order: [[0, "desc"]],
            ajax: {
                url: "{{ route('account.billing.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.year  = $('select[name=year]').val();
                    d.month = $('select[name=month]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('[data-target="#datatable-extracts"] .filter-datatable').on('change', function (e) {
            oTableExtracts.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);
        });


        @if(hasModule('invoices'))
        oTableInvoices = $('#datatable-invoices').DataTable({
            columns: [
                {data: 'id', name: 'id', visible: false},
                /*{data: 'select', name: 'select', orderable: false, searchable: false},*/
                {data: 'sort', name: 'sort'},
                {data: 'doc_serie', name: 'doc_serie'},
                {data: 'doc_type', name: 'doc_type'},
                {data: 'reference', name: 'reference'},
                {data: 'doc_total', name: 'doc_total'},
                {data: 'due_date', name: 'due_date'},
                {data: 'is_settle', name: 'is_settle', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'doc_date', name: 'doc_date', visible: false},
            ],
            order: [[1, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.billing.invoices.datatable') }}",
                data: function (d) {
                    d.date_min  = $('[name="date_min"]').val(),
                    d.date_max  = $('[name="date_max"]').val(),
                    d.sense     = $('select[name="sense"]').val(),
                    d.paid      = $('select[name="paid"]').val()
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });

        $('[data-target="#datatable-invoices"] .filter-datatable').on('change', function (e) {
            oTableInvoices.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);
        });
        @endif
    });

    @if(hasModule('invoices'))
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
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
                $('.balance-total-expired').html(data.totalExpired);
                $('.balance-total-unpaid').html(data.totalUnpaid);
                oTableInvoices.draw();
            } else {
                $.bootstrapGrowl(data.feedback, {type: 'error', align: 'center', width: 'auto', delay: 8000});
            }
        }).fail(function () {
            $.bootstrapGrowl("<i class='fas fa-exclamation-circle'></i> Ocorreu um erro ao tentar obter os dados do programa de faturação.",
                {type: 'error', align: 'center', width: 'auto', delay: 8000});
        }).always(function () {
            $('#modal-sync-balance .loading-status').hide();
            $('#modal-sync-balance .loading-status').prev().show();
            $('#modal-sync-balance').modal('hide');
            $submitBtn.button('reset');
        });
    })
    @endif
</script>
@stop