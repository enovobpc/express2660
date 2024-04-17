<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-fixed-costs">
    <li>
        <a href="{{ route('admin.fleet.fixed-costs.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
    <li class="fltr-primary">
        <strong>@trans('Viatura')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-115px">
            {{ Form::select('costs_vehicle', ['' => __('Todos')] + $vehicles, fltr_val(Request::all(), 'costs_vehicle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<table id="datatable-fixed-costs" class="table table-striped table-dashed table-hover table-condensed">
    <thead>
    <tr>
        <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
        <th></th>
        <th>@trans('Viatura')</th>
        <th>@trans('Descrição')</th>
        <th class="w-100px">@trans('Tipo')</th>
        <th class="w-70px">@trans('Data Início')</th>
        <th class="w-70px">@trans('Data Fim')</th>
        <th class="w-80px">@trans('Valor')</th>
        <th class="w-80px">@trans('Ações')</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div class="selected-rows-action hide">
    {{ Form::open(['route' => ['admin.fleet.fixed-costs.selected.destroy']]) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>