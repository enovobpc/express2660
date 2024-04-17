<ul class="datatable-filters list-inline hide pull-left"  data-target="#datatable-zipcodes">
    <li>
        <a href="{{ route('admin.zip-codes.create') }}" class="btn btn-success btn-sm"
           data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li class="fltr-primary w-200px">
        <strong>País</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-130px">
            {{ Form::select('zp_country', ['' => 'Todos'] + trans('country'), Request::has('zp_country') ? Request::get('zp_country') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable">
    <ul class="list-inline pull-left">
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Agência</strong><br/>
            <div class="w-160px">
                {{ Form::selectMultiple('agency', $agenciesList, Request::has('agency') ? Request::get('agency') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>País</strong><br/>
            <div class="w-160px">
                {{ Form::select('country', ['' => 'Todos'] + trans('districts_codes.countries'), Request::has('country') ? Request::get('country') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Distrito</strong><br/>
            <div class="w-160px">
                {{ Form::select('district', ['' => 'Todos'] + trans('districts_codes.districts'), Request::has('district') ? Request::get('district') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Fornecedor</strong><br/>
            <div class="w-160px">
                {{ Form::selectMultiple('provider', ['-1' => 'Sem Fornecedor'] + $providersList, Request::has('provider') ? Request::get('provider') : null, array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;"  class="col-xs-6">
            <strong>Regional</strong><br/>
            <div class="w-130px">
                {{ Form::select('regional', [''=>'Todos','1'=>'Sim','0'=>'Não'], Request::has('regional') ? Request::get('regional') : null, array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
    </ul>
</div>
<div class="table-responsive">
    <table id="datatable-zipcodes" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-1">País</th>
            <th class="w-50px">Código</th>
            <th>Localidade</th>
            <th>Desig. Postal</th>
            <th class="w-1">Estado</th>
            <th>Distrito</th>
            <th>Concelho</th>
            <th>Rua</th>
            @if(Auth::user()->isAdmin())
            <th class="w-65px">Source</th>
            @endif
            <th class="w-65px">Criado em</th>
            <th class="w-1">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.zip-codes.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados">
        <i class="fas fa-trash-alt"></i> Apagar Selecionados
    </button>
    {{ Form::close() }}
</div>