@extends('layouts.default.style01')

@section('title')
    {{ config('app.name') }} |
@stop

@section('metatags')
    <meta name="description" content="">
    <meta property="og:title" content="">
    <meta property="og:description" content="">
    <meta property="og:image" content="{{ trans('seo.og-image.url') }}">
    <meta property="og:image:width" content="{{ trans('seo.og-image.width') }}">
    <meta property="og:image:height" content="{{ trans('seo.og-image.height') }}">
    <meta name="robots" content="index, follow">
@stop

@section('content')
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-7">
                <img src="{{ asset('assets/img/logo/logo.svg') }}" style="max-height: 65px">
                <p class="mb-4">
                    &nbsp;
                </p>
                {{--<form action="#" method="post">
                    <div class="form-group first">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" placeholder="your-email@gmail.com" id="username">
                    </div>
                    <div class="form-group last mb-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" placeholder="Your Password" id="password">
                    </div>

                    <div class="d-flex mb-5 align-items-center">
                        <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                            <input type="checkbox" checked="checked"/>
                            <div class="control__indicator"></div>
                        </label>
                        <span class="ml-auto"><a href="#" class="forgot-pass">Forgot Password</a></span>
                    </div>

                    <input type="submit" value="Log In" class="btn btn-block btn-primary">
                </form>--}}


                <div style="position: relative">
                    <div class="login-panel">
                        {{ Form::open(['route' => 'account.login.submit', 'method' => 'POST']) }}
                        <div class="form-group">
                            {{ Form::label('email', trans('account/global.word.email'), ['class' => 'control-label']) }}
                            {{ Form::text('email', null, ['class' => 'form-control nospace', 'autofocus', 'required']) }}
                        </div>
                        <div class="form-group" style="position: relative">
                            {{ Form::label('password', trans('account/global.word.password')) }}
                            <span class="btn-show-pass">
                                <i class="far fa-eye"></i>
                            </span>
                            {{ Form::password('password', ['class' => 'form-control', 'required']) }}
                        </div>
                        @if (Session::has('errors'))
                            <p class="text-red bold" style="margin-top: -10px"><i class="fas fa-times-circle"></i> {{ Session::get('errors')->first() }}</p>
                        @endif
                        <div class="d-flex m-b-15 align-items-center">
                            <div class="checkbox">
                                <label style="padding: 0; margin-bottom: 5px;">
                                    <input type="checkbox" name="remember"> {{ trans('account/global.auth.remember-me') }}
                                </label>
                            </div>
                            <span class="ml-auto">
                                <a href="#" class="reset-password forgot-pass">
                                    <i class="fas fa-lock"></i> {{ trans('auth.forgot.title') }}
                                </a>
                            </span>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-lg btn-block btn-primary">{{ trans('account/global.auth.login') }}</button>
                        </div>
                        {{ Form::close() }}
                    </div>
                    <div class="recovery-panel" style="display: none">
                        {{ Form::open(array('route' => array('account.password.email'))) }}
                        <h5 class="m-b-30 text-primary"><i class="fas fa-key"></i> {{ trans('account/global.auth.forgot') }}</h5>
                        <div class="form-group">
                            {{ Form::label('email', 'E-mail da conta cliente') }}
                            {{ Form::email('email', null, array('class' => 'form-control nospace email lowercase', 'autocomplete' => 'off', 'required')) }}
                        </div>
                        <button type="submit" class="btn btn-lg btn-block btn-primary" data-loading-text="A enviar pedido...">{{ trans('account/global.auth.forgot') }}</button>
                        <a href="#" class="m-t-10 reset-login" style="display: block"><i class="fas fa-lock"></i> {{ trans('account/global.auth.login') }}</a>
                        {{ Form::close() }}
                    </div>
                </div>
                <hr style="margin: 30px 0;"/>
                <div class="text-center show-trk">
                    <a href="#">{{ trans('account/global.auth.tracking') }} <i class="fas fa-arrow-circle-right"></i></a>
                </div>
                {{ Form::open(['route' => 'tracking.index', 'method' => 'GET', 'class' => 'search-guide','style' => 'display: none']) }}
                <h5><i class="fas fa-check-circle"></i> {{ trans('account/global.auth.tracking') }}</h5>
                <div class="form-group form-group-sm">
                    <div class="input-group">

                        {{ Form::text('tracking', null, ['class' => 'form-control nospace', 'required', 'placeholder' => trans('account/global.auth.tracking-placeholder')]) }}
                        <div class="input-group-addon">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-arrow-circle-right"></i></button>
                        </div>
                    </div>

                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <style>
        .recovery-panel {

        }
    </style>
@stop

@section('scripts')
    <script>
        /*==================================================================
            [ Show pass ]*/
        var showPass = 0;
        $('.btn-show-pass').on('click', function(){
            if(showPass == 0) {
                $(this).next('input').attr('type','text');
                $(this).find('i').removeClass('fa-eye');
                $(this).find('i').addClass('fa-eye-slash');
                showPass = 1;
            }
            else {
                $(this).next('input').attr('type','password');
                $(this).find('i').addClass('fa-eye');
                $(this).find('i').removeClass('fa-eye-slash');
                showPass = 0;
            }
        });

        $('.reset-password').on('click', function(){
            $('.login-panel').hide();
            $('.recovery-panel').show();
        })

        $('.reset-login').on('click', function(){
            $('.login-panel').show();
            $('.recovery-panel').hide();
        })

        $('.show-trk').on('click', function(){
            $('.search-guide').slideDown();
            $(this).hide();
        })
    </script>
@stop