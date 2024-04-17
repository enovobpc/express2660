@section('title')
    Empresas e Centros Logísticos
@stop

@section('content-header')
    Empresas e Centros Logísticos
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Empresas e Armazéns</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs tabs-filter">
                    <li class="active"><a href="#tab-agencies" data-toggle="tab">Centros Logísticos</a></li>
                    @if(hasPermission('companies'))
                        <li><a href="#tab-companies" data-toggle="tab">Empresas</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-agencies">
                        @include('admin.agencies.agencies.tabs.agencies')
                    </div>
                    @if(hasPermission('companies'))
                        <div class="tab-pane" id="tab-companies">
                            @include('admin.agencies.agencies.tabs.companies')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable, oTableCompanies;
    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                /*{data: 'photo', name: 'photo', orderable: false, searchable: false, class:'vertical-algin-middle'},*/
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'company', name: 'company'},
                {data: 'phone', name: 'phone'},
                @if(Auth::user()->hasRole([config('permissions.role.admin')]))
                {data: 'agencies', name: 'agencies'},
                @endif
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            stateSave: false,
            order: [[3, "asc"]],
            ajax: {
                url: "{{ route('admin.agencies.datatable') }}",
                type: "POST",
                data: function (d) {},
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-agencies .filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });


        oTableCompanies = $('#datatable-companies').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'photo', name: 'photo', orderable: false, searchable: false, class:'vertical-algin-middle'},
                {data: 'vat', name: 'vat'},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'charter', name: 'charter'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            stateSave: false,
            order: [[3, "desc"]],
            ajax: {
                url: "{{ route('admin.companies.datatable') }}",
                type: "POST",
                data: function (d) {},
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-companies .filter-datatable').on('change', function (e) {
            oTableCompanies.draw();
            e.preventDefault();
        });
    });

</script>
@stop