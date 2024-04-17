<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-models">
    <li>
        <a href="{{ route('admin.fleet.brand-models.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
    <li>
        <strong>@trans('Marca')</strong>
        {{ Form::select('brand', ['' => __('Todas')] + $brands, Request::has('brand') ? Request::get('brand') : null, array('class' => 'form-control input-sm filter-datatable')) }}
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-models" class="table table-condensed table-striped table-dashed table-hover">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-120px">@trans('Marca')</th>
            <th>@trans('Designação')</th>
            <th class="w-70px">@trans('Criado em')</th>
            <th class="w-20px">@trans('Ações')</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.fleet.brand-models.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i
                class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')
    </button>
    {{ Form::close() }}
</div>