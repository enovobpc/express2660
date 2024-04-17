<div class="modal fade" id="modal-select-shipments">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">@trans('Procura manual de envios')</h4>
            </div>
            <div class="modal-body">
                <ul class="datatable-filters list-inline pull-left" data-target="#datatable-shipments" style="position: relative; bottom: 13px;">
                    <li class="fltr-primary w-105px">
                        <strong>@trans('Fornecedor')</strong><br class="visible-xs"/>
                        <div class="w-100px form-group-sm">
                            {{ Form::selectMultiple('provider', $providers, fltr_val(Request::all(), 'provider'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-110px">
                        <strong>@trans('Motorista')</strong><br class="visible-xs"/>
                        <div class="w-105px form-group-sm">
                            {{ Form::selectMultiple('operator', $operators, fltr_val(Request::all(), 'operator'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-120px">
                        <strong>@trans('Estado')</strong><br class="visible-xs"/>
                        <div class="w-120px form-group-sm">
                            {{ Form::selectMultiple('status', $status, fltr_val(Request::all(), 'status'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                        </div>
                    </li>
                    <li>
                        <button type="button" class=" btn btn-sm btn-filter-datatable btn-default m-l-5">
                            <i class="fas fa-filter"></i> @trans('Filtrar') <i class="fas fa-angle-down"></i>
                        </button>
                    </li>
                </ul>
                <ul class="datatable-filters-extended dtb-modal list-inline hide pull-left" data-target="#datatable-shipments">
                    <li class="form-group-sm hidden-xs col-xs-12">
                        <strong>@trans('Filtrar Data')</strong><br/>
                        <div class="w-100px m-r-4" >
                            {{ Form::select('date_unity', ['delivery' => 'Entrega', 'pickup' => 'Recolha'], fltr_val(Request::all(), 'date_unity'), array('class' => 'form-control input-sm filter-datatable select2')) }}
                        </div>
                    </li>
                    <li class="shp-date col-xs-12">
                        <strong>@trans('Data')</strong><br/>
                        <div class="input-group input-group-sm w-220px">
                            {{ Form::text('date_min', fltr_val(Request::all(), 'date_min'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                            <span class="input-group-addon">@trans('até')</span>
                            {{ Form::text('date_max', fltr_val(Request::all(), 'date_max'), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-105px" style="position: relative; bottom: 8px;">
                        <strong>@trans('Tipo Trans.')</strong><br class="visible-xs"/>
                        <div class="w-100px form-group-sm">
                            {{ Form::selectMultiple('transport_type', $transportTypes, fltr_val(Request::all(), 'transport_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-115px" style="position: relative; bottom: 8px;">
                        <strong>@trans('Rota Rec.')</strong><br class="visible-xs"/>
                        <div class="w-110px form-group-sm">
                            {{ Form::selectMultiple('pickup_route', $routes, fltr_val(Request::all(), 'transport_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-95px" style="position: relative; bottom: 8px;">
                        <strong>@trans('Rota Entrega')</strong><br class="visible-xs"/>
                        <div class="w-90px form-group-sm">
                            {{ Form::selectMultiple('delivery_route', $routes, fltr_val(Request::all(), 'transport_type'), array('class' => 'form-control input-sm filter-datatable select2-multiple')) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-180px" style="position: relative; bottom: 8px;">
                        <strong>@trans('Agencia')</strong><br class="visible-xs"/>
                        <div class="w-120px pull-left form-group-sm">
                            {{ Form::selectMultiple('sender_agency', $agencies, fltr_val(Request::all(), 'sender_agency'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                        </div>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable-shipments" class="table table-condensed table-striped table-dashed table-hover">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="w-1">TRK</th>
                            <th class="w-1">@trans('Referência')</th>
                            <th>@trans('Remetente')</th>
                            <th>@trans('Destinatário')</th>
                            <th class="w-1">@trans('Serviço')</th>
                            <th class="w-120px">@trans('Data')</th>
                            <th class="w-1">@trans('Remessa')</th>
                            <th class="w-1">@trans('Viagem')</th>
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
            </div>
        </div>
    </div>
</div>
<style>
    .dtb-modal.datatable-filters li {
        padding-left: 2px;
        padding-right: 2px;
        height: 41px;
    }
</style>
