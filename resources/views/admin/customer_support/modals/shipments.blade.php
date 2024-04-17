<div class="modal fade" id="modal-select-shipments">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Associar envio ao orçamento')</h4>
            </div>
            <div class="modal-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-shipments">
                    <li>
                        <strong>@trans('Fornecedor')</strong>
                        {{ Form::select('provider', ['' => __('Todos')] + $providers, Request::has('provider') ? Request::get('provider') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                    </li>
                    <li>
                        <strong>@trans('Estado')</strong>
                        {{ Form::select('status', ['' => __('Todos')] + $shippingStatus, Request::has('status') ? Request::get('status') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable-shipments" class="table table-condensed table-striped table-dashed table-hover">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="w-1">@trans('TRK')</th>
                            <th>@trans('Remetente')</th>
                            <th>@trans('Destinatário')</th>
                            <th class="w-1">@trans('Serviço')</th>
                            <th class="w-1">@trans('Remessa')</th>
                            <th class="w-1">@trans('Estado')</th>
                            <th class="w-1"></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="margin-top: -8px; margin-bottom: -10px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                <button type="button" class="btn btn-success btn-assign-shipment"><i class="fas fa-check"></i> @trans('Associar')</button>
            </div>
        </div>
    </div>
</div>
