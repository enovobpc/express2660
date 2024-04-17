<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-maintenances">
            <li>
                <a href="{{ route('admin.fleet.maintenances.create', ['vehicle' => $vehicle->id]) }}"
                   class="btn btn-success btn-sm"
                   data-toggle="modal"
                   data-target="#modal-remote-xl">
                    <i class="fas fa-plus"></i> @trans('Novo')
                </a>
            </li>
            <li>
                <div class="btn-group btn-group-sm" role="group">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('admin.fleet.export', ['maintenances', 'vehicle' => $vehicle->id] + Request::all()) }}"
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
            <li>
                <strong>@trans('Data')</strong>
                <div class="input-group input-group-sm w-200px">
                    {{ Form::text('maintenance_date_min', fltr_val(Request::all(), 'maintenance_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início')]) }}
                    <span class="input-group-addon">@trans('até')</span>
                    {{ Form::text('maintenance_date_max', fltr_val(Request::all(), 'maintenance_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim')]) }}
                </div>
            </li>
            <li class="fltr-primary w-170px">
                <strong>@trans('Peças')</strong><br class="visible-xs"/>
                <div class="pull-left form-group-sm w-125px">
                    {{ Form::selectMultiple('maintenance_parts', $parts, fltr_val(Request::all(), 'maintenance_parts'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                </div>
            </li>
        </ul>
        <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-maintenances">
            <ul class="list-inline pull-left">
                <li style="margin-bottom: 5px;" class="col-xs-12">
                    <strong>Oficina</strong><br/>
                    <div class="w-160px">
                        {{ Form::select('maintenance_provider', ['' => __('Todos')] + $mechanicProviders, fltr_val(Request::all(), 'maintenance_provider'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-12">
                    <strong>@trans('Motorista')</strong><br/>
                    <div class="w-140px">
                        {{ Form::select('maintenance_operator', ['' => __('Todos')] + $operators, fltr_val(Request::all(), 'maintenance_operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
            </ul>
        </div>
        <table id="datatable-maintenances" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
                <tr>
                    <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                    <th></th>
                    <th class="w-65px">@trans('Data')</th>
                    <th>@trans('Manutenção')</th>
                    <th>@trans('Fornecedor')</th>
                    <th class="w-60px">Km</th>
                    <th class="w-50px">@trans('Valor')</th>
                   {{-- @if(hasPermission('purchase_invoices'))
                        <th class="w-65px">Fatura</th>
                    @endif--}}
                    <th class="w-50px">@trans('Ações')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.fleet.maintenances.selected.destroy']]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="@trans('Apagar selecionados')"><i class="fas fa-trash-alt"></i> @trans('Apagar Selecionados')</button>
            {{ Form::close() }}
            <div class="pull-left">
                <a href="{{ route('admin.fleet.export', 'maintenances') }}" class="btn btn-sm btn-default m-l-5" data-toggle="export-selected">
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