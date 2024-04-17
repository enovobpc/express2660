@section('title')
    Inventários
@stop

@section('content-header')
    Inventários
@stop

@section('breadcrumb')
<li class="active">@trans('Gestão Logística')</li>
<li class="active">@trans('Inventários')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.logistic.inventories.create') }}"
                           class="btn btn-success btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-xl">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                    <li>
                        <div class="btn-group btn-group-sm" role="group">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-boxes"></i> @trans('Gerir Stock') <i class="fas fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.logistic.products.stock.add') }}"
                                           data-toggle="modal"
                                           data-target="#modal-remote-xs">
                                            <i class="fas fa-fw fa-plus"></i> @trans('Adicionar Stocks')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.logistic.products.adjustment.edit') }}"
                                           data-toggle="modal"
                                           data-target="#modal-remote">
                                            <i class="fas fa-fw fa-check"></i> @trans('Corrigir Stocks')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.logistic.products.move.edit') }}"
                                           data-toggle="modal"
                                           data-target="#modal-remote">
                                            <i class="fas fa-fw fa-exchange-alt"></i> @trans('Transferir Stocks')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="btn-group btn-group-sm" role="group">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-print"></i> @trans('Relatórios') <i class="fas fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#modal-map-print-stocks">
                                            <i class="fas fa-fw fa-print"></i> @trans('Mapa Existências')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#modal-map-export-stocks">
                                            <i class="fas fa-fw fa-file-excel"></i> @trans('Mapa Existências')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-filter-datatable btn-sm btn-default">
                                <i class="fas fa-filter"></i> <i class="fas fa-angle-down"></i>
                            </button>
                        </div>
                    </li>
                    <li class="fltr-primary w-160px">
                        <strong>@trans('Estado')</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-100px">
                            {{ Form::selectMultiple('status', $status, fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li class="col-xs-12">
                            <strong>@trans('Data')</strong><br/>
                            <div class="input-group input-group-sm w-240px">
                                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                                <span class="input-group-addon">@trans('até')</span>
                                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Cliente')</strong><br/>
                            <div class="w-250px">
                                {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => __('Todos'), 'data-query-text' => 'true')) }}
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
                                <th class="w-80px">@trans('Inventário')</th>
                                <th>@trans('Descrição')</th>
                                <th class="w-1">@trans('Artigos')</th>
                                <th class="w-50px">@trans('Estimado')</th>
                                <th class="w-50px">@trans('Real')</th>
                                <th class="w-70px">@trans('Data')</th>
                                <th class="w-80px">@trans('Estado')</th>
                                <th class="w-1">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.logistic.inventories.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.logistic.inventories.modals.map_print_stocks')
@include('admin.logistic.inventories.modals.map_export_stocks')
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
                {data: 'description', name: 'description'},
                {data: 'items', name: 'items', class:'text-center'},
                {data: 'qty_existing', name: 'qty_existing', class:'text-center'},
                {data: 'qty_real', name: 'qty_real', class:'text-center'},
                {data: 'date', name: 'date'},
                {data: 'status_id', name: 'status_id', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[2, "desc"]],
            ajax: {
                url: "{{ route('admin.logistic.inventories.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.status   = $('[name="status"]').val();
                    d.date_min = $('[name="date_min"]').val();
                    d.date_max = $('[name="date_max"]').val();
                    d.customer = $('[name="dt_customer"]').val();
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

    $("select[name=dt_customer], #modal-map-print-stocks select[name=customer], #modal-map-export-stocks select[name=customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.logistic.products.search.customer') }}")
    });

    $("#modal-remote-lg").on('hidden.bs.modal', function () {
        $(this).data('bs.modal', null);
    });

    $('.modal-filter-dates .btn-submit').on('click', function(e) {
        $(this).closest('form').submit();
        $(this).button('reset');
        $('.modal-filter-dates').modal('hide')
    })
</script>
@stop