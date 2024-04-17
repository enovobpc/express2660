<div class="modal" id="modal-mass-inactivate">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.customers.selected.inactivate', 'ids' => 'all'], 'method' => 'POST']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Inativar clientes sem atividade')</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-sm-8">
                        <h4 class="m-0">@trans('Inativar clientes sem atividade há mais de:')</h4>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group m-b-0 m-t-3">
                            <div class="input-group">
                                {{ Form::text('inactivate_limit_days', Setting::get('alert_max_days_without_shipments'), ['class' => 'form-control']) }}
                                <div class="input-group-addon">@trans('dias')</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">@trans('Inativar')</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>