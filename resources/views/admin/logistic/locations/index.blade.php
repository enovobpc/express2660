@section('title')
    Armazéns e Localizações
@stop

@section('content-header')
    Armazéns e Localizações
@stop

@section('breadcrumb')
<li class="active">@trans('Gestão Logística')</li>
<li class="active">@trans('Armazéns e Localizações')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-locations" data-toggle="tab">@trans('Zonas e Localizações')</a></li>
                @if(Auth::user()->perm('logistic_warehouses'))
                <li><a href="#tab-warehouses" data-toggle="tab">@trans('Armazéns')</a></li>
                @endif
            </ul>
            <div class="tab-content">
                <div class="box-body tab-pane active" id="tab-locations">
                    @include('admin.logistic.locations.partials.locations')
                </div>
                @if(Auth::user()->perm('logistic_warehouses'))
                <div class="box-body tab-pane" id="tab-warehouses">
                    @include('admin.logistic.locations.partials.warehouses')
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable, oTableWarehouses;
    $(document).ready(function () {
        Init.imagePreview();
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'warehouse.name', name: 'warehouse.name'},
                {data: 'code', name: 'code'},
                {data: 'barcode', name: 'barcode'},
                {data: 'type_id', name: 'type_id', searchable: false},
                {data: 'dimensions', name: 'dimensions', class: 'text-center', orderable: false, searchable: false},
                {data: 'max_pallets', name: 'max_pallets', class: 'text-center'},
                {data: 'max_weight', name: 'max_weight', class: 'text-center'},
                {data: 'status', name: 'status', class: 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.logistic.locations.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.warehouse = $('#tab-locations select[name=warehouse]').val()
                    d.status    = $('#tab-locations select[name=status]').val()
                    d.type      = $('#tab-locations select[name=type]').val()
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-locations .filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });

        @if(Auth::user()->perm('logistic_warehouses'))
        oTableWarehouses = $('#datatable-warehouses').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'code', name: 'code', class:'text-center'},
                {data: 'name', name: 'name'},
                {data: 'address', name: 'address'},
                {data: 'email', name: 'email'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'phone', name: 'phone', visible:false},
                {data: 'mobile', name: 'mobile', visible:false},
            ],
            ajax: {
                url: "{{ route('admin.logistic.warehouses.datatable') }}",
                type: "POST",
                data: function (d) {},
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableWarehouses) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('#tab-warehouses .filter-datatable').on('change', function (e) {
            oTableWarehouses.draw();
            e.preventDefault();
        });
        @endif
    });

</script>
@stop