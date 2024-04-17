@section('title')
    Ajudas de Custo
@stop

@section('content-header')
    Ajudas de Custo
@stop

@section('breadcrumb')
    <li class="active">Configurações</li>
    <li class="active">Ajudas de Custo</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li class="fltr-primary w-105px">
                        <strong>Ano</strong><br class="visible-xs"/>
                        <div class="w-70px pull-left form-group-sm">
                            {{ Form::select('year', $years, $year, array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-155px">
                        <strong>Mês</strong><br class="visible-xs"/>
                        <div class="w-115px pull-left form-group-sm" style="position: relative">
                            {{ Form::select('month', $months, $month, array('class' => 'form-control input-sm filter-datatable select2')) }}
                            <i class="fas fa-spin fa-circle-notch filter-loading" style="display: none; position: absolute; top: 8px; right: -18px;"></i>
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Grupo Trabalho</strong><br/>
                            <div class="w-160px">
                                {{ Form::selectMultiple('workgroups', $workgroups, fltr_val(Request::all(), 'workgroups'), array('class' => 'form-control input-sm filter-datatable select2-multiple', 'placeholder' => 'Todos')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Perfil</strong><br/>
                            <div class="w-160px">
                                {{ Form::select('role', ['' => 'Todos'] + $roles, Request::has('role') ? Request::get('role') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
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
                                <th class="w-70px">Período</th>
                                <th class="w-1">Código</th>
                                <th class="w-60px">NIF</th>
                                <th>Nome</th>
                                <th class="w-1">Viagens</th>
                                <th class="w-1">Serviços</th>
                                <th class="w-60px">Ajudas</th>
                                <th class="w-60px">Fim Sem.</th>
                                <th class="w-60px">Total</th>
                                <th class="w-65px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                   {{-- {{ Form::open(array('route' => 'admin.allowances.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar</button>
                    {{ Form::close() }}--}}
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
                {data: 'period', name: 'period', orderable: false, searchable: false},
                {data: 'code', name: 'code', class: 'text-center'},
                {data: 'vat', name: 'vat'},
                {data: 'name', name: 'name'},
                {data: 'trips', name: 'trips', class:'text-center', orderable: false, searchable: false},
                {data: 'shipments', name: 'shipments', class:'text-center', orderable: false, searchable: false},
                {data: 'allowances_price', name: 'allowances_price', class:'text-right', orderable: false, searchable: false},
                {data: 'weekend_price', name: 'weekend_price', class:'text-right', orderable: false, searchable: false},
                {data: 'total_price', name: 'total_price', class:'text-right', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.allowances.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.year       = $('select[name=year]').val();
                    d.month      = $('select[name=month]').val();
                    d.workgroups = $('select[name=workgroups]').val();
                    d.role       = $('select[name=role]').val();
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