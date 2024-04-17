<div class="box no-border">
    <div class="box-body">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-history">
            <li>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-filter-datatable btn-default">
                        <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                    </button>
                </div>
            </li>
            <li class="fltr-primary w-260px">
                <strong>@trans('Data')</strong><br class="visible-xs"/>
                <div class="w-150px pull-left form-group-sm">
                    <div class="input-group input-group-sm w-220px">
                        {{ Form::text('history_date_min', fltr_val(Request::all(), 'history_date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Início'), 'autocomplete' => 'field-1']) }}
                        <span class="input-group-addon">@trans('até')</span>
                        {{ Form::text('history_date_max', fltr_val(Request::all(), 'history_date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => __('Fim'), 'autocomplete' => 'field-1']) }}
                    </div>
                </div>
            </li>
            <li class="fltr-primary w-0px">
                <strong>@trans('Tipo')</strong><br class="visible-xs"/>
                <div class="pull-left form-group-sm w-125px">
                    {{ Form::select('history_type', ['' => __('Todas')] + trans('admin/fleet.providers.types'), fltr_val(Request::all(), 'history_type'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
        </ul>
        <div class="datatable-filters-extended m-t-5 hide {{ Request::has('filter') ? 'active' : null }}" data-target="#datatable-history">
            <ul class="list-inline pull-left">
                <li style="margin-bottom: 5px;" class="col-xs-12">
                    <strong>@trans('Fornecedor')</strong><br/>
                    <div class="w-140px">
                        {{ Form::select('history_provider', ['' => __('Todos')], fltr_val(Request::all(), 'history_provider'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
                <li style="margin-bottom: 5px;" class="col-xs-12">
                    <strong>@trans('Motorista')</strong><br/>
                    <div class="w-140px">
                        {{ Form::select('history_operator', ['' => __('Todos')] + $operators, fltr_val(Request::all(), 'history_operator'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                    </div>
                </li>
            </ul>
        </div>
        <table id="datatable-history" class="table table-striped table-dashed table-hover table-condensed">
            <thead>
            <tr>
                <th class="w-1">{{ Form::checkbox('select-all', '') }}</th>
                <th></th>
                <th class="w-70px">@trans('Data')</th>
                <th class="w-70px">Km</th>
                <th class="w-150px">@trans('Tipo')</th>
                <th>@trans('Descrição')</th>
                <th>@trans('Fornecedor')</th>
                <th class="w-70px">@trans('Total')</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        {{--<div class="selected-rows-action hide">
            {{ Form::open(['route' => ['admin.fleet.history.selected.destroy']]) }}
            <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fas fa-trash-alt"></i> Apagar Selecionados</button>
            {{ Form::close() }}
        </div>--}}
    </div>
</div>