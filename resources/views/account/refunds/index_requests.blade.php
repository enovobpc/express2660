@section('title')
    {{ trans('account/refunds.title') }} -
@stop

@section('account-content')
{{-- @if($totalUnconfirmed > 0)
     <h4 class="text-red m-b-25 m-t-0">
         <i class="fas fa-exclamation-triangle"></i> {{ trans('account/refunds.confirm.alert.title', ['total' => $totalUnconfirmed]) }}<br/>
        <small class="text-muted">{!! trans('account/refunds.confirm.alert.message') !!}</small>
    </h4>
@endif--}}
<div class="tabbable-line">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-requests" data-toggle="tab">
                Pedidos Reembolso
            </a>
        </li>
        <li>
            <a href="#tab-shipments" data-status="4" data-toggle="tab">
                Disponível reembolsar
            </a>
        </li>
        {{--<li>
            <a href="#tab-requested" data-status="5"  data-toggle="tab">
                Solicitados
            </a>
        </li>
        <li>
            <a href="#tab-refunded" data-status="3"  data-toggle="tab">
                Reembolsados
            </a>
        </li>--}}
        <li>
            <a href="#tab-pending" data-status="1" data-toggle="tab">
                Pendentes
            </a>
        </li>
    </ul>
    <div class="tab-content" style="padding-bottom: 0">
        <div class="tab-pane {{ !Request::has('tab') || Request::get('tab') == 'requests' ? 'active' : '' }}" id="tab-requests">
           @include('account.refunds.tabs.requests')
        </div>
        <div class="tab-pane {{ in_array(Request::get('tab'), ['requested', 'refunded', 'pending']) ? 'active' : '' }}" id="tab-shipments">
            @include('account.refunds.tabs.shipments')
        </div>
    </div>
</div>
@stop

@section('styles')
    <style>
        .modal-xs .col-sm-12 .select2-container{
            max-width: 100%;
        }
    </style>
@stop

@section('scripts')
<script type="text/javascript">

    $(document).on('click', '[data-status]', function(){
        $('.tab-pane').removeClass('active');
        $('#tab-shipments').addClass('active')

        var status = $(this).data('status');
        var url = Url.current();
        $('#tab-shipments [name="status"]').val(status).trigger('change');
        $('.selected-rows-action').addClass('hide');

        if(status != '4') {
            $('.btn-request').hide();
            $('.btn-request-disabled').hide();
        } else {
            $('.btn-request').hide();
            $('.btn-request-disabled').show();
        }

        url = Url.updateParameter(url, 'status', status)
        Url.change(url)
    })


    var oTableRequests, oTableShipments;

    $(document).ready(function () {
        oTableShipments = $('#datatable-shipments').DataTable({
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'date', name: 'date'},
                {data: 'status_id', name: 'status_id', class: 'text-center'},
                {data: 'recipient_name', name: 'recipient_name'},
                {data: 'charge_price', name: 'charge_price'},
                    {{-- {data: 'requested_method', name: 'requested_method', orderable: false, searchable: false},
                     @if(!Setting::get('refunds_control_customers_hide_paid_column'))
                     {data: 'payment_method', name: 'payment_method', orderable: false, searchable: false},
                     @endif
                     {data: 'confirmed', name: 'confirmed', orderable: false, searchable: false},
                     {data: 'customer_obs', name: 'customer_obs', orderable: false, searchable: false},--}}
                {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                {data: 'recipient_city', name: 'recipient_city', visible: false},
            ],
            order: [[2, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.refunds.datatable') }}",
                data: function (d) {
                    d.status           = $('#tab-shipments select[name=status]').val();
                    d.date_min         = $('#tab-shipments input[name=date_min]').val();
                    d.date_max         = $('#tab-shipments input[name=date_max]').val();
                    d.payment_date_min = $('#tab-shipments input[name=payment_date_min]').val();
                    d.payment_date_max = $('#tab-shipments input[name=payment_date_max]').val();
                    d.payment_method   = $('#tab-shipments select[name=payment_method]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableShipments) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });


        $('#tab-shipments .filter-datatable').on('change', function (e) {
            oTableShipments.draw();
            e.preventDefault();

           /* var exportUrl = Url.removeQueryString($('#tab-shipments [data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('#tab-requesshipmentsts [data-toggle="export-url"]').attr('href', exportUrl);*/
        });

        oTableRequests = $('#datatable-requests').DataTable({
            columns: [
                {data: 'id', name: 'id', visible: false},
                /*{data: 'select', name: 'select', orderable: false, searchable: false},*/
                {data: 'created_at', name: 'created_at'},
                {data: 'count_shipments', name: 'count_shipments', orderable: false, searchable: false},
                {data: 'total', name: 'total', orderable: false, searchable: false},
                {data: 'requested_method', name: 'requested_method'},
                {data: 'payment_method', name: 'payment_method'},
                {data: 'payment_date', name: 'payment_date'},
                {data: 'status', name: 'status', class: 'text-center'},
                {data: 'actions', name: 'actions', class: 'text-center', orderable: false, searchable: false},
            ],
            order: [[1, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.refunds.requests.datatable') }}",
                data: function (d) {
                    /*d.status           = $('select[name=status]').val();*/
                    d.date_min         = $('input[name=date_min]').val();
                    d.date_max         = $('input[name=date_max]').val();
                    d.requested_method = $('select[name=requested_method]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableRequests) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });


        $('#tab-requests .filter-datatable').on('change', function (e) {
            oTableRequests.draw();
            e.preventDefault();
/*
            var exportUrl = Url.removeQueryString($('#tab-requests [data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('#tab-requests [data-toggle="export-url"]').attr('href', exportUrl);*/
        });
    });

    $(document).on('change', '.row-select', function(){
        if($('[href="#tab-shipments"]').closest('li').hasClass('active')) {
            if($('.row-select:checked').length) {
                $('.btn-request').show();
                $('[data-target="#modal-request-shipments"]').removeClass('hide');
                $('.btn-request-disabled').hide();
            } else {
                $('.btn-request').hide();
                $('[data-target="#modal-request-shipments"]').addClass('hide');
                $('.btn-request-disabled').show();
            }
        } else {
            $('.btn-request').hide();
            $('.btn-request-disabled').hide();
            $('[data-target="#modal-request-shipments"]').addClass('hide');
        }
    });

    $(document).on('click', '.btn-request', function(){
        $('[data-target="#modal-request-shipments"]').trigger('click');
    });
</script>
@stop