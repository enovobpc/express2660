@section('title')
{{ trans('website.seo.account.title') }} |
@stop

@section('metatags')
<meta name="description" content="{{ trans('website.seo.account.description') }}">
<meta property="og:title" content="{{ trans('website.seo.account.facebook.title') }}">
<meta property="og:description" content="{{ trans('website.seo.account.facebook.description') }}">
<meta property="og:image" content="{{ trans('website.seo.og-image.url') }}">
<meta property="og:image:width" content="{{ trans('website.seo.og-image.width') }}">
<meta property="og:image:height" content="{{ trans('website.seo.og-image.height') }}">
@stop

@section('content')
    <section class="header-title">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-7">
                    <h1>Área do Cliente</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="">
        <div class="container">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-md-offset-3">
                @include('partials.alerts')
                {{ Form::open(['route' => 'account.login.submit', 'method' => 'POST']) }}
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    {{ Form::text('email', null, ['class' => 'form-control', 'autofocus', 'required', 'placeholder' => 'Email']) }}
                </div>
                <div class="sp-15"></div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    {{ Form::password('password', ['class' => 'form-control', 'required', 'placeholder' => 'Password']) }}
                </div>
                <div class="sp-15"></div>
                <div class="form-group">
                    <button type="submit"
                        class="btn btn-block btn-primary btn-submit">{{ trans('website.word.login') }}</button>
                    <div class="sp-15"></div>
                    <a href="#" data-toggle="modal" data-target="#reset-password">
                        <i class="fas fa-lock"></i> {{ trans('auth.forgot.title') }}
                    </a>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </section>
@include('auth.passwords.email')
@endsection

@section('scripts')
    <script>
        $('input').iCheck(Init.iCheck())
    </script>
@stop