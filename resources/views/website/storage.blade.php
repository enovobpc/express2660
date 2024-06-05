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
        <h1 class="text-top-about">{{trans('website.services.storage.title')}}</h1>
        <div style="display: flex; align-items: center; margin-top:15px;">
            <p class="text-top2">HOME > </p>
            <p class="text-top2">&nbspServiços > </p>
            <p class="text-top2" style="text-decoration: underline;">&nbsp{{trans('website.services.storage.title')}}</p>
        </div>
    </div>
</section> 
<section class="row service-page-row">
    <div class="col-sm-12 col-md-5 col-xl-5 class-imagea hidden-xs" style="display: grid; align-content: center; justify-content: center;">
        <img class="visible-xl hidden-md hidden-sm  img2-about" src="{{ asset('assets/website/img/armazenagem-lg.png') }}" alt="sobre-nos" style="width:auto; max-height: 540px;">
        <img class="hidden-xl hidden-lg img2-about" src="{{ asset('assets/website/img/armazenagem-xs.png') }}" alt="sobre-nos" style="width:100%;">
    </div>
    <div class="col-sm-12 col-md-7 col-xl-7 " style=" display: flow; align-content: center;">
        <div class="rectangleab-storage" style="margin-top: 0">
            <h4 class="about-home">{{trans('website.services.storage.title')}}</h4>
            <h2 class="about2-home">{{trans('website.services.storage.pageSubtitle')}}</h2>
            <p class="textabout-home">{{trans('website.services.storage.longDescription')}}</p>
        </div>
    </div>
    <div class="col-sm-12 col-md-5 col-xl-5 class-imagea visible-xs" style="display: grid; align-content: center; justify-content: center;">
        <img class="img2-about" src="{{ asset('assets/website/img/storage-lg.png') }}" alt="sobre-nos" style="width:auto; max-height: 540px;">
    <div class="col-sm-12 col-md-5 col-xl-5 class-imagea" style="display: flow; align-content: center;">
</section>
@include('website.partials.qualities')
{{-- @include('partials.recruitment') --}}

@stop
@section ('styles')
    <style>
        .row {
            margin-right: 0px;
            margin-left: 0px;
        }
    </style>

@stop
{{-- @section ('scripts')
    <script>
        Copy code$("#myImage").hover(function() { 
            $(this).attr("src", "/assets/website/img/seguranca-storagebranco.svg"); 
        }); 

    </script>

@stop --}}

