@section('title')
    {{ trans('website.legal.terms.subtitle') }} |
@stop

@section('metatags')
    <meta property="og:title" content="{{ trans('website.legal.terms.title') }}">
    <meta property="og:description" content="">
    <meta property="og:image" content="{{ trans('website.seo.image.url') }}">
    <meta property="og:image:width" content="{{ trans('website.seo.image.width') }}">
    <meta property="og:image:height" content="{{ trans('website.seo.image.height') }}">
@stop

@section('content')
<div id="slider" class="carousel carousel-banner carousel-fade slide" data-ride="carousel">
    <div class="carousel-inner" role="listbox">
        <div class="item active">
            <div style="background-image: url({{ asset('assets/website/img/page-bg.png') }})" class="page-bg"></div>
            {{-- {{--<img src="{{ asset('assets/img/website/home_bg.jpg') }}" class="width-100"> --}}
        </div>
    </div>
</div>
<div class="title-page">
    <img src="{{ asset('assets/website/img/page-bar-big.png') }}" alt="" >
    <h2 class="text-white">{!! trans('website.documents.title')!!}</h2>
</div>
    <section class="legal">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1 style="margin-left:0;" class="title">{{ trans('website.legal.terms.title') }}</h1>
                </div>
                <div class="col-xs-12 col-lg-12">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </section>
@stop