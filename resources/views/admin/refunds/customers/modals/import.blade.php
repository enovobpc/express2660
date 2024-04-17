<div class="modal" id="modal-refund-import">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => ['admin.refunds.customers.import'],'method' => 'post','class' => 'import-form','files' => true]) }}
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Importar confirmação de reembolsos</h4>
            </div>
            <div class="modal-body">
                <div class="import-results-area"></div>
                <div class="import-form-area">
                    <div class="row row-5">
                        <div class="col-sm-2">
                            <div class="form-group">
                                {{ Form::label('refund_file_type', 'Ficheiro') }}
                                {{ Form::select('refund_file_type',['' => '','envialia' => 'Envialia','tipsa' => 'Tipsa','gls' => 'GLS','ctt' => 'CTT','via_directa' => 'Via Directa','ctt_spain' => 'CTT Espanha'],null,['class' => 'form-control select2']) }}
                            </div>
                        </div>
                        <div class="col-sm-10">
                            <div class="form-group m-b-0" id="upload">
                                {{ Form::label('file', 'Ficheiro a carregar (.xls, .xlsx, .csv ou .txt)', ['class' => 'control-label']) }}
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="fas fa-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Selecionar</span>
                                        <span class="fileinput-exists">Alterar</span>
                                        <input type="file" name="file" data-file-format="xls,xlsx,csv,txt">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists"
                                        data-dismiss="fileinput">Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary"
                        data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
