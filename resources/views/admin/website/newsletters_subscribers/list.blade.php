<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="font-size-32px" aria-hidden="true">&times;</span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Lista de E-mails</h4>
</div>
<div class="modal-body">
    <div class="form-group margin-o">
        {{ Form::label('', 'Selecione a seguinte lista e use-a na sua aplicação de e-mail.') }}
        {{ Form::textarea('', $emails, ['class' => 'form-control']) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>