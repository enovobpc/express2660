{{ Form::model($content, ['route' => ['admin.website.pages.sections.content.update', $pageId, $content->page_section_id, $content->block], 'method' => 'PUT', 'files' => true]) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Editar conteúdos</h4>
</div>
<div class="modal-body">
    @include('admin.website.pages.contents.content_types.'.$content->content_type)
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}

{{ HTML::style('vendor/prism/themes/prism.css') }}
{{--{{ HTML::style('vendor/codeflask.js/src/styles/codeflask.css') }}--}}

{{ HTML::script('vendor/admin-lte/plugins/ckeditor/ckeditor.js')}}
{{ HTML::script('vendor/prism/prism.js')}}
{{--{{ HTML::script('vendor/codeflask.js/src/codeflask.js')}}--}}
<script src="https://unpkg.com/codeflask/build/codeflask.min.js" />
<style>
/*    .CodeFlask__textarea {
        white-space: nowrap;
        overflow-x: hidden;
    }

    .CodeFlask__code {
        margin-top: 0 !important;
        position: relative;
    }*/
</style>
<script>

    @if($content->content_type == 'html')
        @foreach(app_locales() as $code => $lang)

        var flaskHtml = new CodeFlask('.html-editor-{{ $code }}', {
            language: 'html',
            lineNumbers: true
        });

        flaskHtml.onUpdate(function(code) {
            $('[name="{{ $code }}[content]"]').val(code)
        });
        @endforeach
    @endif

    @foreach(app_locales() as $code => $lang)
    CKEDITOR.replace("{{ 'ckeditor-' . $code }}");
    @endforeach

</script>