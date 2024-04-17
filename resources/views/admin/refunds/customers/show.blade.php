<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Envios a reembolsar</h4>
</div>
<div class="modal-body">
    <div class="row row-5" style="    margin: -14px -15px 15px -15px;
    border-bottom: 1px solid #ddd;
    padding: 0 15px 10px;">
        <div class="col-sm-9">
            <h3 class="m-0"><small>Reembolso a: </small><br/>
                {{ @$customer->name }}
            </h3>
        </div>
        {{--<div class="col-sm-1 text-center">
            <h3 class="m-0"><small>Guias </small><br/>1</h3>
        </div>--}}
        <div class="col-sm-3 text-right">
            <h3 class="m-0"><small>Total ({{ $shipments->count() }} envio) </small><br/><b class="text-blue">
                {{ money($shipments->sum('charge_price'), Setting::get('app_currency')) }}</b>
            </h3>
        </div>
    </div>
    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-details">
        <li class="fltr-primary w-260px">
            <strong>Data</strong><br class="visible-xs"/>
            <div class="input-group input-group-sm w-220px">
                {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                <span class="input-group-addon">até</span>
                {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
            </div>
        </li>
        <li class="fltr-primary w-190px">
            <strong>Recebido</strong><br class="visible-xs"/>
            <div class="w-120px pull-left form-group-sm">
                {{ Form::select('received_method', [''=>'Todos'] + trans('admin/refunds.payment-methods-list'), Request::has('received_method') ? Request::get('received_method') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
    </ul>
    <div class="table-responsive">
        <table id="datatable-details" class="table table-condensed table-striped table-dashed table-hover">
            <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th class="th-standard w-90px">TRK</th>
                <th class="th-standard">Destinatário</th>
                <th class="th-standard w-75px">Data Envio</th>
                <th class="th-standard w-60px">Entrega</th>
                <th class="th-standard w-1">Valor</th>
                <th class="th-standard w-90px">Recebimento</th>
                <th class="th-standard w-170px">Observações</th>
                <th class="w-1"></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        {{--@if(Auth::user()->isAdmin() || Auth::user()->can('refunds_customers'))
            <button class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-refund-all">
                <i class="fas fa-pencil-alt"></i> Editar Reembolsos
            </button>
            <button class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-mass-refund-destroy">
                <i class="fas fa-times"></i> Cancelar Reembolsos
            </button>
            @include('admin.refunds.customers.modals.mass_edit')
            @include('admin.refunds.customers.modals.mass_delete')
        @endif--}}
    </div>
</div>
<div class="modal-footer" style="margin-top: -8px; margin-bottom: -10px;">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>

<script>
    $('.select2').select2(Init.select2());

    var oTableDetail;
    $(document).ready(function () {

        oTableDetail = $('#datatable-details').DataTable({
            columns: [
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'tracking_code', name: 'tracking_code', visible: false},
                {data: 'shipments.id', name: 'shipments.id'},
                {data: 'recipient_name', name: 'recipient_name'},
                {data: 'date', name: 'date', searchable: false, class: 'text-center'},
                {data: 'delivery_date', name: 'delivery_date', searchable: false, class: 'text-center'},
                {data: 'charge_price', name: 'charge_price', class: 'text-center'},
                {data: 'refund_control.received_date', name: 'refund_control.received_date', searchable: false},
                {data: 'refund_control.obs', name: 'obs', orderable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'provider_tracking_code', name: 'provider_tracking_code', visible: false},
                {data: 'refund_control.customer_obs', name: 'refund_control.customer_obs', visible: false},
            ],
            order: [[5, "desc"]],
            ajax: {
                url: "{{ route('admin.refunds.customers.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.customer        = "{{ $customer->id }}";
                    d.refund_status   = 2;
                    d.received_method = $(document).find('#modal-xl select[name="received_method"]').val();
                    d.received_date   = $(document).find('#modal-xl input[name=received_date]').val();
                    /*d.payment_method = $('#tab-shipments select[name=payment_method]').val();
                    d.payment_date = $('#tab-shipments input[name=payment_date]').val();
                    d.confirmed = $('#tab-shipments select[name=confirmed]').val();
                    d.customer = $('#tab-shipments select[name=customer]').val();
                    d.date_unity = $('#tab-shipments select[name=date_unity]').val();
                    d.date_min = $('#tab-shipments input[name=date_min]').val();
                    d.date_max = $('#tab-shipments input[name=date_max]').val();
                    d.shipment_status = $('#tab-shipments select[name=shipment_status]').val();
                    d.provider = $('#tab-shipments select[name=provider]').val();
                    d.operator = $('#tab-shipments select[name=operator]').val();
                    d.agency = $('#tab-shipments select[name=agency]').val();
                    d.sender_agency = $('#tab-shipments select[name=sender_agency]').val();
                    d.recipient_agency = $('#tab-shipments select[name=recipient_agency]').val();
                    d.sender_country = $('#tab-shipments select[name=sender_country]').val();
                    d.recipient_country = $('#tab-shipments select[name=recipient_country]').val();
                    d.delayed_reception = $('#tab-shipments select[name=delayed_reception]').val();*/
                },
                beforeSend: function () {
                    Datatables.cancelDatatableRequest(oTableDetail)
                },
                complete: function () {
                    Datatables.complete()
                }
            }
        });

        $('.modal .filter-datatable').on('change', function (e) {
            oTableDetail.draw();
            e.preventDefault();
        });
    });
</script>