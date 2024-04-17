<div class="modal fade" id="modal-role" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('route' => array('admin.roles.store'))) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span><span class="sr-only">Fechar</span></button>
                <h4 class="modal-title"><i class="ace-icon fas fa-cog"></i> Criar um perfil</h4>
            </div>
            <div class="modal-body">
                <div class="form-group is-required">
                    {{ Form::label('display_name', 'Nome do perfil') }}
                    {{ Form::text('display_name', null, array('class' => 'form-control', 'autocomplete' => 'off', 'required' => true)) }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary" data-loading-text="Gravar...">Gravar</button>
            </div>
            {{ Form::close() }}
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->