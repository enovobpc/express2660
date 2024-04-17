<?php 
$modalHash = str_random(5); 
$formOptions = $formOptions ?? ['route' => ['admin.emails.store'], 'method' => 'POST', 'files' => true];
?>
{{ Form::model($email, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $email->modal_title ?? 'Escrever e-mail' }}</h4>
</div>
<div class="modal-body {{ $modalHash }} p-0">
    <div style="padding: 5px 5px 5px 10px;" class="main-area">
        <table class="w-100">
            <tr>
                <td class="text-right p-r-10 w-30px vertical-align-middle">Para</td>
                <td colspan="3">
                    {{ Form::text('to', null, ['class' => 'form-control email input-sm m-b-3 tagsinput', 'required']) }}
                </td>
                <td style="width: 81px">
                    <div class="btn-group btn-group-sm btn-group-cc" role="group">
                        <button type="button" class="btn {{ @$email->cc ? 'btn-primary' : 'btn-default' }} btn-cc">
                            Cc
                        </button>
                        <button type="button" class="btn {{ @$email->bcc ? 'btn-primary' : 'btn-default' }} btn-bcc">
                            Bcc
                        </button>
                    </div>
                </td>
            </tr>
            <tr class="tocc" style="{{ @$email->cc ? '' : 'display:none' }}">
                <td class="text-right p-r-8">
                    <i class="fas fa-times text-red remove-tocc"></i>
                    Cc
                </td>
                <td colspan="3">{{ Form::text('cc', null, ['class' => 'form-control email input-sm tagsinput m-b-3']) }}</td>
            </tr>
            <tr class="tobcc" style="{{ @$email->bcc ? '' : 'display:none' }}">
                <td class="text-right p-r-8">
                    <i class="fas fa-times text-red remove-tobcc"></i>
                    Bcc
                </td>
                <td colspan="2">{{ Form::text('bcc', null, ['class' => 'form-control email input-sm tagsinput m-b-3']) }}</td>
            </tr>
            <tr>
                <td class="text-right p-r-8">Assunto</td>
                <td>{{ Form::text('subject', null, ['class' => 'form-control input-sm', 'required']) }}</td>
                <td >
                    <div style="position: relativ">
                        <button type="button" class="btn btn-default btn-sm btn-attachment">
                            <i class="fas fa-paperclip"></i> Anexar
                        </button>
                    </div>
                </td>
            </tr>
            <tr class="attachments" style="{{ $email->attached_docs ? '' : 'display:none' }}">
                <td class="text-right p-r-8">Anexos</td>
                <td colspan="4" style="height: 33px;padding-top: 3px;" class="attachments-panel">
                @if(!empty($email->attached_docs))
                    @foreach ($email->attached_docs as $attachment)
                    <div class="attachment-block" data-filename="{{ $attachment->title }}" title="{{ $attachment->title }}">
                        <i class="fas fa-times m-r-1 text-red"></i>
                        @if(@$attachment->url)
                            <a href="{{ @$attachment->url }}" target="_blank">
                                <i class="fas fa-file"></i> {{ $attachment->title }}
                                <input type="hidden" name="docs_attached[]" value="{{ $attachment->url }}"/>
                                <input type="hidden" name="docs_attached_title[]" value="{{ $attachment->title }}"/>
                       {{--      </a>
                        @else
                        <input type="hidden" name="docs_attached[]" value="{{ $attachment->url }}"/>
                        <input type="hidden" name="docs_attached_name[]" value="{{ $attachment->title }}"/> --}}
                        @endif
                    </div>
                    @endforeach
                @endif
                </td>
            </tr>
        </table>
    </div>
    {{ Form::textarea('message', null, ['class' => 'form-control ' . $modalHash, 'required', 'rows' => 9, 'id' => $modalHash]) }}
</div>
<div class="modal-footer" style="border: none">
    <small class="text-info loading-autosave m-r-5 m-t-10"></small>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    @if(@$email->exists)
    <button type="button" class="btn btn-default btn-save" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar..."><i class="fas fa-save"></i> Guardar</button>
    @endif
    <button type="button" class="btn btn-primary btn-submit" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A enviar..."><i class="fas fa-paper-plane"></i> Enviar</button>
</div>
{{ Form::hidden('is_draft', @$email->is_draft) }}
{{ Form::hidden('id', @$email->id) }}
{{ Form::close() }}
{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
{{ HTML::script('vendor/bootstrap-tagsinput/src/bootstrap-tagsinput.js')}}
{{ HTML::style('vendor/bootstrap-tagsinput/src/bootstrap-tagsinput.css')}}
<style>
    .bootstrap-tagsinput {
        display: block;
        width: 100%;
        min-height: 30px;
        margin-bottom: 3px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
        box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
        -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        padding: 0 5px;
        border-radius: 0;
        box-shadow: none;
        border-color: #d2d6de;
        font-size: 13px;
    }

    .bootstrap-tagsinput input {
        border: 1px solid #fff;
        padding: 0px 5px 1px;
        height: 26px;
        min-width: 200px;
        margin-top: 1px;
    }

    .bootstrap-tagsinput input:focus,
    .bootstrap-tagsinput input:focus-visible {
        border: 1px solid #ddd;
        border-radius: 3px;
        background: #eee;
        outline: none;
    }

    .bootstrap-tagsinput .tag {
        font-size: 13px;
        font-weight: normal;
        border-radius: 2px;
        color: #333 !important;
        background: #c9e2ff !important;
    }

    .bootstrap-tagsinput .tag.tag-error {
        color: #dc0000 !important;
        background: #ffc9c9 !important;
    }

    .bootstrap-tagsinput .tag [data-role="remove"]:after {
        content: "⨉";
        padding: 0 0 0 4px;
        font-weight: bold;
        color: red;
    }

    .btn-group-cc {
        position: absolute;
        height: 30px;
        right: 5px;
        top: 5px;
    }

    .btn-group-cc .btn {
        padding: 0 11px;
        border-radius: 0 !important;
        line-height: 17px;
    }

    .btn-attachment {
        position: absolute;
        /* top: -15px;
        right: 0;*/
        top: 38px;
        right: 5px;
        height: 30px;
        border-radius: 0 !important;
        width: 78px;
        line-height: 12px;
    }

    .btn-models {
        height: 30px;
        border-radius: 0 !important;
        line-height: 12px;
        margin-left: 3px;
    }


    .attachment-block {
        border: 1px solid #ccc;
        background: #eee;
        color: #333;
        width: 135px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        float: left;
        margin-right: 3px;
        margin-bottom: 1px;
        margin-top: 1px;
    }

    .attachment-block a {
        color: #555;
        cursor: pointer;
    }

    .attachment-block a:hover {
        color: #000;
    }

    .modal-footer {
        margin-top: -28px;
        z-index: 1;
        position: relative;
        background: #fff;
        border-top: 1px solid #ccc !important;
    }

    .cke_chrome {
        border-right: 0;
        border-left: 0;
    }
    
    .category-select .select2-container .select2-selection--single {
        padding: 4px 10px;
        height: 30px;
        margin-left: 2px;
    }
    
    .category-select .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px;
        right: 2px;
    }
</style>
<script>
    $('.select2').select2(Init.select2());
    $(window).bind('beforeunload', function(){
        return "Pretende sair desta página?";
    });

    /* $(document).ready(function (){

        $(document).find('.bootstrap-tagsinput input').autocomplete({
            serviceUrl: "{{-- route('admin.emails.search.email') --}}",
            onSearchStart: function () {},
            onSelect: function (suggestion) {
                $tr = $('.{{ $modalHash }} .bootstrap-tagsinput.focus').closest('tr')
                $tr.find('.tagsinput').tagsinput('add', suggestion.data);
                $tr.find('.bootstrap-tagsinput input').val('');
            },
        });
    }) */

    $(".tagsinput").tagsinput({
        cancelConfirmKeysOnEmpty: true
    });

    $('.tagsinput').on('itemAdded', function(event) {
        var tag = event.item;
        if (!Str.isEmail(tag)) {
            $(".bootstrap-tagsinput .tag:contains('"+tag+"')").addClass('tag-error');
        }
    });

    $('.{{$modalHash}} .mail-models-list li > a').on('click', function(e){
        e.preventDefault();
        var content      = $(this).data('content');
        var originalHtml = CKEDITOR.instances["{{ $modalHash }}"].getData();
        content = content + originalHtml;
        CKEDITOR.instances["{{ $modalHash }}"].setData(content);
    })

    /**
     * Attachments
     */
    $('.btn-attachment').on('click', function (){
        html = '<input multiple="multiple" style="display:none" name="attachments[]" type="file" data-inputid="'+Str.random(4)+'">';
        $('.main-area').append(html);
        $(document).find('.{{ $modalHash }} [name="attachments[]"]:last-child').trigger('click');
    })

    $(document).on('change', '.{{ $modalHash }} [name="attachments[]"]',function(){
        $('tr.attachments').show();
        for (var i = 0; i < $(this).get(0).files.length; ++i) {
            file = $(this).get(0).files[i]
            displayPreviewFile(file);
        }
    })

    $(document).on('click', '.{{ $modalHash }} .attachment-block a', function(e){
        e.preventDefault();
        var base64content = $(this).attr('href');
        var filename = $(this).closest('.attachment-block').data('filename');

        let newTab = window.open("")
        newTab.document.write(
            "<iframe height='100%' width='100%' style='position: absolute;left: 0;right: 0;top: 0;bottom: 0;border: 0; width: 100%; height: 100%' src='" + encodeURI(base64content) + "'></iframe>"
        )
        newTab.document.title = filename;
    })

    function displayPreviewFile(file) {
        var reader = new FileReader();
        reader.onload = function(e) {

            html = '<div class="attachment-block" data-filename="' + file.name + '" title="' + file.name + '">' +
                '<i class="fas fa-times m-r-1 text-red"></i> ' +
                '<a href="'+e.target.result +'" target="_blank">' +
                '<i class="fas fa-file"></i> ' + file.name +
                '<input type="hidden" name="allowed_attachments[]" value="' + file.name +'"/>' +
                '</a>' +
                '</div>'

            $('.attachments-panel').append(html);
        };
        reader.readAsDataURL(file);
    }

    $(document).on('click', '.{{ $modalHash }} .attachment-block .fa-times', function (){
        $(this).closest('.attachment-block').remove();
        if($('.{{ $modalHash }} .attachment-block .fa-times').length == 0) {
            $('tr.attachments').hide();
        }
    })

    $('.btn-cc').on('click', function (){
        $('.tocc').toggle().focus();
        $('.tocc').find('input').val('');
        $(this).toggleClass('btn-default').toggleClass('btn-primary')
    })

    $('.btn-bcc').on('click', function (){
        $('.tobcc').toggle().focus();
        $('.tocc').find('input').val('');
        $(this).toggleClass('btn-default').toggleClass('btn-primary')
    })

    $('.remove-tocc').on('click', function(){
        $('.btn-cc').trigger('click');
    })

    $('.remove-tobcc').on('click', function(){
        $('.btn-bcc').trigger('click');
    })

    $('.btn-save').on('click', function(){
        $(this).closest('form').find('[name="draft"]').val(1);
        $(this).button('loading');
        $(this).closest('form').submit()
    })

    $('.btn-submit').on('click', function(){
        
        var requiredEmpty = false;
        
        if($('.modal [name="category_id"]').val() == '') {
            requiredEmpty = true;
        }
        
        if(requiredEmpty) { 
            Growl.error('Campos obrigatórios vazios.')
        } else {
            $(this).closest('form').find('[name="draft"]').val(0);
            $(this).button('loading');
            $(this).closest('form').submit()
        }
    })

    @if($email->exists)
    //autosave
    var counterSeconds = 0;
    var autosaveTime   = 120; //cada 2 min
    var downtimerTime  = autosaveTime - 3; //3 countdown 3 segundos antes
    var $autosaveLoading = $('.loading-autosave');

    setInterval(function(){

        if(counterSeconds == 3) { //após 3 segundos iniciais limpa a caixa de autosave
            $autosaveLoading.animate({'opacity' : '0'}, 500);
        } 

        if(counterSeconds >= downtimerTime) { //3 segundos antes
            var downtime = autosaveTime - counterSeconds;
            $autosaveLoading.html('Autosave '+downtime+'s').animate({'opacity' : '1' },500);
        }

        if($(".{{$modalHash}}").is(':visible') && counterSeconds == autosaveTime) {
            counterSeconds = 0; //reset

            $form = $(".{{$modalHash}}").closest('form');
            $form.find('[name="draft"]').val(1);

            var loadingHtml = 'Autosave <i class="fas fa-spin fa-circle-notch"></i>';
          
            $autosaveLoading.html(loadingHtml);
            
            ckeditorData =ckeditorInstance.getData();
            $form.find('[name="message"]').val(ckeditorData)
            $.post($form.attr('action'), $form.serialize(), function(data){
                if(data.result) {
                    $form.attr('action', data.formUrl)
                    $autosaveLoading.html('<span class="text-green">Autosave <i class="fas fa-check"></i></span>');
                } else {
                    $autosaveLoading.html('<span class="text-red">Autosave falhou</span>');
                }
            }).always(function(){
                $autosaveLoading.css('opacity', 1);
            }).fail(function(){
                $autosaveLoading.html('<span class="text-red">Autosave erro 500</span>');
            })
        }
        counterSeconds++;
    }, 1000);
    @endif

    var customConfig = [
        { name: 'basicstyles', items: [ 'Undo', 'Redo', 'Blockquote', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'NumberedList', 'BulletedList'] },
        //{ name: 'styles', items: [ 'Font', 'FontSize' ] },
        { name: 'styles', items: [ 'FontSize' ] },
        { name: 'links', items: ['TextColor', '-', 'Outdent', 'Indent', '-', 'Image', 'Table', 'Link', 'HorizontalRule','-', 'Source' ] },
    ]

    var ckeditorInstance = CKEDITOR.replace('{{ $modalHash }}', {
        height: 400,
        toolbar: customConfig,
        //extraPlugins: ['easyimage']
    });


</script>