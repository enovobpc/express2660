@section('title')
    Extrato Conta Corrente |
@stop

@section('metatags')
    <meta name="description" content="">
    <meta property="og:title" content="Extrato Conta Corrente">
    <meta property="og:description" content="">
    <meta property="og:image" content="{{ trans('seo.og-image.url') }}">
    <meta property="og:image:width" content="{{ trans('seo.og-image.width') }}">
    <meta property="og:image:height" content="{{ trans('seo.og-image.height') }}">
    <meta name="robots" content="index, follow">
@stop

@section('content')
    <header>
        <div class="container">
            <div class="row">
                <div class="col-xs-9 col-sm-4">
                    <a href="{{ route('home.index') }}">
                        <img src="{{ asset('assets/img/logo/logo_sm.png') }}" class="header-logo"/>
                    </a>
                </div>
                <div class="col-xs-3 col-sm-8">
                    <div class="btn-group btn-username pull-right" role="group">
                        <a href="{{ route('account.index') }}" class="btn btn-user">
                            <img src="{{ asset('assets/img/default/avatar.png') }}"/>
                            <span class="username hidden-xs m-t-7">
                            {{ trans('account/global.word.login') }}<br/>
                            </small>
                        </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <section class="m-b-20">
        <div class="container account-container">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="bold m-b-20">
                        Extrato Conta Corrente<br/>
                        <small>{{ @$customer->code }} - {{ @$customer->name }}</small>
                    </h3>
                </div>
                <div class="col-xs-12">
                    <div class="card card-tracking m-b-0 p-20">
                        <div class="card-body">
                            <ul class="list-inline pull-right">
                                <li class="text-muted">
                                    <span class="text-yellow" data-toggle="tooltip" title="{{ $lastBalanceDate->format('Y-m-d H:i:s') }}">
                                        <span class="balance-update-time">
                                            <i class="far fa-clock"></i> {{ $balanceDiff > 0 ? trans('account/billing.last-update.hours', ['time' => $balanceDiff]) : trans('account/billing.last-update.minutes') }}
                                        </span>
                                    </span>
                                </li>
                            </ul>
                            <ul class="list-inline" style="margin-top: -5px">
                                <li>
                                    <h3 style="margin-top: -5px;" class="fs-20">
                                        <small>{{ trans('account/billing.word.unpaid') }}</small><br/>
                                        <b class="balance-total-unpaid {{ $totalUnpaid ? 'text-red' : 'text-green' }}">{{ money($totalUnpaid, Setting::get('app_currency')) }}</b>
                                    </h3>
                                </li>
                                @if($totalExpired)
                                    <li>
                                        <h3 class="m-l-15 fs-20" style="margin-top: -5px">
                                            <small>{{ trans('account/billing.word.expired-docs') }}</small><br/>
                                            <b class="balance-total-expired {{ $totalExpired ? 'text-red' : 'text-green' }}">{{ $totalExpired }} {{ trans('account/global.word.documents') }}</b>
                                        </h3>
                                    </li>
                                @endif
                                @if(@$customer->payment_method)
                                    <li>
                                        <h3 class="m-l-15 fs-20" style="margin-top: -5px">
                                            <small>{{ trans('account/global.word.payment') }}</small><br/>
                                            <b>{{ @$customer->paymentCondition->name }}</b>
                                        </h3>
                                    </li>
                                @endif
                            </ul>
                            <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-invoices">
                                <li>
                                    <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                                        <i class="fas fa-filter"></i> {{ trans('account/global.word.filter') }} <i class="fas fa-angle-down"></i>
                                    </button>
                                </li>
                            </ul>
                            <div class="datatable-filters-extended m-t-0 hide {{ Request::has('filter') ? ' active' : null }}" data-target="#datatable-invoices">
                                <ul class="list-inline pull-left">
                                    <li style="width: 230px" class="input-sm">
                                        <strong>{{ trans('account/global.word.doc-date') }}</strong><br/>
                                        <div class="input-group input-group-sm">
                                            {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable', 'placeholder' => 'De', 'style' => 'padding-right: 0; margin-right: -10px;']) }}
                                            <span class="input-group-addon">{{ trans('account/global.word.to') }}</span>
                                            {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable']) }}
                                        </div>
                                    </li>
                                    <li class="input-sm">
                                        <strong>{{ trans('account/global.word.type') }}</strong>
                                        {{ Form::select('sense', trans('account/billing.filters.sense'), Request::has('sense') ? Request::get('sense') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                    </li>
                                    <li class="w-120px input-sm">
                                        <strong>{{ trans('account/global.word.status') }}</strong>
                                        {{ Form::select('paid', trans('account/billing.filters.paid'), Request::has('paid') ? Request::get('paid') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="table-responsive w-100">
                                <table id="datatable-invoices" class="table table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        {{--<th class="w-1" style="padding-right: 0;">{{ Form::checkbox('select-all', '') }}</th>--}}
                                        <th class="w-80px">{{ trans('account/global.word.date') }}</th>
                                        <th>{{ trans('account/global.word.document') }}</th>
                                        <th>{{ trans('account/global.word.type') }}</th>
                                        <th>{{ trans('account/global.word.reference') }}</th>
                                        <th class="w-90px">{{ trans('account/global.word.debit') }}</th>
                                        <th class="w-90px">{{ trans('account/global.word.credit') }}</th>
                                        <th class="w-100px">{{ trans('account/global.word.due_date') }}</th>
                                        <th class="w-90px">{{ trans('account/global.word.status') }}</th>
                                        <th class="w-1"></th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                {{--            @include('account.billing.modals.sync_balance')--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="loading-balance">
        <i class="fas fa-spin fa-circle-notch"></i>
        <h4>A atualizar conta corrente. Aguarde.</h4>
    </div>
    <style>
        .loading-balance {
            position: fixed;
            left: 0;
            right: 0;
            top: 71px;
            bottom: 0;
            text-align: center;
            background: rgba(255,255,255,0.7);
            z-index: 1000;
            font-size: 25px;
            padding-top: 9%;
        }
    </style>
@stop

@section('scripts')
<script>

    var oTableInvoices;

    $(document).ready(function () {
        oTableInvoices = $('#datatable-invoices').DataTable({
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'date', name: 'date'},
                {data: 'doc_serie', name: 'doc_serie'},
                {data: 'doc_type', name: 'doc_type'},
                {data: 'reference', name: 'reference'},
                {data: 'debit', name: 'total'},
                {data: 'credit', name: 'credit', orderable: false, searchable: false},
                {data: 'due_date', name: 'due_date'},
                {data: 'is_paid', name: 'is_paid'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[0, "desc"]],
            ajax: {
                type: "POST",
                url: "{{ route('account.public.balance.datatable', $hash) }}",
                data: function (d) {
                    d.date_min  = $('[name="date_min"]').val(),
                        d.date_max  = $('[name="date_max"]').val(),
                        d.sense     = $('select[name="sense"]').val(),
                        d.paid      = $('select[name="paid"]').val()
                },
                beforeSend: function () { Datatables.cancelDatatableRequest(oTableInvoices) },
                complete: function () { Datatables.complete(); },
                error: function () { Datatables.error(); }
            },
        });

        $('[data-target="#datatable-invoices"] .filter-datatable').on('change', function (e) {
            oTableInvoices.draw();
            e.preventDefault();
        });


        $.post("{{ route('account.public.balance.sync', $hash) }}", function(data){
            if(data.result) {
                Growl.success(data.feedback)
                $('.balance-total-expired').html(data.totalExpired);
                $('.balance-total-unpaid').html(data.totalUnpaid);
                oTableInvoices.draw();
            } else {
                Growl.error(data.feedback)
            }
        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $('.loading-balance').hide();
        });
    })

</script>
@stop