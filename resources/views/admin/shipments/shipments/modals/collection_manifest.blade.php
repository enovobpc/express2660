<div class="modal fade" id="modal-collection-manifest">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-print"></i> Obter manifesto de entrega</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group is-required">
                            {{ Form::label('operator', 'Operador', ['class' => 'control-label']) }}
                            {{ Form::select('operator',  ['' => ''] + $operators, null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group is-required">
                            {{ Form::label('date', 'Data dos envios', ['class' => 'control-label']) }}
                            {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <a href="" data-toggle="print-manifest-url" class="btn btn-primary" disabled target="_blank">Imprimir</a>
            </div>
        </div>
    </div>
</div>