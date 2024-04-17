<div class="modal" id="modal-import-logos" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['route' => 'admin.core.install.upload-logos', 'class' => 'import-form','files' => true]) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="fas fa-upload"></i> Importar Logotipos</h4>
            </div>
            <div class="modal-body">
                <div class="form-group m-b-0">
                    {{ Form::label('file', 'Logótipos a carregar', ['class' => 'control-label']) }}
                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="fas fa-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">Selecionar</span>
                            <span class="fileinput-exists">Alterar</span>
                            <input type="file" name="files[]" data-file-format="png,svg" multiple/>
                        </span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">
                            Remover
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A carregar...">
                        <i class="fas fa-upload"></i> Carregar
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>