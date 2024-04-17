@section('title')
    Devoluções
@stop

@section('content-header')
    Devoluções
@stop

@section('breadcrumb')
    <li class="active">Devoluções</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
               <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                       <div class="btn-group btn-group-sm" role="group">
                           <button type="button" class="btn btn-filter-datatable btn-default">
                               <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                           </button>
                       </div>
                    </li>
                   <li class="fltr-primary w-150px">
                       <strong>Conferido</strong><br class="visible-xs"/>
                       <div class="w-80px pull-left form-group-sm">
                           {{ Form::select('conferred', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], Request::has('conferred') ? Request::get('conferred') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                       </div>
                   </li>
                   <li class="fltr-primary w-230px">
                       <strong>Cliente</strong><br class="visible-xs"/>
                       <div class="w-180px pull-left form-group-sm">
                           {{ Form::select('customer',  Request::has('customer') ? [''=>'', Request::get('customer') => Request::get('customer-text')] : [''=>''], Request::has('customer') ? Request::get('customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
                       </div>
                   </li>
                   <li class="fltr-primary w-300px">
                       <strong>Devolução</strong><br class="visible-xs"/>
                       <div class="pull-left input-group input-group-sm w-220px">
                           {{ Form::text('dev_date_min', fltr_val(Request::all(), 'dev_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                           <span class="input-group-addon">até</span>
                           {{ Form::text('dev_date_max', fltr_val(Request::all(), 'dev_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                       </div>
                   </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li style="margin-bottom: 5px;" class="form-group-sm hidden-xs col-xs-12">
                            <strong>Filtar Data</strong><br/>
                            <div class="w-100px m-r-4" style="position: relative; z-index: 5;">
                                {{ Form::select('date_unity', ['' => 'Data Envio'], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                            </div>
                        </li>
                        <li class="shp-date col-xs-12">
                            <strong>Data</strong><br/>
                            <div class="input-group input-group-sm w-220px">
                                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                                <span class="input-group-addon">até</span>
                                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Serviço</strong><br/>
                            <div class="w-140px">
                                {{ Form::selectMultiple('service', $services, Request::has('service') ? Request::get('service') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Fornecedor</strong><br/>
                            <div class="w-140px">
                                {{ Form::selectMultiple('provider', $providers, Request::has('provider') ? Request::get('provider') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-6">
                            <strong>Motorista</strong><br/>
                            <div class="w-150px">
                                {{ Form::selectMultiple('operator', ['not-assigned' => 'Sem operador'] + $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-12">
                            <strong>Agência Origem</strong><br/>
                            <div class="w-150px">
                                {{ Form::selectMultiple('sender_agency', $agencies, fltr_val(Request::all(), 'sender_agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;" class="col-xs-12">
                            <strong>Agência Destino</strong><br/>
                            <div class="w-150px">
                                {{ Form::selectMultiple('recipient_agency',$agencies, fltr_val(Request::all(), 'recipient_agency'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        @if(Setting::get('shipments_limit_search'))
                            <li style="width: 130px">
                                <div class="checkbox" style="margin-top: 20px">
                                    <label>
                                        {{ Form::checkbox('limit_search', 1, Request::has('limit_search') ? Request::get('limit_search') : true) }}
                                        Últimos {{ Setting::get('shipments_limit_search') }} meses
                                    </label>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table w-100 table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-90px">TRK</th>
                                <th>Remetente</th>
                                <th>Destinatário</th>
                                <th class="w-70px">Fornecedor</th>
                                <th class="w-70px">Remessa</th>
                                <th class="w-70px">Data Envio</th>
                                <th class="w-70px">Devolução</th>
                                <th class="w-1"><i class="fas fa-check-circle"></i></th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    <div>
                        <a href="#" class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-confirm-selected-shipments">
                            <i class="fas fa-check-circle"></i> Conferir
                        </a>
                        @include('admin.refunds.devolutions.modals.confirm_shipments')
                        <a href="{{ route('admin.printer.refunds.devolutions.summary') }}" data-toggle="datatable-action-url" target="_blank" class="btn btn-sm btn-default">
                            <i class="fas fa-print"></i> Imprimir Listagem
                        </a>
                        <a href="{{ route('admin.export.shipments') }}" data-toggle="datatable-action-url" target="_blank" class="btn btn-sm btn-default">
                            <i class="fas fa-file-excel"></i> Exportar
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
                {data: 'tracking_code', name: 'tracking_code', visible: false},
                {data: 'id', name: 'id'},
                {data: 'sender_name', name: 'sender_name'},
                {data: 'recipient_name', name: 'recipient_name'},
                {data: 'provider_id', name: 'provider_id', searchable: false, class:'text-center'},
                {data: 'volumes', name: 'volumes', orderable: false, searchable: false},
                {data: 'date', name: 'date', searchable: false, class:'text-center'},
                {data: 'last_history.created_at', name: 'last_history.created_at', searchable: false, class: 'text-center'},
                {data: 'devolution_conferred', name: 'devolution_conferred', searchable: false, class:'text-center'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[7, "desc"]],
            ajax: {
                url: "{{ route('admin.devolutions.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.payment_method    = $('select[name=payment_method]').val();
                    d.payment_date      = $('input[name=payment_date]').val();
                    d.customer          = $('select[name=customer]').val();
                    d.conferred         = $('select[name=conferred]').val();
                    d.date_unity        = $('select[name=date_unity]').val();
                    d.date_min          = $('input[name=date_min]').val();
                    d.date_max          = $('input[name=date_max]').val();
                    d.dev_date_min      = $('input[name=dev_date_min]').val();
                    d.dev_date_max      = $('input[name=dev_date_max]').val();
                    d.service           = $('select[name=service]').val();
                    d.provider          = $('select[name=provider]').val();
                    d.operator          = $('select[name=operator]').val();
                    d.limit_search      = $('input[name=limit_search]:checked').length;
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

    $("select[name=assign_customer_id], select[name=customer], select[name=webservice_sync_customer]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
    });
</script>
@stop