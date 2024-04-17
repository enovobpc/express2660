<div class="modal" id="modal-mass-update">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.tracking.incidences.selected.update'], 'method' => 'POST']) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Alterar incidências em massa</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('is_active', 'Ativo') }}
                            {{ Form::select('is_active', ['' => 'Não alterar', '1' => 'Sim', '0' => 'Não'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('operator_visible', 'Visivel Motorista') }}
                            {{ Form::select('operator_visible', ['' => 'Não alterar', '1' => 'Sim', '0' => 'Não'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-0">
                            {{ Form::label('is_shipment', 'Incidência Envio') }}
                            {{ Form::select('is_shipment', ['' => 'Não alterar', '1' => 'Sim', '0' => 'Não'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-0">
                            {{ Form::label('is_pickup', 'Incidência Recolha') }}
                            {{ Form::select('is_pickup', ['' => 'Não alterar', '1' => 'Sim', '0' => 'Não'], null, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>