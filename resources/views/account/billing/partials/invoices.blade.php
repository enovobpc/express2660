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
    @if(@$customer->paymentCondition->name)
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
        <a href="{{ route('account.billing.invoices.print', Request::all()) }}"
           data-toggle="export-url"
           class="btn btn-sm btn-black"
           target="_blank">
            <i class="fas fa-print"></i> {{ trans('account/global.word.print-list') }}
        </a>
    </li>
    @if(Setting::get('invoice_software') != 'EnovoTms')
    <li>
        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                data-toggle="modal"
                data-target="#modal-sync-balance">
            <i class="fas fa-sync-alt"></i> {{ trans('account/global.word.update') }}
        </button>
    </li>
    @endif
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
            <th class="w-90px">{{ trans('account/global.word.total') }}</th>
            <th class="w-100px">{{ trans('account/global.word.due_date') }}</th>
            <th class="w-1">{{ trans('account/global.word.status') }}</th>
            <th class="w-1"></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
{{--<div class="selected-rows-action hide m-t-10">
    <div>
        <a href="{{ route('account.refunds.selected.export') }}" data-toggle="datatable-action-url" class="btn btn-sm btn-default">
            <i class="fas fa-print"></i> {{ trans('account/global.word.print') }}
        </a>
    </div>
</div>--}}
@if(Setting::get('invoice_software') == 'KeyInvoice')
@include('account.billing.modals.sync_balance')
@endif