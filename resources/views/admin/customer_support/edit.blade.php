<?php $ckeditor = str_random(5); ?>
{{ Form::model($ticket, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('subject', __('Assunto do pedido')) }}
                {{ Form::text('subject', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-5">
            {{ Form::label('category', __('Categoria')) }}
            <div class="btn-group btn-category" role="group">
                @foreach(trans('admin/customers_support.categories') as $key => $value)
                <button type="button" class="btn {{ $key == $ticket->category ? 'btn-primary' : 'btn-default' }}" data-id="{{ $key }}">{{ $value }}</button>
                @endforeach
            </div>
            {{ Form::select('category', trans('admin/customers_support.categories'), null, ['class' => 'hidden', 'required']) }}
        </div>
    </div>
    <div class="form-group is-required">
        {{ Form::label('message', __('Descrição do pedido')) }}
        {{ Form::textarea('message', null, ['class' => 'form-control ' . $ckeditor, 'required', 'rows' => 4, 'id' => $ckeditor]) }}
    </div>
    @if(!$ticket->exists)
    <div class="form-group" id="upload">
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
    @endif
    <div class="row row-5">
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('customer_id', __('Cliente associado')) }}
                {{ Form::select('customer_id', $ticket->exists && $ticket->customer_id ? [$ticket->customer->id => $ticket->customer->name] : [], null, ['class' => 'form-control', 'data-placeholder' => '', 'required']) }}
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('shipment_id', __('Envio associado')) }}
                {{ Form::select('shipment_id', $ticket->shipment_id ? [$ticket->shipment->id => '#' . $ticket->shipment->tracking_code . ' - ' . $ticket->shipment->recipient_name] : [], null, ['class' => 'form-control', 'data-placeholder' => '']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group m-b-0">
                {{ Form::label('obs', __('Observações (visivel só internamente)')) }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 5]) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('date', __('Data')) }}
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                    {{ Form::text('date', $ticket->exists ? $ticket->date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                </div>
            </div>
            <div class="form-group form-group-sm m-b-0">
                {{ Form::label('user_id', __('Responsável')) }}
                {{ Form::select('user_id', ['' => __('- Sem Responsável -')] + $operators, !$ticket->exists ? Auth::user()->id : null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    @if(!$ticket->exists)
        <div class="extra-options w-75">
            <div class="input-group input-email pull-left m-r-20" style="width: 280px">
                <div class="input-group-addon" data-toggle="tooltip" title="@trans('Ative esta opção para enviar e-mail ao cliente.')">
                    <i class="fas fa-envelope"></i>
                    {{ Form::checkbox('send_email', 1, false) }}
                </div>
                {{ Form::text('email', $ticket->email, ['class' => 'form-control pull-left email nospace lowercase', 'placeholder' => __('Notificar cliente')]) }}
            </div>
        </div>
    @endif
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}
{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
<script>
    $('.select2').select2(Init.select2())
    $('.datepicker').datepicker(Init.datepicker())

    $(".modal .btn-category button").on('click', function () {
        $(".modal .btn-category button").removeClass('btn-primary').addClass('btn-default')
        $('.modal select[name=category]').val($(this).data('id'));
        $(this).removeClass('btn-default').addClass('btn-primary')
    });

    $(".modal select[name=customer_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
    });

    $(".modal select[name=shipment_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.customer-support.search.shipment') }}")
    });

    CKEDITOR.config.height = '200px';
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