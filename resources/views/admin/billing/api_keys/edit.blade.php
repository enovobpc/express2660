{{ Form::model($apiKey, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="font-size-32px" aria-hidden="true">&times;</span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('company_id', 'Empresa') }}
                {{ Form::select('company_id', [''=>'']+$companies, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 15]) }}
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('token', 'Token API') }}
                {{ Form::text('token', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('start_date', 'Válida Deste') }}
                <div class="input-group">
                    {{ Form::text('start_date', null, ['class' => 'form-control datepicker', 'required']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('end_date', 'Válida Até') }}
                <div class="input-group">
                    {{ Form::text('end_date', null, ['class' => 'form-control datepicker', 'required']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="checkbox m-t-25">
                <label>
                    {{ Form::checkbox('is_active') }}
                    Ativa
                </label>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="checkbox m-t-25">
                <label style="padding: 0">
                    {{ Form::checkbox('is_default') }}
                    Por Defeito
                </label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2())
    $('.modal .datepicker').datepicker(Init.datepicker());
</script>
