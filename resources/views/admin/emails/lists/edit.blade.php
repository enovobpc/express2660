{{ Form::model($mailingList, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group is-required">
        {{ Form::label('name', 'Nome') }}
        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
    </div>
    <div class="form-group is-required m-0">
        {{ Form::label('emails', 'Emails (Separe os emails com virgula)') }}
        {{ Form::textarea('emails', null, ['class' => 'form-control nospace lowercase email', 'required']) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}