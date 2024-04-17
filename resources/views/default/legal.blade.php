@section('title')
    Avisos Legais |
@stop

@section('metatags')
    <meta name="description" content="Acompanhe de perto os seus envios.">
    <meta property="og:title" content="Seguimento de Envios">
    <meta property="og:description" content="Acompanhe de perto os seus envios.">
    <meta property="og:image" content="{{ trans('seo.og-image.url') }}">
    <meta property="og:image:width" content="{{ trans('seo.og-image.width') }}">
    <meta property="og:image:height" content="{{ trans('seo.og-image.height') }}">
    <meta name="robots" content="index, follow">
@stop

@section('content')
    <div class="container">
        <div class="col-md-12">
            <div class="card card-tracking">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="content-left">
                                <div class="logo">
                                    <img src="{{ asset('assets/img/logo/logo_sm.png') }}">
                                    <a href="{{ route('home.index') }}" class="btn btn-default pull-right m-t-5 hidden-xs">Iniciar Sessão</a>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-3">
                                        @include('legal.partials.sidebar')
                                    </div>
                                    <div class="col-sm-9">
                                        @include($include)
                                        <div class="spacer-50"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            @if(!env('APP_HIDE_CREDITS'))
                <p class="credits">
                    <span class="p-t-3 pull-left">©{{ date('Y') }}. Software para Transportes e Logística. | <a href="{{ route('legal.show') }}">Avisos Legais</a> </span>
                    <a href="https://www.enovo.pt" class="hide"><img src="https://enovo.pt/assets/img/signatures/enovo_color.png" /></a>
                    {!! app_brand() !!}
                </p>
            @endif
        </div>
    </div>
    @include('auth.passwords.email')
@stop

@section('styles')
    <style>
        .list-group-item.active, .list-group-item.active:hover, .list-group-item.active:focus {
            z-index: 2;
            color: #fff;
            background-color: #444;
            border-color: transparent;
        }
    </style>
@stop
@section('scripts')
    <script>
        $('#{{$slug}}').addClass('active')
    </script>
@stop