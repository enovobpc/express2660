@section('title')
    Taxas Adicionais
@stop

@section('content-header')
    Taxas Adicionais
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Taxas Adicionais</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-others" data-toggle="tab">Taxas Gerais</a></li>
                    <li><a href="#tab-fuel" data-toggle="tab"><i class="fas fa-gas-pump"></i> Taxas Combustível</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-others">
                        @include('admin.expenses.tabs.others')
                    </div>
                    <div class="tab-pane" id="tab-fuel">
                        @include('admin.expenses.tabs.fuel')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        var oTable, oTableFuel;

        $(document).ready(function () {
            oTable = $('#datatable').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'code', name: 'code', class: 'text-center'},
                    {data: 'name', name: 'name'},
                    {data: 'type', name: 'type', orderable: false, searchable: false},
                    {data: 'zones', name: 'zones', orderable: false, searchable: false},
                    /*{data: 'min_price', name: 'min_price', orderable: false, searchable: false},*/
                    /*{data: 'services', name: 'services', orderable: false, searchable: false},*/
                    {data: 'triggers', name: 'triggers', orderable: false, searchable: false},
                    {data: 'settings', name: 'settings', orderable: false, searchable: false},
                    {data: 'sort', name: 'sort', class: 'text-center'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[8, "asc"]],
                ajax: {
                    url: "{{ route('admin.expenses.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.type = $('select[name=type]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('#tab-others .filter-datatable').on('change', function (e) {
                oTable.draw();
                e.preventDefault();
            });



            oTableFuel = $('#datatable-fuel').DataTable({
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'id', name: 'id', visible: false},
                    {data: 'code', name: 'code', class: 'text-center'},
                    {data: 'name', name: 'name'},
                    {data: 'zones', name: 'zones', orderable: false, searchable: false},
                    {data: 'start_at', name: 'start_at'},
                    {data: 'end_at', name: 'end_at'},
                    {data: 'status', name: 'status', class: 'text-center', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[5, "desc"]],
                ajax: {
                    url: "{{ route('admin.expenses.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.type = 'fuel';
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTableFuel) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('#tab-fuel .filter-datatable').on('change', function (e) {
                oTableFuel.draw();
                e.preventDefault();
            });
        });
    </script>
@stop