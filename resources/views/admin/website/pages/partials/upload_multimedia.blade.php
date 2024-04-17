<div class="modal fade" id="upload-multimedia" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('route' => array('admin.website.multimedia.store'), 'files' => true)) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="font-size-32px" aria-hidden="true">&times;</span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-upload"></i> Carregar ficheiro</h4>
            </div>
            <div class="modal-body">
                <div class="form-group is-required">
                    {{ Form::label('name', 'Ficheiro') }}
                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="fa fa-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">Procurar...</span>
                            <span class="fileinput-exists">Alterar</span>
                            <input type="file" name="file" required>
                        </span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>