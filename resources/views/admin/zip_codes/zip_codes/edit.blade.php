<?php 
$disabled = 'disabled';
if(Auth::user()->isAdmin()) {
    $disabled = '';
} elseif(!$zipCode->exists || (!Auth::user()->isAdmin() && config('app.source') == $zipCode->source)) {
    $disabled = '';
}
?>
{{ Form::model($zipCode, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('country', 'País') }}
                {{ Form::select('country', ['' => ''] + trans('country'), $zipCode->exists ? null : Setting::get('app_country'), ['class' => 'form-control select2', 'required', $disabled]) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('zip_code', 'Cód Postal') }}
                {{ Form::text('zip_code', null, ['class' => 'form-control uppercase', 'required', 'maxlength' => '10', $disabled]) }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('city', 'Localidade') }}
                {{ Form::text('city', null, ['class' => 'form-control', 'required', $disabled]) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('postal_designation', 'Designação Postal') }}
                {{ Form::text('postal_designation', null, ['class' => 'form-control', 'required', $disabled]) }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group">
                {{ Form::label('street', 'Rua/Avenida') }}
                {{ Form::text('street', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('district_code', 'Nº Distrito') }}
                {{ Form::text('district_code', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('district_name', 'Nome Distrito') }}
                {{ Form::text('district_name', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('county_code', 'Nº Concelho') }}
                {{ Form::text('county_code', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('county_name', 'Nome Concelho') }}
                {{ Form::text('county_name', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
</script>
