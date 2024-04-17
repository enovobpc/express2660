@section('title')
    @trans('Ordens de Saída')
@stop

@section('content-header')
    @trans('Ordens de Saída')
@stop

@section('breadcrumb')
<li class="active">@trans('Gestão Logística')</li>
<li class="active">@trans('Ordens de Saída')</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    @if(config('app.source') == 'activos24')
                        <li>
                            <button class="btn btn-success btn-sm" disabled>
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </button>
                        </li>
                        <li>
                            <button type="button" class="btn btn-sm btn-default" disabled>
                                <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                            </button>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('admin.logistic.shipping-orders.create') }}"
                               class="btn btn-success btn-sm"
                               data-toggle="modal"
                               data-target="#modal-remote-lg">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.logistic.shipping-orders.confirmation.create') }}"
                               class="btn btn-primary btn-sm"
                               data-toggle="modal"
                               data-target="#modal-remote-xs">
                                <i class="fas fa-barcode"></i> Picking Out
                            </a>
                        </li>

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
                                        <a href="{{ route('admin.logistic.shipping-orders.export') }}" data-toggle="export-url" target="_blank">
                                            <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                                        </a>
                                    </li>
                                </ul>

                                <button type="button" class="btn btn-filter-datatable btn-sm btn-default">
                                    <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                                </button>
                            </div>
                        </li>
                    @endif

                    <li class="fltr-primary w-160px">
                        <strong>@trans('Estado')</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-100px">
                            {{ Form::selectMultiple('status', $status, fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2-multiple', 'data-placeholder' => 'Todos')) }}
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
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>@trans('Cliente')</strong><br/>
                            <div class="w-250px">
                                {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => __('Todos'), 'data-query-text' => 'true')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-12">
                            <strong>@trans('Armazém')</strong><br/>
                            <div class="w-120px">
                                {{ Form::selectMultiple('warehouse', $warehouses, fltr_val(Request::all(), 'warehouse'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-12">
                            <strong>@trans('Agência')</strong><br/>
                            <div class="w-120px">
                                {{ Form::selectMultiple('agency', $agencies, fltr_val(Request::all(), 'agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-12">
                            <strong>@trans('Motorista')</strong><br/>
                            <div class="w-120px">
                                {{ Form::selectMultiple('operator', $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-12">
                            <strong>@trans('Fornecedor')</strong><br/>
                            <div class="w-120px">
                                {{ Form::selectMultiple('provider', $providers, fltr_val(Request::all(), 'provider'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="width: 170px; margin-top: 21px">
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('hide_concluded', 1, Request::has('hide_concluded') ? Request::get('hide_concluded') : 1) }}
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
                                <th class="w-70px">@trans('Nº Ordem')</th>
                                <th class="w-100px">@trans('Referência')</th>
                                <th>@trans('Cliente')</th>
                                <th>@trans('Destinatário')</th>
                                <th class="w-95px">@trans('Expedição')</th>
                                <th class="w-1">@trans('Art.')</th>
                                @if(config('app.source') != 'activos24')
                                <th class="w-55px"><span data-toggle="tooltip" title="Quantidade Expedida">@trans('Qtd Exp.')</span></th>
                                @endif
                                <th class="w-1">@trans('Preço')</th>
                                <th class="w-70px">@trans('Pedido')</th>
                                <th class="w-70px">@trans('Estado')</th>
                                <th class="w-70px">@trans('Criado em')</th>
                                <th class="w-1">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.logistic.shipping-orders.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar')</button>
                    {{ Form::close() }}
                    <div>
                        <a href="{{ route('admin.logistic.selected.print.wave-picking') }}"
                           data-toggle="datatable-action-url"
                           target="_blank"
                           class="btn btn-sm btn-default m-l-5">
                            <i class="fas fa-print"></i> @trans('Wave Picking')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(Request::has('edit-shipment'))
    <a href="{{ route('admin.shipments.create', ['logistic-shipping-order' => Request::get('edit-shipment')]) }}"
       data-toggle="modal"
       data-target="#modal-remote-xl"
       id="open-modal-shipment"
       style="display: none"
    ></a>
@endif
@stop

@section('scripts')
<script type="text/javascript">

    $("#modal-remote-lg").on('hidden.bs.modal', function () {
        $(this).data('bs.modal', null);
    });

    $("select[name=dt_customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.logistic.shipping-orders.search.customer') }}")
    });

    var oTable;
    $(document).ready(function () {

        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id', visible: false},
                {data: 'code', name: 'code'},
                {data: 'document', name: 'document'},
                {data: 'customer.name', name: 'customer.name', orderable: false, searchable: false},
                {data: 'shipment.recipient_name', name: 'shipment.recipient_name', orderable: false, searchable: false},
                {data: 'shipment_id', name: 'shipment_id'},
                {data: 'total_items', name: 'total_items', class: 'text-center'},
                @if(config('app.source') != 'activos24')
                {data: 'qty_satisfied', name: 'qty_satisfied', class: 'text-center'},
                @endif
                {data: 'total_price', name: 'total_price'},
                {data: 'date', name: 'date'},
                {data: 'status_id', name: 'status_id'},
                {data: 'created_at', name: 'created_at'},
                {data: 'actions', name: 'actions', class: 'text-center', orderable: false, searchable: false},
                {data: 'shipment_trk', name: 'shipment_trk', visible: false},
            ],
            order: [[10, "asc"],[2, "desc"]],
            ajax: {
                url: "{{ route('admin.logistic.shipping-orders.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.date_min  = $('[name="date_min"]').val();
                    d.date_max  = $('[name="date_max"]').val();
                    d.customer  = $('[name="dt_customer"]').val();
                    d.warehouse = $('[name="warehouse"]').val();
                    d.agency    = $('[name="agency"]').val();
                    d.status    = $('[name="status"]').val();
                    d.operator  = $('select[name="operator"]').val();
                    d.provider  = $('select[name="provider"]').val();
                    d.hide_concluded = $('input[name=hide_concluded]:checked').length;
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { 
                    Datatables.complete(); 

                    $(document).find('[data-line-color]').each(function(){
                        $(this).closest('tr').css('background', $(this).data('line-color'))
                    })
                },
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
    });

    @if(Request::has('edit-shipment'))
        $('#open-modal-shipment').trigger('click');
        newUrl = Url.removeParameter(Url.current(), 'edit-shipment')
        Url.change(newUrl);
    @endif
</script>
@stop