@section('metatags')
    <meta name="description" content="{{ trans('website.seo.home.description') }}">
    <meta property="og:title" content="2660 Express - Logistica e Distribuição">
    <meta property="og:description" content="{{ trans('website.seo.home.description') }}">
    <meta property="og:image" content="{{ trans('website.seo.image.url') }}">
    <meta property="og:image:width" content="{{ trans('website.seo.image.width') }}">
    <meta property="og:image:height" content="{{ trans('website.seo.image.height') }}">
    <meta name="description" content="{{ trans('website.seo.home.description') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="2660 Express - Logistica e Distribuição">
    <meta name="reply-to" content="geral@2660express.pt">
    <meta name="keywords" content="logistica, distribuicao">
@stop

@section('content')
<section class="topo-about" style="height: 380px; position:relative; background: url('/assets/website/img/topo-servicos.png'); background-size: cover; background-position: bottom 0px left 0px; display: flex; align-items: flex-end; display: flex; align-items: center;">
    <div class="col-sm-12 todos-topos">
        <h1 class="text-top-about">{{trans('website.services.title')}}</h1>
        <div style="display: flex; align-items: center; margin-top:15px;">
            <p class="text-top2">HOME > </p>
            <p class="text-top2" style="text-decoration: underline;">&nbsp{{trans('website.services.title')}}</p>
        </div>
    </div>
</section> 
<section class="services-passo ">
    <div class="row">
        <div class="h-75 col-12 col-xs-12 col-sm-6 col-xl-4" style="display: flex; flex-direction: column; align-items: center; padding-left: 30px; padding-right: 30px;">
            <img src="{{ asset('assets/website/img/passos-1.svg') }}" alt="passo-1" style="width: 50%;">
            <h3 class="service1-text">{{trans('website.services.steps.step01.title')}}</h3>
            <p class="text-pservice">{{trans('website.services.steps.step01.description')}}</p>
        </div>
        <div class="h-75 col-12 col-xs-12 col-sm-6  col-xl-4" style="display: flex; flex-direction: column; align-items: center; padding-left: 30px; padding-right: 30px;">
            <img src="{{ asset('assets/website/img/passo2.svg') }}" alt="passo-1" style="width: 50%;">
            <h3 class="service1-text">{{trans('website.services.steps.step02.title')}}</h3>
            <p class="text-pservice">{{trans('website.services.steps.step02.description')}}</p>
        </div>
        <div class="h-75 col-12 col-xs-12 col-sm-6  col-xl-4" style="display: flex; flex-direction: column; align-items: center; padding-left: 30px; padding-right: 30px;">
            <img src="{{ asset('assets/website/img/passo-3.svg') }}" alt="passo-1" style="width: 50%;">
            <h3 class="service1-text">{{trans('website.services.steps.step03.title')}}</h3>
            <p class="text-pservice">{{trans('website.services.steps.step03.description')}}</p>
        </div>
        <div class="h-75 col-12 col-xs-12 col-sm-6  col-xl-4" style="display: flex; flex-direction: column; align-items: center; padding-left: 30px; padding-right: 30px; margin-top:40px;">
            <img src="{{ asset('assets/website/img/passo4.svg') }}" alt="passo-1" style="width: 50%;">
            <h3 class="service1-text">{{trans('website.services.steps.step04.title')}}</h3>
            <p class="text-pservice">{{trans('website.services.steps.step04.description')}}</p>
        </div>
        <div class="h-75 col-12 col-xs-12 col-sm-6  col-xl-4" style="display: flex; flex-direction: column; align-items: center; padding-left: 30px; padding-right: 30px; margin-top:40px;">
            <img src="{{ asset('assets/website/img/passo5.svg') }}" alt="passo-1" style="width: 50%;">
            <h3 class="service1-text">{{trans('website.services.steps.step05.title')}}</h3>
            <p class="text-pservice">{{trans('website.services.steps.step05.description')}}</p>
        </div>
        <div class="h-75 col-12 col-xs-12 col-sm-6  col-xl-4" style="display: flex; flex-direction: column; align-items: center; padding-left: 30px; padding-right: 30px; margin-top:40px;">
            <img src="{{ asset('assets/website/img/passo6.svg') }}" alt="passo-1" style="width: 50%;">
            <h3 class="service1-text">{{trans('website.services.steps.step06.title')}}</h3>
            <p class="text-pservice">{{trans('website.services.steps.step06.description')}}</p>
        </div>
    </div>    
</section>
@include('website.partials.services')

{{-- @include('webiste.partials.contacts') --}}
@stop


@section ('styles')
    <style>
        .row {
            margin-right: 0px;
            margin-left: 0px;
        }
    </style>

@stop


