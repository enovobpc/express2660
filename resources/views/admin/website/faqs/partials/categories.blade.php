<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-categories">
    <li>
        <a href="{{ route('admin.website.faqs.categories.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li>
        <a href="{{ route('admin.website.faqs.categories.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-sort-amount-down"></i> Ordenar
        </a>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-categories" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th>Designação</th>
                <th class="w-1">Perguntas</th>
                <th class="w-1">Visivel</th>
                <th class="w-1">Pos.</th>
                <th class="w-70px">Criado em</th>
                <th class="w-80px">Ações</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.website.faqs.categories.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados">
        <i class="fa fa-trash"></i> Apagar Selecionados
    </button>
    {{ Form::close() }}
</div>