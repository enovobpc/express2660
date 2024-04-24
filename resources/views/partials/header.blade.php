<header>
    <div class="primary-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-7 hidden-xs">
                    <ul class="list-inline m-0">
                        <li>
                            <a href="{{ Setting::get('facebook') }}" class="link-animated" target="_blank">
                                <i class="fas fa-facebook-official"></i> Facebook
                            </a>
                        </li>
                        <li class="m-l-15">
                            <a href="{{ route('about.index') }}" class="link-animated" title="{{ trans('website_global.menu.about-us') }}">
                                {{ trans('website_global.menu.about-us') }}
                            </a>
                        </li>
                       {{-- <li class="m-l-15">
                            <a href="{{ route('recruitment.index') }}" class="link-animated" title="{{ trans('website_global.menu.recruitment') }}">
                                {{ trans('website_global.menu.recruitment') }}
                            </a>
                        </li> --}}
                    </ul>
                </div>
                <div class="col-xs-12 col-sm-5">
                    <div class="language-area">&nbsp;</div>
                    <a href="{{ route('account.index') }}" class="login-btn">
                        @if($auth)
                         <i class="fas fa-user"></i> {{ str_limit($auth->display_name, 15) }}
                        @else
                        {{ trans('website_global.menu.customer-area') }}
                        @endif
                    </a>
                    <div class="btn-group language-btn pull-right">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Lang::locale() }} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach(trans('website_languages') as $code => $locale)
                            <li>
                                <a href="{{ LaravelLocalization::getLocalizedURL($code) }}">
                                    <span class="text-uppercase">{{ $code }}</span> - {{ $locale }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="white-end"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="main-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 hidden-xs">
                    <div class="call-us">
                        <ul class="list-unstyled list-inline">
                            <li>
                                <img src="{{ asset('assets/img/iphone.png') }}" alt="{{ trans('website_global.menu.customer-support') }}">
                                <span class="transport text-uppercase">
                                    {!! trans('website_global.header.customer-support') !!}
                                </span>
                            </li>
                            <li>
                                <a href="tel:(+351){{ Setting::get('phone_1') }}">{{ phone_format(Setting::get('phone_1')) }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <a href="{{ route('home.index') }}" class="logo" title="Asfalto Largo - Transportes & Logística"> 
                        <img src="{{ asset('assets/img/logo/logo_sm.png') }}" alt="Asfalto Largo - Transportes & Logística"> 
                    </a>
                </div>
                <div class="col-xs-12 col-md-9 custom-nav">
                    <nav class="navbar navbar-default">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-menu" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>

                        <div class="collapse navbar-collapse" id="main-menu">
                            <ul class="nav navbar-nav navbar-right">
                                <li id="menu-services" class="menu-services">
                                    <a href="{{ route('services.index') }}" title="{{ trans('website_global.menu.services') }}">
                                        {{ trans('website_global.menu.services') }}
                                    </a>
                                </li>
                                <li id="menu-delivery-areas">
                                    <a href="{{ route('delivery-areas.index') }}" title="{{ trans('website_global.menu.delivery-areas') }}">
                                        {{ trans('website_global.menu.delivery-areas') }}
                                    </a>
                                </li>
                                <li id="menu-be-customer">
                                    <a href="{{ route('be-customer.index') }}" title="{{ trans('website_global.menu.be-customer') }}">
                                        {{ trans('website_global.menu.be-customer') }}
                                    </a>
                                </li>
                                <li id="menu-quotation">
                                    <a href="{{ route('quotation.index') }}" title="{{ trans('website_global.menu.quotation') }}">
                                        {{ trans('website_global.menu.quotation') }}
                                    </a>
                                </li>
                                <li id="menu-about"  class="visible-xs">
                                    <a href="{{ route('about.index') }}" title="{{ trans('website_global.menu.about-us') }}">
                                        {{ trans('website_global.menu.about-us') }}
                                    </a>
                                </li>
                                <li id="menu-recruitment" class="visible-xs">
                                    <a href="{{ route('recruitment.index') }}" title="{{ trans('website_global.menu.recruitment') }}">
                                        {{ trans('website_global.menu.recruitment') }}
                                    </a>
                                </li>
                                <li id="menu-contacts">
                                    <a href="{{ route('contacts.index') }}" title="{{ trans('website_global.menu.contacts') }}">
                                        {{ trans('website_global.menu.contacts') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>