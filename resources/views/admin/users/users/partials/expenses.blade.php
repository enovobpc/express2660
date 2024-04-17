@if(!hasModule('purchase_invoices'))
    @include('admin.partials.denied_message')
@else
    <div class="box no-border">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-costs-expenses" data-toggle="tab">@trans('Despesas Gerais')</a></li>
                <li><a href="#tab-costs-fixed" data-toggle="tab">@trans('Custos Fixos')</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-costs-expenses" data-empty="1">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-expenses">
                        <li>
                            <a href="{{ route('admin.users.expenses.create', $user->id) }}"
                               class="btn btn-success btn-sm"
                               data-toggle="modal"
                               data-target="#modal-remote">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                        <li>
                            <button type="button" class="btn btn-sm btn-filter-datatable btn-default">
                                <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                            </button>
                        </li>
                        <li class="fltr-primary w-215px">
                            <strong>@trans('Data')</strong><br class="visible-xs"/>
                            <div class="w-150px pull-left form-group-sm">
                                <div class="input-group input-group-sm w-220px">
                                    {{ Form::text('expenses_date_min', fltr_val(Request::all(), 'expenses_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                                    <span class="input-group-addon">@trans('até')</span>
                                    {{ Form::text('expenses_date_max', fltr_val(Request::all(), 'expenses_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-expenses">
                        <ul class="list-inline pull-left">
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>@trans('Fornecedor')</strong><br/>
                                <div class="w-220px">
                                    {{ Form::select('expenses_provider', [''=>''], fltr_val(Request::all(), 'expenses_provider'), array('class' => 'form-control input-sm filter-datatable', 'data-placeholder' => '')) }}
                                </div>
                            </li>
                            <li style="margin-bottom: 5px;" class="col-xs-12">
                                <strong>@trans('Tipo Despesa')</strong><br/>
                                <div class="w-140px">
                                    {{ Form::selectMultiple('expenses_type', @$expensesTypes ? $expensesTypes : [], fltr_val(Request::all(), 'expenses_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
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
                            <th>@trans('Tipo Despesa')</th>
                            <th class="w-65px">@trans('Total')</th>
                            <th class="w-65px">@trans('Fatura')</th>
                            <th class="w-65px">@trans('Ações')</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="selected-rows-action hide">
                        {{--{{ Form::open(['route' => ['admin.users.expenses.selected.destroy']]) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                        {{ Form::close() }}--}}
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
                <div class="tab-pane" id="tab-costs-fixed" data-empty="1">
                    <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-expenses-fixed">
                        <li>
                            <a href="{{ route('admin.users.expenses.create', [$user->id, 'fixed' => 1]) }}"
                               class="btn btn-success btn-sm"
                               data-toggle="modal"
                               data-target="#modal-remote">
                                <i class="fas fa-plus"></i> @trans('Novo')
                            </a>
                        </li>
                    </ul>
                    <table id="datatable-expenses-fixed" class="table table-striped table-dashed table-hover table-condensed">
                        <thead>
                        <tr>
                            <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                            <th></th>
                            <th class="w-70px">@trans('Início')</th>
                            <th class="w-70px">@trans('Fim')</th>
                            <th>@trans('Despesa')</th>
                            <th>@trans('Fornecedor')</th>
                            <th>@trans('Tipo Despesa')</th>
                            <th class="w-65px">@trans('Total')</th>
                            <th class="w-65px">@trans('Ações')</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="selected-rows-action hide">
                       {{-- {{ Form::open(['route' => ['admin.users.selected.destroy']]) }}
                        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
                        {{ Form::close() }}--}}
                        <div class="pull-left">
                            <h4 style="margin: -2px 0 -6px 10px;
                    padding: 1px 3px 3px 9px;
                    border-left: 1px solid #999;
                    line-height: 17px;">
                                <small>@trans('Total Selecionado')</small><br/>
                                <span class="dt-sum-total bold"></span><b>€</b>
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif