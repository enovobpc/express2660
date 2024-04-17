<div class="modal fade" id="modal-select-shipments">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Procura Manual de Envios</h4>
            </div>
            <div class="modal-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-shipments">
                    <li class="fltr-primary w-200px">
                        <strong>A. Origem</strong><br class="visible-xs"/>
                        <div class="w-130px pull-left form-group-sm">
                            {{ Form::selectMultiple('sender_agency', $agencies, fltr_val(Request::all(), 'sender_agency'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-180px">
                        <strong>Forn.</strong><br class="visible-xs"/>
                        <div class="w-130px pull-left form-group-sm">
                            {{ Form::selectMultiple('provider', $providers, fltr_val(Request::all(), 'provider'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-180px">
                        <strong>Operador</strong><br class="visible-xs"/>
                        <div class="w-105px pull-left form-group-sm">
                            {{ Form::selectMultiple('operator', $operators, fltr_val(Request::all(), 'operator'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                        </div>
                    </li>
                    <li class="fltr-primary w-215px">
                        <strong>Estado</strong><br class="visible-xs"/>
                        <div class="w-150px pull-left form-group-sm">
                            {{ Form::selectMultiple('status', $status, fltr_val(Request::all(), 'status'), ['class' => 'form-control input-sm filter-datatable select2-multiple']) }}
                        </div>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable-shipments" class="table table-condensed table-striped table-dashed table-hover">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="w-1">TRK</th>
                            <th>Remetente</th>
                            <th>Destinatário</th>
                            <th class="w-1">Serviço</th>
                            <th class="w-1">Remessa</th>
                            <th class="w-1">Estado</th>
                            <th class="w-1"></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="margin-top: -8px; margin-bottom: -10px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
