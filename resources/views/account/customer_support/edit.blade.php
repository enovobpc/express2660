<?php $ckeditor = str_random(5); ?>
{{ Form::model($ticket, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required m-b-5">
                {{ Form::label('subject', trans('account/customers-support.form.ticket.subject')) }}
                {{ Form::text('subject', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-12 m-b-10">
            <div>{{ Form::label('category', 'Categoria') }}</div>
            <div class="btn-group btn-category" role="group">
                @foreach(trans('admin/customers_support.categories') as $key => $value)
                    <button type="button" class="btn {{ $key == $ticket->category ? 'btn-primary' : 'btn-default' }}" data-id="{{ $key }}">{{ $value }}</button>
                @endforeach
            </div>
            {{ Form::select('category', trans('admin/customers_support.categories'), null, ['class' => 'hidden', 'required']) }}
        </div>
    </div>
    <div class="form-group is-required">
        {{ Form::label('message', trans('account/customers-support.form.ticket.message')) }}
        {{ Form::textarea('message', null, ['class' => 'form-control ' . $ckeditor, 'required', 'rows' => 4, 'id' => $ckeditor]) }}
    </div>
    <div class="row">
        <div class="col-sm-8">
            @if(!$ticket->exists)
                <div class="form-group m-b-0" id="upload">
                    {{ Form::label('attachments', trans('account/customers-support.form.ticket.attachments'), ['class' => 'control-label']) }}
                    <div class="fileinput fileinput-new input-group m-b-0" data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="fas fa-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                    <span class="fileinput-new">{{ trans('account/global.word.search') }}</span>
                    <span class="fileinput-exists">{{ trans('account/global.word.change') }}</span>
                    <input type="file" name="attachments[]" multiple="true">
                </span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">{{ trans('account/global.word.remove') }}</a>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-sm-4">
            <div class="form-group select2-fullwidth m-b-0">
                {{ Form::label('shipment_id', 'Envio associado (campo opcional)...') }}
                {{ Form::select('shipment_id', $ticket->shipment_id ? [$ticket->shipment->id => '#' . $ticket->shipment->tracking_code . ' - ' . $ticket->shipment->recipient_name] : [], null, ['class' => 'form-control', 'data-placeholder' => '']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
    <button type="submit" class="btn btn-black" data-loading-text="{{ trans('account/global.word.loading') }}">{{ trans('account/global.word.save') }}</button>
</div>
{{ Form::close() }}

{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker())

    $(".modal .btn-category button").on('click', function () {
        $(".modal .btn-category button").removeClass('btn-primary').addClass('btn-default')
        $('.modal select[name=category]').val($(this).data('id'));
        $(this).removeClass('btn-default').addClass('btn-primary')
    });

    $(".modal select[name=shipment_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('account.customer-support.search.shipment') }}")
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

