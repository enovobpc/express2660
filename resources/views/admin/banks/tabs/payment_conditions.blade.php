<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-payment-condition">
    <li>
        <a href="{{ route('admin.payment-conditions.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li>
        <a href="{{ route('admin.payment-conditions.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-sort-amount-down"></i> Ordenar
        </a>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-payment-condition" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th class="w-1">Código</th>
                <th>Designação</th>
                <th class="w-1">Dias</th>
                <th class="w-1">Vendas</th>
                <th class="w-1">Compras</th>
                <th class="w-1"><i class="fas fa-check-circle"></i></th>
                <th class="w-1">Pos.</th>
                <th class="w-60px">Ações</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.payment-conditions.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fa fa-trash"></i> Apagar Selecionados</button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>