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
<section class="topo-about" style="height: 380px; position:relative; background: url('/assets/website/img/contactos-topo.png'); background-size: cover; background-position: bottom 0px left 0px; display: flex; align-items: flex-end; display: flex; align-items: center;">
    <div class="col-sm-12 todos-topos">
        <h1 class="text-top-about">Área de Cliente</h1>
        <div style="display: flex; align-items: center; margin-top:15px;">
            <p class="text-top2">HOME > </p>
            <p class="text-top2" style="text-decoration: underline;">&nbspÁrea Cliente</p>
        </div>
    </div>
</section> 
    <section class="">
        <div class="container">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-md-offset-3 m-t-50">
                
                <h1>Iniciar Sessão</h1>
                <br>
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