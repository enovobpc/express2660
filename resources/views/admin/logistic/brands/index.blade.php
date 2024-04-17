@section('title')
    Gerir Marcas e Categorias
@stop

@section('content-header')
    Gerir Marcas e Categorias
@stop

@section('breadcrumb')
    <li class="active">@trans('Gestão Logística')</li>
    <li class="active">@trans('Gerir Marcas e Categorias')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-brands" data-toggle="tab">@trans('Marcas')</a></li>
                <li><a href="#tab-models" data-toggle="tab">@trans('Modelos')</a></li>
                <li><a href="#tab-families" data-toggle="tab">@trans('Famílias')</a></li>
                <li><a href="#tab-categories" data-toggle="tab">@trans('Categorias')</a></li>
                <li><a href="#tab-subcategories" data-toggle="tab">@trans('Subcategorias')</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-brands">
                    @include('admin.logistic.brands.tabs.brands')
                </div>
                <div class="tab-pane" id="tab-models">
                    @include('admin.logistic.brands.tabs.models')
                </div>
                <div class="tab-pane" id="tab-families">
                    @include('admin.logistic.brands.tabs.families')
                </div>
                <div class="tab-pane" id="tab-categories">
                    @include('admin.logistic.brands.tabs.categories')
                </div>
                <div class="tab-pane" id="tab-subcategories">
                    @include('admin.logistic.brands.tabs.subcategories')
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTableBrands, oTableModels, oTableFamilies, oTableCategories, oTableSubcategories;
    $(document).ready(function () {

        oTableBrands = $('#datatable-brands').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'customer_id', name: 'customer_id'},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false}
            ],
            order: [[4, "desc"]],
            ajax: {
                url: "{{ route('admin.logistic.brands.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.customer    = $('[data-target="#datatable-brands"] select[name=dt_customer]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableBrands) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('[data-target="#datatable-brands"] .filter-datatable').on('change', function (e) {
            oTableBrands.draw();
            e.preventDefault();
        });

        oTableModels = $('#datatable-models').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'brand_id', name: 'brand_id'},
                {data: 'customer_id', name: 'customer_id'},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false}
            ],
            order: [[5, "desc"]],
            ajax: {
                url: "{{ route('admin.logistic.models.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.customer = $('[data-target="#datatable-models"] select[name=dt_customer]').val();
                    d.brand    = $('[data-target="#datatable-models"] select[name=brand]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableModels) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('[data-target="#datatable-models"] .filter-datatable').on('change', function (e) {
            oTableModels.draw();
            e.preventDefault();
        });


        oTableFamilies = $('#datatable-families').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'customer_id', name: 'customer_id'},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false}
            ],
            order: [[4, "desc"]],
            ajax: {
                url: "{{ route('admin.logistic.families.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.customer    = $('[data-target="#datatable-families"] select[name=dt_customer]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableFamilies) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('[data-target="#datatable-families"] .filter-datatable').on('change', function (e) {
            oTableFamilies.draw();
            e.preventDefault();
        });

        oTableCategories = $('#datatable-categories').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'family_id', name: 'family_id'},
                {data: 'customer_id', name: 'customer_id'},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false}
            ],
            order: [[5, "desc"]],
            ajax: {
                url: "{{ route('admin.logistic.categories.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.customer = $('[data-target="#datatable-categories"] select[name=dt_customer]').val();
                    d.families = $('[data-target="#datatable-categories"] select[name=family]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableCategories) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('[data-target="#datatable-categories"] .filter-datatable').on('change', function (e) {
            oTableCategories.draw();
            e.preventDefault();
        });

        oTableSubcategories = $('#datatable-subcategories').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'category_id', name: 'category_id'},
                {data: 'customer_id', name: 'customer_id'},
                {data: 'sort', name: 'sort', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false}
            ],
            order: [[5, "desc"]],
            ajax: {
                url: "{{ route('admin.logistic.subcategories.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.customer = $('[data-target="#datatable-subcategories"] select[name=dt_customer]').val();
                    d.category = $('[data-target="#datatable-subcategories"] select[name=category]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableSubcategories) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('[data-target="#datatable-subcategories"] .filter-datatable').on('change', function (e) {
            oTableSubcategories.draw();
            e.preventDefault();
        });

    });

    $("select[name=dt_customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.logistic.products.search.customer') }}")
    });
</script>
@stop