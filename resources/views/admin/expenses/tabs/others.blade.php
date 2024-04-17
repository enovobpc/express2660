<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable">
    <li>
        <a href="{{ route('admin.expenses.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li>
        <a href="{{ route('admin.expenses.sort') }}"
           class="btn btn-default btn-sm"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-sort-amount-down"></i> Ordenar
        </a>
    </li>
    <li class="fltr-primary w-200px">
        <strong>Tipo</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-130px">
            {{ Form::select('type', ['' => 'Todos'] + trans('admin/expenses.types'), Request::has('type') ? Request::get('type') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable" class="table table-condensed table-striped table-dashed table-hover">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-1">Código</th>
            <th>Designação</th>
            <th class="w-120px">Tipo de Taxa</th>
            <th class="w-120px">Preço</th>
            {{--<th class="w-85px">Regras Preço</th>--}}
            {{--<th>Serviços</th>--}}
            <th>Regras Automáticas</th>
            <th class="w-150px">Configurações</th>
            <th class="w-1"><i class="fas fa-sort-amount-down"></i></th>
            <th class="w-55px">Ações</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.expenses.selected.destroy')) }}
    <button class="btn btn-sm btn-danger"
            data-action="confirm"
            data-title="Apagar selecionados">
        <i class="fas fa-trash-alt"></i> Apagar Selecionados
    </button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>