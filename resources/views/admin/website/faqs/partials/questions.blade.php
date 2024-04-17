<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-faqs">
    <li>
        <a href="{{ route('admin.website.faqs.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-lg">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li class="fltr-primary w-200px">
        <strong>Categoria</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-120px">
            {{ Form::select('locale', ['' => 'Todas'] + $categories, fltr_val(Request::all(), 'category'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-faqs" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th>Pergunta</th>
                <th>Categoria</th>
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
    {{ Form::open(array('route' => 'admin.website.faqs.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados">
        <i class="fa fa-trash"></i> Apagar Selecionados
    </button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>