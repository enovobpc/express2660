<?php
$ckeditor = str_random(5);
?>
{{ Form::model($propose, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('subject', 'Assunto') }}
                {{ Form::text('subject', 'Pedido de Orçamento para Envio', ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('to', 'E-mail') }}
                {{ Form::text('to', Request::has('to') ? Request::get('to') : null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('group', 'Grupo de Contactos') }}
                {{ Form::select('group', ['' => ''] + $groupsList, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="form-group is-required m-b-0">
        {{ Form::label('message', 'Mensagem') }}
        {{ Form::textarea('message', null, ['class' => 'form-control ' . $ckeditor, 'required', 'rows' => 8, 'id' => $ckeditor]) }}
    </div>
    @if(Setting::get('budgets_mail_signature'))
        @if(Setting::get('budgets_mail_signature_html'))
        {!! Setting::get('budgets_mail_signature') !!}
        @else
        {!! nl2br(Setting::get('budgets_mail_signature')) !!}
        @endif
    @endif
</div>
<div class="modal-footer">
    @foreach($contacts as $group => $contact)
        {{ Form::hidden('contacts['.$group.']', $contact) }}
    @endforeach
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar</button>
</div>
{{ Form::close() }}

{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
<script>
    $('.select2').select2(Init.select2());

    $('[name="group"]').on('change', function(){
        var group = $(this).val();
        var contacts = $('[name="contacts['+group+']"]').val();
        $('[name="to"]').val(contacts)
    })

    CKEDITOR.config.height = '300px';
    CKEDITOR.replace('{{ $ckeditor }}');
</script>