@section('title')
    {{ trans('website.seo.tracking.title') }} |
@stop

@section('metatags')
    <meta name="description" content="{{ trans('website.seo.tracking.description') }}">
    <meta property="og:title" content="{{ trans('website.seo.tracking.title') }}">
    <meta property="og:description" content="{{ trans('website.seo.tracking.description') }}">
    <meta property="og:image" content="{{ trans('website.seo.image.url') }}">
    <meta property="og:image:width" content="{{ trans('website.seo.image.width') }}">
    <meta property="og:image:height" content="{{ trans('website.seo.image.height') }}">
    <meta name="robots" content="index, follow">
@stop

@section('content')
    <section class="header-title">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-7">
                    <h1>{{ trans('website.seo.tracking.title') }}</h1>
                </div>

            </div>
        </div>
    </section>
    <section class="tracking" style="background: transparent">
        <div class="container">
            <div class="row">
               {{-- <div class="col-sm-5">
                    <h4 class="m-t-0 text-uppercase">Da origem até ao destino</h4>
                    <p>
                        Necessita do estado da sua expedição ou da prova de entrega?
                        Introduza o seu número de encomenda ou número de referência na caixa ao lado.
                    </p>
                </div>--}}
                <div class="col-xs-12 col-md-8 col-md-offset-2">
                    {{ Form::open(['route' => 'website.tracking.index', 'method' => 'GET', 'class' => 'tracking-form']) }}
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <h2>{{ trans('website.tracking.subtitle') }}</h2>
                            <p class="text-white m-b-30">{{ trans('website.tracking.text02') }}</p>
                        </div>
                    </div>
                        <div class="row row-0">
                            <div class="col-xs-12 col-sm-8 col-md-6 col-md-offset-2">
                                <div class="form-group m-b-0">
                                    {{ Form::text('tracking', null, ['class' => 'form-control input-lg']) }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm- col-md-3">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-lg btn-block btn-primary fs-16">
                                        <i class="fas fa-search"></i> {{ trans('website.word.search') }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-8 col-md-6 col-md-offset-2">
                                @if(Request::has('tracking') && !@$shipmentsResults)
                                    <p style="color: #ff0000"><i class="fas fa-exclamation-circle"></i> Envio não encontrado.</p>
                                @endif
                            </div>
                        </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>
    <div class="sp-50"></div>
@stop