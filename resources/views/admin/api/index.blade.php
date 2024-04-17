@section('title')
    Chaves da API
@stop

@section('content-header')
    Chaves da API
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Chaves da API</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.api.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th class="w-1">ID</th>
                                <th>Nome</th>
                                <th>Secret</th>
                                {{--<th>Redirect</th>--}}
                                <th class="w-1">Autenticação</th>
                                <th class="w-80px">Chamadas</th>
                                <th class="w-65px">Last Call</th>
                                <th class="w-1">Ativo</th>
                                <th class="w-1">Detalhe</th>
                                <th class="w-1">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.api.selected.destroy')) }}
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
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'secret', name: 'secret'},
                /*{data: 'redirect', name: 'redirect'},*/
                {data: 'password_client', name: 'password_client'},
                {data: 'daily_counter', name: 'daily_counter', class:'text-center'},
                {data: 'last_call', name: 'last_call'},
                {data: 'revoked', name: 'revoked'},
                {data: 'details', name: 'details', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.api.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.agency  = $('select[name=agency]').val()
                },
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