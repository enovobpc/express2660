@section('title')
Marcas e Modelos de Viaturas
@stop

@section('content-header')
    Marcas e Modelos de Viaturas
@stop

@section('breadcrumb')
<li class="active">@trans('Gestão de Frota')</li>
<li class="active">@trans('Marcas e Modelos de Viaturas')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-brands" data-toggle="tab">@trans('Marcas')</a></li>
                <li><a href="#tab-models" data-toggle="tab">@trans('Modelos')</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-brands">
                    @include('admin.fleet.brands.partials.brands')
                </div>
                <div class="tab-pane" id="tab-models">
                    @include('admin.fleet.brands.partials.models')
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {

        var oTable = $('#datatable-brands').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'photo', name: 'photo', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.fleet.brands.datatable') }}",
                type: "POST",
                data: function (d) {},
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });


        var oTable2 = $('#datatable-models').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'brand_id', name: 'brand_id', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.fleet.brand-models.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.brand = $('[name="brand"]').val();
                },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable2.draw();
            e.preventDefault();
        });
    });

</script>
@stop