@section('metatags')
    <meta name="description" content="{{ trans('website.seo.home.description') }}">
    <meta property="og:title" content="2660 Express - Logistica e Distribuição">
    <meta property="og:description" content="{{ trans('website.seo.home.description') }}">
    <meta property="og:image" content="{{ trans('website.seo.image.url') }}">
    <meta property="og:image:width" content="{{ trans('website.seo.image.width') }}">
    <meta property="og:image:height" content="{{ trans('website.seo.image.height') }}">
    <meta name="description" content="{{ trans('website.seo.home.description') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="2660 Express - Logistica e Distribuição">
    <meta name="reply-to" content="geral@2660express.pt">
    <meta name="keywords" content="logistica, distribuicao">
@stop

@section('content')
<section id="recruitment">
    <div class="container m-t-50 m-b-50">
        <div class="row">
            <div class="col-sm-12">
                <h2>{!! trans('website.recruitment.intro.title') !!}</h2>
                <h3>{!! trans('website.recruitment.intro.subtitle') !!}</h3>
            </div>
        </div> 
    
        <div class="recruitment-form m-t-50 m-b-20">
        {{ Form::open(['route' => array('website.contacts.mail', 'type'=>'recruitment'), 'class' => 'ajax-form', 'required']) }}
            <div class="col-sm-6 col-md-3 col-xs-12">
                <div class="form-group is-required">
                    {{ Form::label('name', trans('website.recruitment.form.name'), ['style' => 'font-weight: normal'] ) }}
                    {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>

            <div class="col-sm-6 col-md-3 col-xs-12">
                <div class="form-group is-required" style="background:none;">
                    {{ Form::label('email', trans('website.recruitment.form.email'), ['style' => 'font-weight: normal']) }}
                    {{ Form::text('email', null, ['class' => 'form-control' , 'required']) }}
                </div>
            </div>

            <div class="col-sm-6 col-md-3 col-xs-12">
                <div class="form-group is-required" style="background:none;">
                    {{ Form::label('phone_rec', trans('website.recruitment.form.phone'), ['style' => 'font-weight: normal']) }}
                    {{ Form::text('phone_rec', null, ['class' => 'form-control required', 'required']) }}
                </div>
            </div>

            <div class="col-sm-6 col-md-3 col-xs-12">
                <div class="form-group is-required">
                    {{ Form::label('residency_rec', trans('website.recruitment.form.residency'), ['style' => 'font-weight: normal']) }}
                    {{ Form::text('residency_rec', null, ['class' => 'form-control','required']) }}
                </div>
            </div>

            <div class="col-sm-6 col-md-3 col-xs-12">
                <div class="form-group is-required">
                    {{ Form::label('license', trans('website.recruitment.form.license'), ['style' => 'font-weight: normal']) }}
                    <div class="d-flex">
                        {{ Form::select('license', ['' => ''] + trans('website.recruitment.license'), null, ['id' => 'license' ,'class' => 'form-control','placeholder' => trans('website.recruitment.form.license') , 'required']) }}
                        <span class="caret"></span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3 col-xs-12 hidden" id="license_type">
                <div class="form-group">
                    {{ Form::label('license_type', trans('website.recruitment.form.license_type'), ['style' => 'font-weight: normal']) }}
                    <div class="d-flex">
                        {{ Form::select('license_type', ['' => ''] + trans('website.recruitment.license_type'), null, ['class' => 'form-control','placeholder' => trans('website.recruitment.form.license_type')]) }}
                        <span class="caret"></span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3 col-xs-12">
                <div class="form-group is-required">
                    {{ Form::label('role_rec', trans('website.recruitment.form.role'), ['style' => 'font-weight: normal']) }}
                    {{ Form::text('role_rec', null, ['class' => 'form-control', 'required']) }}
                </div>
            </div>

            <div class="col-sm-6 col-md-3 col-xs-12">
                <div class="form-group is-required">
                    {{ Form::label('qualifications_rec', trans('website.recruitment.form.qualifications'), ['style' => 'font-weight: normal']) }}
                    <div class="d-flex">
                        {{ Form::select('qualifications_rec', ['' => ''] + trans('website.recruitment.qualifications'), null, ['class' => 'form-control' , 'required']) }}
                        <span class="caret"></span>
                    </div>  
                </div>
            </div>

            <div class="col-sm-6 col-md-3 col-xs-12">
                <div class="form-group is-required">
                    {{ Form::label('gender_rec', trans('website.recruitment.form.gender'), ['style' => 'font-weight: normal']) }}
                    <div>
                    {{ Form::select('gender_rec', ['' => ''] + trans('website.recruitment.gender'), null, ['class' => 'form-control', 'required']) }}
                    <span class="caret"></span>
                </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3 col-xs-12">
                <div class="form-group is-required">
                    {{ Form::label('birthdate_rec', trans('website.recruitment.form.date'), ['style' => 'font-weight: normal']) }} <br>
                    {{ Form::date('birthdate_rec', null, ['class' => 'form-control', 'placeholder' => trans('website.recruitment.form.date') , 'required']) }}
                </div>
            </div>

            <div class="col-sm-6 col-md-4 col-xs-12">
                <div class="form-group is-required">
                    {{ Form::label('curriculum_rec', trans('website.recruitment.form.curriculum'), ['style' => 'font-weight: normal;']) }} <small>(max 15Mb)</small>
                    <div class="fileinput fileinput-new input-group" data-provides="fileinput" >
                        <div class="form-control" data-trigger="fileinput" style="height: 37px;" name="curriculum_rec">
                            <i class="fa fa-file fileinput-exists"></i> 
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">{{ trans('website.word.search') }}...</span>
                            <span class="fileinput-exists">{{ trans('website.word.change') }}</span>
                            <input type="hidden"><input type="file" name="curriculum" data-max-size="15" data-file-format="pdf,docx,doc,png,jpg,jpeg">
                        </span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">{{ trans('website.word.cancel') }}</a>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-xs-12">
                <div class="form-group">
                    {{ Form::label('message', trans('website.recruitment.form.obs'), ['style' => 'font-weight: normal']) }}
                    {{ Form::textarea('message', null, ['class' => 'form-control', 'rows' => 3]) }}
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-xs-12">
                <div class="form-group">
                    <label style="font-size: 13px; font-weight: 400; text-transform: none;line-height: 1.2; ">
                        {{ Form::checkbox('accept', 1, null, ['class' => 'form-control', 'required']) }}
                        Declaro que aceito e autorizo que os meus dados pessoais sejam guardados e utilizados pela {{ Setting::get('company_name') }} para fins de recrutamento.
                        <br/>
                        Tomei conhecimento que tenho direito de acesso, de modificação, de rectificação e de eliminação definitiva dos dados que me dizem respeito junto da {{ Setting::get('company_name') }}.
                    </label>
                </div>
                <button type="submit" class="btn btn-primary m-b-30" data-loading-text="{{ trans('website.word.submitting') }}...">{{ trans('website.recruitment.form.submit') }}</button>
            </div>
            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
            {{ Form::close() }}
        </div>
    </div>
</div>
</section>
@stop

