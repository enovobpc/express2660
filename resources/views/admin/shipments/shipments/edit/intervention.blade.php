{{ Form::model($intervention, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group is-required">
        {{ Form::label('subject', 'Assunto') }}
        {{ Form::text('subject', null, ['class' => 'form-control', 'required']) }}
    </div>
    <div class="form-group is-required m-b-0">
        {{ Form::label('action_taken', 'Ações Tomadas') }}
        {{ Form::textarea('action_taken', null, ['class' => 'form-control', 'rows' => 6]) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}
