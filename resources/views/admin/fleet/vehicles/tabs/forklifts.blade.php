<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-forklifts">
    <li>
        <a href="{{ route('admin.fleet.vehicles.create', ['type' => 'forklift']) }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
    </li>
    <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
        <i class="fas fa-filter"></i> <i class="fas fa-angle-down"></i>
    </button>
    <li class="fltr-primary w-170px">
        <strong>@trans('Estado')</strong><br class="visible-xs"/>
        <div class="pull-left form-group-sm w-110px">
            {{ Form::selectMultiple('t_status', trans('admin/fleet.vehicles.status'), fltr_val(Request::all(), 't_status'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
        </div>
    </li>
</ul>
<div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-forklifts">
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
    <table id="datatable-forklifts" class="table table-condensed table-striped table-dashed table-hover">
        <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th class="w-1"></th>
                <th class="w-1">@trans('Matrícula')</th>
                <th>@trans('Designação')</th>
                {{--<th class="w-50px">Reboque</th>--}}
               {{--  <th >Motorista</th> --}}
                <th style="width: 65px !important;">@trans('Seguro')</th>
                <th style="width: 65px !important;">@trans('IUC')</th>
                <th style="width: 65px !important;">@trans('IPO')</th>
                <th class="w-60px">Km</th>
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
    <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')">
        <i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')
    </button>
    {{ Form::close() }}
    <a href="{{ route('admin.fleet.export', 'vehicles') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
        <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
    </a>
</div>