<div class="box no-border">
    <div class="box-body">
        {{--<h4 class="form-divider no-border" style="margin-top: -8px; margin-bottom: 20px;">
            <i class="fas fa-fw fa-exchange-alt"></i> Movimentações
        </h4>--}}
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-history">
            <li class="fltr-primary w-140px">
                <strong>@trans('Ação')</strong><br class="visible-xs"/>
                <div class="w-100px pull-left form-group-sm">
                    {{ Form::select('action', ['' => __('Todos')] + trans('admin/logistic.history.actions'), fltr_val(Request::all(), 'action'), ['class' => 'form-control input-sm filter-datatable select2']) }}
                </div>
            </li>
            <li class="fltr-primary w-155px">
                <strong>@trans('Origem')</strong><br class="visible-xs"/>
                <div class="w-100px pull-left form-group-sm">
                    {{ Form::select('source', ['' => __('Todos')] + $locations, fltr_val(Request::all(), 'source'), ['class' => 'form-control input-sm filter-datatable select2']) }}
                </div>
            </li>
            <li class="fltr-primary w-155px">
                <strong>@trans('Destino')</strong><br class="visible-xs"/>
                <div class="w-100px pull-left form-group-sm">
                    {{ Form::select('action', ['' => __('Todos')] + $locations, fltr_val(Request::all(), 'action'), ['class' => 'form-control input-sm filter-datatable select2']) }}
                </div>
            </li>
            <li class="fltr-primary w-170px">
                <strong>@trans('Utilizador')</strong><br class="visible-xs"/>
                <div class="w-100px pull-left form-group-sm">
                    {{ Form::select('operator', ['' => __('Todos')] + $operators, fltr_val(Request::all(), 'operator'), ['class' => 'form-control input-sm filter-datatable select2']) }}
                </div>
            </li>
        </ul>
        <table id="datatable-history" class="table table-condensed table-striped table-dashed table-hover" style="width: 100%">
            <thead>
                <tr>
                    <th class="w-1">@trans('Ação')</th>
                    <th class="w-120px">@trans('Data')</th>
                    <th>@trans('Origem')</th>
                    <th>@trans('Destino')</th>
                    <th class="w-1">@trans('Stock')</th>
                    <th>@trans('Documento')</th>
                    <th>@trans('Observações')</th>
                    <th class="w-150px">@trans('Utilizador')</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>