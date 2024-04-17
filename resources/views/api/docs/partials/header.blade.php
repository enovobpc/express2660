<header class="header fixed-top">
    <div class="branding docs-branding">
        <div class="container-fluid position-relative py-2">
            <div class="docs-logo-wrapper">
                <div class="site-logo">
                    <button id="docs-sidebar-toggler" class="docs-sidebar-toggler docs-sidebar-visible mr-2 d-xl-none" type="button">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <a class="navbar-brand" href="{{ route('api.docs.index', [$level, $version]) }}">
                        <img src="{{ asset('assets/img/logo/logo_sm.png') }}"
                             onerror="this.src = '{{ asset('assets/img/default/logo/logo_sm.png') }}'"
                             class="logo-icon me-2" style="height: 35px; margin-bottom: -7px">
                        <span class="logo-text">API <span class="text-alt">Docs</span> <span class="badge badge-level badge-{{ $level }}">{{ ucwords($level) }}</span></span>
                    </a>
                </div>
            </div>
            <div class="docs-top-utilities d-flex justify-content-end align-items-center">
                @foreach($allCategories as $categoryItem)
                    @if(empty($categoryItem->module) || hasModule($categoryItem->module))
                        <a href="{{ route('api.docs.index', [$level, $version, $categoryItem->slug]) }}" class="btn btn-sm {{ $categoryItem->slug == $categorySlug ? 'btn-primary' : 'btn-default' }} d-none d-lg-flex">
                            {!! $categoryItem->name !!}
                        </a>
                    @endif
                @endforeach
                <a href="{{ route('api.docs.index', [$level, $version, 'examples']) }}" class="btn btn-sm {{ $categorySlug == 'examples' ? 'btn-primary' : 'btn-default' }} d-none d-lg-flex">
                    Examples & Docs
                </a>
                {{--<a href="{{ route('api.docs.index', [$version, $categoryItem->slug]) }}" class="btn {{ $categoryItem->slug == $categorySlug ? 'btn-primary' : 'btn-default' }} d-none d-lg-flex">
                   PT
                </a>--}}
            </div>
        </div>
    </div>
</header>