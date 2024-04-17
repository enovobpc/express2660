@section('title')
Pagamentos no Destino
@stop

@section('content-header')
    Pagamentos no Destino
@stop

@section('breadcrumb')
<li class="active">Pagamentos no Destino</li>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box no-border">
            <div class="box-body">
               <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
                   <li>
                        <strong>Estado</strong>
                        {{ Form::select('status', ['' => 'Todos', '2' => 'Recebido', '1' => 'Pendente'], Request::has('status') ? Request::get('status') : '1', array('class' => 'form-control input-sm filter-datatable')) }}
                    </li>
                    <li>
                        <strong>Receb.</strong>
                        {{ Form::select('type', ['' => 'Todos'] + trans('admin/shipments.charge_payment_methods'), Request::has('type') ? Request::get('type') : '', array('class' => 'form-control input-sm filter-datatable')) }}
                    </li>
                   <li>
                       <strong>Data Pag.</strong>
                       {{ Form::text('payment_date', Request::has('payment_date') ? Request::get('payment_date') : null, ['class' => 'form-control input-sm datepicker filter-datatable']) }}
                   </li>
                    <li>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
                            </button>
                        </div>
                    </li>
                </ul>
                <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
                    <ul class="list-inline pull-left">
                        <li>
                            <strong>Data</strong><br/>
                            <div class="input-group input-group-sm">
                                {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable', 'placeholder' => 'Início']) }}
                                <span class="input-group-addon">até</span>
                                {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable']) }}
                            </div>
                        </li>
                        <li>
                            <strong>Estado</strong><br/>
                            {{ Form::select('shipment_status', ['' => 'Todos'] + $status, Request::has('status') ? Request::get('status') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>A. Origem</strong><br/>
                            {{ Form::select('agency', ['' => 'Todos'] + $agencies, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>A. Destino</strong><br/>
                            {{ Form::select('recipient_agency', ['' => 'Todos'] + $myAgencies, Request::has('recipient_agency') ? Request::get('recipient_agency') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
                        <li>
                            <strong>Via</strong><br/>
                            {{ Form::select('provider', ['' => 'Todos'] + $providers, Request::has('provider') ? Request::get('provider') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                        </li>
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
                                <th class="w-80px">Criado Por</th>
                                <th class="w-1">Cliente</th>
                                <th class="w-70px">Data Envio</th>
                                <th class="w-60px">Valor</th>
                                <th class="w-120px">Recebido</th>
                                <th>Observações</th>
                                <th class="w-80px"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="selected-rows-action hide">
                    <div>
                        @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'payments_at_recipient'))
                        <button class="btn btn-sm btn-default m-r-5" data-toggle="modal" data-target="#modal-refund-all">
                            <i class="fas fa-check-circle-o"></i> Marcar como Recebido
                        </button>
                        @include('admin.payments_at_recipient.modals.refund_all')
                        @endif
                        <a href="{{ route('admin.payments-at-recipient.selected.print') }}" data-toggle="datatable-action-url" target="_blank" class="btn btn-sm btn-default">
                            <i class="fas fa-print"></i> Imprimir Listagem
                        </a>
                        <a href="#" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-assign-customer">
                            <i class="fas fa-user-plus"></i> Associar a Cliente
                        </a>
                    </div>
                    @include('admin.payments_at_recipient.modals.assign_customer')
                </div>
            </div>
        </div>
    </div>
</div>

@stop
<!---->
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
                {data: 'requested_customer.code', name: 'requested_customer.code'},
                {data: 'customer.code', name: 'customer.code'},
                {data: 'date', name: 'date', searchable: false},
                {data: 'total_price_for_recipient', name: 'total_price_for_recipient'},
                {data: 'method', name: 'method', searchable: false},
                {data: 'obs', name: 'obs', orderable: false, searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            ajax: {
                url: "{{ route('admin.payments-at-recipient.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.status = $('select[name=status]').val();
                    d.type = $('select[name=type]').val();
                    d.date_min = $('input[name=date_min]').val();
                    d.date_max = $('input[name=date_max]').val();
                    d.payment_date = $('input[name="payment_date"]').val();
                    d.agency = $('select[name=agency]').val();
                    d.recipient_agency = $('select[name=recipient_agency]').val();
                    d.provider = $('select[name=provider]').val();
                    d.shipment_status = $('select[name=shipment_status]').val();
                },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });

    $("select[name=assign_customer_id], select[name=customer], select[name=webservice_sync_customer]").select2({
        ajax: {
            url: "{{ route('admin.shipments.search.customer') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });
</script>
@stop