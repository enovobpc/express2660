@section('title')
    Métodos de Webservice
@stop

@section('content-header')
    Métodos de Webservice <span data-toggle="tooltip" data-placement="right" title="Funcionalidade apenas para administradores"><i class="fas fa-user-shield"></i></span>
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Métodos de Webservice</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.webservice-methods.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.webservice-methods.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-sort-amount-down"></i> Ordenar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.webservices.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-plug"></i> Configurar Webservices Globais
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th>Método</th>
                                {{--<th>Ativo para as plataformas</th>--}}
                                <th class="w-1">Ativo</th>
                                <th class="w-1">Pos.</th>
                                <th class="w-70px">Criado em</th>
                                <th class="w-1">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.webservice-methods.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar</button>
                    {{ Form::close() }}
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
                {data: 'name', name: 'name'},
                /*{data: 'sources', name: 'sources'},*/
                {data: 'enabled', name: 'enabled', 'class': 'text-center'},
                {data: 'sort', name: 'sort', 'class': 'text-center'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[4, "asc"]],
            ajax: {
                url: "{{ route('admin.webservice-methods.datatable') }}",
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