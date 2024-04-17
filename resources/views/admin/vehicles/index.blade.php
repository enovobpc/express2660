@section('title')
    Viaturas
@stop

@section('content-header')
    Viaturas
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Viaturas</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.vehicles.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.vehicles.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-sort-amount-down"></i> Ordenar
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-1">Matrícula</th>
                                <th>Designação</th>
                                <th>Tipo</th>
                                <th class="w-70px">Peso Bruto</th>
                                <th class="w-60px">Capacidade</th>
                                <th class="w-200px">Agências</th>
                                <th class="w-1"><i class="fas fa-star" data-toggle="tooltip" title="Viatura por defeito"></i></th>
                                <th class="w-1"><i class="fas fa-sort-amount-asc"></i></th>
                                <th class="w-65px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.vehicles.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable
    $(document).ready(function () {
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'license_plate', name: 'license_plate'},
                {data: 'name', name: 'name'},
                {data: 'type', name: 'type'},
                {data: 'gross_weight', name: 'gross_weight'},
                {data: 'usefull_weight', name: 'usefull_weight'},
                {data: 'agencies', name: 'agencies'},
                {data: 'is_default', name: 'is_default', class:'text-center', orderable: false, searchable: false},
                {data: 'sort', name: 'sort', class: 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[9, "desc"]],
            ajax: {
                url: "{{ route('admin.vehicles.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.agency  = $('select[name=agency]').val()
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
</script>
@stop