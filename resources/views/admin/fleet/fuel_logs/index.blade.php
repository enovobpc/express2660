@section('title')
    Registo de Abastecimentos
@stop

@section('content-header')
    Registo de Abastecimentos
@stop

@section('breadcrumb')
    <li class="active">@trans('Gestão de Frota')</li>
    <li class="active">@trans('Registo de Abastecimentos')</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box no-border">
                <div class="box-body">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                        <li>
                            <a href="{{ route('admin.fleet.fuel.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                        <li>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.importer.index', ['type' => 'fuel']) }}" target="_blank" class="btn btn-filter-datatable btn-default">
                                    <i class="fas fa-upload"></i> @trans('Importar')
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.fleet.export', ['fuel'] + Request::all()) }}"
                                   class="btn btn-default"
                                   data-toggle="export-url">
                                    <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                                </a>
                            </div>
                        </li>
                        <li>
                            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                            </button>
                        </li>
                        <li class="fltr-primary w-260px">
                            <strong>@trans('Data')</strong><br class="visible-xs"/>
                            <div class="w-150px pull-left form-group-sm">
                                <div class="input-group input-group-sm w-220px">
                                    {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                                    <span class="input-group-addon">@trans('até')</span>
                                    {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                                </div>
                            </div>
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
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>@trans('Posto')</strong><br/>
                                <div class="w-140px">
                                    {{ Form::select('provider', ['' => __('Todos')] + $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>@trans('Motorista')</strong><br/>
                                <div class="w-140px">
                                    {{ Form::select('operator', ['' => __('Todos')] + $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>@trans('Produto')</strong><br/>
                                <div class="w-80px">
                                    {{ Form::select('product', ['' => __('Todos'), 'fuel' => __('Gasóleo'), 'adblue' => 'Adblue'], fltr_val(Request::all(), 'product'), array('class' => 'form-control input-sm filter-datatable select2')) }}
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
                                <th>@trans('Fornecedor')</th>
                                <th>@trans('Operador')</th>
                                <th class="w-60px">Km</th>
                                <th class="w-60px">@trans('Produto')</th>
                                <th class="w-40px">@trans('Litros')</th>
                                <th class="w-40px">@trans('Preço/L')</th>
                                <th class="w-60px">@trans('Total')</th>
                                <th class="w-60px">@trans('Duração')</th>
                                <th class="w-1">@trans('Consumo')</th>
                                <th class="w-120px">@trans('Observações')</th>
                                <th class="w-80px">@trans('Ações')</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="selected-rows-action hide">
                        {{ Form::open(array('route' => 'admin.fleet.fuel.selected.destroy')) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                        {{ Form::close() }}
                        <div class="pull-left">
                            <a href="{{ route('admin.fleet.export', 'fuel') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
                                <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                            </a>
                        </div>
                        <div class="pull-left">
                            <h4 style="margin: -2px 0 -6px 10px;
                        padding: 1px 3px 3px 9px;
                        border-left: 1px solid #999;
                        line-height: 17px;">
                                <small>@trans('Total Selecionado')</small><br/>
                                <span class="dt-sum-total bold"></span><b>€</b>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .dtbr-2 {
            border-right: 3px solid #888 !important;
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
                    {data: 'date', name: 'date'},
                    {data: 'vehicle', name: 'vehicle', orderable: false, searchable: false},
                    {data: 'provider', name: 'provider', orderable: false, searchable: false},
                    {data: 'operator', name: 'operator', orderable: false, searchable: false},
                    {data: 'km', name: 'km', class: 'text-right'},
                    {data: 'product', name: 'product', class: 'text-right'},
                    {data: 'liters', name: 'liters', class: 'text-right'},
                    {data: 'price_per_liter', name: 'price_per_liter', class: 'text-right'},
                    {data: 'total', name: 'total', class: 'text-right dtbr-2'},
                    {data: 'balance_km', name: 'balance_km', class: 'text-center'},
                    {data: 'balance_liter_km', name: 'balance_liter_km', class: 'text-center dtbr-2'},
                    {data: 'obs', name: 'obs'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                ajax: {
                    url: "{{ route('admin.fleet.fuel.datatable') }}",
                    type: "POST",
                    data: function (d) {
                        d.vehicle   = $('select[name=vehicle]').val();
                        d.provider  = $('select[name=provider]').val();
                        d.operator  = $('select[name=operator]').val();
                        d.date_min  = $('input[name=date_min]').val();
                        d.date_max  = $('input[name=date_max]').val();
                        d.product   = $('select[name=product]').val();
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