@section('title')
    Suporte ao Cliente
@stop

@section('content-header')
    <i class="fas fa-headset"></i> @trans('Suporte ao Cliente')
@stop

@section('breadcrumb')
    <li class="active">@trans('Suporte ao Cliente')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <div class="w-25 pull-right">
                    <div class="input-group w-100">
                        <div class="input-group-addon datatable-search-loading" style="    border: none;
    position: absolute;
    top: 2px;
    left: 2px;">
                            <i class="fas fa-search" style="
                                margin-left: -5px;
                                margin-top: 1px;
                                margin-top: 0;
                                z-index: 10;
                                position: absolute;"></i>
                        </div>
                        {{ Form::text('data_search', Request::has('data_search') ? Request::get('data_search') : null, ['class' => 'form-control input-sm filter-datatable', 'style' => 'font-size: 14px; padding-left: 27px;']) }}
                    </div>
                </div>
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.customer-support.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
                            <i class="fas fa-plus"></i> @trans('Novo')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.customer-support.sync.emails') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-sync-alt"></i> @trans('Sync. E-mails')
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li class="fltr-primary w-200px">
                        <strong>@trans('Categoria')</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-130px">
                            {{ Form::selectMultiple('category', trans('admin/customers_support.categories'), fltr_val(Request::all(), 'category'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-190px">
                        <strong>@trans('Estado')</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-130px">
                            {{ Form::selectMultiple('status', trans('admin/customers_support.status'), fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-200px">
                        <div class="checkbox">
                            <label>
                                {{ Form::checkbox('hide_concluded', 1, Request::has('hide_concluded') ? Request::get('hide_concluded') : 1) }}
                                @trans('Ocultar Fechados')
                            </label>
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                <ul class="list-inline pull-left">
                    <li style="margin-bottom: 5px;" class="form-group-sm hidden-xs col-xs-12">
                        <strong>@trans('Filtar Data')</strong><br/>
                        <div class="w-100px m-r-4" style="position: relative; z-index: 5;">
                            {{ Form::select('date_unity', ['' => __('Data Registo'), '2' => __('Ultimo Estado')], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="shp-date col-xs-12">
                        <strong>@trans('Data')</strong><br/>
                        <div class="input-group input-group-sm w-250px">
                            {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                            <span class="input-group-addon">@trans('até')</span>
                            {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                        </div>
                    </li>
                    <li class="w-150px">
                        <strong>@trans('Responsável')</strong><br/>
                        {{ Form::select('operator', ['' => __('Todos'), '-1' => __('Sem operador')] + $operators, Request::has('operator') ? Request::get('operator') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </li>
                </ul>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-1">@trans('Pedido')</th>
                                <th>@trans('Assunto')</th>
                                <th class="w-70px">@trans('Envio')</th>
                                <th class="w-70px">@trans('Dt. Pedido')</th>
                                <th class="w-130px">@trans('Responsável')</th>
                                <th class="w-80px">@trans('Estado')</th>
                                <th class="w-55px">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.customer-support.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('styles')
    <style>
        .MsoNormal {
            margin: 0 !important;
        }
        .popover {
            max-width: 600px !important;
        }

    </style>
@stop

@section('scripts')
<script type="text/javascript">
    var editor;
    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'code', name: 'code', visible: false},
                {data: 'id', name: 'id'},
                {data: 'subject', name: 'subject'},
                {data: 'shipment_id', name: 'shipment_id', orderable: false, searchable: false},
                {data: 'date', name: 'date'},
                {data: 'user_id', name: 'user_id'},
                {data: 'status', name: 'status', searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'name', name: 'name', visible: false},
                {data: 'email', name: 'email', visible: false},
                {data: 'message', name: 'message', visible: false},
            ],
            dom: "<'row row-0'<'col-md-9 col-sm-8 datatable-filters-area'><'col-sm-4 col-md-3'><'col-sm-12 datatable-filters-area-extended'>>" +
            "<'row row-0'<'col-sm-12'tr>>" +
            "<'row row-0'<'col-sm-5'li><'col-sm-7'p>>",
            order: [[7, "asc"]],
            ajax: {
                url: "{{ route('admin.customer-support.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.date_unity= $('select[name=date_unity]').val();
                    d.date_min  = $('input[name=date_min]').val();
                    d.date_max  = $('input[name=date_max]').val();
                    d.status    = $('select[name=status]').val();
                    d.category  = $('select[name=category]').val();
                    d.operator  = $('select[name=operator]').val();
                    d.customer  = $('select[name=customer]').val();
                    d.hide_concluded  = $('input[name=hide_concluded]:checked').length;
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        //show concluded shipments
        $(document).on('change', '[name="hide_concluded"]', function (e) {
            oTable.draw();
            e.preventDefault();

            var name = $(this).attr('name');
            var value = $(this).is(':checked');
            value = value == false ? 0 : 1;

            newUrl = Url.updateParameter(Url.current(), name, value)
            Url.change(newUrl);

        });

        $('[name="date_max"]').on( 'keyup', function () {
            oTable.filter('pedido espanha', true, true, false ).draw();
        } );

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });

        $(document).on('keyup', '[name="data_search"]', function(){
            $('.datatable-search-loading').find('i').addClass('fa-circle-notch fa-spin');

            var searchValue = $(this).val();
            searchValue = searchValue.replace(' ', '%')
            oTable.search(searchValue).draw();
        })

        //show concluded shipments
        $(document).on('change', '[name="limit_search"]', function (e) {
            oTable.draw();
            e.preventDefault();

            var name = $(this).attr('name');
            var value = $(this).is(':checked');
            value = value == false ? 0 : 1;

            newUrl = Url.updateParameter(Url.current(), name, value)
            Url.change(newUrl);
        });
    });

</script>
@stop