@section('content')
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100 @if($errors->has('email')) animated shake @endif">
                {{ Form::open(array('route' => 'admin.password.forgot.email', 'class' => 'login100-form validate-form')) }}
                <span class="login100-form-title" style="padding-bottom: 48px">
                    <img src="{{ asset('assets/img/logo/logo_sm.png') }}"/>
                </span>
                <div class="main-block">
                    @if (session('status'))
                        <div class="help-block block-success">
                            <i class="zmdi zmdi-check"></i> {{ session('status') }}
                        </div>
                    @endif

                    @if($errors->has('email'))
                        <div class="help-block">
                            <i class="zmdi zmdi-alert-circle"></i> {{ $errors->first('email') }}
                        </div>
                    @endif
                    <div class="wrap-input100 validate-input" data-validate="Insira um e-mail válido.">
                        {{ Form::text('email', null, ['class' => 'input100 nospace']) }}
                        <span class="focus-input100" data-placeholder="E-mail"></span>
                    </div>

                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button class="login100-form-btn">
                                Recuperar Palavra-Passe
                            </button>
                        </div>
                    </div>

                    <div class="text-center" style="padding-top: 20px; line-height: 0;">
                        <span class="txt1">Sabe qual é a sua palavra-passe?</span>
                        <br/>
                        <a href="{{ route('admin.login') }}" class="txt2">
                            Iniciar Sessão
                        </a>
                    </div>
                </div>

                <div class="text-center submit-loading" style="padding-top: 0; line-height: 0; display: none">
                    <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                    <h4>Aguarde por-favor.</h4>
                    <p>Estamos a enviar-lhe os dados de reposição.</p>
                    <div style="margin-bottom: 30px"></div>
                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button class="login100-form-btn">
                                A Enviar E-mail...
                            </button>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop