@if(Session::has('source_user_id'))
<div class="remote-login-warning">
    <i class="fas fa-exclamation-triangle"></i> @trans('Sessão iniciada como :name', ['name' => Auth::user()->name]). <a href="{{ route('admin.users.remote-logout',  Session::get('source_user_id')) }}">@trans('Voltar à minha sessão')</a>
</div>
@endif

@if(Setting::get('maintenance_mode'))
    <div class="remote-login-warning">
        <i class="fas fa-exclamation-triangle"></i> @trans('Sistema em modo de Manutenção.')</a>
    </div>
@endif

<header class="main-header">
    <a href="{{ route('admin.dashboard')}}" class="logo">
        <span class="logo-mini">
            <img src="{{ asset(config('app.logo_square')) }}" onerror="this.src = '{{ asset('assets/img/default/logo/logo_square_xs.png') }}'"/>
        </span>
        <span class="logo-lg">
            <img src="{{ asset(config('app.logo_xs')) }}" onerror="this.src = '{{ asset('assets/img/default/logo/logo_white_xs.png') }}'"/>
        </span>
    </a>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                {{-- NAVIGATE BETWEEN PLATFORMS --}}
                @if(Auth::user()->isAdmin())
                <li class="app-toggle-menu input-sm hidden-xs">
                    {{ Form::select('app_switcher', ['' => ''] + ($sources ?? []), config('app.source'), ['class' => 'form-control select2', 'data-base-url' => route('core.remote.auth')]) }}
                </li>
                @endif

                {{-- FAST SEARCH --}}
                <li>
                    <a href="#" data-toggle="modal" data-target="#fast-search">
                        <i class="fas fa-search"></i>
                    </a>
                </li>

                {{-- SHORTCUTS --}}
                {{-- @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'cashier,log_errors,shipments'))
                    <li class="dropdown user-menu hidden-xs">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span><i class="fas fa-external-link-square-alt"></i></span> <i class="caret"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <ul class="options-menu">
                                    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'shipments'))
                                        <li>
                                            <a href="{{ route('admin.shipments.create') }}" data-toggle="modal" data-target="#modal-remote-xl">
                                                <i class="fas fa-fw fa-truck"></i> @trans('Criar Envio')
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.shipments.budget.create') }}" data-toggle="modal" data-target="#modal-remote-lg">
                                                <i class="fas fa-fw fa-calculator"></i> Calcular Custos de Envio
                                            </a>
                                        </li>
                                    @endif

                                    @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'invoices'))
                                        <li>
                                            <a href="{{ route('admin.invoices.create') }}" data-toggle="modal" data-target="#modal-remote-xl">
                                                <i class="fas fa-fw fa-file-invoice"></i>  @trans('Criar Fatura')
                                            </a>
                                        </li>
                                    @endif

                                    <li>
                                        <a href="{{ route('admin.operator.tasks.index') }}" data-toggle="modal" data-target="#modal-remote-xl">
                                            <i class="fas fa-fw fa-tasks"></i> Gerir Tarefas aos Operadores
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif --}}

                @if(Auth::user()->ability(Config::get('permissions.role.admin'), 'notifications'))
                {{-- NOTIFICATIONS --}}
                <li class="dropdown notifications-menu" data-href="{{ route('admin.notifications.load') }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="label label-counter animated infinite tada" data-toggle="notifications-counter" style="{{ @$totalNotifications ? 'display: block' : 'display:none' }}">{{ @$totalNotifications }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">
                            <a href="{{ route('admin.notifications.all-read') }}" class="mark-all-read btn-read-all-notifications"> @trans('Marcar tudo como lido')</a>
                            <h5 class="pull-left bold m-b-0 m-t-5"> @trans('Notificações')</h5>
                            <div class="clearfix"></div>
                        </li>
                        <li>
                            <ul class="menu">
                                <li style="margin-top: 85px; text-align: center">
                                    <i class="fas fa-spin fa-circle-notch"></i>  @trans('A carregar...')
                                </li>
                            </ul>
                        <li class="footer"><a href="{{ route('admin.notifications.index') }}"> @trans('Ver todas')</a></li>
                    </ul>
                </li>
                @endif

                {{-- MOBILE APP
                @if(hasModule('app_apk') || hasModule('app'))
                    <li class="dropdown user-menu hidden-xs">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span><i class="fas fa-mobile-alt"></i></span> <i class="caret"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header bold">Aplicação móvel motoristas</li>
                            <li>
                                <ul class="options-menu">
                                    @if(hasModule('app_apk'))
                                    <li>
                                        <a href="{{ route('admin.mobile.install') }}"
                                           data-toggle="modal"
                                           data-target="#modal-remote">
                                            <i class="fab fa-fw fa-android"></i> Download App Android
                                        </a>
                                    </li>
                                    @else
                                        <li>
                                            <a href="#"
                                               style="color: #999"
                                                data-toggle="tooltip"
                                                title="A sua licença não inclui esta versão.">
                                                <i class="fab fa-fw fa-android"></i> Download App Android
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{ route('admin.mobile.install', ['mode' => 'web']) }}"
                                           data-toggle="modal"
                                           data-target="#modal-remote-xl">
                                            <i class="fab fa-fw fa-chrome"></i> Download App Web
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ coreUrl('mobile/manual-utilizacao.pdf') }}" target="_blank">
                                            <i class="fas fa-fw fa-book"></i> Manual de utilização
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif --}}

                {{-- CALENDAR --}}
                @if(hasPermission('calendar_events'))
                    <li>
                        <a href="{{ route('admin.calendar.events.index') }}">
                            <i class="fas fa-calendar-alt"></i>
                        </a>
                    </li>
                @endif

                <li class="notifications-menu">
                    <a href="{{ route('admin.customer-support.index') }}">
                        <i class="fas fa-headset"></i>
                        <span class="label label-counter animated infinite tada" data-toggle="notifications-counter" style="{{ @$totalSupportNotifications ? 'display: block' : 'display:none' }}">{{ @$totalSupportNotifications }}</span>
                    </a>
                </li>

                @if(0)
                <li>
                    <a href="{{ route('admin.calendar.events.index') }}">
                        <i class="fas fa-comment"></i>
                    </a>
                </li>
                @endif

                {{-- SETTINGS --}}
                @if(hasPermission('licenses') || hasModule('app_apk') || hasModule('app'))
                <li class="dropdown user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span><i class="fas fa-question-circle"></i></span> <span class="hidden-xs">@trans('Ajuda') <i class="caret"></i></span></a>
                    </a>
                    <ul class="dropdown-menu">
                    
                        <li class="header bold">@trans('Centro de Ajuda e Suporte')</li>
                        <li>
                            <ul class="options-menu" style="max-height: 500px">
                                @if(hasPermission('licenses'))
                                <li>
                                    <form action="{{ route('admin.helpcenter.index', 'search') }}" class="helpcenter-search" target="_blank" method="get" style="margin-top: -1px">
                                        <div class="input-group">
                                            {{ Form::text('search', null, ['class' => 'form-control', 'placeholder' => __('Procurar no manual de ajuda...')]) }}
                                            <div class="input-group-btn">
                                                <button class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </li>
                                <li>
                                    <a href="{{ route('admin.helpcenter.index', 'help') }}" target="_blank"
                                        class="text-yellow bold">
                                        <i class="fas fa-fw fa-question-circle"></i> @trans('Aceder ao Centro de Suporte')
                                        <i class="fas fa-angle-right pull-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.helpcenter.index', 'tickets') }}" target="_blank">
                                        <i class="fas fa-fw fa-headset"></i>  @trans('Pedidos de Suporte')
                                        <i class="fas fa-angle-right pull-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.helpcenter.index', 'meetings') }}" target="_blank">
                                        <i class="fas fa-fw fa-calendar-alt"></i>  @trans('Reuniões e Formação')
                                        <i class="fas fa-angle-right pull-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.helpcenter.index', 'billing') }}" target="_blank">
                                        <i class="fas fa-fw fa-euro-sign"></i>  @trans('Faturas e Pagamentos')
                                        <i class="fas fa-angle-right pull-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ knowledgeArticle(114) }}" target="_blank">
                                        <i class="fas fa-fw fa-puzzle-piece"></i>  @trans('Integração API/Lojas Online')
                                        <i class="fas fa-angle-right pull-right"></i>
                                    </a>
                                </li>
                                @endif
                                @if(hasModule('app_apk') || hasModule('app'))
                                    <li class="header bold">@trans('App Mobile Motoristas')</li>
                                    @if(hasModule('app_apk'))
                                        <li>
                                            <a href="https://play.google.com/store/apps/details?id=my.application.enovotms"
                                                target="_blank">
                                                <i class="fab fa-fw fa-android"></i> @trans('Download App Android')
                                            </a>

                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{ route('admin.mobile.install', ['mode' => 'web']) }}"
                                            data-toggle="modal"
                                            data-target="#modal-remote-xl">
                                            <i class="fab fa-fw fa-chrome"></i> @trans('Download App Web (iOS)')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ coreUrl('mobile/manual-utilizacao.pdf') }}" target="_blank">
                                            <i class="fas fa-fw fa-book"></i> @trans('Manual de utilização App')
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- SETTINGS --}}
                @if(Auth::user()->isAdmin())
                    <li class="dropdown user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span><i class="fas fa-wrench"></i></span> <i class="caret"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header bold">Ferramentas de Administrador</li>
                            <li>
                                <ul class="options-menu dinamic-height">
                                    <li>
                                        <a href="{{ route('admin.settings.index', ['tab' => 'maintenance']) }}" target="_blank" class="text-blue">
                                            <i class="fas fa-fw fa-wrench text-blue"></i> Definições Gerais
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.logs.errors.index') }}" target="_blank" class="text-red">
                                           <i class="fas fa-fw fa-exclamation-triangle"></i> Erros do sistema
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('core.license.index') }}"
                                           data-toggle="modal"
                                           data-target="#modal-remote-lg" class="text-yellow">
                                            <i class="fas fa-fw fa-bookmark"></i> Licença e Manutenção
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.core.install.index') }}" class="text-purple">
                                            <i class="fas fa-fw fa-toolbox"></i> Instalação e Setup
                                        </a>
                                    </li>
                                    <li style="border-top: 2px solid #ccc; margin-top: -1px;"></li>
                                    <li>
                                        <a href="{{ route('admin.cpanel.emails.index') }}" class="text-info">
                                            <i class="fas fa-fw fa-envelope"></i> Espaço e Contas E-mail
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.webservice-methods.index') }}">
                                            <i class="fas fa-fw fa-plug"></i> Métodos dos Webservices
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.notices.index') }}">
                                            <i class="fas fa-fw fa-info-circle"></i> Notificações emitidas
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('core.provider.agencies.index') }}">
                                            <i class="fas fa-fw fa-home"></i> Gerir Agências Fornecedor
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.api.docs.methods.index') }}">
                                            <i class="fas fa-fw fa-book"></i> Gerir Documentação API
                                        </a>
                                    </li>
                                    <li style="border-top: 2px solid #ccc; margin-top: -1px;"></li>
                                    <li>
                                        <a href="{{ route('admin.roles.index') }}" class="text-info">
                                            <i class="fas fa-fw fa-users"></i> Perfis e Permissões
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.api.index') }}">
                                            <i class="fas fa-fw fa-plug"></i> Chaves API
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- USER ACCOUNT --}}
                <li class="dropdown user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ Auth::user()->filepath ? asset(Auth::user()->filepath) : '' }}" class="user-image" alt="{{ Auth::user()->name }}" onerror="this.src='{{ asset('assets/img/default/avatar.png') }}';">
                        <span class="hidden-xs">{{ Auth::user()->name }}</span> <i class="caret"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <ul class="options-menu">
                                <li>
                                    <a href="{{ route('admin.version.about') }}" data-toggle="modal" data-target="#modal-remote-xs">
                                        <i class="fas fa-fw fa-info-circle"></i> @trans('Sobre o ENOVO TMS')
                                        <i class="fas fa-angle-right pull-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.account.edit') }}" data-toggle="modal" data-target="#modal-remote-xs">
                                        <i class="flag-icon flag-icon-{{ App::getLocale() }}"></i> @trans('Idioma'): {{ trans('locales.'.App::getLocale()) }}
                                        <i class="fas fa-angle-right pull-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.account.edit', ['action' => 'password']) }}" data-toggle="modal" data-target="#modal-remote-xs">
                                        <i class="fas fa-fw fa-lock"></i> @trans('Alterar palavra-passe')
                                        <i class="fas fa-angle-right pull-right"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.logout') }}">
                                        <i class="fas fa-fw fa-power-off"></i> @trans('Terminar Sessão')
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="hidden-xs">
                    <a href="{{ route('admin.logout') }}"  data-toggle="tooltip" title="@trans('Terminar Sessão')" data-placement="bottom">
                        <i class="fas fa-power-off fa-lg"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>
