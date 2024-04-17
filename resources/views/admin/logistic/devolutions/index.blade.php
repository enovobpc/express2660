@section('title')
    Devoluções
@stop

@section('content-header')
    Devoluções
@stop

@section('breadcrumb')
<li class="active">@trans('Gestão Logística')</li>
<li class="active">@trans('Devoluções')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.logistic.devolutions.create') }}"
                           class="btn btn-success btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-xl">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.logistic.devolutions.create', ['picking-mode' => true]) }}"
                           class="btn btn-default btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-xs">
                            <i class="fas fa-barcode"></i> @trans('Devolução')
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                        </button>
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
                                <th class="w-70px">@trans('Devolução')</th>
                                <th class="w-80px">@trans('Ordem Saída')</th>
                                <th>@trans('Cliente')</th>
                                <th class="w-80px">@trans('Envio')</th>
                                <th class="w-1"><span data-toggle="tooltip" title="Qtd Expedida">@trans('Exp')</span></th>
                                <th class="w-1"><span data-toggle="tooltip" title="Qtd Devolvida">@trans('Dev')</span></th>
                                <th class="w-1"><span data-toggle="tooltip" title="Qtd Danificada">@trans('Dan')</span></th>
                                <th class="w-1">@trans('Estado')</th>
                                <th class="w-1">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.logistic.devolutions.selected.destroy')) }}
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
                {data: 'shipping_order_code', name: 'shipping_order_code'},
                {data: 'customer.name', name: 'customer.name', orderable: false, searchable: false},
                {data: 'shipment_trk', name: 'shipment_trk'},
                {data: 'total_qty_original', name: 'total_qty_original', class:'text-center'},
                {data: 'total_qty', name: 'total_qty', class:'text-center'},
                {data: 'total_qty_damaged', name: 'total_qty_damaged', class:'text-center'},
                {data: 'status', name: 'status', class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[2, "desc"]],
            ajax: {
                url: "{{ route('admin.logistic.devolutions.datatable') }}",
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