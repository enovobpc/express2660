@section('content')
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100 @if($errors->has('email')) animated shake @endif">
                {{ Form::open(['route' => 'admin.password.reset.submit', 'class' => 'login100-form validate-form']) }}
                <input type="hidden" name="token" value="{{ $token }}">
                <span class="login100-form-title" style="padding-bottom: 48px">
                    <img src="{{ asset('assets/img/logo/logo_sm.png') }}"/>
                </span>
                <div class="main-block">
                    @if($errors->has('email'))
                        <div class="help-block block-success">
                            <i class="zmdi zmdi-alert-circle"></i> {{ $errors->first('email') }}
                        </div>
                    @endif
                    <div class="wrap-input100 validate-input"  data-validate="Insira um e-mail válido.">
                        {{ Form::email('email', null, ['class' => 'input100 nospace']) }}
                        <span class="focus-input100" data-placeholder="E-mail"></span>
                    </div>
                    <div class="wrap-input100 validate-input" data-validate="Indique a nova password.">
                        <span class="btn-show-pass">
                            <i class="zmdi zmdi-eye"></i>
                        </span>
                        {{ Form::password('password', ['class' => 'input100 nospace']) }}
                        <span class="focus-input100" data-placeholder="Nova Palavra-Passe."></span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Preencha este campo.">
                        {{ Form::password('password_confirmation', ['class' => 'input100 nospace']) }}
                        <span class="focus-input100" data-placeholder="Confirmar Palavra-Passe"></span>
                    </div>

                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button class="login100-form-btn">
                                Redefinir e Iniciar Sessão
                            </button>
                        </div>
                    </div>
                </div>

                <div class="text-center submit-loading" style="padding-top: 0; line-height: 0; display: none">
                    <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                    <h4>Bem-vindo de volta!</h4>
                    <p>Estamos a preparar a sua sessão.</p>
                    <div style="margin-bottom: 30px"></div>
                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button class="login100-form-btn">
                                A Iniciar Sessão...
                            </button>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
{{--
@section('content')

{{ Form::open(array('route' => array('admin.password.reset.submit'))) }}

    {{ csrf_field() }}
    {{ Form::hidden('token', $token) }}

    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
        {{ Form::email('email', null, array('class' => 'form-control', 'placeholder' => 'E-mail', 'required')) }}
        @if ($errors->has('email'))
        <span class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
        {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Nova palavra-passe', 'required')) }}
        @if ($errors->has('password'))
        <span class="help-block">
            <strong>{{ $errors->first('password') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
        {{ Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => 'Confirmar palavra-passe', 'required')) }}
        @if ($errors->has('password'))
        <span class="help-block">
            <strong>{{ $errors->first('password_confirmation') }}</strong>
        </span>
        @endif
    </div>
    <button type="submit" class="btn btn-primary btn-block">
        Redefinir palavra-passe
    </button>
    <br/>
    <div class="text-center">
        <a href="{{ route('admin.login') }}"><i class="fas fa-lock"></i> Iniciar Sessão</a>
    </div>
{{ Form::close() }}
@endsection--}}
