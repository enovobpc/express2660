@section('title')
    Ordens de Recepção
@stop

@section('content-header')
    Ordens de Recepção
@stop

@section('breadcrumb')
<li class="active">@trans('Gestão Logística')</li>
<li class="active">@trans('Ordens de Recepção')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    @if(config('app.source') != 'activos24')
                    <li>
                        <a href="{{ route('admin.logistic.reception-orders.create') }}"
                           class="btn btn-success btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-xl">
                            <i class="fas fa-plus"></i> @trans('Novo Pedido')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.logistic.reception-orders.confirmation.create') }}"
                           class="btn btn-primary btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-xs">
                            <i class="fas fa-check"></i> @trans('Picking In')
                        </a>
                    </li>
                    @endif
                    <li>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    data-loading-text="<i class='fas fa-spin fa-sync-alt'></i> A sincronizar">
                                <i class="fas fa-wrench"></i> @trans('Ferramentas') <i class="fas fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('admin.importer.index') }}" target="_blank">
                                        <i class="fas fa-fw fa-upload"></i> @trans('Importador de Ficheiros Excel')
                                    </a>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="{{ route('admin.logistic.reception-orders.export') }}" target="_blank" data-toggle="export-url">
                                        <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                                    </a>
                                </li>
                            </ul>

                            <button type="button" class="btn btn-filter-datatable btn-sm btn-default">
                                <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                            </button>
                        </div>
                    </li>
                    
                    <li class="fltr-primary w-160px">
                        <strong>@trans('Estado')</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-100px">
                            {{ Form::selectMultiple('status', $status, fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-280px">
                        <strong>@trans('Data')</strong><br class="visible-xs"/>
                        <div class="input-group input-group-sm w-235px">
                            {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                            <span class="input-group-addon">@trans('até')</span>
                            {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        @if(config('app.source') != 'activos24')
                        <li style="margin-bottom: 5px; width: 110px">
                            <strong>@trans('Satisfeito')</strong><br/>
                            <div class="w-100px">
                                {{ Form::select('satisfied', [''=>__('Todos'), '1' => __('Safisfeito'), '0' => __('Não Satisfeito')], Request::get('satisfied'), array('class' => 'form-control select2 input-sm w-100 filter-datatable')) }}
                            </div>
                        </li>
                        @endif
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Cliente')</strong><br/>
                            <div class="w-250px">
                                {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => __('Todos'), 'data-query-text' => 'true')) }}
                            </div>
                        </li>
                        <li style="width: 170px; margin-top: 21px">
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('hide_concluded', 1, Request::has('hide_concluded') ? Request::get('hide_concluded') : 1, ['class' => 'filter-datatable']) }}
                                    @trans('Ocultar Finalizados')
                                </label>
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
                                <th class="w-80px">@trans('Processo')</th>
                                <th>@trans('Cliente')</th>
                                <th class="w-140px">@trans('Documento')</th>
                                <th class="w-1">@trans('Artigos')</th>
                                <th class="w-60px">@trans('Qtd')</th>
                                <th class="w-75px">@trans('Data Prev.')</th>
                                @if(config('app.source') != 'activos24')
                                <th class="w-75px">@trans('Data Recb.')</th>
                                <th class="w-50px">@trans('Preço')</th>
                                <th class="w-80px">@trans('Estado')</th>
                                @endif
                                <th class="w-1">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.logistic.reception-orders.selected.destroy')) }}
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

    var oTable;
    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'code', name: 'code'},
                {data: 'customer.name', name: 'customer.name', orderable: false, searchable: false},
                {data: 'document', name: 'document'},
                {data: 'total_items', name: 'total_items', class: 'text-center'},
                {data: 'total_qty', name: 'total_qty', class: 'text-center'},
                {data: 'requested_date', name: 'requested_date'},
                @if(config('app.source') != 'activos24')
                {data: 'received_date', name: 'received_date'},
                {data: 'total_price', name: 'total_price'},
                {data: 'status_id', name: 'status_id', class:'text-center', searchable: false},
                @endif
                {data: 'actions', name: 'actions', class:'text-center', orderable: false, searchable: false},
            ],
            order: [[2, "desc"]],
            ajax: {
                url: "{{ route('admin.logistic.reception-orders.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.status    = $('[name="status"]').val();
                    d.date_min  = $('[name="date_min"]').val();
                    d.date_max  = $('[name="date_max"]').val();
                    d.customer  = $('[name="dt_customer"]').val();
                    d.satisfied = $('[name="satisfied"]').val();
                    d.hide_concluded = $('input[name=hide_concluded]:checked').length;
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { 
                    // Datatables.error(); 
                }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
            
            var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);
        });
        
        $('[data-toggle="export-url"]').on('click', function (e) {
            var exportUrl = Url.removeQueryString($(this).attr('href'));

            var value = $('input[name="hide_concluded"]').is(':checked') ? 1 : 0;
            newUrl = Url.updateParameter(Url.current(), 'hide_concluded', value);
            Url.change(newUrl);

            exportUrl += '?' + Url.getQueryString(Url.current());

            $(this).attr('href', exportUrl);
        });
        
        //show concluded shipments
        // $(document).on('change', '[name="hide_concluded"]', function (e) {
        //     oTable.draw();
        //     e.preventDefault();

        //     var name = $(this).attr('name');
        //     var value = $(this).is(':checked');
        //     value = value == false ? 0 : 1;

        //     newUrl = Url.updateParameter(Url.current(), name, value)
        //     Url.change(newUrl);

        // });
    });

    $("select[name=dt_customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.logistic.products.search.customer') }}")
    });

    $("#modal-remote-lg").on('hidden.bs.modal', function () {
        $(this).data('bs.modal', null);
    });

</script>
@stop