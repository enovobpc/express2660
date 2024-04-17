<header>
    <div class="primary-header">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <div class="language-area">&nbsp;</div>
                    <a href="{{ route('account.index') }}" class="login-btn">
                        {{ trans('website.navbar.account') }}
                    </a>
                    <div class="btn-group language-btn pull-right">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Lang::locale() }} 
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
                                <p><i class="fas fa-phone"></i>{{ trans('website.word.phone') }}| Whatsapp</p>
                                <a href="">{{ Setting::get('support_phone_1') }}</a>
                            </li>
                            <li>
                                <p><i class="fas fa-envelope"></i>Email</p>
                                <a href="">{{ Setting::get('support_email_1') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-xl-3">
                    <a href="{{ route('home.index') }}" class="logo" title="2660"> 
                        <img src="{{ asset('assets/img/logo/logo.svg') }}" alt="2660 Express" style="max-height: 50px; height:50px;" /> 
                    </a>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-9 col-xl-9 custom-nav">
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
                                <li>
                                    <a href="{{ route('home.index') }}" title="{{ trans('website_global.menu.services') }}">
                                        Home
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('home.index') }}#{{ trans('website.routes.about') }}" title="{{ trans('website.navbar.about') }}">
                                        {{ trans('website.navbar.about') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('home.index') }}#{{ trans('website.routes.services') }}" title="{{ trans('website.navbar.services') }}">
                                        {{ trans('website.navbar.services') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('home.index') }}#{{ trans('website.routes.tracking') }}" title="{{ trans('website_global.menu.contacts') }}">
                                        {{ trans('website.tracking.title') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('home.index') }}#{{ trans('website.routes.contacts') }}" title="{{ trans('website.navbar.contacts') }}">
                                        {{ trans('website.navbar.contacts') }}
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