<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-warehouses">
    <li>
        <a href="{{ route('admin.logistic.warehouses.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-warehouses" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-1">@trans('Código')</th>
            <th>@trans('Armazém')</th>
            <th>@trans('Morada')</th>
            <th>@trans('Contactos')</th>
            <th class="w-1">@trans('Ações')</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.logistic.warehouses.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
    {{ Form::close() }}
</div>