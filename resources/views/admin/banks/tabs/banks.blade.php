<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-banks">
    <li>
        <a href="{{ route('admin.banks.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li>
        <a href="{{ route('admin.banks.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-sort-amount-down"></i> Ordenar
        </a>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-banks" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th class="w-120px">Empresa</th>
                <th>Designação</th>
                <th class="w-240px">Titular</th>
                <th class="w-140px">Banco</th>
                <th class="w-180px">IBAN</th>
                <th class="w-80px">ID Credor</th>
                <th class="w-1"><i class="fas fa-check-circle"></i></th>
                <th class="w-1">Pos</th>
                <th class="w-1">Pos</th>
                <th class="w-60px">Ações</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.banks.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>