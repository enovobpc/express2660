@section('title')
Tipos de Incidência
@stop

@section('content-header')
Tipos de Incidência
@stop

@section('breadcrumb')
<li class="active">Tabelas Auxiliares</li>
<li class="active">Tipos de Incidência</li>
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
                <li class="active">
                    <a href="{{ route('admin.tracking.incidences.index') }}">
                        Motivos de Incidência
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.transport-types.index') }}">
                        Tipos Transporte
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pack-types.index') }}">
                        Tipos Mercadoria
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    @if(Auth::user()->isAdmin())
                    <li>
                        <a href="{{ route('admin.tracking.incidences.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tracking.incidences.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
                            <i class="fas fa-sort-amount-down"></i> Ordenar
                        </a>
                    </li>
                    @endif
                    <li class="fltr-primary w-180px">
                        <strong>Ativo</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-110px">
                            {{ Form::select('active', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('active') ? Request::get('active') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable" class="table table-condensed table-striped table-dashed table-hover">
                        <thead>
                        <tr>
                            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                            <th></th>
                            <th>Designação</th>
                            <th class="w-1">Expedição</th>
                            <th class="w-1">Recolha</th>
                            <th class="w-1">Foto</th>
                            <th class="w-1">Data</th>
                            <th class="w-1">Pudo</th>
                            <th class="w-50px">Vis. App</th>
                            <th class="w-1">Ativo</th>
                            <th class="w-1">Pos</th>
                            <th class="w-1">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    @if(Auth::user()->isAdmin())
                        {{ Form::open(array('route' => 'admin.tracking.incidences.selected.destroy')) }}
                        <button class="btn btn-sm btn-danger m-r-5" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                        {{ Form::close() }}
                    @endif
                    <button type="button" class="btn btn-sm btn-default"
                            data-toggle="modal"
                            data-target="#modal-mass-update">
                        <i class="fas fa-fw fa-pencil-alt"></i> Editar Massivo
                    </button>
                    @include('admin.shipments.incidences_types.modals.mass_update')
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    td .text-muted {
        color: #ddd !important;
    }
</style>
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
                {data: 'is_shipment', name: 'is_shipment', class:'text-center'},
                {data: 'is_pickup', name: 'is_pickup', class:'text-center'},
                {data: 'photo_required', name: 'photo_required', class:'text-center'},
                {data: 'date_required', name: 'date_required', class:'text-center'},
                {data: 'pudo_required', name: 'pudo_required', class:'text-center'},
                {data: 'operator_visible', name: 'operator_visible', class:'text-center'},
                {data: 'is_active', name: 'is_active', class:'text-center'},
                {data: 'sort', name: 'sort', class: 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[10, "asc"]],
            ajax: {
                url: "{{ route('admin.tracking.incidences.datatable') }}",
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