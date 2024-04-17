<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-vatrates">
    <li>
        <a href="{{ route('admin.billing.vat-rates.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li>
        <a href="{{ route('admin.billing.vat-rates.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-sort-amount-down"></i> Ordenar
        </a>
    </li>
    <li class="fltr-primary w-210px">
        <strong>Empresa</strong><br class="visible-xs"/>
        <div class="w-140px pull-left form-group-sm">
            {{ Form::select('company', ['' => 'Todos'] + $companies, fltr_val(Request::all(), 'company'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-vatrates" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-1">Empresa</th>
            <th class="w-1">Código</th>
            <th>Designação</th>
            <th class="w-50px">Abrv</th>
            <th class="w-1">Classe</th>
            <th class="w-70px">Subclasse</th>
            <th class="w-140px">Zona Fiscal</th>
            <th class="w-1">Valor</th>
            <th class="w-1">Motivo</th>
            <th class="w-1"><span data-toggle="tooltip" title="Visivel Vendas">V</span></th>
            <th class="w-1"><span data-toggle="tooltip" title="Visivel Compras">C</span></th>
            <th class="w-1"><span data-toggle="tooltip" title="Ativa"><i class="fas fa-check-circle"></i></span></th>
            <th class="w-1">Cod.AT</th>
            <th class="w-1"><i class="fas fa-sort-amount-up"></i></th>
            <th class="w-65px">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.billing.vat-rates.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
</div>