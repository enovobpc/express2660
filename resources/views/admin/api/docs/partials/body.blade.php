<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            {{ Form::label('headers', 'Headers') }}
            {{ Form::textarea('headers', null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            {{ Form::label('body', 'BODY') }}
            {{ Form::textarea('body', null, ['class' => 'form-control', 'rows' => 25]) }}
        </div>
    </div>
</div>