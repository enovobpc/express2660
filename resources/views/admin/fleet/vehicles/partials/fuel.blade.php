<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-fuel">
            <li>
                <a href="{{ route('admin.fleet.fuel.create', ['vehicle' => $vehicle->id]) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-plus"></i> @trans('Novo')
                </a>
            </li>
            <li>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.importer.index', ['type' => 'fuel']) }}" target="_blank" class="btn btn-filter-datatable btn-default">
                        <i class="fas fa-upload"></i> @trans('Importar')
                    </a>
                </div>
            </li>
            <li>
                <div class="btn-group btn-group-sm" role="group">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('admin.fleet.export', ['fuel', 'vehicle' => $vehicle->id] + Request::all()) }}"
                           class="btn btn-default"
                           data-toggle="export-url">
                            <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                        </a>
                        <button type="button" class="btn btn-filter-datatable btn-default">
                            <i class="fas fa-filter"></i> <i class="fas fa-angle-down"></i>
                        </button>
                    </div>
                </div>
            </li>
            <li class="fltr-primary w-215px">
                <strong>@trans('Data')</strong><br class="visible-xs"/>
                <div class="w-150px pull-left form-group-sm">
                    <div class="input-group input-group-sm w-220px">
                        {{ Form::text('fuel_date_min', fltr_val(Request::all(), 'fuel_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                        <span class="input-group-addon">@trans('até')</span>
                        {{ Form::text('fuel_date_max', fltr_val(Request::all(), 'fuel_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                    </div>
                </div>
            </li>
        </ul>
        <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-fuel">
            <ul class="list-inline pull-left">

                <li style="margin-bottom: 5px;" class="col-xs-12">
                    <strong>@trans('Posto')</strong><br/>
                    <div class="w-140px">
                        {{ Form::select('fuel_provider', ['' => 'Todos'] + $fuelProviders, fltr_val(Request::all(), 'fuel_provider'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-12">
                    <strong>@trans('Motorista')</strong><br/>
                    <div class="w-140px">
                        {{ Form::select('fuel_operator', ['' => __('Todos')] + $operators, fltr_val(Request::all(), 'fuel_operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-12">
                    <strong>@trans('Produto')</strong><br/>
                    <div class="w-80px">
                        {{ Form::select('fuel_product', ['' => __('Todos'), 'fuel' => __('Gasóleo'), 'adblue' => 'Adblue'], fltr_val(Request::all(), 'fuel_product'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
            </ul>
        </div>
        <table id="datatable-fuel" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-70px">@trans('Data')</th>
                    <th>@trans('Posto')</th>
                    <th>@trans('Motorista')</th>
                    <th class="w-1">@trans('Prod.')</th>
                    <th class="w-60px">Km</th>
                    <th class="w-40px">@trans('Litros')</th>
                    <th class="w-40px">@trans('Preço/L')</th>
                    <th class="w-60px">@trans('Valor')</th>
                    <th class="w-60px">@trans('Duração')</th>
                    <th class="w-60px">@trans('Consumo')</th>
                    {{--@if(hasPermission('purchase_invoices'))
                        <th class="w-65px">Fatura</th>
                    @endif--}}
                    <th class="w-80px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.fleet.fuel.selected.destroy']]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
            <div class="pull-left">
                <a href="{{ route('admin.fleet.export', 'fuel') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
                    <i class="fas fa-fw fa-file-excel"></i> @trans('Exportar')
                </a>
            </div>
            <div class="pull-left">
                <h4 style="margin: -2px 0 -6px 10px;
                        padding: 1px 3px 3px 9px;
                        border-left: 1px solid #999;
                        line-height: 17px;">
                    <small>@trans('Total Selecionado')</small><br/>
                    <span class="dt-sum-total bold"></span><b>€</b>
                </h4>
            </div>
        </div>
    </div>
</div>