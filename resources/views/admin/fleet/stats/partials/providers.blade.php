<div class="box no-border">
    <div class="box-header">
        <h4 class="box-title">@trans('Gastos por Fornecedor')</h4>
    </div>
    <div class="box-body m-t-0" style="min-height: 420px">
        <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-providers">
            <li class="fltr-primary w-180px">
                <strong>@trans('Tipo')</strong><br class="visible-xs"/>
                <div class="pull-left form-group-sm w-145px">
                    {{ Form::select('status', ['' => __('Todos')] + trans('admin/fleet.providers.types'), fltr_val(Request::all(), 'status', 1), array('class' => 'form-control input-sm filter-datatable select2')) }}
                </div>
            </li>
        </ul>
        <table class="table" id="datatable-providers">
            <thead>
                <tr>
                    <th class="w-1"></th>
                    <th>@trans('Fornecedor')</th>
                    <th class="text-center w-1">@trans('Total')</th>
                    <th class="text-center w-1">@trans('Gasto')</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4"><i class="fas fa-spin fa-circle-notch"></i> @trans('A carregar...')</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>