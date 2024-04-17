@section('title')
    Estados de Envio e Recolha
@stop

@section('content-header')
    Estados de Envio e Recolha
@stop

@section('breadcrumb')
<li class="active">Tabelas Auxiliares</li>
<li class="active">Estados de Envio e Recolha</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-tab-url tabs-filter">
                    <li class="active">
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
                    <li>
                        <a href="{{ route('admin.pack-types.index') }}">
                            Tipos Mercadoria
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                        <li>
                            <a href="{{ route('admin.tracking.status.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                                <i class="fas fa-plus"></i> Novo
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.tracking.status.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
                                <i class="fas fa-sort-amount-down"></i> Ordenar
                            </a>
                        </li>
                        <li>
                            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                            </button>
                        </li>
                        <li class="fltr-primary w-180px">
                            <strong>Ativo</strong><br class="visible-xs"/>
                            <div class="pull-left form-group-sm w-80px">
                                {{ Form::select('is_visible', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('is_visible') ? Request::get('is_visible') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    </ul>
                    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                        <ul class="list-inline pull-left">
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>Est. Envio</strong><br/>
                                <div class="w-100px">
                                    {{ Form::select('is_shipment', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('is_shipment') ? Request::get('is_shipment') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>Est. Rec.</strong><br/>
                                <div class="w-100px">
                                    {{ Form::select('is_collection', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('is_collection') ? Request::get('is_collection') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>Est. Final</strong><br/>
                                <div class="w-100px">
                                    {{ Form::select('is_final', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('is_final') ? Request::get('is_final') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            @if(Auth::user()->isAdmin())
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>Est. Visivel</strong><br/>
                                <div class="w-100px">
                                    {{ Form::select('is_visible', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('is_visible') ? Request::get('is_visible') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            @endif
                            <li style="margin-bottom: 5px;"  class="col-xs-6">
                                <strong>Est. Público</strong><br/>
                                <div class="w-100px">
                                    {{ Form::select('is_public', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('is_public') ? Request::get('is_public') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table w-100 table-striped table-condensed table-dashed table-hover">
                            <thead>
                                <tr>
                                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                    <th></th>
                                    <th class="w-150px">Designação</th>
                                    <th class="w-330px">Descrição</th>
                                    <th class="w-1">Envio</th>
                                    <th class="w-1">Recolha</th>
                                    <th class="w-1">Final</th>
                                    <th class="w-1">Picking</th>
                                    <th class="w-1">Público</th>
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
                        {{ Form::open(array('route' => 'admin.tracking.status.selected.destroy')) }}
                        <button class="btn btn-sm btn-danger m-r-5" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                        {{ Form::close() }}
                        @endif
                        <button type="button" class="btn btn-sm btn-default"
                            data-toggle="modal"
                            data-target="#modal-mass-update">
                            <i class="fas fa-fw fa-check"></i> Ativar/Inativar
                        </button>
                        @include('admin.shipments.status.modals.mass_update')
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
                /*{data: 'custom_name', name: 'custom_name', orderable: false, searchable: false},*/
                {data: 'description', name: 'description'},
                {data: 'is_shipment', name: 'is_shipment', class: 'text-center'},
                {data: 'is_collection', name: 'is_collection', class: 'text-center'},
                {data: 'is_final', name: 'is_final', class: 'text-center'},
                {data: 'is_traceability', name: 'is_traceability', class: 'text-center'},
                {data: 'is_public', name: 'is_public', class: 'text-center'},
                {data: 'is_visible', name: 'is_visible', class: 'text-center'},
                {data: 'sort', name: 'sort', class: 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[10, "asc"]],
            ajax: {
                url: "{{ route('admin.tracking.status.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.is_shipment   = $('select[name=is_shipment]').val();
                    d.is_collection = $('select[name=is_collection]').val();
                    d.is_final      = $('select[name=is_final]').val();
                    d.is_visible    = $('select[name=is_visible]').val();
                    d.is_public     = $('select[name=is_public]').val();
                    d.platform      = $('select[name=platform]').val();
                    d.sources       = $('select[name=sources]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                //error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });

</script>
@stop