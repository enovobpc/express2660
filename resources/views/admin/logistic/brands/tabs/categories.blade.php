<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-categories">
    <li>
        <a href="{{ route('admin.logistic.categories.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xs">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
    <li>
        <a href="{{ route('admin.logistic.categories.sort') }}"
           class="btn btn-default btn-sm"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-sort-amount-down"></i> @trans('Ordenar')
        </a>
    </li>
    <li class="fltr-primary w-240px">
        <strong>Cliente</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-180px">
            {{ Form::select('dt_customer',  Request::has('dt_customer') ? [''=>'', Request::get('dt_customer') => Request::get('dt_customer-text')] : [''=>''], Request::has('dt_customer') ? Request::get('dt_customer') : null, array('class' => 'form-control input-sm w-100 filter-datatable', 'data-placeholder' => __('Todos'), 'data-query-text' => 'true')) }}
        </div>
    </li>
    <li class="fltr-primary w-200px">
        <strong>@trans('Família')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-140px">
            {{ Form::selectMultiple('family', $families, Request::has('family') ? Request::get('family') : null, array('class' => 'form-control input-sm w-100 filter-datatable select2-multiple', 'data-placeholder' => 'Todos')) }}
        </div>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-categories" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th>@trans('Nome')</th>
            <th class="w-250px">@trans('Família')</th>
            <th class="w-250px">@trans('Cliente')</th>
            <th class="w-1">@trans('Pos')</th>
            <th class="w-65px"></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.logistic.categories.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>