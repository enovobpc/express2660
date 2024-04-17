<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-usage">
            <li>
                <a href="{{ route('admin.fleet.usages.create', ['vehicle' => $vehicle->id]) }}"
                   class="btn btn-success btn-sm"
                   data-toggle="modal"
                   data-target="#modal-remote">
                    <i class="fas fa-plus"></i> @trans('Registar')
                </a>
            </li>
            <li>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.fleet.export', ['usages', 'fleet' => $vehicle->id] + Request::all()) }}"
                       class="btn btn-default"
                       data-toggle="export-url">
                        <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                    </a>
                </div>
            </li>
            <li>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-filter-datatable btn-default">
                        <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                    </button>
                </div>
            </li>
            <li class="fltr-primary w-215px">
                <strong>@trans('Data')</strong><br class="visible-xs"/>
                <div class="w-150px pull-left form-group-sm">
                    <div class="input-group input-group-sm w-220px">
                        {{ Form::text('usage_date_min', fltr_val(Request::all(), 'usage_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                        <span class="input-group-addon">@trans('até')</span>
                        {{ Form::text('usage_date_max', fltr_val(Request::all(), 'usage_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                    </div>
                </div>
            </li>
           
        </ul>
        <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-usage">
            <ul class="list-inline pull-left">
                <li style="margin-bottom: 5px;" class="col-xs-6">
                    <strong>@trans('Motorista')</strong><br/>
                    <div class="w-140px">
                        {{ Form::select('usage_operator', ['' => __('Todos')] + $operators, fltr_val(Request::all(), 'usage_operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-6">
                    <strong>@trans('Tipo')</strong><br class="visible-xs"/>
                    <div class="w-140px">
                        {{ Form::select('type', ['' => __('Todas')] + $types, fltr_val(Request::all(), 'type'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
            </ul>
        </div>
        <table id="datatable-usage" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th class="w-180px">@trans('Motorista')</th>
                <th class="w-100px">@trans('Tipo')</th>
                <th class="w-110px">@trans('Início Condução')</th>
                <th class="w-70px">@trans('Km Iniciais')</th>
                <th class="w-110px">@trans('Fim Condução')</th>
                <th class="w-70px">@trans('Km Finais')</th>
                <th class="w-70px">@trans('Duração')</th>
                <th class="w-70px">@trans('Km Totais')</th>
                <th class="w-150">@trans('Serviços')</th>
                <th class="w-80px">@trans('Ações')</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.fleet.usages.selected.destroy']]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
            <div class="pull-left">
                <a href="{{ route('admin.fleet.export', 'usages') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
                    <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                </a>
            </div>
        </div>
    </div>
</div>