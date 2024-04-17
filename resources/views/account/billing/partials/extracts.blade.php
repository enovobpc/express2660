<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-extracts">
    <li class="fltr-primary input-sm w-110px">
        <strong>{{ trans('account/global.word.year') }}</strong><br class="visible-xs"/>
        <div class="pull-left w-70px">
            {{ Form::select('year', ['' => trans('account/global.word.all')] + $years, Request::get('year'), ['class' => 'form-control select2 filter-datatable']) }}
        </div>
    </li>
    <li class="fltr-primary input-sm w-130px">
        <strong>{{ trans('account/global.word.month') }}</strong><br class="visible-xs"/>
        <div class="pull-left w-95px">
            {{ Form::select('month', ['' => trans('account/global.word.all')] + trans('datetime.list-month'), Request::get('month'), ['class' => 'form-control select2 filter-datatable']) }}
        </div>
    </li>
</ul>
<div class="table-responsive w-100">
    <table id="datatable-extracts" class="table table-condensed table-hover">
        <thead>
        <tr>
            <th>{{ trans('account/global.word.date') }}</th>
            <th class="w-1">{{ trans('account/global.word.shipments') }}</th>
            <th class="w-60px">{{ trans('account/global.word.price') }}</th>
            <th class="w-60px">{{ trans('account/global.word.covenants') }}</th>
            <th class="w-60px">{{ trans('account/global.word.others') }}</th>
            <th class="w-60px">
                <span data-toggle="tooltip" title="{{ trans('account/billing.word.weight-avg') }}">
                    {{ trans('account/global.word.weight_avg') }}
                </span>
            </th>
            <th class="w-60px">
                <span data-toggle="tooltip" title="{{ trans('account/billing.word.price-avg') }}">
                    {{ trans('account/global.word.price_avg') }}
                </span>
            </th>
            <th class="w-60px">{{ trans('account/global.word.total') }}</th>
            <th class="w-1"></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<hr/>
<p class="text-center text-muted">
    <small>
        <i class="fas fa-info-circle"></i>
        {{ trans('account/billing.tip-archive') }}
    </small>
</p>