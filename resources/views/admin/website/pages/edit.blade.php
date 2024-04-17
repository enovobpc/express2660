@section('title')
    Gestor de Páginas
@stop

@section('content-header')
    Gestor de Páginas
@stop

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.website.pages.index') }}">Gestor de Páginas</a>
    </li>
    <li class="active">Editar Página</li>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <a href="/{{ $page->url }}" class="btn btn-sm btn-primary pull-right m-t-5 m-r-5" target="_blank">Pré-visualizar página</a>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-structure" data-toggle="tab"><i class="fas fa-sitemap"></i> Conteúdo</a></li>
                    <li><a href="#tab-multimedia" data-toggle="tab"><i class="fas fa-images"></i> Livraria Multimédia</a></li>
                    <li><a href="#tab-css" data-toggle="tab"><i class="fas fa-code"></i> CSS & JavaScript</a></li>
                    <li><a href="#tab-info" data-toggle="tab"><i class="fas fa-globe"></i> Dados da Página</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-structure">
                        @include('admin.website.pages.tabs.structure')
                    </div>
                    <div class="tab-pane" id="tab-multimedia">
                        @include('admin.website.pages.tabs.multimedia')
                    </div>
                    <div class="tab-pane" id="tab-css">
                        @include('admin.website.pages.tabs.styles')
                    </div>
                    <div class="tab-pane" id="tab-info">
                        @include('admin.website.pages.tabs.info')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('styles')
{{ HTML::style('vendor/prism/themes/prism.css') }}
    {{--{{ HTML::style('vendor/codeflask.js/src/codeflask.css') }}--}}
{{--    <style>
        :not(pre) > code[class*="language-"],
        pre[class*="language-"] {
            background: transparent;
            border: 1px solid transparent;
            padding: 8px;
        }
    </style>--}}
@stop

@section('plugins')
    {{ HTML::script('vendor/html.sortable/dist/html.sortable.min.js')}}
    {{ HTML::script('vendor/prism/prism.js')}}
    {{--{{ HTML::script('vendor/codeflask.js/src/codeflask.js')}}--}}
    <script src="https://unpkg.com/codeflask/build/codeflask.min.js" />
@stop

@section('scripts')
    <script type="text/javascript">
        $('[data-dismiss="fileinput"]').on('click', function () {
            $('[name=delete_photo]').val(1);
        })


        $(document).ready(function () {

            $('.sortable').sortable({
                forcePlaceholderSize: true,
                placeholder: '<li><div class="width-300px"></div></li>'
            }).bind('sortupdate', function (e, ui) {
                //get array of ordered IDs
                var dataList = $(".sortable > li").map(function () {
                    return $(this).data("id");
                }).get();

                $.post("{{ route('admin.website.pages.sections.sort.update', $page->id) }}", {'ids[]': dataList}, function (data) {
                    Growl.success(data.message)
                }).fail(function () {
                    Growl.error500();
                });
            });
        });

        var flaskCss = new CodeFlask('#css-editor', {
            language: 'css',
            lineNumbers: true
        });


        flaskCss.onUpdate(function(code) {
            $('[name="css"]').val(code)
        });

        var flaskJs = new CodeFlask('#js-editor', {
            language: 'js',
            lineNumbers: true
        });

        flaskJs.onUpdate(function(code) {
            $('[name="js"]').val(code)
        });

        $('.file-preview').not('.file-preview .actions').on('click', function(){
            Helper.copyToClipboard($(this));
        })
    </script>
@stop