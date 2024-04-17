@section('title')
    Notícias e Publicações
@stop

@section('content-header')
    Notícias e Publicações
<small>{{ $action }}</small>
@stop

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.website.blog.posts.index') }}">
            Notícias e Publicações
        </a>
    </li>
    <li class="active">
        {{ $action }}
    </li>
@stop

@section('styles')
{{ HTML::style('vendor/jquery.filer/css/jquery.filer.css') }}
@stop

@section('plugins')
{{ HTML::script('vendor/admin-lte/plugins/ckeditor/ckeditor.js')}}
{{ HTML::script('vendor/jquery.filer/js/jquery.filer.min.js') }}
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-solid">
            {{ Form::model($post, $formOptions) }}
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-9">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                @foreach(app_locales() as $code => $locale)
                                    <li class="{{ $code == config('app.locale') ? 'active' : ''}}">
                                        <a href="#tab-{{ $code }}" data-toggle="tab">
                                            <i class="flag-icon flag-icon-{{ $code }}"></i> {{ $locale }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content" style="    border: 1px solid #ddd;">
                                @foreach(app_locales() as $code => $locale)
                                    <div class="tab-pane {{ $code == config('app.locale') ? 'active' : ''}}" id="tab-{{ $code }}">
                                        <div class="form-group is-required">
                                            {{ Form::label($code.'[title]', 'Título do artigo ('.strtoupper($code).')', ['data-content' => ''])}}
                                            {{ Form::text($code.'[title]', $post->hasTranslation($code) ? $post->translate($code)->title : null, array('class' =>'form-control', 'maxlength' => 100, 'required')) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label($code.'[summary]', 'Resumo breve ('.strtoupper($code).')', ['data-content' => ''])}}
                                            {{ Form::textarea($code.'[summary]', $post->hasTranslation($code) ? $post->translate($code)->summary : null, array('class' =>'form-control', 'rows' => 2, 'maxlength' => 255)) }}
                                        </div>
                                        <div class="form-group is-required m-b-0">
                                            {{ Form::label('content', 'Corpo da Noticia ('.strtoupper($code).')') }}
                                            {{ Form::textarea($code.'[content]', $post->hasTranslation($code) ? $post->translate($code)->content : null, array('class' =>'form-control ckeditor-'.$code, 'id' => 'ckeditor-'.$code, 'required')) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="row row-5">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {{ Form::label('date', 'Data da Notícia') }}
                                    <div class="input-group">
                                        @if($post->date)
                                            {{ Form::text('date', date('Y-m-d', strtotime($post->date)), array('class' =>'form-control datepicker', 'required' => true)) }}
                                        @else
                                            {{ Form::text('date', date('Y-m-d'), array('class' =>'form-control datepicker', 'required' => true)) }}
                                        @endif
                                        <span class="input-group-addon"><i class="fa fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group m-t-25">
                                    <div class="checkbox">
                                        <label style="padding-left: 0 !important">
                                            {{ Form::checkbox('highlight', 1, false) }}
                                            Notícia em destaque
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {{ Form::label('tags', 'Tags (separadas por vírgula)') }}
                                    <div class="input-group">
                                        {{ Form::textarea('tags', implode(',',$post->tags->pluck('tag')->toArray()), array('class' =>'form-control', 'required' => true, 'rows' => 2)) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('image', 'Imagem Principal', array('class' => 'form-label')) }}<br/>
                            <div class="fileinput {{ $post->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 100%; max-height: 220px;">
                                    <img src="{{ asset('assets/img/default/default.png') }}">
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100%; max-height: 200px;">
                                    @if($post->filepath)
                                        <img src="{{ asset($post->getCroppa(250)) }}">
                                    @endif
                                </div>
                                <div>
                                    <span class="btn btn-default btn-block btn-sm btn-file">
                                        <span class="fileinput-new">Procurar...</span>
                                        <span class="fileinput-exists"><i class="fa fa-refresh"></i> Alterar</span>
                                        <input type="file" name="image">
                                    </span>
                                    <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                                        <i class="fa fa-close"></i> Remover
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-12">
                                {{ Form::hidden('delete_photo') }}
                                {{ Form::submit('Gravar', array('class' => 'btn btn-primary' )) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>  
    </div>
</div>
    <style>
        .nav-tabs-custom > .nav-tabs > li:first-of-type.active > a {
            border-left-color: #ddd;
        }
    </style>
@stop

@section('scripts')
<script>
    
    $('[data-dismiss="fileinput"]').on('click', function () {
        $('[name=delete_photo]').val(1);
    })

    CKEDITOR.editorConfig = function( config ) {
        config.toolbar = [
            { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
            '/',
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
            '/',
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
            { name: 'about', items: [ 'About' ] }
        ];
    };

    @foreach(app_locales() as $code => $locale)
    CKEDITOR.replace('ckeditor-{{ $code }}', { height: 400 });
    @endforeach

</script>
@stop

