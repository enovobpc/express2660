@section('title')
    Custos de Frota
@stop

@section('content-header')
    Custos de Frota
@stop

@section('breadcrumb')
    <li class="active">@trans('Gestão de Frota')</li>
    <li class="active">@trans('Custos de Frota')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-expenses" data-toggle="tab">@trans('Despesas Gerais')</a></li>
                    <li><a href="#tab-tolls" data-toggle="tab">@trans('Portagens')</a></li>
                    <li><a href="#tab-costs-fixed" data-toggle="tab">@trans('Custos Fixos')</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-expenses" data-empty="1">
                        @include('admin.fleet.expenses.partials.expenses')
                    </div>
                    <div class="tab-pane" id="tab-tolls" data-empty="1">
                        @include('admin.fleet.expenses.partials.tolls')
                    </div>
                    <div class="tab-pane" id="tab-costs-fixed"  data-empty="1">
                        @include('admin.fleet.expenses.partials.fixed_costs')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.fleet.tolls.modals.import')
@stop

@section('scripts')
    <script type="text/javascript">

        var oTableExpenses;
        var oTableTolls;
        var oTableFixedCosts;

        /**
         * Tab expenses
         */
        $(document).ready(function(){
            $('a[href="#tab-{{ Request::get('tab') ? Request::get('tab') : 'expenses' }}"]').trigger('click')
        })


        $(document).on('click', 'a[href="#tab-expenses"]', function(){
            $tab = $('#tab-expenses');

            console.log($tab.data('empty'));
            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);


                oTableExpenses = $('#datatable-expenses').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'date', name: 'date'},
                        {data: 'vehicle_id', name: 'vehicle_id'},
                        {data: 'title', name: 'title'},
                        {data: 'provider_id', name: 'provider_id'},
                        {data: 'operator_id', name: 'operator_id'},
                        {data: 'km', name: 'km'},
                        {data: 'total', name: 'total'},
                        {data: 'assigned_invoice_id', name: 'assigned_invoice_id'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    ajax: {
                        url: "{{ route('admin.fleet.expenses.datatable') }}",
                        type: "POST",
                        data: function (d) {
                            d.vehicle  = $('[data-target="#datatable-expenses"] select[name=expenses_vehicle]').val();
                            d.date_min = $('[data-target="#datatable-expenses"] input[name=expenses_date_min]').val();
                            d.date_max = $('[data-target="#datatable-expenses"] input[name=expenses_date_max]').val();
                            d.provider = $('[data-target="#datatable-expenses"] select[name=expenses_provider]').val();
                            d.operator = $('[data-target="#datatable-expenses"] select[name=expenses_operator]').val();
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableExpenses) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('[data-target="#datatable-expenses"] .filter-datatable').on('change', function (e) {
                    oTableExpenses.draw();
                    e.preventDefault();

                    var exportUrl = Url.removeQueryString($('[data-target="#datatable-expenses"] [data-toggle="export-url"]').attr('href'));
                    exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                    $('[data-target="#datatable-expenses"] [data-toggle="export-url"]').attr('href', exportUrl);
                });
            }
        })

        /**
         * Tab tolls
         */
        $(document).on('click', 'a[href="#tab-tolls"]', function(){
            $tab = $('#tab-tolls');

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableTolls = $('#datatable-tolls').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'vehicle_id', name: 'vehicle_id'},
                        {data: 'entry_date', name: 'entry_date'},
                        {data: 'provider_id', name: 'provider_id', orderable: false, searchable: false},
                        {data: 'toll_provider', name: 'toll_provider', orderable: false, searchable: false},
                        {data: 'count', name: 'count', class: 'text-center', orderable: false, searchable: false},
                        {data: 'total', name: 'total', orderable: false, searchable: false},
                        {data: 'class', name: 'class', class: 'text-center', orderable: false, searchable: false},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    order: [[3, "desc"]],
                    ajax: {
                        url: "{{ route('admin.fleet.tolls.datatable') }}",
                        type: "POST",
                        data: function (d) {
                            d.vehicle  = $('[data-target="#datatable-tolls"] select[name=tolls_vehicle]').val();
                            d.date_min = $('[data-target="#datatable-tolls"] input[name=tolls_date_min]').val();
                            d.date_max = $('[data-target="#datatable-tolls"] input[name=tolls_date_max]').val();
                            d.provider = $('[data-target="#datatable-tolls"] select[name=tolls_provider]').val();
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableTolls) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('[data-target="#datatable-tolls"] .filter-datatable').on('change', function (e) {
                    oTableTolls.draw();
                    e.preventDefault();

                    var exportUrl = Url.removeQueryString($('[data-target="#datatable-tolls"] [data-toggle="export-url"]').attr('href'));
                    exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                    $('[data-target="#datatable-tolls"] [data-toggle="export-url"]').attr('href', exportUrl);
                });
            }
        })

        /**
         * Tab Fixed Costs
         */
        $(document).on('click', 'a[href="#tab-costs-fixed"]', function(){
            $tab = $('#tab-costs-fixed');

            if($tab.data('empty') == '1') {
                $tab.data('empty', 0);

                oTableFixedCosts = $('#datatable-fixed-costs').DataTable({
                    columns: [
                        {data: 'select', name: 'select', orderable: false, searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'vehicle_id', name: 'vehicle_id'},
                        {data: 'description', name: 'description'},
                        {data: 'type', name: 'type'},
                        {data: 'start_date', name: 'start_date'},
                        {data: 'end_date', name: 'end_date'},
                        {data: 'total', name: 'total'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    order: [[4, "desc"]],
                    ajax: {
                        url: "{{ route('admin.fleet.fixed-costs.datatable') }}",
                        type: "POST",
                        data: function (d) {
                            d.vehicle  = $('[data-target="#datatable-fixed-costs"] select[name=vehicle]').val();
                            d.provider = $('[data-target="#datatable-fixed-costs"] select[name=provider]').val();
                        },
                        beforeSend: function () { Datatables.cancelDatatableRequest(oTableFixedCosts) },
                        complete: function () { Datatables.complete(); },
                        error: function () { Datatables.error(); }
                    }
                });

                $('[data-target="#datatable-fixed-costs"] .filter-datatable').on('change', function (e) {
                    oTableFixedCosts.draw();
                    e.preventDefault();

                    var exportUrl = Url.removeQueryString($('[data-target="#datatable-fixed-costs"] [data-toggle="export-url"]').attr('href'));
                    exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                    $('[data-target="#datatable-fixed-costs"] [data-toggle="export-url"]').attr('href', exportUrl);
                });
            }
        });

        //export selected
        $(document).on('change', '.row-select',function(){
            var $targetTable = $(this).closest('.dataTables_wrapper');

            var queryString = '';
            $($targetTable).find('input[name=row-select]:checked').each(function(i, selected){
                queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
            });

            var exportUrl = $targetTable.next().find('[data-toggle="export-selected"]').attr('href');
            if(typeof exportUrl != 'undefined') {
                exportUrl = Url.removeQueryString(exportUrl);
                $('[data-toggle="export-selected"]').attr('href', exportUrl + '?' + queryString);
            }
        });

        $('form.import-form').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type=submit]');
            $submitBtn.button('loading');

            var form = $(this)[0];
            var formData = new FormData(form);

            $('.import-inputs-area').hide();
            $('.import-results-area').html('<div class="text-center"><i class="fas fa-spin fa-circle-notch fs-30 m-b-5"></i><br/>A importar ficheiro. Aguarde...</div>');
            $.ajax({
                url: $form.attr('action'),
                data: formData,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function(data) {

                    if (data.totalErrors == 0) {
                        Growl.success(data.feedback)
                        $('#modal-import-tolls').modal('hide');
                        oTableTolls.draw();
                    } else {
                        if (data.totalErrors > 0) {
                            $('.import-results-area').html(data.html)
                        } else {
                            Growl.error(data.feedback);
                        }
                    }
                }

            }).fail(function () {
                $('.import-inputs-area').show();
                $('.import-results-area').html('');
                Growl.error500();
            }).always(function () {
                $submitBtn.button('reset');
            });
        });
    </script>
@stop