<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            {{ Form::label('response_ok', 'Response OK') }}
            {{ Form::textarea('response_ok', null, ['class' => 'form-control', 'rows' => 15]) }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            {{ Form::label('response_error', 'Response ERROR') }}
            {{ Form::textarea('response_error', null, ['class' => 'form-control', 'rows' => 5]) }}
        </div>
    </div>
</div>