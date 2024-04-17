<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-tolls">
    <li>
        <a href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-import-tolls">
            <i class="fas fa-upload"></i> @trans('Importar Via Verde')
        </a>
    </li>
    <li>
        <a href="{{ route('admin.importer.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-fw fa-upload"></i> @trans('Importador de Ficheiros Excel')
        </a>
    </li>
    {{-- <li>
        <a href="{{ route('admin.importer.index') }}" class="btn btn-success btn-sm">
            <i class="fas fa-fw fa-upload"></i> Importar
        </a>
    </li> --}}
    <li class="fltr-primary">
        <strong>@trans('Viatura')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-115px">
            {{ Form::select('tolls_vehicle', ['' => __('Todos')] + $vehicles, fltr_val(Request::all(), 'tolls_vehicle'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="fltr-primary w-260px">
        <strong>@trans('Data')</strong><br class="visible-xs"/>
        <div class="w-150px pull-left form-group-sm">
            <div class="input-group input-group-sm w-220px">
                {{ Form::text('tolls_date_min', fltr_val(Request::all(), 'tolls_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                <span class="input-group-addon">@trans('até')</span>
                {{ Form::text('tolls_date_max', fltr_val(Request::all(), 'tolls_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
            </div>
        </div>
    </li>
    <li class="fltr-primary w-0px">
        <strong>@trans('Fornecedor')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-125px">
            {{ Form::select('tolls_provider', ['' => __('Todos')] + $tollsProviders, fltr_val(Request::all(), 'tolls_provider'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
</ul>
<table id="datatable-tolls" class="table table-striped table-dashed table-hover table-condensed">
    <thead>
    <tr>
        <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
        <th></th>
        <th>@trans('Viatura')</th>
        <th class="w-80px">@trans('Data')</th>
        <th>@trans('Fornecedor')</th>
        <th>@trans('Operador Via Verde')</th>
        <th class="w-1">@trans('Portagens')</th>
        <th class="w-60px">@trans('Total')</th>
        <th class="w-1">@trans('Classe')</th>
        <th class="w-1">@trans('Ações')</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div class="selected-rows-action hide">
    {{--{{ Form::open(['route' => ['admin.fleet.tolls.selected.destroy']]) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
    {{ Form::close() }}--}}
    <div class="pull-left">
        <h4 style="margin: -2px 0 -6px 0px;
                        padding: 1px 3px 3px 9px;
                        line-height: 17px;">
            <small>@trans('Total Selecionado')</small><br/>
            <span class="dt-sum-total bold"></span><b>€</b>
        </h4>
    </div>
</div>
<div class="clearfix"></div>