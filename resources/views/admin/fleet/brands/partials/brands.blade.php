<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-brands">
    <li>
        <a href="{{ route('admin.fleet.brands.create') }}" class="btn btn-success btn-sm" data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-brands" class="table table-condensed table-striped table-dashed table-hover">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-1"></th>
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
    {{ Form::open(array('route' => 'admin.fleet.brands.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i
                class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')
    </button>
    {{ Form::close() }}
</div>