<div class="box no-border">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                @if(hasPermission('fleet_expenses'))
                <li class="active"><a href="#tab-costs-expenses" data-toggle="tab">@trans('Despesas Gerais')</a></li>
                @endif
                @if(hasPermission('fleet_tolls_log'))
                <li><a href="#tab-costs-tolls" data-toggle="tab">@trans('Portagens')</a></li>
                @endif
                @if(hasPermission('fleet_fixed_costs'))
                <li><a href="#tab-costs-fixed" data-toggle="tab">@trans('Custos Fixos')</a></li>
                @endif
            </ul>
            <div class="tab-content">

                @if(hasPermission('fleet_expenses'))
                <div class="tab-pane active" id="tab-costs-expenses" data-empty="1">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-expenses">
                        <li>
                            <a href="{{ route('admin.fleet.expenses.create', ['vehicle' => $vehicle->id]) }}"
                               class="btn btn-success btn-sm"
                               data-toggle="modal"
                               data-target="#modal-remote">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                        <li>
                            <div class="btn-group btn-group-sm" role="group">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.fleet.export', ['expenses', 'vehicle' => $vehicle->id] + Request::all()) }}"
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
                                    {{ Form::text('expenses_date_min', fltr_val(Request::all(), 'expenses_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                                    <span class="input-group-addon">@trans('até')</span>
                                    {{ Form::text('expenses_date_max', fltr_val(Request::all(), 'expenses_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-expenses">
                        <ul class="list-inline pull-left">
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>@trans('Fornecedor')</strong><br/>
                                <div class="w-140px">
                                    {{ Form::select('expenses_provider', ['' => __('Todos')] + $allProviders, fltr_val(Request::all(), 'expenses_provider'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>@trans('Motorista')</strong><br/>
                                <div class="w-140px">
                                    {{ Form::select('expenses_operator', ['' => __('Todos')] + $operators, fltr_val(Request::all(), 'expenses_operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                                </div>
                            </li>
                        </ul>
                    </div>
                    <table id="datatable-expenses" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                                <th></th>
                                <th class="w-70px">@trans('Data')</th>
                                <th>@trans('Despesa')</th>
                                <th>@trans('Fornecedor')</th>
                                <th>@trans('Motorista')</th>
                                <th class="w-65px">Km</th>
                                <th class="w-65px">@trans('Total')</th>
                                <th class="w-65px">@trans('Fatura')</th>
                                <th class="w-80px">@trans('Ações')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="selected-rows-action hide">
                        {{ Form::open(['route' => ['admin.fleet.expenses.selected.destroy']]) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                        {{ Form::close() }}
                        <div class="pull-left">
                            <a href="{{ route('admin.fleet.export', 'expenses') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
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
                    <div class="clearfix"></div>
                </div>
                @endif

                @if(hasPermission('fleet_tolls_log'))
                <div class="tab-pane" id="tab-costs-tolls" data-empty="1">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-tolls">
                        <li>
                            <a href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-import-tolls">
                                <i class="fas fa-upload"></i> @trans('Importar')
                            </a>
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
                </div>
                @endif

                @if(hasPermission('fleet_fixed_costs'))
                <div class="tab-pane" id="tab-costs-fixed" data-empty="1">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-fixed-costs">
                        <li>
                            <a href="{{ route('admin.fleet.fixed-costs.create', ['vehicle' => $vehicle->id]) }}"
                               class="btn btn-success btn-sm"
                               data-toggle="modal"
                               data-target="#modal-remote">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                    </ul>
                    <table id="datatable-fixed-costs" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                        <tr>
                            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                            <th></th>
                            <th>@trans('Descrição')</th>
                            <th class="w-100px">@trans('Tipo')</th>
                            <th class="w-70px">@trans('Data Início')</th>
                            <th class="w-70px">@trans('Data Fim')</th>
                            <th class="w-80px">@trans('Valor')</th>
                            <th class="w-80px">@trans('Ações')</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="selected-rows-action hide">
                        {{ Form::open(['route' => ['admin.fleet.fixed-costs.selected.destroy']]) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
                        {{ Form::close() }}
                        <div class="clearfix"></div>
                    </div>
                </div>
                @endif
            </div>
    </div>
</div>