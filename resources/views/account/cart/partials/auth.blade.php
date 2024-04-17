<div class="col-sm-6">
    <div class="register-panel m-t-15">
        <h4 class="text-uppercase text-primary m-t-0 m-b-20">{{ trans('global.cart.first-buy.title') }}</h4>
        <p class="bigger-110 p-r-40">{{ trans('global.cart.first-buy.text-01') }}</p>
        <a href="{{ route('cart.index') }}" class="btn btn-default m-t-10">
            <i class="fas fa-angle-left"></i> Voltar ao Cesto
        </a>
        <a href="{{ route('cart.checkout.buy-without-account') }}" class="btn btn-primary m-t-10">
            Continuar Compra
            <i class="fas fa-angle-right"></i> &nbsp;
        </a>
    </div>
</div>
<div class="col-sm-6 col-md-4 col-md-offset-1">
    <div class="cart-login-panel">
        <h4 class="text-uppercase text-primary m-t-15">{{ trans('global.cart.login.title') }}</h4>
        {{ Form::open(['route' => 'customers.login.submit', 'method' => 'POST']) }}
        {{ Form::hidden('source', 'cart') }}
        <div class="form-group">
            {{ Form::label('email', trans('global.word.email'), ['class' => 'control-label']) }}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-fw fa-envelope"></i>
                </span>
                {{ Form::email('email', null, ['class' => 'form-control', 'autofocus', 'required']) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('password', trans('global.word.password')) }}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-fw fa-lock"></i>
                </span>
                {{ Form::password('password', ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="form-group">
            <div class="pretty p-icon">
                <input type="checkbox" name="remember">
                <div class="state">
                    <i class="icon fa fa-check"></i>
                    <label>{{ trans('auth.remember') }}</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <button type="submit" class="btn btn-primary">{{ trans('global.word.login') }}</button>
            </div>
            <div class="col-sm-8">
                <a href="#" data-toggle="modal" data-target="#reset-password" class="m-t-5 pull-right">
                    <i class="fa fa-lock"></i> {{ trans('auth.forgot.title') }}
                </a>
            </div>
        </div>
        {{ Form::close() }}
        <div class="spacer-30"></div>
    </div>
</div>
{{--<div class="col-xs-12">
    <hr/>
    <a href="{{ route('cart.index') }}" class="btn btn-default">
        <i class="fa fa-angle-left"></i> {{ trans('global.word.back') }}
    </a>
</div>--}}
@include('auth.passwords.email')
    @include('auth.modals.signup')

