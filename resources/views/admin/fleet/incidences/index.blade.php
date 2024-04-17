@section('title')
    Registo de Ocorrências
@stop

@section('content-header')
    Registo de Ocorrências
@stop

@section('breadcrumb')
    <li class="active">@trans('Gestão de Frota')</li>
    <li class="active">@trans('Registo de Ocorrências')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                        <li>
                            <a href="{{ route('admin.fleet.incidences.create') }}"
                               class="btn btn-success btn-sm"
                               data-toggle="modal"
                               data-target="#modal-remote-lg">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                        <li>
                            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                            </button>
                        </li>
                        <li class="fltr-primary w-180px">
                            <strong>@trans('Viatura')</strong><br class="visible-xs"/>
                            <div class="pull-left form-group-sm w-125px">
                                {{ Form::select('vehicle', ['' => __('Todas')] + $vehicles, fltr_val(Request::all(), 'vehicle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                    </ul>
                    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                        <ul class="list-inline pull-left">
                            <li class="col-xs-12">
                                <strong>@trans('Data')</strong><br/>
                                <div class="input-group input-group-sm w-220px">
                                    {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                                    <span class="input-group-addon">@trans('até')</span>
                                    {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>@trans('Oficina')</strong><br/>
                                <div class="w-160px">
                                    {{ Form::select('provider', ['' => __('Todos')] + $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>@trans('Motorista')</strong><br/>
                                <div class="w-140px">
                                    {{ Form::select('operator', ['' => __('Todos')] + $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
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
                                <th class="w-70px">@trans('Data')</th>
                                <th>@trans('Viatura')</th>
                                <th>@trans('Ocorrência')</th>
                                <th class="w-200px">@trans('Operador')</th>
                                <th class="w-60px">Km</th>
                                <th class="w-1">@trans('Resolução')</th>
                                <th class="w-60px">@trans('Estado')</th>
                                <th class="w-80px">@trans('Ações')</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="selected-rows-action hide">
                        {{ Form::open(array('route' => 'admin.fleet.incidences.selected.destroy')) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
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
                    {data: 'date', name: 'date'},
                    {data: 'vehicle_id', name: 'vehicle_id'},
                    {data: 'title', name: 'title'},
                    {data: 'operator_id', name: 'operator_id'},
                    {data: 'km', name: 'km'},
                    {data: 'total', name: 'total'},
                    {data: 'is_fixed', name: 'is_fixed'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    url: "{{ route('admin.fleet.incidences.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle  = $('select[name=vehicle]').val();
                        d.provider = $('select[name=provider]').val();
                        d.operator = $('select[name=operator]').val();
                        d.date_min = $('input[name=date_min]').val();
                        d.date_max = $('input[name=date_max]').val();
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