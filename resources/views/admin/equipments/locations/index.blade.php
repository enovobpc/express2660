@section('title')
    Gerir Localizações
@stop

@section('content-header')
    Gerir Localizações
@stop

@section('breadcrumb')
<li class="active">Equipamentos</li>
<li class="active">Gerir Localizações</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-locations" data-toggle="tab">Localizações</a></li>
                @if(Auth::user()->perm('equipments_warehouses'))
                <li><a href="#tab-warehouses" data-toggle="tab">Armazéns</a></li>
                @endif
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-locations">
                    @include('admin.equipments.locations.partials.locations')
                </div>
                @if(Auth::user()->perm('equipments_warehouses'))
                <div class="tab-pane" id="tab-warehouses">
                    @include('admin.equipments.locations.partials.warehouses')
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
                {data: 'warehouse_id', name: 'warehouse_id'},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name', searchable: false},
                {data: 'operator_id', name: 'operator_id', searchable: false},
                {data: 'total_equipments', name: 'total_equipments', class: 'text-center', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.equipments.locations.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.warehouse = $('#tab-locations select[name=warehouse]').val()
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () {
                    Datatables.complete();
                    $('.image-preview').popover({
                        placement: 'auto bottom',
                        container: 'body',
                        trigger: 'hover',
                        html: true,
                        delay: 100,
                        content: function() {
                            return '<img class="img-responsive" src="'+$(this).data('img') + '" />';
                        },
                    });
                }
            }
        });

        $('#tab-locations .filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });

        @if(Auth::user()->perm('equipments_warehouses'))
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
                url: "{{ route('admin.equipments.warehouses.datatable') }}",
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