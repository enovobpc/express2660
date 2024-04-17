<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-methods">
    <li>
        <a href="{{ route('admin.api.docs.methods.create') }}"
           class="btn btn-success btn-sm"
           data-toggle="modal"
           data-target="#modal-remote-xl">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li>
        <a href="{{ route('admin.api.docs.methods.sort') }}" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-sort-amount-down"></i> Ordenar
        </a>
    </li>
    <li class="fltr-primary w-130px">
        <strong>Level</strong><br class="visible-xs"/>
        <div class="w-80px pull-left form-group-sm">
            {{ Form::select('level', ['' => 'Todas', 'public' => 'Public', 'partners' => 'Partners', 'mobile' => 'Mobile'], fltr_val(Request::all(), 'level'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="fltr-primary w-130px">
        <strong>Versão</strong><br class="visible-xs"/>
        <div class="w-80px pull-left form-group-sm">
            {{ Form::select('version', ['' => 'Todas'] + $versions, fltr_val(Request::all(), 'version'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="fltr-primary w-180px">
        <strong>Categoria</strong><br class="visible-xs"/>
        <div class="w-110px pull-left form-group-sm">
            {{ Form::select('category', ['' => 'Todas'] + $categories, fltr_val(Request::all(), 'category'), ['class' => 'form-control input-sm filter-datatable select2']) }}
        </div>
    </li>
    <li class="fltr-primary w-160px">
        <strong>Secção</strong><br class="visible-xs"/>
        <div class="w-100px pull-left form-group-sm">
            {{ Form::select('section', ['' => 'Todas'] + $sections, fltr_val(Request::all(), 'section'), ['class' => 'form-control input-sm filter-datatable select2']) }}
        </div>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-methods" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th class="w-1">Versão</th>
                <th>Categoria</th>
                <th>Secção</th>
                <th>Metodo</th>
                <th>Endpoint</th>
                <th>Níveis</th>
                <th class="w-1">Pos</th>
                <th class="w-60px">Ações</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.api.docs.methods.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>