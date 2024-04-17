<header>
    <div class="container">
        <div class="row">
            <div class="col-xs-2 visible-xs">
                <button type="button" class="btn btn-default toggle-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="col-xs-7 col-sm-4 center-middle-xs">
                <a href="{{ route('home.index') }}">
                @if($auth->agency->filepath)
                    <span class="helper"></span>
                    <img src="{{ asset('assets/img/logo/logo_sm.png') }}" class="header-logo"/>
                @else
                    <h1 class="title bold">{{ $auth->agency->company  }}</h1>
                @endif
                </a>
            </div>
            <div class="col-xs-3 col-sm-8">
                <ul class="list-unstyled m-0">
                    {{-- @if(hasModule('products') && !@$auth->settings['hide_products_sales'])
                    <li>
                        <div class="btn-group btn-username pull-right m-r-15" role="group">
                            <a href="{{ route('account.cart.index') }}">
                                <h4 class="pull-left m-0 text-left">
                                    <i class="fa fa-shopping-bag" style="font-size: 25px; padding:10px"></i>
                                </h4>
                            </a>
                        </div>
                    </li>
                    @endif --}}
                    <li>
                        <div class="btn-group btn-username pull-right" role="group">
                            <button type="button" class="btn btn-user dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                @if($auth->filepath)
                                    <img src="{{ asset($auth->getThumb()) }}" onerror="this.src='{{ asset("assets/img/default/avatar.png") }}'"/>
                                @else
                                    <img src="{{ asset('assets/img/default/avatar.png') }}"/>
                                @endif
                                <span class="username hidden-xs">
                            {{ str_limit($auth->display_name, 35) }}<br/>
                            <small>{{ $auth->email }}</small>
                        </span>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('account.details.index') }}">
                                        <i class="fas fa-fw fa-user-cog"></i> {{ trans('account/global.header.edit-account') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('account.details.index', ['tab' => 'contacts']) }}">
                                        <i class="fas fa-fw fa-phone"></i> {{ trans('account/global.header.edit-contacts') }}
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="{{ route('account.logout') }}">
                                        <i class="fas fa-fw fa-power-off"></i> {{ trans('account/global.word.logout') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @if(config('app.source') == 'activos24')
                        <li>
                            <a href="{{ route('account.logistic.cart.show') }}" class="pull-right btn btn-user m-r-15 text-black"
                                        data-toggle="modal"
                                        data-target="#modal-remote-lg">
                                    <h4 class="pull-left m-0 text-left">
                                        <small>O meu pedido</small><br/>
                                        <b><i class="fas fa-shopping-bag"></i> <span class="cart-logistic-total">{{ \App\Models\Logistic\CartProduct::where('customer_id', $auth->id)->where(function ($q){$q->where('reference', NULL)->orWhere('closed', 0);})->sum('qty') }}</span> Items</b>
                                    </h4>
                                </a>
                        </li>
                    @endif
                    @if(hasModule('account_wallet') && !$auth->is_mensal)
                    <li>
                        <div class="btn-group btn-username pull-right m-r-15" role="group">
                            <button type="button" class="btn btn-user dropdown-toggle"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">
                                <h4 class="pull-left m-0 text-left">
                                    <small>Saldo Atual</small><br/>
                                    <b><span class="wallet-amount">{{ money($auth->wallet_balance) }}</span>{{ Setting::get('app_currency') }}</b>
                                </h4>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('account.wallet.create') }}"
                                    data-toggle="modal"
                                    data-target="#modal-remote-xs">
                                        <i class="fas fa-fw fa-credit-card"></i> Adicionar Saldo
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('account.wallet.index') }}">
                                        <i class="fas fa-fw fa-history"></i> Histórico de saldos
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</header>