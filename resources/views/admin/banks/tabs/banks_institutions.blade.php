<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-banks-institutions">
    <li>
        <a href="{{ route('admin.banks-institutions.create') }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li class="fltr-primary w-200px">
        <strong>País</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-130px">
            {{ Form::select('institution_country', ['' => 'Todos'] + trans('country'), Request::has('institution_country') ? Request::get('institution_country') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<div class="table-responsive">
    <table id="datatable-banks-institutions" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th class="w-50px">País</th>
                <th class="w-50px">Banco</th>
                <th>Designação Oficial</th>
                <th class="w-140px">BIC/SWIFT</th>
                <th class="w-65px">Criado em</th>
                <th class="w-1"><i class="fas fa-check-circle"></i></th>
                <th class="w-60px">Ações</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.banks-institutions.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}
    <div class="clearfix"></div>
</div>