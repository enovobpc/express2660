@section('title')
    Reuniões
@stop

@section('content-header')
    Reuniões
@stop

@section('breadcrumb')
    <li class="active">@trans('Reuniões')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.meetings.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-xl">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li>
                        <strong>@trans('Data')</strong>
                        <div class="input-group input-group-sm w-200px">
                            {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início')]) }}
                            <span class="input-group-addon">@trans('até')</span>
                            {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim')]) }}
                        </div>
                    </li>
                    <li>
                        <strong>@trans('Estado')</strong>
                        {{ Form::select('status', array('' => __('Todos')) + trans('admin/meetings.status'), Request::has('status') ? Request::get('status') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        @if(count($agencies) > 1)
                            <li>
                                <strong>@trans('Agência')</strong><br/>
                                {{ Form::select('agency', ['' => __('Todos')] + $agencies, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                            </li>
                        @endif
                        @if(!Auth::user()->hasRole([config('permissions.role.seller')]) && $sellers)
                            <li>
                                <strong>@trans('Comercial')</strong><br/>
                                {{ Form::select('seller', array('' => __('Todos')) + $sellers, Request::has('seller') ? Request::get('seller') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                            </li>
                        @endif
                        <li>
                            <strong>@trans('Tipo')</strong><br/>
                            {{ Form::select('prospect', array('' => __('Todos'), '1' => __('Prospect'), '0' => __('Clientes')), Request::has('prospect') ? Request::get('prospect') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>@trans('Cliente')</strong><br class="visible-xs"/>
                            {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
                        </li>
                    </ul>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-60px">@trans('Data')</th>
                                <th>@trans('Cliente')</th>
                                <th class="w-150px">@trans('Detalhes')</th>
                                <th class="w-120px">@trans('Objetivos')</th>
                                <th class="w-120px">@trans('Acontecimentos')</th>
                                <th class="w-120px">@trans('Cobranças')</th>
                                <th class="w-1">@trans('Estado')</th>
                                <th class="w-65px">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.meetings.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar')</button>
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
            stateSave: false,
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'date', name: 'date'},
                {data: 'customer_id', name: 'customer_id'},
                {data: 'seller_id', name: 'seller_id'},
                {data: 'objectives', name: 'objectives', orderable: false, searchable: false},
                {data: 'occurrences', name: 'occurrences', orderable: false, searchable: false},
                {data: 'charges', name: 'charges', orderable: false, searchable: false},
                {data: 'status', name: 'status'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},

            ],
            order: [[2, "desc"]],
            ajax: {
                url: "{{ route('admin.meetings.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.agency   = $('select[name=agency]').val()
                    d.prospect = $('select[name=prospect]').val();
                    d.seller   = $('select[name=seller]').val();
                    d.status   = $('select[name=status]').val();
                    d.date_min = $('input[name=date_min]').val();
                    d.date_max = $('input[name=date_max]').val();
                    d.customer = $('select[name=dt_customer]').val();
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
            ajax: Init.select2Ajax("{{ route('admin.meetings.search.customer') }}")
        });
</script>
@stop