<div class="modal" id="modal-file-conference">
    <div class="modal-dialog" style="padding-left: 20px;padding-right: 20px;">
        <div class="modal-content">
            {{ Form::open(['route' => 'admin.equipments.conference.store', 'files' => true]) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">Fechar</span>
                </button>
                <h4 class="modal-title">Conferir estado equipamentos</h4>
            </div>
            <div class="modal-body">
                <div class="row row-5">
                    <div class="col-sm-9"> 
                        <div class="form-group is-required">
                        {{ Form::label('name', 'Ficheiro a verificar') }}
                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="fas fa-file fileinput-exists"></i>
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
                        <div class="col-sm-3"> 
                            <div class="form-group is_require">
                                {{ Form::label('type_file', 'Tipo de Fich.') }}
                                {{ Form::select('type_file', ['Némesis' => 'Némesis', 'Click' => 'Click'], null, ['class' => 'form-control select2', 'required']) }}
                            </div>
                        </div>
                </div>
                <div class="row row-5">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('action', 'Ação') }}
                            {{ Form::select('action', ['out' => 'Baixa', 'transfer' => 'Transferência', 'reception' => 'Recepção'], null, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="row row-5">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('column_sku', 'Col. SKU') }}
                                    {{ Form::text('column_sku', null, ['class' => 'form-control nospace uppercase', 'required']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('column_date', 'Col. Data') }}
                                    {{ Form::text('column_date', null, ['class' => 'form-control nospace uppercase']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('column_ot', 'Col. OT') }}
                                    {{ Form::text('column_ot', null, ['class' => 'form-control nospace uppercase']) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::label('column_operator', 'Col. Estafeta') }}
                                    {{ Form::text('column_operator', null, ['class' => 'form-control nospace uppercase']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Importar</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>