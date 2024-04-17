@section('title')
    Orçamentos - Transporte de Animais
@stop

@section('content-header')
    Orçamentos - Transporte de Animais
@stop

@section('breadcrumb')
<li class="active">Orçamentos</li>
<li class="active">Orçamentos - Transporte de Animais</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.budgets.courier.create', ['type' => 'animals']) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
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
                                    <li>
                                        <a href="{{ route('admin.budgets.courier.models.index') }}">
                                            <i class="fas fa-fw fa-list"></i> Gerir Textos por defeito
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.budgets.courier.services.index') }}" data-toggle="modal" data-target="#modal-remote">
                                            <i class="fas fa-fw fa-list"></i> Gerir tipos de Serviço
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.budgets.courier.stats') }}" data-toggle="modal" data-target="#modal-remote">
                                            <i class="fas fa-fw fa-bar-chart"></i> Estatísticas Mensal
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.budgets.courier.stats', ['type' => 'total']) }}" data-toggle="modal" data-target="#modal-remote">
                                            <i class="fas fa-fw fa-bar-chart"></i> Estatísticas Totais
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                            </button>
                        </div>
                    </li>
                    <li class="fltr-primary w-215px">
                        <strong>Estado</strong><br class="visible-xs"/>
                        <div class="w-150px pull-left form-group-sm">
                            {{ Form::selectMultiple('status', trans('admin/budgets.status'), fltr_val(Request::all(), 'status'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                        </div>
                    </li>
                    <li>
                        <div class="checkbox">
                            <label>
                                {{ Form::checkbox('hide_concluded', 1, true) }}
                                Ocultar Aceites
                            </label>
                        </div>
                    </li>
                    <li>
                        <div class="checkbox">
                            <label>
                                {{ Form::checkbox('hide_rejected', 1, true) }}
                                Ocultar Rejeitados
                            </label>
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li class="col-xs-12">
                            <strong>Data</strong><br/>
                            <div class="input-group input-group-sm w-220px">
                                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                                <span class="input-group-addon">até</span>
                                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>Web</strong><br/>
                            <div class="w-80px">
                                {{ Form::select('web', array('' => 'Todos', '1' => 'Sim', '0' => 'Não'), fltr_val(Request::all(), 'web'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>Responsável</strong><br/>
                            <div class="w-120px">
                                {{ Form::selectMultiple('operator', array('not-assigned' => 'Sem Responsável') + $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
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
                                <th class="w-1">Pedido</th>
                                <th>Cliente</th>
                                @if(config('app.source') == 'intercourier')
                                <th class="w-50px">Aeroporto</th>
                                @endif
                                <th class="w-50px">Total</th>
                                <th class="w-1">Estado</th>
                                <th class="w-65px">Data Doc.</th>
                                <th class="w-65px">Validade</th>
                                <th class="w-70px">Últ. Estado</th>
                                <th class="w-150px">Responsável</th>
                                <th class="w-65px">Criado Em</th>
                                <th class="w-1">Língua</th>
                                <th class="w-1">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.budgets.courier.selected.destroy')) }}
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
    var oTable;

    $(document).ready(function () {
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'budget_no', name: 'budget_no'},
                {data: 'name', name: 'name'},
                @if(config('app.source') == 'intercourier')
                {data: 'delivery_airport', name: 'delivery_airport', orderable: false, searchable: false},
                @endif
                {data: 'total', name: 'total'},
                {data: 'status', name: 'status', class: 'text-center', orderable: false, searchable: false},
                {data: 'budget_date', name: 'budget_date'},
                {data: 'validity_date', name: 'validity_date'},
                {data: 'status_date', name: 'status_date'},
                {data: 'operator_id', name: 'operator_id', orderable: false, searchable: false},
                {data: 'created_at', name: 'created_at'},
                {data: 'locale', name: 'locale', class: 'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'phone', name: 'phone', visible: false},
            ],
            ajax: {
                url: "{{ route('admin.budgets.courier.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.type           = 'animals';
                    d.date_min       = $('[name=date_min]').val();
                    d.date_max       = $('[name=date_max]').val();
                    d.status         = $('select[name=status]').val();
                    d.web            = $('select[name=web]').val();
                    d.operator       = $('select[name=operator]').val();
                    d.hide_concluded = $('input[name=hide_concluded]:checked').length;
                    d.hide_rejected  = $('input[name=hide_rejected]:checked').length;
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

    //show concluded shipments
    $(document).on('change', '[name="hide_concluded"],[name="hide_rejected"]', function (e) {
        oTable.draw();
    });
</script>
@stop