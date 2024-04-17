@section('title')
    Documentação API
@stop

@section('content-header')
    Documentação API
@stop

@section('breadcrumb')
<li class="active">Documentação API</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs tabs-filter">
                    <li class="active"><a href="#tab-methods" data-toggle="tab">Métodos API</a></li>
                    <li><a href="#tab-sections" data-toggle="tab">Secções API</a></li>
                    <li><a href="#tab-categories" data-toggle="tab">Categorias API</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-methods">
                        @include('admin.api.docs.tabs.methods')
                    </div>
                    <div class="tab-pane" id="tab-sections">
                        @include('admin.api.docs.tabs.sections')
                    </div>
                    <div class="tab-pane" id="tab-categories">
                        @include('admin.api.docs.tabs.categories')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
    var oTableMethods, oTableCategories, oTableSections;

    $(document).ready(function () {

        oTableMethods = $('#datatable-methods').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'api_version', name: 'api_version'},
                {data: 'category_id', name: 'category_id'},
                {data: 'section_id', name: 'section_id'},
                {data: 'name', name: 'name'},
                {data: 'url', name: 'url'},
                {data: 'levels', name: 'levels', orderable: false},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
            ],
            order: [[7, "desc"]],
            ajax: {
                url: "{{ route('admin.api.docs.methods.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.level    = $('select[name="level"]').val();
                    d.version  = $('select[name="version"]').val();
                    d.category = $('select[name="category"]').val();
                    d.section  = $('select[name="section"]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableMethods) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-methods .filter-datatable').on('change', function (e) {
            oTableMethods.draw();
            e.preventDefault();
        });


        /**
         * Payment Conditions
         * @type {*|jQuery}
         */
        oTableSections = $('#datatable-sections').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'api_version', name: 'api_version'},
                {data: 'category_id', name: 'category_id'},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
            ],
            order: [[5, "asc"]],
            ajax: {
                url: "{{ route('admin.api.docs.sections.datatable') }}",
                type: "POST",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableSections) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-payment-conditions .filter-datatable').on('change', function (e) {
            oTableSections.draw();
            e.preventDefault();
        });

        /**
         * Payment Methods
         * @type {*|jQuery}
         */
        oTableCategories = $('#datatable-categories').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'api_version', name: 'api_version'},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                {data: 'module', name: 'module'},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
            ],
            order: [[5, "desc"]],
            ajax: {
                url: "{{ route('admin.api.docs.categories.datatable') }}",
                type: "POST",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableCategories) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-payment-methods .filter-datatable').on('change', function (e) {
            oTableCategories.draw();
            e.preventDefault();
        });
    });

</script>
@stop