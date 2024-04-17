@section('title')
Tipos de Mercadoria
@stop

@section('content-header')
Tipos de Mercadoria
@stop

@section('breadcrumb')
<li class="active">Tabelas Auxiliares</li>
<li class="active">Tipos de Mercadoria</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs nav-tab-url tabs-filter">
                <li>
                    <a href="{{ route('admin.tracking.status.index') }}">
                        Estados de Envio
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tracking.incidences.index') }}">
                        Motivos de Incidência
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.transport-types.index') }}">
                        Tipos Transporte
                    </a>
                </li>
                <li class="active">
                    <a href="{{ route('admin.pack-types.index') }}">
                        Tipos Mercadoria
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.pack-types.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pack-types.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-sort-amount-down"></i> Ordenar
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-condensed table-striped table-dashed table-hover">
                        <thead>
                        <tr>
                            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                            <th></th>
                            <th class="w-50px">Código</th>
                            <th>Designação</th>
                            <th class="w-200px">Dimensões</th>
                            <th class="w-60px">Peso Max</th>
                            <th class="w-1">Pos</th>
                            <th class="w-20px">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    @if(Auth::user()->isAdmin())
                        {{ Form::open(array('route' => 'admin.pack-types.selected.destroy')) }}
                        <button class="btn btn-sm btn-danger m-r-5" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                        {{ Form::close() }}
                    @endif
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
    var oTable;

    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'width', name: 'width'},
                {data: 'weight', name: 'weight'},
                {data: 'sort', name: 'sort', class: 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'length', name: 'length', visible: false},
                {data: 'height', name: 'height', visible: false},
            ],
            order: [[6, "asc"]],
            ajax: {
                url: "{{ route('admin.pack-types.datatable') }}",
                type: "POST",
                data: function (d) {},
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