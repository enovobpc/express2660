<?php $hash = str_random() ?>
{{ Form::open(['route' => ['admin.budgets.courier.email.send', $budget->id], 'class' => 'send-email']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Enviar Orçamento por E-mail</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('subject', 'Assunto') }}
                {{ Form::text('subject', $subject, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('email', 'Enviar para') }}
                {{ Form::text('email', $budget->email, ['class' => 'form-control email nospace lowecase']) }}
            </div>
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('message', 'Corpo da Mensagem') }}
        {{ Form::textarea('message', $defaultMessage, ['class' => 'form-control ckeditor-' . $hash, 'id' => 'ckeditor-' . $hash]) }}
    </div>
    <div class="form-group m-0">
        <div class="checkbox">
            <label style="padding-left: 0">
                {{ Form::checkbox('answered', 1, true) }}
                Alterar estado do orçamento para "Respondido Cliente"
            </label>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-right">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary"
                data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A enviar e-mail...">Enviar E-mail
        </button>
    </div>
</div>
{{ Form::close() }}

{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
<script>
    var editor = CKEDITOR.replace('ckeditor-{{ $hash }}');

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('form.send-email').on('submit', function(e){
        e.preventDefault();

        $('[name="message"]').val(editor.getData()).trigger('change');

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                $('#modal-remote-lg').modal('hide');
                oTable.draw();
                Growl.success(data.feedback)
            } else {
                Growl.error(data.feedback)
            }
        }).fail(function () {
            $button.button('reset');
            Growl.error500()
        }).always(function(){
            $button.button('reset');
        })
    });
</script>