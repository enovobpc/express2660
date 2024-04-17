{{ Form::model($subscriber, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('email', 'E-mail') }}
        {{ Form::text('email', null, ['class' => 'form-control', 'autocomplete' => 'input1']) }}
    </div>
    <div class="form-group m-0">
        <div class="checkbox">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('active', 1, $subscriber->exists ? null : true) }}
                Subscritor ativo
            </label>
        </div>
    </div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}