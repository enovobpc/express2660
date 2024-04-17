@section('content')
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100 @if($errors->has('email')) animated shake @endif">
                {{ Form::open(array('route' => 'admin.login.submit', 'class' => 'login100-form validate-form')) }}
                <span class="login100-form-title" style="padding-bottom: 48px">
                    @if(File::exists(public_path() . '/assets/img/logo/logo.svg'))
                        <img src="{{ asset('assets/img/logo/logo.svg') }}"/>
                    @elseif(File::exists(public_path() . '/assets/img/logo/logo_sm.png'))
                        <img src="{{ asset('assets/img/logo/logo_sm.png') }}" onerror="this.src = '{{ asset('assets/img/default/logo/logo_sm.png') }}'"/>
                    @else
                        <h4>{{ Setting::get('company_name') }}</h4>
                    @endif

                </span>
                <div class="main-block">
                    @if($errors->has('email'))
                    <div class="help-block">
                        <i class="zmdi zmdi-alert-circle"></i> {{ $errors->first('email') }}
                    </div>
                    @endif
                    <div class="wrap-input100 validate-input" data-validate="{{ trans('admin/auth.login.empty-email') }}">
                        {{ Form::text('email', null, ['class' => 'input100 nospace']) }}
                        <span class="focus-input100" data-placeholder="{{ trans('admin/auth.login.email') }}"></span>
                    </div>
                    <div class="wrap-input100 validate-input" data-validate="{{ trans('admin/auth.login.empty-password') }}">
                        <span class="btn-show-pass">
                            <i class="zmdi zmdi-eye"></i>
                        </span>
                        {{ Form::password('password', ['class' => 'input100 nospace']) }}
                        <span class="focus-input100" data-placeholder="{{ trans('admin/auth.login.password') }}"></span>
                    </div>

                    <label class="text-center remember-btn">
                        <div class="pretty p-svg">
                            <input type="checkbox" name="remember">
                            <div class="state p-primary">
                                <svg class="svg svg-icon" viewBox="0 0 20 20">
                                    <path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path>
                                </svg>
                                <label>{{ trans('admin/auth.login.remember') }}</label>
                            </div>
                        </div>
                    </label>

                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button class="login100-form-btn">
                                {{ trans('admin/auth.login.btn-login') }}
                            </button>
                        </div>
                    </div>

                    <div class="text-center" style="padding-top: 20px; line-height: 0;">
                        <span class="txt1">{{ trans('admin/auth.login.recover-title') }}</span>
                        <br/>
                        <a href="{{ route('admin.password.forgot') }}" class="txt2">
                            {{ trans('admin/auth.login.recover-btn') }}
                        </a>
                    </div>
                </div>

                <div class="text-center submit-loading" style="padding-top: 0; line-height: 0; display: none">
                    <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                    <h4>{{ trans('admin/auth.login.feedback.success.title') }}</h4>
                    <p>{{ trans('admin/auth.login.feedback.success.subtitle') }}</p>
                    <div style="margin-bottom: 30px"></div>
                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button class="login100-form-btn">
                                {{ trans('admin/auth.login.feedback.success.loading') }}
                            </button>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop