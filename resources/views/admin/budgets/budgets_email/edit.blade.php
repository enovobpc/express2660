<?php $ckeditor = str_random(5); ?>
{{ Form::model($budget, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('subject', 'Assunto') }}
                {{ Form::text('subject', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('name', 'Nome') }}
                {{ Form::text('name', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('email', 'E-mail') }}
                {{ Form::text('email', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('date', 'Data') }}
                <div class="input-group">
                    {{ Form::text('date', $budget->exists? $budget->date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                    <span class="input-group-addon"><i class="fas fa-calendar"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group is-required m-b-0">
        {{ Form::label('message', 'Mensagem') }}
        {{ Form::textarea('message', null, ['class' => 'form-control ' . $ckeditor, 'required', 'rows' => 9, 'id' => $ckeditor]) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}
{{ HTML::script('vendor/ckeditor/ckeditor.js')}}
<script>
    $('.select2').select2(Init.select2())
    $('.datepicker').datepicker(Init.datepicker())

    CKEDITOR.config.height = '330px';
    CKEDITOR.replace('{{ $ckeditor }}');
</script>