{{ Form::model($attachment, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="form-group is-required">
        {{ Form::label('name', trans('account/global.word.document-title')) }}
        {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
    </div>
    @if(!$attachment->exists)
        <div class="form-group is-required">
            {{ Form::label('name', trans('account/global.word.file-to-attach')) }}
            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                <div class="form-control" data-trigger="fileinput">
                    <i class="fas fa-file fileinput-exists"></i>
                    <span class="fileinput-filename"></span>
                </div>
                <span class="input-group-addon btn btn-default btn-file">
                <span class="fileinput-new">{{ trans('account/global.word.search') }}...</span>
                <span class="fileinput-exists">{{ trans('account/global.word.change') }}</span>
                <input type="file" name="file" required>
            </span>
                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">{{ trans('account/global.word.remove') }}</a>
            </div>
        </div>
    @endif
    <div class="form-group">
        {{ Form::label('obs', trans('account/global.word.notes')) }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
    <button type="submit" class="btn btn-primary">{{ trans('account/global.word.save') }}</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
</script>