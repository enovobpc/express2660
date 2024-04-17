<div class="datatable-filters-extended m-t-0 hide {{ Request::has('filter') ? ' active' : null }}" data-target="#datatable">
    <ul class="list-inline pull-left">
        <li style="width: 230px" class="input-sm">
            <strong>{{ trans('account/global.word.created_at') }}</strong><br/>
            <div class="input-group input-group-sm">
                {{ Form::text('date_min', Request::has('date_min') ? Request::get('date_min') : null, ['class' => 'form-control datepicker filter-datatable', 'placeholder' => 'De', 'style' => 'padding-right: 0; margin-right: -10px;']) }}
                <span class="input-group-addon">{{ trans('account/global.word.to') }}</span>
                {{ Form::text('date_max', Request::has('date_max') ? Request::get('date_max') : null, ['class' => 'form-control datepicker filter-datatable']) }}
            </div>
        </li>
        {{-- <li style="width: 130px" class="input-sm">
            <strong>{{ trans('account/global.word.category') }}</strong><br/>
            {{ Form::select('category', ['' => trans('account/global.word.all')] + trans('admin/customers_support.categories') , Request::has('category') ? Request::get('category') : '', array('class' => 'form-control filter-datatable select2')) }}
        </li>--}}
        <li style="width: 160px" class="input-sm">
            <strong>{{ trans('account/global.word.status') }}</strong><br/>
            {{ Form::select('status', ['' => trans('account/global.word.all')] + trans('admin/event-manager.status'), Request::has('status') ? Request::get('status') : null, array('class' => 'form-control filter-datatable select2')) }}
        </li> 
        <li style="width: 160px" class="input-sm">
            <strong>{{ trans('account/global.word.type') }}</strong><br/>
            {{ Form::select('types', ['' => trans('account/global.word.all')] + trans('admin/event-manager.types'), Request::has('types') ? Request::get('types') : null, array('class' => 'form-control filter-datatable select2')) }}
        </li> 
    </ul>
    <div class="clearfix"></div>
</div>