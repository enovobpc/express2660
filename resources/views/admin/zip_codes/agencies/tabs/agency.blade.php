<ul class="datatable-filters list-inline hide pull-left"  data-target="#datatable-zipcodes-agency">
    <li>
        <a href="{{ route('admin.zip-codes.agencies.create') }}" class="btn btn-success btn-sm"
           data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-plus"></i> Novo
        </a>
    </li>
    <li>
        <a href="#" class="btn btn-default btn-sm" data-toggle="modal"
           data-target="#modal-import-zip-codes">
            <i class="fas fa-upload"></i> Carregar em Massa
        </a>
    </li>
    @if(Auth::user()->hasRole([config('permissions.role.admin')]))
        <li>
            <a href="#" class="btn btn-default btn-sm" data-toggle="modal"
               data-target="#modal-import-from-agency">
                <i class="fas fa-upload"></i> Importar de Agência
            </a>
        </li>
    @endif
    <li>
        <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
            <i class="fas fa-filter"></i> Filtrar <i class="fas fa-angle-down"></i>
        </button>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-zipcodes-agency">
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
    <table id="datatable-zipcodes-agency" class="table table-striped table-dashed table-hover table-condensed">
        <thead>
        <tr>
            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
            <th></th>
            <th class="w-50px">Código</th>
            <th class="w-1">País</th>
            <th>Localidade</th>
            <th class="w-20">Centro Logístico</th>
            <th class="w-1"><span style="white-space: nowrap" data-toggle="tooltip" title="Kms desde a agência">Kms</span></th>
            <th class="w-1"><span style="white-space: nowrap">Serviços Permitidos</span></th>
            <th class="w-1"><span style="white-space: nowrap" data-toggle="tooltip" title="Código postal local/regional">Regional</span></th>
            <th class="w-1">Fornecedor</th>
            <th class="w-1">Ações</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.zip-codes.agencies.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados">
        <i class="fas fa-trash-alt"></i> Apagar Selecionados
    </button>
    {{ Form::close() }}
    <button class="btn btn-sm btn-default m-l-5" data-toggle="modal" data-target="#modal-mass-edit">
        <i class="fas fa-pencil-alt"></i> Editar Selecionados
    </button>
    @include('admin.zip_codes.agencies.modals.mass_edit')
</div>