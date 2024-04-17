<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-requests">
    <li class="fltr-primary input-sm w-280px">
        <strong>{{ trans('account/global.word.date') }}</strong><br class="visible-xs"/>
        <div class="pull-left w-230px">
        <div class="input-group input-group-sm">
            {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable', 'placeholder' => 'De', 'style' => 'padding-right: 0; margin-right: -10px;']) }}
            <span class="input-group-addon">{{ trans('account/global.word.to') }}</span>
            {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable']) }}
        </div>
        </div>
    </li>
    <li class="fltr-primary input-sm w-230px">
        <strong>{{ trans('account/global.word.requested-method') }}</strong><br class="visible-xs"/>
        <div class="pull-left w-120px">
            {{ Form::select('requested_method', ['' => trans('account/global.word.all')] + trans('admin/refunds.refunds-methods') , Request::has('requested_method') ? Request::get('requested_method') : '', array('class' => 'form-control filter-datatable select2')) }}
        </div>
    </li>
</ul>
<div class="table-responsive w-100">
    <table id="datatable-requests" class="table table-condensed table-hover">
        <thead>
        <tr>
            <th></th>
            {{--<th></th>--}}
            <th class="w-150px">{{ trans('account/global.word.date') }}</th>
            <th class="w-80px">{{ trans('account/global.word.shipments') }}</th>
            <th class="w-80px">{{ trans('account/global.word.total') }}</th>
            <th class="w-150px">Forma Reembolso</th>
            <th>{{ trans('account/global.word.payment-method') }}</th>
            <th>{{ trans('account/global.word.payment-date') }}</th>
            <th class="w-60px">{{ trans('account/global.word.status') }}</th>
            <th class="w-20px"></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>