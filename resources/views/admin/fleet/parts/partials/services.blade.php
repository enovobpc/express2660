<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-services">
    <li>
        <a href="{{ route('admin.fleet.services.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xs">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
    <li class="fltr-primary w-215px">
        <strong>@trans('Tipo')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-145px">
            {{ Form::select('type', ['' => __('Todas'), 'maintenance' => __('Serviços Manutenção'), 'expense' => __('Despesas gerais')], fltr_val(Request::all(), 'type'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-services" class="table table-condensed table-striped table-dashed table-hover">
        <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th>@trans('Designação')</th>
                <th>@trans('Tipo')</th>
                <th class="w-70px">@trans('Criado em')</th>
                <th class="w-20px">@trans('Ações')</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.fleet.services.selected.destroy')) }}
    <button class="btn btn-sm btn-danger"
            data-action="confirm"
            data-title="@trans('Apagar selecionados')">
        <i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')
    </button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>