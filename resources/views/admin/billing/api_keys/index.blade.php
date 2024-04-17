<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-apikeys">
    <li>
        <a href="{{ route('admin.billing.api-keys.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li class="fltr-primary w-140px">
        <strong>Ativo</strong><br class="visible-xs"/>
        <div class="w-80px pull-left form-group-sm">
            {{ Form::select('is_active', ['' => 'Todos', '1' => 'Sim', '0' => 'Não'], fltr_val(Request::all(), 'is_active'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-apikeys" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-200px">Empresa</th>
            <th class="w-200px">Designação</th>
            <th>Token</th>
            <th class="w-65px">Início</th>
            <th class="w-65px">Fim</th>
            <th class="w-1"><span data-toggle="tooltip" title="Ativa"><i class="fas fa-check-circle"></i></span></th>
            <th class="w-65px">Criado em</th>
            <th class="w-65px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.billing.api-keys.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
</div>