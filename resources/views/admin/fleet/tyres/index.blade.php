@section('title')
    Gestão de Pneus
@stop

@section('content-header')
Gestão de Pneus
@stop

@section('breadcrumb')
    <li class="active">@trans('Gestão de Frota')</li>
    <li class="active">@trans('Gestão de Pneus')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                        <li>
                            <a href="{{ route('admin.fleet.accessories.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                        <li>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.fleet.export', ['tyres'] + Request::all()) }}"
                                   class="btn btn-default"
                                   data-toggle="export-url">
                                    <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar listagem')
                                </a>
                            </div>
                        </li>
                        {{--<li>
                            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                            </button>
                        </li>--}}
                        <li class="fltr-primary w-180px">
                            <strong>@trans('Viatura')</strong><br class="visible-xs"/>
                            <div class="pull-left form-group-sm w-125px">
                                {{ Form::select('vehicle', ['' => 'Todas'] + $vehicles, fltr_val(Request::all(), 'vehicle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        
                        <li class="fltr-primary w-205px">
                            <strong>@trans('Compra')</strong><br class="visible-xs"/>
                            <div class="w-150px pull-left form-group-sm">
                                <div class="input-group input-group-sm w-220px">
                                    {{ Form::text('buy_date_min', fltr_val(Request::all(), 'buy_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                                    <span class="input-group-addon">@trans('at')</span>
                                    {{ Form::text('buy_date_max', fltr_val(Request::all(), 'buy_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                    <th></th>
                                    <th class="w-60px">@trans('Referência')</th>
                                    <th>@trans('Viatura')</th>
                                    <th>@trans('Acessório')</th>
                                    <th>@trans('Tipo')</th>
                                    <th class="w-120px">@trans('Marca')</th>
                                    <th class="w-120px">@trans('Modelo')</th>
                                    <th class="w-65px">@trans('Compra')</th>
                                    <th class="w-65px">@trans('Validade')</th>
                                    <th class="w-40px">@trans('Estado')</th>
                                    <th class="w-80px">@trans('Ações')</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="selected-rows-action hide">
                        {{ Form::open(array('route' => 'admin.fleet.accessories.selected.destroy')) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                        {{ Form::close() }}
                        <div class="pull-left">
                            <a href="{{ route('admin.fleet.export', 'accessories') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
                                <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                            </a>
                        </div>
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
                    {data: 'code', name: 'code'},
                    {data: 'vehicle', name: 'vehicle', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'type', name: 'type'},
                    {data: 'brand', name: 'brand'},
                    {data: 'model', name: 'model'},
                    {data: 'buy_date', name: 'buy_date'},
                    {data: 'validity_date', name: 'validity_date'},
                    {data: 'status', name: 'status'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[2, "desc"]],
                ajax: {
                    url: "{{ route('admin.fleet.accessories.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle  = $('select[name=vehicle]').val();
                        d.type     = $('select[name=type]').val();
                        d.buy_date_min = $('input[name=buy_date_min]').val();
                        d.buy_date_max = $('input[name=buy_date_max]').val();
                    },
                    beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                    complete: function () { Datatables.complete(); },
                    error: function () { Datatables.error(); }
                }
            });

            $('.filter-datatable').on('change', function (e) {
                oTable.draw();
                e.preventDefault();

                var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $('[data-toggle="export-url"]').attr('href', exportUrl);
            });
        });

        //export selected
        $(document).on('change', '.row-select',function(){
            var queryString = '';
            $('input[name=row-select]:checked').each(function(i, selected){
                queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
            });

            var exportUrl = Url.removeQueryString($('[data-toggle="export-selected"]').attr('href'));
            $('[data-toggle="export-selected"]').attr('href', exportUrl + '?' + queryString);
        });
    </script>
@stop