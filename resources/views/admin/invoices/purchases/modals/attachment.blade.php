{{ Form::model($attachment, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group is-required">
        {{ Form::label('name', 'Título do documento') }}
        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
    </div>
    @if(!$attachment->exists)
        <div class="form-group is-required">
            {{ Form::label('name', 'Ficheiro a anexar') }}
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
    @endif
<!--    <div class="form-group">
        {{ Form::label('obs', 'Anotações ou Observações') }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
    </div>
    <div class="form-group m-b-5">
        <div class="checkbox m-0">
            <label style="padding-left: 0">
                {{ Form::checkbox('operator_visible', 1) }}
                Anexo visivel aos motoristas na App Mobile
            </label>
        </div>
    </div>
    <div class="form-group m-0">
        <div class="checkbox m-0">
            <label style="padding-left: 0">
                {{ Form::checkbox('customer_visible', 1) }}
                Anexo visivel aos clientes na Área de Cliente
            </label>
        </div>
    </div>-->
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::hidden('source_class', $attachment->source_class) }}
{{ Form::close() }}
<script>
    $('.select2').select2(Init.select2());
</script>