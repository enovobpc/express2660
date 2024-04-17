@section('title')
    {{ trans('global.menu.cart') }} |
@stop

@section('metatags')
    <meta name="description" content="">
    <meta property="og:title" content="{{ trans('global.menu.cart') }}">
    <meta property="og:description" content="">
    <meta property="og:image" content="{{ asset('assets/img/og_image.png') }}">
@stop

@section('content')
    <div class="container">
        <div class="row">
            {{--<div class="col-sm-12">
                <ol class="breadcrumb">
                    <li><a href="{{ route('home.index') }}">{{ trans('global.menu.home') }}</a></li>
                    <li><a href="{{ route('cart.index') }}">{{ trans('global.menu.cart') }}</a></li>
                    <li class="active">{{ trans('global.cart.conclude.title') }}</li>
                </ol>
            </div>--}}
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <br>
                </ol>
            </div>
            <div class="col-sm-12">
                <h1 class="title pull-left">Cesto de Compras</h1>
            </div>
            <div class="col-sm-12">
                @include('partials.alerts')
            </div>
            <div class="col-xs-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-7 col-md-8 col-lg-9">
                                <div class="tabbable-line">
                                    <ul class="nav nav-tabs nav-tabs-account">
                                        <li class="{{ $step == 'billing' ? 'active' : '' }}" data-href="{{ route('account.cart.checkout', 'billing') }}">
                                            <a href="#tab-billing" data-toggle="tab">
                                                {{--<i class="fas fa-fw fa-receipt hidden-xs"></i>--}}
                                                <span class="step-circle hidden-xs">1.</span> Dados de Faturação
                                            </a>
                                        </li>
                                        <li class="{{ $step == 'shipping' ? 'active' : ' ' }}" data-href="{{ route('account.cart.checkout', 'shipping') }}">
                                            <a href="#tab-shipping" data-toggle="tab">
                                                {{--<i class="fa fa-fw fa-truck hidden-xs"></i>--}}
                                                <span class="step-circle hidden-xs">2.</span> Dados de Envio
                                            </a>
                                        </li>
                                        <li class="{{ $step == 'payment' ? 'active' : ' ' }}" data-href="{{ route('account.cart.checkout', 'payment') }}">
                                            <a href="#tab-payment" data-toggle="tab">
                                                {{--<i class="fa fa-fw fa-credit-card hidden-xs"></i>--}}
                                                <span class="step-circle hidden-xs">3.</span> Formas de Pagamento
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content no-border" style="padding: 15px 0 0">
                                        <div class="tab-pane {{ $step == 'billing' ? 'active' : '' }}" id="tab-billing">
                                            @include('account.cart.partials.billing')
                                        </div>
                                        <div class="tab-pane {{ $step == 'shipping' ? 'active' : '' }}" id="tab-shipping">
                                            @include('account.cart.partials.shipping')
                                        </div>
                                        <div class="tab-pane {{ $step == 'payment' ? 'active' : '' }}" id="tab-payment">
                                            @include('account.cart.partials.payment')
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5 col-md-4 col-lg-3">
                                @include('account.cart.partials.resume_panel')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="spacer-30"></div>
@stop