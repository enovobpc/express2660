@section('title')
    Gerir Pagamentos Automáticos
@stop

@section('content-header')
    Gerir Pagamentos Automáticos
@stop

@section('breadcrumb')
    <li class="active">Gerir Pagamentos Automáticos</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                    <li>
                        <a href="{{ route('admin.gateway.payments.create') }}" class="btn btn-success btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-xs">
                            <i class="fas fa-plus"></i> Criar Pagamento
                        </a>
                    </li>
                    @if(hasModule('account_wallet'))
                    <li>
                        <a href="{{ route('admin.gateway.payments.wallet.edit') }}" class="btn btn-default btn-sm"
                           data-toggle="modal"
                           data-target="#modal-remote-xs">
                            <i class="fas fa-wallet"></i> Gerir Saldo Conta
                        </a>
                    </li>
                    @endif
                    <li>
                        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                    <li class="fltr-primary w-200px">
                        <strong>Método</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-140px">
                            {{ Form::selectMultiple('method', trans('admin/billing.gateway-payment-methods'), fltr_val(Request::all(), 'method'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-150px">
                        <strong>Estado</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-100px">
                            {{ Form::selectMultiple('status', trans('admin/billing.gateway-payment-status'), fltr_val(Request::all(), 'status'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-130px">
                        <strong>Pago</strong><br class="visible-xs"/>
                        <div class="pull-left form-group-sm w-80px">
                            {{ Form::select('paid', [''=>'Todos', '1'=>'Pago', '0' => 'Não Pago'], fltr_val(Request::all(), 'paid'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li class="col-xs-12">
                            <strong>Data</strong><br/>
                            <div class="input-group input-group-sm w-220px">
                                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                                <span class="input-group-addon">até</span>
                                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Operador</strong><br/>
                            <div class="w-160px">
                                {{ Form::selectMultiple('operator', [''=>''], fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                            </div>
                        </li>
                        <li style="margin-bottom: 5px;"  class="col-xs-6">
                            <strong>Cliente</strong><br/>
                            <div class="w-250px">
                                {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => 'Todos', 'data-query-text' => 'true')) }}
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
                                <th class="w-1">Método</th>
                                <th class="w-60px">ID</th>
                                <th  class="w-200px">Descrição</th>
                                <th class="w-40px">Valor</th>
                                <th class="w-100px">Referência</th>
                                <th>Dados Pagamento</th>
                                <th class="w-1">Estado</th>
                                <th class="w-1">Gateway</th>
                                <th class="w-80px">Criado em</th>
                                <th class="w-80px">Expira em</th>
                                <th class="w-80px">Pago em</th>
                                <th class="w-60px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    {{ Form::open(array('route' => 'admin.gateway.payments.selected.destroy')) }}
                    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar</button>
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
                {data: 'method', name: 'method', class:'text-center', orderable: false, searchable: false},
                {data: 'code', name: 'code'},
                {data: 'description', name: 'description'},
                {data: 'value', name: 'value', class:'text-center'},
                {data: 'reference', name: 'reference'},
                {data: 'payment_details', name: 'payment_details', orderable: false, searchable: false},
                {data: 'status', name: 'status', class:'text-center', orderable: false, searchable: false},
                {data: 'gateway', name: 'gateway'},
                {data: 'created_at', name: 'created_at'},
                {data: 'expires_at', name: 'expires_at'},
                {data: 'paid_at', name: 'paid_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'mb_entity', name: 'mb_entity', visible: false},
                {data: 'mb_reference', name: 'mb_reference', visible: false},
                {data: 'mbway_phone', name: 'mbway_phone', visible: false},
                {data: 'cc_first_name', name: 'cc_first_name', visible: false},
                {data: 'cc_last_name', name: 'cc_last_name', visible: false}
            ],
            ajax: {
                url: "{{ route('admin.gateway.payments.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.method    = $('select[name=method]').val();
                    d.status    = $('select[name=status]').val();
                    d.customer  = $('select[name=dt_customer]').val();
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

        $("select[name=dt_customer]").select2({
            minimumInputLength: 2,
            allowClear: true,
            ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
        });
    });
</script>
@stop