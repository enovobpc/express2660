@section('title')
    {{ trans('account/shipments.title') }} -
@stop

@section('account-content')
    @include('account.partials.alert_unpaid_invoices')
    <ul class="datatable-filters list-inline hide pull-left m-0" data-target="#datatable">
        <li>
            @if($isShippingBlocked)
                <button class="btn btn-black btn-sm" disabled>
                    <i class="fas fa-plus"></i> {{ trans('account/global.word.new') }}
                </button>
            @else
                <a href="{{ route('account.pickups.create') }}" class="btn btn-black btn-sm" data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-plus"></i> {{ trans('account/global.word.new') }}
                </a>
            @endif
        </li>
        @if($auth->show_billing && !empty($auth->enabled_services) && !in_array(config('app.source'), ['baltrans']))
            <li>
                <a href="{{ route('account.budgeter.preview-prices.index') }}" class="btn btn-default btn-sm" data-toggle="modal"
                   data-target="#modal-remote-lg">
                    <i class="fas fa-calculator"></i> {{ trans('account/global.word.quote') }}
                </a>
            </li>
        @endif
        <li>
            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> {{ trans('account/global.word.filter') }} <span class="caret"></span>
            </button>
        </li>
    </ul>
    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : '' }}" data-target="#datatable">
        <ul class="list-inline pull-left">
            <li style="width: 250px" class="input-sm">
                <strong>{{ trans('account/global.word.date') }}</strong><br/>
                <div class="input-group input-group-sm">
                    {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable', 'placeholder' => 'Início']) }}
                    <span class="input-group-addon">{{ trans('account/global.word.to') }}</span>
                    {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable']) }}
                </div>
            </li>
            <li style="width: 120px" class="input-sm">
                <strong>{{ trans('account/global.word.service') }}</strong><br/>
                {{ Form::select('service', ['' => trans('account/global.word.all')] + $services, Request::has('service') ? Request::get('service') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li style="width: 120px" class="input-sm">
                <strong>{{ trans('account/global.word.status') }}</strong><br/>
                {{ Form::select('status', ['' => trans('account/global.word.all')] + $status, Request::has('status') ? Request::get('status') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li class="input-sm">
                <strong>{{ trans('account/global.word.charge') }}</strong><br/>
                {{ Form::select('charge', trans('account/shipments.filters.charge'), Request::has('charge') ? Request::get('charge') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li class="input-sm">
                <strong>{{ trans('account/global.word.sender-country') }}</strong><br/>
                {{ Form::select('filter_sender_country', ['' => trans('account/global.word.all')] + trans('country'), Request::has('filter_sender_country') ? Request::get('filter_sender_country') : null, array('class' => 'form-control filter-datatable select2')) }}
            </li>
            <li class="input-sm">
                <strong>{{ trans('account/global.word.recipient-country') }}</strong><br/>
                {{ Form::select('filter_recipient_country', ['' => trans('account/global.word.all')] + trans('country'), Request::has('filter_recipient_country') ? Request::get('filter_recipient_country') : null, array('class' => 'form-control filter-datatable select2')) }}
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
                <th class="w-1">{{ trans('account/global.word.request') }}</th>
                <th>{{ Setting::get('app_mode' == 'cargo') ? trans('account/global.word.cargo') : trans('account/global.word.pickup') }}</th>
                <th>{{ Setting::get('app_mode' == 'cargo') ? trans('account/global.word.discharge') : trans('account/global.word.recipient') }}</th>
                <th>{{ trans('account/global.word.service') }}</th>
                <th class="w-80px">{{ trans('account/global.word.status') }}</th>
                <th class="w-90px">{{ trans('account/global.word.generated-shipment') }}</th>
                @if($auth->show_billing)
                <th class="w-40px">{{ trans('account/global.word.price') }}</th>
                @endif
                <th class="w-65px"></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="selected-rows-action hide">
        <div>
            <a href="{{ route('account.export.shipments') }}"
               data-toggle="datatable-action-url"
               class="btn btn-sm btn-default m-l-0">
                <i class="fas fa-file-excel"></i> {{ trans('account/global.word.export') }}
            </a>
            <a href="{{ route('account.shipments.selected.print.pickup-manifest') }}"
                data-toggle="datatable-action-url"
                target="_blank"
                class="btn btn-sm btn-default">
                {{ trans('account/shipments.selected.pickup-manifest') }}
            </a>
        </div>
    </div>
    @include('account.shipments.modals.print')
    @include('account.shipments.modals.signature')
@stop

@section('scripts')
<script type="text/javascript">
var oTable;

    $(document).ready(function () {
        oTable = $('#datatable').DataTable({
            columns: [
                {data: 'tracking_code', name: 'tracking_code', visible: false},
                {data: 'select', name: 'select', orderable: false, searchable: false},
                {data: 'id', name: 'id'},
                {data: 'sender_name', name: 'sender_name'},
                {data: 'recipient_name', name: 'recipient_name'},
                {data: 'service_id', name: 'service_id', class: 'text-center', orderable: false, searchable: false},
                {data: 'shipping_date', name: 'shipping_date', searchable: false},
                {data: 'children_tracking_code', name: 'children_tracking_code', class: 'text-center', searchable: false, orderable: false},
                @if($auth->show_billing)
                {data: 'total_price', name: 'total_price', class: 'text-center', searchable: false, orderable: false},
                @endif
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
                {data: 'sender_zip_code', name: 'sender_zip_code', visible: false},
                {data: 'sender_city', name: 'sender_city', visible: false},
                {data: 'sender_phone', name: 'sender_phone', visible: false},
                {data: 'recipient_zip_code', name: 'recipient_zip_code', visible: false},
                {data: 'recipient_city', name: 'recipient_city', visible: false},
                {data: 'recipient_phone', name: 'recipient_phone', visible: false},
                {data: 'reference', name: 'reference', visible: false},
                {data: 'reference2', name: 'reference2', visible: false},
                {data: 'id', name: 'id', visible: false},
            ],
            order: [[2, "desc"]],
            ajax: {
                url: "{{ route('account.shipments.datatable') }}",
                type: "POST",
                data: function (d) {
                    d.pickup    = 1,
                    d.date_min  = $('input[name=date_min]').val();
                    d.date_max  = $('input[name=date_max]').val();
                    d.service   = $('select[name=service]').val();
                    d.status    = $('select[name=status]').val();
                    d.charge    = $('select[name=charge]').val();
                    d.label     = $('select[name=label]').val();
                    d.sender_country    = $('select[name=filter_sender_country]').val();
                    d.recipient_country = $('select[name=filter_recipient_country]').val();
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTable) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            }
        });

        $('.filter-datatable').on('change', function (e) {
            oTable.draw();
            e.preventDefault();

            var exportUrl = Url.removeQueryString($('[data-toggle="export-url"]').attr('href'));
            exportUrl = exportUrl + '?' + Url.getQueryString(Url.current())
            $('[data-toggle="export-url"]').attr('href', exportUrl);
        });
    });

    $(document).on('change', '[name="print_type"]',function(){
        updatePrintUrl();
    })

    $(document).on('change', '[name="print_min_date"], [name="print_max_date"]', function(e){
        e.preventDefault()
        updatePrintUrl();
    })

    function updatePrintUrl(){
        var minDate = $('[name="print_min_date"]').val();
        var maxDate = $('[name="print_max_date"]').val();
        var printType = $('[name="print_type"]:checked').val();
        var $printBtn = $('.btn-print');
        var url = $printBtn.attr('href');

        url = Url.updateParameter(url, 'min-date', minDate);
        url = Url.updateParameter(url, 'max-date', maxDate);
        url = Url.updateParameter(url, 'print-type', printType);

        $printBtn.attr('href', url);
    }

    $(document).on('click', '.btn-print', function(e){
        $('#modal-print-shipments').modal('hide');
    })

    $(document).on('change', '[name="mass_print_type"]',function(){
        var url = $('#mass-print-url').attr('href')
        var type = $(this).val();
        url = Url.updateParameter(url, 'print-type', type);

        $('#mass-print-url').attr('href', url);
    })

    /**
     * Close shipments
     */
    $('form.close-shipments').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        $submitBtn.button('loading')

        $('.close-shipments .message').hide();
        $('.close-shipments .loading').show();

        $.post($form.attr('action'), $form.serialize(), function(data){

            if(data.result) {
                oTable.draw();
                if(data.filepath) {
                    window.open(data.filepath, '_blank');
                }
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
            } else {
                $.bootstrapGrowl(data.feedback, {type: 'error', align: 'center', width: 'auto', delay: 8000});
            }

        }).error(function(){
            $.bootstrapGrowl("<i class='fas fa-exclamation-circle'></i> Ocorreu um erro interno ao fechar os envios.",
                {type: 'error', align: 'center', width: 'auto', delay: 8000});
        }).always(function(){
            $('.close-shipments .message').show();
            $('.close-shipments .loading').hide();
            $('#modal-close-shipments').modal('hide');
            $submitBtn.button('reset');
        })
    })

$('.btn-print-grouped').on('click', function (e) {
    e.preventDefault();
    var $modal = $(this).closest('.modal');
    var packing = $modal.find('[name=packing_type]').val();
    var vehicle = $modal.find('[name=vehicle]').val();
    var description = $modal.find('[name=description]').val();

    var url = $('#url-grouped-transport-guide').attr('href');

    url = Url.updateParameter(url, 'grouped', '1');
    url = Url.updateParameter(url, 'packing', packing);
    url = Url.updateParameter(url, 'vehicle', vehicle);
    url = Url.updateParameter(url, 'description', description);
    $('#url-grouped-transport-guide').attr('href', url);

    document.getElementById('url-grouped-transport-guide').click();
    $(this).closest('.modal').modal('hide');
})
</script>
@stop