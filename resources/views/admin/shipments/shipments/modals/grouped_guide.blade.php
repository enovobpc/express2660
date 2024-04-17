<div class="modal fade" id="modal-grouped-guide">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="float: right">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Guia de transporte agrupada</h4>
            </div>
            <div class="modal-body">
                <h4 class="lh-1-3">Pretende gerar uma guia de transporte agrupada para os envios selecionados?</h4>
                <hr/>
                <div class="row row-5">
                    <div class="col-sm-3">
                        <div class="form-group m-b-0">
                            {{ Form::label('packing_type', 'Tipo Embalagem', ['class' => 'control-label']) }}
                            {{ Form::select('packing_type', ['VOLUME' => 'Volume', 'CAIXA' => 'Caixa', 'PALETE' => 'Palete'], null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-0">
                            {{ Form::label('description', 'Designação Merc.', ['class' => 'control-label']) }}
                            {{ Form::text('description', null, ['class' => 'form-control', 'placeholder' => 'Volume']) }}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        @if(@$vehicles)
                        <div class="form-group m-b-0">
                            {{ Form::label('vehicle', 'Viatura', ['class' => 'control-label']) }}
                            {{ Form::select('vehicle', ['' => ''] + $vehicles,null, ['class' => 'form-control select2']) }}
                        </div>
                        @else
                        <div class="form-group m-b-0">
                            {{ Form::label('vehicle', 'Matrícula Viatura', ['class' => 'control-label']) }}
                            {{ Form::text('vehicle', null, ['class' => 'form-control']) }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.printer.shipments.transport-guide') }}" id="url-grouped-transport-guide" data-toggle="datatable-action-url" target="_blank"></a>
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary btn-print-grouped">Imprimir</button>
                </div>
            </div>
        </div>
    </div>
</div>