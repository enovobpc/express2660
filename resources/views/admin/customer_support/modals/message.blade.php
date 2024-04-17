<?php
    $ckeditor = str_random(5);
?>
{{ Form::open(['route' => array('admin.customer-support.messages.store', $ticket->id), 'method' => 'POST', 'files' => 'true']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Nova resposta ao pedido de suporte')</h4>
</div>
<div class="modal-body">
    @if($ticket->user_id && $ticket->user_id != Auth::user()->id)
    <div class="alert alert-warning p-10px">
        <h4 class="m-0">
            <i class="fas fa-info-circle"></i> @trans('Este pedido está a ser tratado por') <b>{{ @$ticket->user->name }}</b>.<br/>
            <small style="color: #fff">@trans('Pode responder ao pedido, contudo, tenha atenção para que o responsável tem conhecimento da resposta.')</small>
        </h4>
    </div>
    @endif
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('subject', __('Assunto')) }}
                <div class="input-group">
                    <div class="input-group-addon">[#{{ $ticket->code }}]</div>
                    {{ Form::text('subject', $ticket->subject, ['class' => 'form-control', 'required']) }}
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('to', __('Para')) }}
                {{ Form::email('to', $ticket->customer_id ? @$ticket->customer->email : $ticket->email, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
    <div class="form-group is-required">
        {{ Form::label('message', __('Mensagem')) }}
        {{ Form::textarea('message', $defaultMsg, ['class' => 'form-control ' . $ckeditor, 'required', 'rows' => 9, 'id' => $ckeditor]) }}
    </div>
    <div class="form-group m-b-0" id="upload">
        {{ Form::label('attachments', __('Anexar ficheiros'), ['class' => 'control-label']) }}
        <div class="fileinput fileinput-new input-group m-b-0" data-provides="fileinput">
            <div class="form-control" data-trigger="fileinput">
                <i class="fas fa-file fileinput-exists"></i>
                <span class="fileinput-filename"></span>
            </div>
            <span class="input-group-addon btn btn-default btn-file">
            <span class="fileinput-new">@trans('Selecionar')</span>
            <span class="fileinput-exists">@trans('Alterar')</span>
            <input type="file" name="attachments[]" multiple="true">
        </span>
            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">@trans('Remover')</a>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> @trans('Enviar')</button>
</div>
{{ Form::close() }}
{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
<script>
    CKEDITOR.config.height = '250px';
    //CKEDITOR.config.toolbar = [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ];
    CKEDITOR.config.toolbarGroups = [
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
        { name: 'forms', groups: [ 'forms' ] },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
        { name: 'links', groups: [ 'links' ] },
        { name: 'insert', groups: [ 'insert' ] },
        { name: 'styles', groups: [ 'styles' ] },
        { name: 'colors', groups: [ 'colors' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'others', groups: [ 'others' ] },
        { name: 'about', groups: [ 'about' ] }
    ];

    CKEDITOR.config.removeButtons = 'Save,Templates,NewPage,Preview,Print,Copy,PasteText,PasteFromWord,Redo,Undo,Cut,Paste,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,HiddenField,ImageButton,Subscript,Superscript,CopyFormatting,RemoveFormat,Outdent,Indent,CreateDiv,Blockquote,BidiRtl,BidiLtr,Language,Anchor,Flash,SpecialChar,PageBreak,Iframe,Format,Styles,BGColor,Maximize,ShowBlocks,About,Smiley';
    CKEDITOR.replace('{{ $ckeditor }}');
</script>

