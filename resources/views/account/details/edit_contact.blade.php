{{ Form::model($contact, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('department', trans('account/global.word.area-department')) }}
                {{ Form::text('department', null, ['class' => 'form-control', 'placeholder' => 'Ex.: Departamento Financeiro', 'autocomplete' => 'off']) }}
            </div>
        </div>
    </div>
    <div class="form-group is-required">
        {{ Form::label('name', trans('account/global.word.responsable')) }}
        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
    </div>
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('mobile', trans('account/global.word.mobile')) }}
                {{ Form::text('mobile', null, ['class' => 'form-control nospace', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('phone', trans('account/global.word.phone')) }}
                {{ Form::text('phone', null, ['class' => 'form-control nospace']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('email', trans('account/global.word.email')) }}
                {{ Form::text('email', null, ['class' => 'form-control nospace lowercase']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
    <button type="submit" class="btn btn-black">{{ trans('account/global.word.save') }}</button>
</div>
{{ Form::close() }}