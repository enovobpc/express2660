<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-families">
    <li>
        <a href="{{ route('admin.logistic.families.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xs">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
    <li>
        <a href="{{ route('admin.logistic.families.sort') }}"
           class="btn btn-default btn-sm"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-sort-amount-down"></i> @trans('Ordenar')
        </a>
    </li>
    <li class="fltr-primary w-250px">
        <strong>@trans('Cliente')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-180px">
            {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => __('Todos'), 'data-query-text' => 'true')) }}
        </div>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-families" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th>@trans('Nome')</th>
            <th class="w-250px">@trans('Cliente')</th>
            <th class="w-1">@trans('Pos')</th>
            <th class="w-65px"></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.logistic.families.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>