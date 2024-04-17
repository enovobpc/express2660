{{ Form::model($auth, ['route' => 'account.details.login.update', 'files' => true]) }}
<div class="row">

    <div class="col-sm-8 col-lg-9">
        <div class="form-group">
            {{ Form::label('display_name', trans('account/global.word.display-name'), array('class' => 'form-label')) }}
            {{ Form::text('display_name', null, array('class' => 'form-control', 'maxlength' => 20)) }}
        </div>
        <div class="form-group">
            {{ Form::label('email', trans('account/global.word.email'), array('class' => 'form-label')) }}
            {{ Form::email('email', null, array('class' => 'form-control nospace lowercase')) }}
        </div>
        <a href="#" data-toggle="modal" data-target="#modal-password" class="btn btn-default m-t-10">
            <i class="fas fa-lock"></i> {{ trans('account/global.settings.password.title') }}
        </a>
        <hr/>
        <button class="btn btn-black pull-left">{{ trans('account/global.word.save') }}</button>
    </div>
    <div class="col-sm-4 col-lg-3">
        {{ Form::label('display_name', trans('account/global.word.image'), array('class' => 'form-label')) }}<br/>
        <div class="fileinput {{ $auth->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
            <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                <img src="{{ asset('assets/img/default/avatar.png') }}">
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;">
                @if($auth->filepath)
                <img src="{{ asset($auth->getThumb()) }}">
                @endif
            </div>
            <div>
                <span class="btn btn-default btn-block btn-sm btn-file">
                    <span class="fileinput-new">{{ trans('account/global.word.search') }}...</span>
                    <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> {{ trans('account/global.word.change') }}</span>
                    <input type="file" name="image">
                </span>
                <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                    <i class="fas fa-close"></i> {{ trans('account/global.word.remove') }}
                </a>
            </div>
        </div>
    </div>
</div>
{{ Form::hidden('delete_photo') }}
{{ Form::close() }}
@include('account.details.partials.password')