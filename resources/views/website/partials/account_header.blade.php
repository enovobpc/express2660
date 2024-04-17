<section class="page-header account-page-header" style="padding: 23px 0;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-2 visible-xs">
                <button type="button" class="btn btn-default toggle-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="col-xs-8 col-sm-6">
                <div class="account-avatar">
                    <div class="pull-left m-r-10">
                        <a href="{{ route('account.details.index', ['tab' => 'login']) }}">
                            @if($auth->filepath)
                                <img src="{{ asset($auth->getCroppa(null, 200)) }}" class="user-avatar"/>
                            @else
                                <img src="{{ asset('assets/img/default/avatar.png') }}" class="user-avatar"/>
                            @endif
                        </a>
                    </div>
                    <div class="pull-left">
                        <h2 class="text-uppercase account-name">
                            <small>My CdT</small><br/>
                            {{ str_limit($auth->display_name, 35) }}
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-xs-2 col-sm-6">
                <ul class="list-inline m-b-0 settings">
                    <li><a href="{{ route('account.details.index', ['tab' => 'login']) }}"><i class="fas fa-cog"></i> Definições de Conta</a></li>
                    <li><a href="{{ route('account.logout') }}"><i class="fas fa-power-off"></i> Sair</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>