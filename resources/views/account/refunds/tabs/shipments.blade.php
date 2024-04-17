<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-shipments">
    <li>
        <a href="#" data-toggle="tooltip" title="Selecione um ou mais envios na lista para solicitar o seu reembolso." class="btn btn-sm btn-black btn-request-disabled" disabled>
            <i class="fas fa-euro-sign"></i> Solicitar Reembolso
        </a>
        <button class="btn btn-sm btn-black btn-request"
                style="display: none;"
                data-toggle="export-url">
            <i class="fas fa-euro-sign"></i> Solicitar Reembolso
        </button>
    </li>
    <li>
        <a href="{{ route('account.refunds.selected.print', Request::all()) }}"
           target="_blanl"
           class="btn btn-sm btn-default"
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
<div class="datatable-filters-extended m-t-0 hide {{ Request::has('filter') ? ' active' : null }}" data-target="#datatable-shipments">
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
        <li style="width: 100px" class="input-sm hide">
            <strong>{{ trans('account/global.word.status') }}</strong><br/>
            {{ Form::select('status', trans('account/refunds.filters.request-status'), Request::has('status') ? Request::get('status') : null, array('class' => 'form-control filter-datatable select2')) }}
        </li>
        <li style="width: 160px" class="input-sm">
            <strong>{{ trans('account/refunds.word.payment-method') }}</strong><br/>
            {{ Form::select('payment_method', ['' => trans('account/global.word.all')] + trans('admin/refunds.payment-methods') , Request::has('payment_method') ? Request::get('payment_method') : '', array('class' => 'form-control filter-datatable select2')) }}
        </li>
    </ul>
    <div class="clearfix"></div>
</div>
<div class="table-responsive w-100">
    <table id="datatable-shipments" class="table table-condensed table-hover">
        <thead>
        <tr>
            <th></th>
            <th class="w-1" style="padding-right: 0;">{{ Form::checkbox('select-all', '') }}</th>
            <th class="w-1">{{ trans('account/global.word.tracking') }}</th>
            <th class="w-70px">{{ trans('account/global.word.status') }}</th>
            <th>{{ trans('account/global.word.recipient') }}</th>
            <th class="w-50px">{{ trans('account/global.word.value') }}</th>
            {{--<th class="w-120px">
                    <span data-toggle="tooltip" title="Solicitado pelo Cliente">
                        {{ trans('account/global.word.requested') }}
                    </span>
            </th>
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
            <th>{{ trans('account/global.word.obs') }}</th>--}}
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="selected-rows-action hide m-t-10">
    <div>
        <button class="btn btn-sm btn-black" data-toggle="modal" data-target="#modal-request-shipments">
            <i class="fas fa-euro-sign"></i> Solicitar Reembolso
        </button>
        @include('account.refunds.modals.request')
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