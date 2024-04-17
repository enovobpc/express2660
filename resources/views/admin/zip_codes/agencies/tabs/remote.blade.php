<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-zipcodes-remote">
    <li>
        <a href="{{ route('admin.zip-codes.zones.create', ['type' => 'remote']) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li>
        <a href="{{ route('admin.zip-codes.zones.sort', ['type' => 'remote']) }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-sort-amount-down"></i> Ordenar
        </a>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-zipcodes-remote" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th class="w-40px">Código</th>
                <th>Nome</th>
                <th class="w-1">País</th>
                <th>Códigos Postais</th>
                <th class="w-200px">Serviços Associados</th>
                <th class="w-1">Fornecedor</th>
                <th class="w-1">Pos</th>
                <th class="w-20px">Ações</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.zip-codes.zones.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>