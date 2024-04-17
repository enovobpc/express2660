@section('title')
    {{ trans('account/refunds.title') }} -
 @stop

 @section('account-content')
 @if($totalUnconfirmed > 0)
     <h4 class="text-red m-b-25 m-t-0">
         <i class="fas fa-exclamation-triangle"></i> {{ trans('account/refunds.confirm.alert.title', ['total' => $totalUnconfirmed]) }}<br/>
        <small class="text-muted">{!! trans('account/refunds.confirm.alert.message') !!}</small>
    </h4>
@endif

<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    <li>
        <a href="{{ route('account.refunds.selected.print', Request::all()) }}"
           target="_blanl"
           class="btn btn-sm btn-black"
           data-toggle="export-url">
            <i class="fas fa-print"></i> {{ trans('account/global.word.print-list') }}
        </a>
    </li>
    <li>
        <a href="{{ route('account.refunds.selected.export', Request::all()) }}"
           class="btn btn-sm btn-default"
           data-toggle="export-url">
            <i class="fas fa-file-excel"></i> {{ trans('account/global.word.export-list') }}
        </a>
    </li>
    <li>
        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
            <i class="fas fa-filter"></i> {{ trans('account/global.word.filter') }} <i class="fas fa-angle-down"></i>
        </button>
    </li>
</ul>
<div class="datatable-filters-extended m-t-0 hide {{ Request::has('filter') ? ' active' : null }}" data-target="#datatable">
    <ul class="list-inline pull-left">
        <li style="width: 230px" class="input-sm">
            <strong>{{ trans('account/global.word.shipment-date') }}</strong><br/>
            <div class="input-group input-group-sm">
                {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable', 'placeholder' => 'De', 'style' => 'padding-right: 0; margin-right: -10px;']) }}
                <span class="input-group-addon">{{ trans('account/global.word.to') }}</span>
                {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable']) }}
            </div>
        </li>
        <li style="width: 230px" class="input-sm">
            <strong>{{ trans('account/refunds.word.payment-date') }}</strong><br/>
            <div class="input-group input-group-sm">
                {{ Form::text('payment_date_min', Request::has('payment_date_min') ? Request::get('payment_date_min') : null, ['class' => 'form-control datepicker filter-datatable', 'placeholder' => 'De', 'style' => 'padding-right: 0; margin-right: -10px;']) }}
                <span class="input-group-addon">{{ trans('account/global.word.to') }}</span>
                {{ Form::text('payment_date_max', Request::has('payment_date_max') ? Request::get('payment_date_max') : null, ['class' => 'form-control datepicker filter-datatable']) }}
            </div>
        </li>
        <li style="width: 100px" class="input-sm">
            <strong>{{ trans('account/global.word.status') }}</strong><br/>
            {{ Form::select('status', trans('account/refunds.filters.status'), Request::has('status') ? Request::get('status') : null, array('class' => 'form-control filter-datatable select2')) }}
        </li>
        <li style="width: 160px" class="input-sm">
            <strong>{{ trans('account/refunds.word.payment-method') }}</strong><br/>
            {{ Form::select('payment_method', ['' => trans('account/global.word.all')] + trans('admin/refunds.payment-methods') , Request::has('payment_method') ? Request::get('payment_method') : '', array('class' => 'form-control filter-datatable select2')) }}
        </li>
        <li style="width: 90px" class="input-sm">
            <strong>{{ trans('account/global.word.confirmed') }}</strong><br/>
            {{ Form::select('confirmed', trans('account/refunds.filters.confirmed'), Request::has('confirmed') ? Request::get('confirmed') : null, array('class' => 'form-control filter-datatable select2')) }}
        </li>
    </ul>
    <div class="clearfix"></div>
</div>
<div class="table-responsive w-100">
    <table id="datatable" class="table table-condensed table-hover">
        <thead>
            <tr>
                <th></th>
                <th class="w-1" style="padding-right: 0;">{{ Form::checkbox('select-all', '') }}</th>
                <th class="w-1">{{ trans('account/global.word.tracking') }}</th>
                <th class="w-70px">{{ trans('account/global.word.status') }}</th>
                <th>{{ trans('account/global.word.recipient') }}</th>
                <th class="w-70px1">{{ trans('account/global.word.reference') }}</th>
                <th class="w-50px">{{ trans('account/global.word.value') }}</th>
                @if(!Setting::get('refunds_control_customers_hide_received_column'))
                <th class="w-120px">
                    <span data-toggle="tooltip" title="Recebido na Agência">
                        {{ trans('account/global.word.received') }}
                    </span>
                </th>
                @endif
                @if(!Setting::get('refunds_control_customers_hide_paid_column'))
                <th class="w-120px">
                    <span data-toggle="tooltip" title="Devolvido pela Agência ao Cliente">
                        {{ trans('account/global.word.devolved') }}
                    </span>
                </th>
                @endif
                <th class="w-1">
                    <span data-toggle="tooltip" title="Devolução Confirmada pelo Cliente">
                        <i class="fas fa-check"></i>
                    </span>
                </th>
                <th>{{ trans('account/global.word.obs') }}</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="selected-rows-action hide m-t-10">
    <div>
        <button class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-close-shipments">
            <i class="fas fa-check"></i> {{ trans('account/refunds.word.confirm-selected') }}
        </button>
        @include('account.refunds.modals.confirm')
        <a href="{{ route('account.refunds.selected.print') }}"
           data-toggle="datatable-action-url"
           class="btn btn-sm btn-default m-l-5">
            <i class="fas fa-print"></i> {{ trans('account/global.word.print') }}
        </a>
        <a href="{{ route('account.refunds.selected.export') }}"
           data-toggle="datatable-action-url"
           class="btn btn-sm btn-default m-l-5">
            <i class="fas fa-file-excel"></i> {{ trans('account/global.word.export') }}
        </a>
    </div>
</div>

@stop

@section('scripts')
<script type="text/javascript">

    $(document).ready(function () {

        var oTable = $('#datatable').DataTable({
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'date', name: 'date'},
                {data: 'status_id', name: 'status_id', class: 'text-center'},
                {data: 'recipient_name', name: 'recipient_name'},
                {data: 'reference', name: 'reference'},
                {data: 'charge_price', name: 'charge_price'},
                @if(!Setting::get('refunds_control_customers_hide_received_column'))
                {data: 'received_method', name: 'received_method', orderable: false, searchable: false},
                @endif
                @if(!Setting::get('refunds_control_customers_hide_paid_column'))
                {data: 'payment_method', name: 'payment_method', orderable: false, searchable: false},
                @endif
                {data: 'confirmed', name: 'confirmed', orderable: false, searchable: false},
                {data: 'customer_obs', name: 'customer_obs', orderable: false, searchable: false},
                {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                {data: 'recipient_city', name: 'recipient_city', visible: false},
                {data: 'tracking_code', name: 'tracking_code', visible: false},
            ],
            order: [[2, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.refunds.datatable') }}",
                data: function (d) {
                    d.status           = $('select[name=status]').val();
                    d.date_min         = $('input[name=date_min]').val();
                    d.date_max         = $('input[name=date_max]').val();
                    d.payment_date_min = $('input[name=payment_date_min]').val();
                    d.payment_date_max = $('input[name=payment_date_max]').val();
                    d.payment_method   = $('select[name=payment_method]').val();
                    d.confirmed        = $('select[name=confirmed]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });


        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();

            $('[data-toggle="export-url"]').each(function() {
                var exportUrl = Url.removeQueryString($(this).attr('href'));
                exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
                $(this).attr('href', exportUrl);
            })
        });
    });
</script>
@stop