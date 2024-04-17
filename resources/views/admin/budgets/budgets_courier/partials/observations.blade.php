<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            {{ Form::label('intro', 'Introdução') }}
            {{ Form::textarea('intro', @$budget->exists ? null : @$defaultModel->intro, ['class' => 'form-control ckeditor', 'id' => 'ckeditor1', 'rows' => 3]) }}
        </div>
        <div class="form-group">
            {{ Form::label('transport_info', 'Informação de Transporte') }}
            {{ Form::textarea('transport_info', @$budget->exists ? null : @$defaultModel->transport_info, ['class' => 'form-control ckeditor', 'id' => 'ckeditor2', 'rows' => 4]) }}
        </div>
        <div class="form-group">
            {{ Form::label('payment_conditions', 'Informação de Pagamento') }}
            {{ Form::textarea('payment_conditions', @$budget->exists ? null : @$defaultModel->payment_conditions, ['class' => 'form-control ckeditor', 'id' => 'ckeditor3', 'rows' => 4]) }}
        </div>
        <div class="form-group">
            <label style="    float: right;
    margin: -3px;
    font-weight: normal;">
                {{ Form::checkbox('geral_conditions_separated', 1, @$budget->exists ? null : true) }}
                Condições em página separada
            </label>

            {{ Form::label('geral_conditions', 'Condições Gerais') }}
            {{ Form::textarea('geral_conditions', @$budget->exists ? null : @$defaultModel->geral_conditions, ['class' => 'form-control ckeditor', 'id' => 'ckeditor4', 'rows' => 4]) }}
        </div>
    </div>
</div>