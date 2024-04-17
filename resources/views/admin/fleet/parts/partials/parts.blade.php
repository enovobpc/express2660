<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-parts">
    <li>
        <a href="{{ route('admin.billing.items.create', ['is_fleet_part' => true]) }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
    {{-- <li class="fltr-primary w-210px">
        <strong>Categoria</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-135px">
            {{ Form::select('category', ['' => 'Todas'] + $categories, fltr_val(Request::all(), 'category'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li> --}}
    <li class="fltr-primary w-180px">
        <strong>@trans('Marca')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-130px">
            {{ Form::selectMultiple('brand', $brands, fltr_val(Request::all(), 'brand'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
    <li class="fltr-primary w-200px">
        <strong>@trans('Modelo')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-130px">
            {{ Form::selectMultiple('model', $models, fltr_val(Request::all(), 'model'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-parts" class="table table-condensed table-striped table-dashed table-hover">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-120px">@trans('Referência')</th>
            <th>@trans('Designação')</th>
            {{-- <th>Categoria</th> --}}
            <th class="w-100px">@trans('Fornecedor')</th>
            <th class="w-1">@trans('Preço')</th>
            <th class="w-70px">@trans('Stock')</th>
            <th class="w-1">@trans('Ativo')</th>
            <th class="w-20px">@trans('Ações')</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.fleet.parts.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')">
        <i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')
    </button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>