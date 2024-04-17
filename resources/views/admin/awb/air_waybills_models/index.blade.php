@section('title')
    Modelos Pré-preenchidos
@stop

@section('content-header')
    Modelos Pré-preenchidos
@stop

@section('breadcrumb')
<li class="active">Cartas de Porte Aéreo</li>
<li class="active">Modelos Pré-preenchidos</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.air-waybills.models.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-xl">
                            <i class="fas fa-plus"></i> Novo
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li>
                        <strong>Tipo</strong>
                        {{ Form::select('type', ['' => 'Todos'] + $goodsTypes, Request::has('type') ? Request::get('type') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline">
                        <li>
                            <strong>Agente</strong><br/>
                            {{ Form::select('agent', array('' => 'Todos') + $agents, Request::has('agent') ? Request::get('agent') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li style="margin-bottom: -14px;">
                            <strong>Transportador</strong><br/>
                            <div class="input-group">
                                <div class="w-150px pull-left">
                                    {{ Form::select('provider', ['' => 'Todos'] + $providers, Request::has('provider') ? Request::get('provider') : null, array('class' => 'form-control input-sm filter-datatable select2 w-100')) }}
                                </div>
                            </div>
                        </li>
                        <li style="margin-bottom: -14px;">
                            <strong>Aeroporto Origem</strong><br/>
                            <div class="input-group">
                                <div class="w-150px pull-left">
                                    {{ Form::select('source_airport', array('' => 'Todos'), Request::has('source_airport') ? Request::get('source_airport') : null, array('class' => 'form-control input-sm w-100 filter-datatable')) }}
                                </div>
                                <span class="input-group-btn">
                                    <button class="btn btn-default clean-select" type="button"><i class="fas fa-times"></i></button>
                                </span>
                            </div>
                        </li>
                        <li style="margin-bottom: -14px;">
                            <strong>Aeroporto Destino</strong><br/>
                            <div class="input-group">
                                <div class="w-150px pull-left">
                                    {{ Form::select('recipient_airport', array('' => 'Todos'), Request::has('recipient_airport') ? Request::get('recipient_airport') : null, array('class' => 'form-control input-sm w-100 filter-datatable')) }}
                                </div>
                                <span class="input-group-btn">
                                    <button class="btn btn-default clean-select" type="button"><i class="fas fa-times"></i></button>
                                </span>
                            </div>
                        </li>
                        <li style="margin-bottom: -14px;">
                            <strong>Expedidor</strong><br/>
                            <div class="input-group">
                                <div class="w-150px pull-left">
                                    {{ Form::select('customer', array('' => 'Todos'), Request::has('customer') ? Request::get('customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable')) }}
                                </div>
                                <span class="input-group-btn">
                                    <button class="btn btn-default clean-select" type="button"><i class="fas fa-times"></i></button>
                                </span>
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
                                <th>Designação</th>
                                <th>Transportador</th>
                                <th class="w-1">Tipo</th>
                                <th class="w-20px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.air-waybills.models.selected.destroy')) }}
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
                {data: 'id', name: 'id', visible: false},
                {data: 'name', name: 'name'},
                {data: 'provider_id', name: 'provider_id', searchable: false},
                {data: 'goods_type', name: 'goods_type', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.air-waybills.models.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.type  = $('select[name=type]').val()
                    d.provider  = $('select[name=provider]').val();
                    d.customer  = $('select[name=customer]').val();
                    d.agent     = $('select[name=agent]').val();
                    d.source_airport    = $('select[name=source_airport]').val();
                    d.recipient_airport = $('select[name=recipient_airport]').val();
                },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });

        $("select[name=customer]").select2({
            ajax: {
                url: "{{ route('admin.shipments.search.customer') }}",
                dataType: 'json',
                method: 'post',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        $(".datatable-filters-extended select[name=source_airport], .datatable-filters-extended select[name=recipient_airport]").select2({
            ajax: {
                url: "{{ route('admin.air-waybills.search.airport') }}",
                dataType: 'json',
                method: 'post',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
    });

</script>
@stop