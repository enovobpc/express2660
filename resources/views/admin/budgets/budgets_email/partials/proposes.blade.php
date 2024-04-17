<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-proposes">
    <li>
        <a href="{{ route('admin.budgets.proposes.create', $budget->id) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
            <i class="fas fa-envelope"></i> Novo Pedido
        </a>
    </li>
    <li>
        <a href="{{ route('admin.budgets.contacts.list') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
            <i class="fas fa-list-ul"></i> Lista de Contactos
        </a>
    </li>
</ul>
<table id="datatable-proposes" class="table table-stripe table-hover table-condensed">
    <thead>
    <tr>
        <th></th>
        <th class="w-150px">Para</th>
        <th>Mensagem</th>
        <th class="w-45px"></th>
    </tr>
    </thead>
    <tbody></tbody>
</table>