<div class="modal" id="modal-print-cold-manifest">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Imprimir manifesto de temperatura</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('temperature', 'Temperatura') }}
                            <div class="input-group">
                                {{ Form::text('temperature', null, ['class' => 'form-control decimal', 'maxlength' => 5]) }}
                                <div class="input-group-addon">ºC</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('humidity', 'Humidade') }}
                            <div class="input-group">
                                {{ Form::text('humidity', null, ['class' => 'form-control decimal', 'maxlength' => 5]) }}
                                <div class="input-group-addon">%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a href="{{ route('account.shipments.selected.print.cold-manifest') }}"
                   data-toggle="datatable-action-url"
                   target="_blank"
                   class="btn btn-primary"><i class="fas fa-print"></i> Imprimir</a>
            </div>
        </div>
    </div>
</div>