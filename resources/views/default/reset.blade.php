@section('title')
    Repôr Palavra-passe |
@stop

@section('metatags')
    <meta name="robots" content="no-index, no-follow">
@stop

@section('content')
    <div class="container">
        <div class="row">
        <div class="col-xs-12 col-md-8 col-md-offset-2 col-lg-4 col-lg-offset-4">
            <div class="card card-tracking">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="content-left">
                                <div class="text-center" style="margin-top: 15px; margin-bottom: 20px">
                                    <a href="{{ route('account.index') }}">
                                    <img src="{{ asset('assets/img/logo/logo_sm.png') }}" style="max-height: 50px; max-width: 200px">
                                    </a>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h4 class="m-t-0 m-b-0 bold text-center">Repôr Palavra-passe</h4>
                                        <div class="spacer-30"></div>

                                        {{ Form::open(['route' => 'account.password.reset.submit']) }}
                                        <input type="hidden" name="token" value="{{ $token }}">

                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                            {{ Form::label('email', 'E-mail') }}
                                            {{ Form::email('email', null, ['class' => 'form-control', 'autofocus']) }}
                                            @if ($errors->has('email'))
                                                <span class="help-block">{{ $errors->first('email') }}</span>
                                            @endif
                                        </div>

                                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                            {{ Form::label('password', 'Nova Palavra-passe') }}
                                            {{ Form::password('password', ['class' => 'form-control']) }}
                                            @if ($errors->has('password'))
                                                <span class="help-block">{{ $errors->first('password') }}</span>
                                            @endif
                                        </div>

                                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                            {{ Form::label('password_confirmation', 'Confirmar Nova Palavra-passe') }}
                                            {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
                                            @if ($errors->has('password_confirmation'))
                                                <span class="help-block">{{ $errors->first('password_confirmation') }}</span>
                                            @endif
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">
                                                Repôr Palavra-passe
                                            </button>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <div class="text-center m-t-20">
        {!! app_brand() !!}
    </div>
@stop
