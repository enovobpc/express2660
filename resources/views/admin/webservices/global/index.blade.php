@section('title')
    Webservices Globais
@stop

@section('content-header')
    Webservices Globais
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Webservices Globais</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.webservices.create') }}" class="btn btn-success btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-lg">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.webservices.mass-config') }}" class="btn btn-default btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote">
                            <i class="fas fa-plug"></i> Ativar/Desativar Webservices em Massa
                        </a>
                    </li>
                    @if(Auth::user()->isAdmin())
                        <li>
                            <a href="{{ route('admin.webservice-methods.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-cog"></i> Configurar Métodos
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                            <th class="w-90px">Conector</th>
                            <th>Envios via</th>
                            <th>Agência/Franquia</th>
                            <th>Utilizador</th>
                            <th>Password</th>
                            <th>Session ID</th>
                            <th>Apenas Agência</th>
                            <th class="w-80px">Forçar Rem.</th>
                            <th class="w-75px">Ativar Auto</th>
                            <th class="w-1">Ativo</th>
                            <th class="w-30px">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.webservices.selected.destroy')) }}
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
                {data: 'id', name: 'id', visible: false},
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'method', name: 'method'},
                {data: 'provider_id', name: 'provider_id'},
                {data: 'agency', name: 'agency'},
                {data: 'user', name: 'user'},
                {data: 'password', name: 'password'},
                {data: 'session_id', name: 'session_id'},
                {data: 'agency_id', name: 'agency_id'},
                {data: 'force_sender', name: 'force_sender', 'class': 'text-center'},
                {data: 'auto_enable', name: 'auto_enable', 'class': 'text-center'},
                {data: 'active', name: 'active', 'class': 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[2, "desc"]],
            ajax: {
                url: "{{ route('admin.webservices.datatable') }}",
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