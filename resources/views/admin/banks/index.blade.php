@section('title')
    Bancos e Pagamentos
@stop

@section('content-header')
    Bancos e Pagamentos
@stop

@section('breadcrumb')
<li class="active">Bancos e Pagamentos</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs tabs-filter">
                    <li class="active"><a href="#tab-banks" data-toggle="tab">Bancos</a></li>
                    @if(hasPermission('payment_conditions'))
                    <li><a href="#tab-payment-conditions" data-toggle="tab">Condições Pagamento</a></li>
                    @endif
                    @if(hasPermission('payment_methods'))
                    <li><a href="#tab-payment-methods" data-toggle="tab">Métodos Pagamento</a></li>
                    @endif
                    <li><a href="#tab-banks-institutions" data-toggle="tab">Instituições Bancárias</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-banks">
                        @include('admin.banks.tabs.banks')
                    </div>
                    @if(hasPermission('payment_conditions'))
                    <div class="tab-pane" id="tab-payment-conditions">
                        @include('admin.banks.tabs.payment_conditions')
                    </div>
                    @endif
                    @if(hasPermission('payment_methods'))
                    <div class="tab-pane" id="tab-payment-methods">
                        @include('admin.banks.tabs.payment_methods')
                    </div>
                    @endif
                    <div class="tab-pane" id="tab-banks-institutions">
                        @include('admin.banks.tabs.banks_institutions')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTableBanks, oTablePaymentMethods, oTablePaymentConditions, oTableBanksInstitutions;

    $(document).ready(function () {

        oTableBanks = $('#datatable-banks').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'company_id', name: 'company_id'},
                {data: 'name', name: 'name'},
                {data: 'titular_name', name: 'titular_name'},
                {data: 'bank_name', name: 'bank_name'},
                {data: 'bank_iban', name: 'bank_iban'},
                {data: 'credor_code', name: 'credor_code'},
                {data: 'active', name: 'active', orderable: false, searchable: false},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
            ],
            order: [[7, "desc"]],
            ajax: {
                url: "{{ route('admin.banks.datatable') }}",
                type: "POST",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableBanks) },
                complete: function () { Datatables.complete(); }
            }
        });

        $('#tab-banks .filter-datatable').on('change', function (e) {
            oTableBanks.draw();
            e.preventDefault();
        });


        /**
         * Payment Conditions
         * @type {*|jQuery}
         */
        oTablePaymentConditions = $('#datatable-payment-condition').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'days', name: 'days', class:'text-center'},
                {data: 'sales_visible', name: 'sales_visible', class:'text-center'},
                {data: 'purchases_visible', name: 'purchases_visible', class:'text-center'},
                {data: 'active', name: 'active', orderable: false, searchable: false, class:'text-center'},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
            ],
            order: [[6, "asc"]],
            ajax: {
                url: "{{ route('admin.payment-conditions.datatable') }}",
                type: "POST",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTablePaymentConditions) },
                complete: function () { Datatables.complete(); }
            }
        });

        $('#tab-payment-conditions .filter-datatable').on('change', function (e) {
            oTablePaymentConditions.draw();
            e.preventDefault();
        });

        /**
         * Payment Methods
         * @type {*|jQuery}
         */
        oTablePaymentMethods = $('#datatable-payment-method').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'active', name: 'active', orderable: false, searchable: false, class:'text-center'},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
            ],
            order: [[4, "desc"]],
            ajax: {
                url: "{{ route('admin.payment-methods.datatable') }}",
                type: "POST",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTablePaymentMethods) },
                complete: function () { Datatables.complete(); }
            }
        });

        $('#tab-payment-methods .filter-datatable').on('change', function (e) {
            oTablePaymentMethods.draw();
            e.preventDefault();
        });

         /**
         * Banks Instituitions
         * @type {*|jQuery}
         */
         oTableBanksInstitutions = $('#datatable-banks-institutions').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id'},
                {data: 'bank_code', name: 'bank_code'},
                {data: 'bank_name', name: 'bank_name'},
                {data: 'bank_swift', name: 'bank_swift'},
                {data: 'created_at', name: 'created_at'},
                {data: 'active', name: 'active', orderable: false, searchable: false, class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'country', name: 'country', visible: false},
                {data: 'code', name: 'code', visible: false},
            ],
            order: [[1, "desc"]],
            ajax: {
                url: "{{ route('admin.banks-institutions.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.country = $('#tab-banks-institutions [name="institution_country"]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableBanksInstitutions) },
                complete: function () { Datatables.complete(); }
            }
        });

        $('#tab-banks-institutions .filter-datatable').on('change', function (e) {
            oTableBanksInstitutions.draw();
            e.preventDefault();
        });
    });

</script>
@stop