@section('title')
    {{ config('app.name') }} |
@stop

@section('metatags')
    <meta name="description" content="">
    <meta property="og:title" content="">
    <meta property="og:description" content="">
    <meta property="og:image" content="{{ trans('seo.og-image.url') }}">
    <meta property="og:image:width" content="{{ trans('seo.og-image.width') }}">
    <meta property="og:image:height" content="{{ trans('seo.og-image.height') }}">
    <meta name="robots" content="index, follow">
@stop

@section('content')
<div class="container account-container">
    <div class="col-md-10 col-md-offset-1">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-7">
                        <div class="content-left">
                            <div class="logo">
                                <img src="{{ asset('assets/img/logo/logo_sm.png') }}">
                            </div>
                            @if(Setting::get('company_address'))
                            <ul class="list-unstyled contacts address hidden-xs">
                                <li>
                                    <h4>
                                        <i class="fas fa-fw fa-map-marker-alt"></i> {{ Setting::get('company_name') }}<br/>
                                        <small class="pull-left">{{ Setting::get('company_address') }}<br/>{{ Setting::get('company_zip_code') }} {{ Setting::get('company_city') }}
                                        </small>
                                    </h4>
                                </li>
                            </ul>
                            @endif
                            <div class="clearfix"></div>
                            <ul class="list-unstyled list-inline contacts m-t-10">
                                @if(Setting::get('company_phone'))
                                <li>
                                    <h4>
                                        <i class="fas fa-fw fa-phone"></i> Telefone<br/>
                                        <small>{{ Setting::get('company_phone') }}</small>
                                        <small style="font-size: 11px;display: block;line-height: 10px;">Chamada para rede fixa nacional</small>
                                    </h4>
                                </li>
                                @endif

                                @if(Setting::get('company_mobile'))
                                <li>
                                    <h4>
                                        <i class="fas fa-fw fa-mobile"></i> Telemóvel<br/>
                                        <small>{{ Setting::get('company_mobile') }}</small>
                                        <small style="font-size: 11px;display: block;line-height: 10px;">Chamada para rede móvel nacional</small>
                                    </h4>
                                </li>
                                @endif

                                @if(Setting::get('company_email'))
                                <li class="email hidden-xs" style="width: 45%; position: absolute;">
                                    <h4>
                                        <i class="fas fa-fw fa-envelope"></i> E-mail<br/>
                                        <small>{{ Setting::get('company_email') }}</small>
                                    </h4>
                                </li>
                                @endif
                            </ul>
                            @if(Setting::get('company_email'))
                                <ul class="list-unstyled list-inline contacts m-t-10 m-b-30 visible-xs">
                                    <li class="email" style="width: 45%; position: absolute;">
                                        <h4>
                                            <i class="fas fa-fw fa-envelope"></i> E-mail<br/>
                                            <small>{{ Setting::get('company_email') }}</small>
                                        </h4>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            @endif
                            <hr style="margin: 5px 20px;"/>
                            <div class="row">
                                <div class="col-sm-2 col-lg-2 p-r-0 hidden-xs">
                                    <img src="{{ asset('assets/img/default/track_trace.svg') }}" class="w-100 m-t-10">
                                </div>
                                <div class="col-xs-12 col-sm-9 col-lg-10">
                                    <h3 class="m-t-0 m-b-5 bold">Track &amp; Trace</h3>
                                    <p class="text-muted">Procure e acompanhe as suas encomendas.</p>
                                    <form method="GET" action="{{ route('tracking.index') }}" accept-charset="UTF-8">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="Ex. 1234567890" name="tracking" type="text">
                                            <div class="input-group-btn">
                                                <button type="submit" class="btn btn-primary">
                                                    Procurar <span class="hidden-xs">Envio</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="content-right">
                            <h2 class="text-primary m-b-30 m-t-15">Iniciar Sessão</h2>
                            <div class="home-alerts">
                                @include('partials.alerts')
                            </div>
                            {{ Form::open(['route' => 'account.login.submit', 'method' => 'POST']) }}
                            <div class="form-group">
                                {{ Form::label('email', 'E-mail', ['class' => 'control-label']) }}
                                {{ Form::text('email', null, ['class' => 'form-control nospace', 'autofocus', 'required']) }}
                            </div>
                            <div class="form-group" style="position: relative">
                                {{ Form::label('password', 'Palavra-passe') }}
                                <span class="btn-show-pass">
                                    <i class="far fa-eye"></i>
                                </span>
                                {{ Form::password('password', ['class' => 'form-control', 'required']) }}
                            </div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <label style="padding: 0; margin-bottom: 5px;">
                                        <input type="checkbox" name="remember"> {{ trans('auth.remember') }}
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-block btn-primary">Iniciar Sessão</button>
                                <div class="spacer-15"></div>
                                <a href="#" data-toggle="modal" data-target="#reset-password" class="reset-password">
                                    <i class="fas fa-lock"></i> {{ trans('auth.forgot.title') }}
                                </a>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-md-offset-1">
        @if(!env('APP_HIDE_CREDITS'))
            <p class="credits">
                <span class="p-t-3 pull-left">©{{ date('Y') }}.
                    <span class="hidden-xs">{{ Setting::get('company_permit') ? 'Alvará '. Setting::get('company_permit') : '' }}</span>
                    | <a href="{{ route('legal.show') }}">Avisos Legais</a>
                    @if(in_array(Setting::get('app_country'), ['pt', 'ptmd', 'ptac']))
                    &bull; <a href="https://www.livroreclamacoes.pt/" target="_blank">Livro Reclamações</a>
                    @endif
                </span>
                <a href="https://www.enovo.pt" class="hide"><img src="https://enovo.pt/assets/img/signatures/enovo_color.png" /></a>
                {!! app_brand(null, 'pull-right h-20px m-t-3') !!}
            </p>
        @endif
    </div>
</div>
@include('auth.passwords.email')
@stop

@section('styles')
    <style>
        @media (max-width: 767px) {
            body {
                padding-top: 0 !important;
            }
        }
    </style>
@stop