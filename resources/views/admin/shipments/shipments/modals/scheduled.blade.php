<div class="modal" id="modal-shipments-scheduled">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="far fa-clock"></i> Agendamentos Periódicos</h4>
            </div>
            <div class="modal-body">
                <div class="modal-alert bg-gray-light">
                    <h5 class="m-b-0 m-t-3 fw-400 text-blue">
                        <i class="fas fa-info-circle"></i> Os envios agendados serão inseridos automáticamente em sistema às 00:00 da data marcada.
                    </h5>
                </div>
                <ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-shipments-scheduled">
                    <li>
                        <a href="{{ route('admin.shipments.create', ['schedule' => true]) }}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-remote-xl">
                            <i class="fas fa-plus"></i> Novo Agendamento
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="datatable-shipments-scheduled" class="table table-condensed table-striped table-dashed table-hover m-b-0">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="w-1"></th>
                            <th class="w-200px">Remetente</th>
                            <th class="w-200px">Destinatário</th>
                            <th class="w-1">Serviço</th>
                            <th class="w-1">Remessa</th>
                            <th>Periodicidade</th>
                            <th class="w-120px">Ult. Agendamento</th>
                            <th class="w-1"></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer text-right">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>