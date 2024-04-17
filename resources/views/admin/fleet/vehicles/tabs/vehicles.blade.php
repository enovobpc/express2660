<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-vehicles">
    <li>
        <a href="{{ route('admin.fleet.vehicles.create') }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
    <li>
        <div class="btn-group btn-group-sm" role="group">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-alt"></i> @trans('Relatórios') <i class="fas fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('admin.fleet.export', ['vehicles'] + Request::all()) }}" data-toggle="export-url">
                            @trans('Exportar listagem atual')
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-print-costs-balance">
                            @trans('Imprimir Balanço')
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-print-validities">
                            @trans('Resumo de validades expirar')
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modal-export-costs-balance">
                            @trans('Balanço de Custos')
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('admin.fleet.vehicles.set.auto-names') }}" data-method="post"
                           data-confirm-title="@trans('Atribuir nomes automático')"
                           data-confirm-label="@trans('Atribuir')"
                           data-confirm-class="btn-success"
                           data-confirm="@trans('Pretende atribuir nomes automatiamente a todas as viaturas?<br/>O nome terá o formato: AA 00 AA | Nome Marca')">
                           @trans('Nomes Automáticos')
                        </a>
                    </li>

                </ul>
            </div>
            <button type="button" class="btn btn-filter-datatable btn-default">
                <i class="fas fa-filter"></i> <i class="fas fa-angle-down"></i>
            </button>
        </div>
    </li>
    <li class="fltr-primary w-150px">
        <strong>@trans('Tipo')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-110px">
            {{ Form::select('type', ['' => __('Todas')] + trans('admin/fleet.vehicles.types'),fltr_val(Request::all(), 'type'), array('class' => 'form-control input-sm filter-datatable select2')) }}
        </div>
    </li>
    <li class="fltr-primary w-170px">
        <strong>@trans('Estado')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-110px">
            {{ Form::selectMultiple('vstatus', trans('admin/fleet.vehicles.status'), fltr_val(Request::all(), 'vstatus'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-vehicles">
    <ul class="list-inline pull-left">
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>@trans('Marca')</strong><br/>
            <div class="w-140px">
                {{ Form::select('brand', ['' => __('Todos')] + $brands, fltr_val(Request::all(), 'brand'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="margin-bottom: 5px;" class="col-xs-12">
            <strong>@trans('Motorista')</strong><br/>
            <div class="w-140px">
                {{ Form::select('operator', ['' => __('Todos')] + $operators, fltr_val(Request::all(), 'operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
            </div>
        </li>
        <li style="width: 145px">
            <div class="checkbox p-t-22">
                <label>
                    {{ Form::checkbox('hide_inactive', 1, Request::has('hide_inactive') ? Request::get('hide_inactive') : 1 ) }}
                    @trans('Ocultar Inativos')
                </label>
            </div>
        </li>
    </ul>
</div>
<div class="table-responsive">
    <table id="datatable-vehicles" class="table table-condensed table-striped table-dashed table-hover">
        <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th class="w-1"></th>
                <th class="w-1">@trans('Matrícula')</th>
                <th class="w-300px">@trans('Designação')</th>
                <th class="w-60px">@trans('Tipo')</th>
                {{--<th class="w-50px">Reboque</th>--}}
                <th>@trans('Motorista')</th>
                <th style="width: 65px !important;">@trans('Seguro')</th>
                <th style="width: 65px !important;">@trans('IUC')</th>
                <th style="width: 65px !important;">@trans('IPO')</th>
                @if (app_mode_cargo())
                <th style="width: 65px !important;">@trans('Tacógrafo')</th>
                @endif
                <th class="w-60px">Km</th>
                <th class="w-120px">@trans('Localização')</th>
                <th class="w-65px"><i class="fas fa-gas-pump"></i> L/100</th>
                {{--<th class="w-70px"><i class="fas fa-tachometer-alt"></i> Km/h</th>--}}
                <th class="w-1">@trans('Estado')</th>
                <th class="w-80px">@trans('Ações')</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="selected-rows-action hide">
    {{ Form::open(array('route' => 'admin.fleet.vehicles.selected.destroy')) }}
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados"')'>
        <i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')
    </button>
    {{ Form::close() }}
    <a href="{{ route('admin.fleet.export', 'vehicles') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
        <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
    </a>
</div>