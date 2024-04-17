@section('title')
    Rotas Fixas
@stop

@section('content-header')
    Rotas Fixas
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Rotas Fixas</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.routes.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <div class="btn-group btn-group-sm" role="group">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-wrench"></i> Ferramentas <i class="fas fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    @if(Auth::user()->hasRole(config('permissions.role.admin')) || Auth::user()->can('importer'))
                                    <li>
                                        <a href="{{ route('admin.importer.index') }}">
                                            <i class="fas fa-fw fa-upload"></i> Importador de Ficheiros Excel
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        <a href="{{ route('admin.routes.groups.index') }}" data-toggle="modal" data-target="#modal-remote">
                                            <i class="fas fa-fw fa-sort-amount-down"></i> Gerir grupos
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="{{ route('admin.routes.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-sort-amount-down"></i> Ordenar
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    @if(count($agencies) > 1)
                        <li class="fltr-primary w-240px">
                            <strong>Agência</strong><br class="visible-xs"/>
                            <div class="pull-left form-group-sm w-170px">
                                {{ Form::selectMultiple('agency', $agencies, fltr_val(Request::all(), 'agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                    @endif
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Fornecedor</strong><br/>
                            <div class="w-160px">
                                {{ Form::selectMultiple('provider', $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Operador</strong><br/>
                            <div class="w-160px">
                                {{ Form::selectMultiple('operator', $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-1">Código</th>
                                <th>Descrição</th>
                                <th class="w-80px">Tipo</th>
                                {{-- <th class="w-1">Fornecedor</th> --}}
                                <th>Códigos Postais</th>
                                <th>Serviços</th>
                                <th class="w-250px">Horários</th>
                                <th class="w-100px">Agências</th>
                                <th class="w-80px">Clientes</th>
                                <th class="w-20px"><i class="fas fa-sort-amount-up" data-toggle="tooltip" title="Ordenação"></i></th>
                                {{-- <th class="w-140px">Operador</th>
                                <th class="w-50px">Viatura</th> --}}
                                <th class="w-65px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.routes.selected.destroy')) }}
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
                {data: 'code', name: 'code', class: 'text-center'},
                {data: 'name', name: 'name'},
                {data: 'type', name: 'type'},
                // {data: 'provider_id', name: 'provider_id'},
                {data: 'zip_codes', name: 'zip_codes'},
                {data: 'services', name: 'services'},
                {data: 'schedules', name: 'schedules', orderable: false, searchable: false},
                {data: 'agencies', name: 'agencies'},
                {data: 'customers', name: 'customers', class: 'text-center', orderable: false, searchable: false},
                {data: 'sort', name: 'sort', searchable: false},
                // {data: 'operator_id', name: 'operator_id'},
                // {data: 'vehicle', name: 'vehicle', class: 'text-center', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.routes.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.agency   = $('select[name=agency]').val();
                    d.operator = $('select[name=operator]').val();
                    d.provider = $('select[name=provider]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () {}
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
</script>
@stop