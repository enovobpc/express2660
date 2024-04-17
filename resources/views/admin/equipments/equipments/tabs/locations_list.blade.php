<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-locations">
    <li>
        <a href="{{ route('admin.logistic.products.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-locations" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-150px">Armazém</th>
            <th>Localização</th>
            <th>Equipamentos</th>
            <th class="w-60px">Ações</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
{{--<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.logistic.locations.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
</div>--}}