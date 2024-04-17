{{ Form::model($bank, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('bank_name', 'Nome Banco') }}
                {{ Form::text('bank_name', null, ['class' => 'form-control', 'required', 'maxlength' => 255]) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('country', 'País') }}
                {{ Form::select('country', [''=>''] + trans('country'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('country_code', 'Cód. País') }}
                {{ Form::text('country_code', null, ['class' => 'form-control uppercase', 'required', 'maxlength' => 4, 'placeholder' => 'Ex: PT50']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('bank_code', 'Cód. Banco') }}
                {{ Form::text('bank_code', null, ['class' => 'form-control lowercase', 'required', 'maxlength' => 4, 'placeholder' => 'Ex: 0031']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('bank_swift', 'BIC/SWIFT') }}
                {{ Form::text('bank_swift', null, ['class' => 'form-control uppercase', 'required', 'maxlength' => 35]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <div class="checkbox m-b-0 m-t-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('is_active', 1, $bank->exists ? null : true) }}
                Ativo
            </label>
        </div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}
<script>
    $('.select2').select2(Init.select2());
</script>