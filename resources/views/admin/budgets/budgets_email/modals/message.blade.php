<?php
    $ckeditor = str_random(5);
?>
{{ Form::open(['route' => array('admin.budgets.messages.store', $budget->id), 'method' => 'POST']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Novo E-mail</h4>
</div>
<div class="modal-body">
    @if($budget->user_id && $budget->user_id != Auth::user()->id)
    <div class="alert alert-warning p-10px">
        <h4 class="m-0">
            <i class="fas fa-info-circle"></i> Este orçamento está a ser tratado por <b>{{ @$budget->user->name }}</b>.<br/>
            <small style="color: #fff">Pode responder ao orçamento, contudo, tenha atenção para que o responsável tem conhecimento da resposta.</small>
        </h4>
    </div>
    @endif
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('subject', 'Assunto') }}
                <div class="input-group">
                    <div class="input-group-addon">[ORC-{{ $budget->budget_no }}]</div>
                    {{ Form::text('subject', $budget->subject, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('to', 'Para') }}
                {{ Form::email('to', $budget->email, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
    <div class="form-group is-required m-b-0">
        {{ Form::label('message', 'Mensagem') }}
        {{ Form::textarea('message', Setting::get('budgets_mail_default_answer'), ['class' => 'form-control ' . $ckeditor, 'required', 'rows' => 9, 'id' => $ckeditor]) }}
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
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar</button>
</div>
{{ Form::close() }}
{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
<script>
    CKEDITOR.config.height = '330px';
    CKEDITOR.replace('{{ $ckeditor }}');
</script>

