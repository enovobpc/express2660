<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-models">
    <li>
        <a href="{{ route('admin.importer.models.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
    @if(Auth::user()->isAdmin())
        <li>
            <a href="#" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-autodetect-fields">
                <i class="fas fa-plus"></i> @trans('Criar Auto')
            </a>
        </li>
    @endif
    <li class="fltr-primary w-200px">
        <strong>Tipo</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-130px">
            {{ Form::select('model_type', ['' => __('Todos')] + trans('admin/importer.import_types'), Request::has('model_type') ? Request::get('model_type') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-models" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th>@trans('Modelo')</th>
            <th class="w-120px">@trans('Tipo')</th>
            <th class="w-1">@trans('Cliente')</th>
            <th class="w-1" data-toggle="tooltip" title="@trans('Disponível na área de cliente')"><i class="fa fa-user-circle"></i></th>
            <th class="w-80px">@trans('Ações')</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.importer.models.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')">
        <i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')
    </button>
    {{ Form::close() }}
</div>