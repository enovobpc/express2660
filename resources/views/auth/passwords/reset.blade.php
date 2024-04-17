@section('title')
    {{ trans('auth.reset.title') }} |
@stop

@section('metatags')
    <meta name="robots" content="noindex, nofollow">
@stop

@section('content')
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h1>{{ trans('auth.reset.title') }}</h1>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="spacer-30"></div>
            <div class="row">
                <div class="col-sm-1 col-md-2"></div>
                <div class="col-sm-8 col-sm-offset-1 col-md-6">
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

                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-primary">
                            {{ trans('auth.reset.title') }}
                        </button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="spacer-50"></div>
    </section>
@endsection
