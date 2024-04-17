@if(!Setting::get('fuel_tax'))
    <div class="alert alert-warning">
        <h4><i class="fas fa-exclamation-triangle"></i> Taxa de combustível inativa nas definições gerais</h4>
        <p class="m-0">A taxa de combustível está inativa nas configurações gerais do sistema. Ative-a para poder usar esta funcionalidade.
        <a href="{{ route('admin.settings.index', ['tab' => 'prices']) }}" target="_blank"
           class="btn btn-xs btn-primary" style="text-decoration: none">
            <i class="fas fa-cog"></i> Aceder às Definições</a>
        </p>
    </div>
@endif
<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-fuel">
    <li>
        <a href="{{ route('admin.expenses.create', ['type' => 'fuel']) }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-fuel" class="table table-condensed table-striped table-dashed table-hover">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-1">Código</th>
            <th>Designação</th>
            <th class="w-120px">Valor</th>
            <th class="w-85px">Início</th>
            <th class="w-85px">Fim</th>
            <th class="w-1">Estado</th>
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
</div>