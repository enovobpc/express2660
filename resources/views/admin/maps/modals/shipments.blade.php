<div class="modal fade" id="modal-select-shipments">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Criar rota a partir de envios</h4>
            </div>
            <div class="modal-body">
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-shipments">
                 
                    {{-- <li>
                        <strong>Origem</strong>
                        {{ Form::select('sender_agency', ['' => 'Todas'] + $agencies, Request::has('sender_agency') ? Request::get('sender_agency') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                    </li> --}}
                   {{-- <li>
                        <strong>Destino</strong>
                        {{ Form::select('recipient_agency', ['' => 'Todas'] + $agencies, Request::has('recipient_agency') ? Request::get('recipient_agency') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                    </li>--}}
                    <li>
                        <strong>Via</strong>
                        {{ Form::select('provider', ['' => 'Todas'] + $providers, Request::has('provider') ? Request::get('provider') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                    </li>
                    <li>
                        <strong>Estado</strong>
                        {{ Form::select('status', ['' => 'Todos'] + $status, Request::has('status') ? Request::get('status') : null, array('class' => 'form-control input-sm filter-datatable')) }}
                    </li>
                    <li>
                        <strong>Op.</strong>
                        {{ Form::select('operator', ['' => 'Todos'] + $operatorsList, Request::has('operator') ? Request::get('operator') : null, array('class' => 'form-control input-sm filter-datatable')) }}
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
                            <th class="w-1">Origem</th>
                            <th class="w-1">Destino</th>
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
