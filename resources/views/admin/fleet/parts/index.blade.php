@section('title')
    Peças e Serviços
@stop

@section('content-header')
    Peças e Serviços
@stop

@section('breadcrumb')
<li class="active">@trans('Gestão de Frota')</li>
<li class="active">@trans('Peças e Serviços')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-parts" data-toggle="tab">@trans('Peças')</a></li>
                <li><a href="#tab-services" data-toggle="tab">@trans('Serviços e Despesas')</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-parts">
                    @include('admin.fleet.parts.partials.parts')
                </div>
                <div class="tab-pane" id="tab-services">
                    @include('admin.fleet.parts.partials.services')
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTableParts;
    var oTableServices;

    $(document).ready(function () {

        oTableParts = $('#datatable-parts').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'reference', name: 'reference'},
                {data: 'name', name: 'name'},
                // {data: 'category', name: 'category'},
                {data: 'provider_id', name: 'provider_id'},
                {data: 'price', name: 'price'},
                {data: 'stock_total', name: 'stock_total', class:'text-center'},
                {data: 'is_active', name: 'is_active', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.fleet.parts.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.category = $('[data-target="#datatable-parts"] select[name=category]').val();
                    d.brand    = $('[data-target="#datatable-parts"] select[name=brand]').val();
                    d.model    = $('[data-target="#datatable-parts"] select[name=model]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableParts) },
                complete: function () { Datatables.complete(); },
                // error: function () { Datatables.error(); }
            }
        });

        $('[data-target="#datatable-parts"] .filter-datatable').on('change', function (e) {
            oTableParts.draw();
            e.preventDefault();
        });


        oTableServices = $('#datatable-services').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'type', name: 'type'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.fleet.services.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.type = $('[data-target="#datatable-services"] select[name=type]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableServices) },
                complete: function () { Datatables.complete(); },
                // error: function () { Datatables.error(); }
            }
        });

        $('[data-target="#datatable-services"] .filter-datatable').on('change', function (e) {
            oTableServices.draw();
            e.preventDefault();
        });
    });
</script>
@stop