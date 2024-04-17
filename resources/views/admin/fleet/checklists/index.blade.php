@section('title')
    Formulários de Controlo
@stop

@section('content-header')
    Formulários de Controlo
@stop

@section('breadcrumb')
    <li class="active">@trans('Gestão de Frota')</li>
    <li class="active">@trans('Formulários de Controlo')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                        <li>
                            <a href="{{ route('admin.fleet.checklists.create') }}"
                               class="btn btn-success btn-sm"
                               data-toggle="modal"
                               data-target="#modal-remote-lg">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                        {{--<li>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.fleet.export', ['checklists'] + Request::all()) }}"
                                   class="btn btn-default"
                                   data-toggle="export-url">
                                    <i class="fas fa-fw fa-file-excel"></i> Exportar
                                </a>
                            </div>
                        </li>
                        <li>
                            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                            </button>
                        </li>--}}
                        {{--<li class="fltr-primary w-260px">
                            <strong>Data</strong><br class="visible-xs"/>
                            <div class="w-150px pull-left form-group-sm">
                                <div class="input-group input-group-sm w-220px">
                                    {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                                    <span class="input-group-addon">até</span>
                                    {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                                </div>
                            </div>
                        </li>
                        <li class="fltr-primary w-180px">
                            <strong>Viatura</strong><br class="visible-xs"/>
                            <div class="pull-left form-group-sm w-125px">
                                {{ Form::select('vehicle', ['' => 'Todas'] + $vehicles, fltr_val(Request::all(), 'vehicle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>--}}
                    </ul>
                    {{--<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                        <ul class="list-inline pull-left">
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>Posto</strong><br/>
                                <div class="w-140px">
                                    {{ Form::select('provider', ['' => 'Todos'] + $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>Motorista</strong><br/>
                                <div class="w-140px">
                                    {{ Form::select('operator', ['' => 'Todos'] + $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                        </ul>
                    </div>--}}
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                            <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th>@trans('Formulário')</th>
                                <th>@trans('Descrição')</th>
                                <th class="w-1">@trans('Respostas')</th>
                                <th class="w-1">@trans('Ativo')</th>
                                <th class="w-80px">@trans('Criado em')</th>
                                <th class="w-1">@trans('Registar')</th>
                                <th class="w-80px">@trans('Ações')</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="selected-rows-action hide">
                        {{ Form::open(array('route' => 'admin.fleet.checklists.selected.destroy')) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                        {{ Form::close() }}
                        {{--<div class="pull-left">
                            <a href="{{ route('admin.fleet.export', 'checklists') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
                                <i class="fas fa-fw fa-file-excel"></i> Exportar
                            </a>
                        </div>--}}
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
                    {data: 'title', name: 'title'},
                    {data: 'description', name: 'description', orderable: false, searchable: false},
                    {data: 'answers', name: 'answers', class: 'text-center', orderable: false, searchable: false},
                    {data: 'is_active', name: 'is_active', class: 'text-center', orderable: false, searchable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'answer', name: 'answer', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    url: "{{ route('admin.fleet.checklists.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.date_min  = $('input[name=date_min]').val();
                        d.date_max  = $('input[name=date_max]').val();
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