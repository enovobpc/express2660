@section('title')
    {{ ucwords($level) }}
@stop

@section('content')
    @include('api.docs.partials.header')
    <div class="page-header theme-bg-dark py-5 text-center position-relative">
        <div class="theme-bg-shapes-right"></div>
        <div class="theme-bg-shapes-left"></div>
        <div class="container">
            <h1 class="page-heading single-col-max mx-auto">{{ $category->name }}</h1>
            <div class="page-intro single-col-max mx-auto">{!! $category->description !!}</div>
            {{-- <div class="main-search-box pt-3 d-block mx-auto">
                <form class="search-form w-100">
                    <input type="text" placeholder="Search the docs..." name="search" class="form-control search-input">
                    <button type="submit" class="btn search-btn" value="Search"><i class="fas fa-search"></i></button>
                </form>
            </div>--}}
        </div>
    </div>
    <div class="page-content">
        <div class="container">
            <div class="docs-overview py-5">
                <div class="row justify-content-center">
                    @if($allSections->isEmpty())
                        <div class="col-12 col-lg-6 py-5 my-5">
                            <h2 class="text-center text-muted">
                                <i class="fas fa-lock"></i><br/>
                                Access unavailable
                            </h2>
                            <p class="text-center">
                                The features of this API are not available to your account.<br/>
                                For more information, contact your supplier.
                            </p>
                        </div>
                    @else
                        @foreach($allSections as $section)
                        <div class="col-12 col-lg-4 py-3">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        <span class="theme-icon-holder card-icon-holder me-2">
                                            <i class="fas {{ $section->icon }}"></i>
                                        </span>
                                        <span class="card-title-text">{{ $section->name }}</span>
                                    </h5>
                                    <div class="card-text">
                                        {{ $section->description }}
                                    </div>
                                    <a class="card-link-mask" href="{{ route('api.docs.index', [$level, $version, $section->category_id, $section->slug]) }}{{ '#section-'.$section->slug }}"></a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{--<section class="cta-section text-center py-5 theme-bg-dark position-relative">
        <div class="theme-bg-shapes-right"></div>
        <div class="theme-bg-shapes-left"></div>
        <div class="container">
            <h3 class="mb-2 text-white mb-3">Launch Your Software Project Like A Pro</h3>
            <div class="section-intro text-white mb-3 single-col-max mx-auto">Want to launch your software project and start getting traction from your target users? Check out our premium <a class="text-white" href="https://themes.3rdwavemedia.com/bootstrap-templates/startup/coderpro-bootstrap-5-startup-template-for-software-projects/">Bootstrap 5 startup template CoderPro</a>! It has everything you need to promote your product.</div>
            <div class="pt-3 text-center">
                <a class="btn btn-light" href="https://themes.3rdwavemedia.com/bootstrap-templates/startup/coderpro-bootstrap-5-startup-template-for-software-projects/">Get CoderPro<i class="fas fa-arrow-alt-circle-right ml-2"></i></a>
            </div>
        </div>
    </section>--}}
@stop