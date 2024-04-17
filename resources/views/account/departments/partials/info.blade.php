{{ Form::model($department, $formOptions) }}
<div class="row row-5">
    <div class="col-sm-3 col-md-2">
        <div class="form-group form-group-sm form-group form-group-sm-sm">
            {{ Form::label('code', 'Código Depart.') }}
            {{ Form::text('code', null, ['class' => 'form-control uppercase nospace']) }}
        </div>
    </div>
    <div class="col-sm-9 col-md-10">
        <div class="form-group form-group-sm form-group form-group-sm-sm is-required">
            {{ Form::label('name', 'Nome Departamento') }}
            {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
</div>
<div class="form-group form-group-sm form-group form-group-sm-sm is-required">
    {{ Form::label('address', 'Morada') }}
    {{ Form::text('address', null, ['class' => 'form-control', 'required']) }}
</div>
<div class="row row-5">
    <div class="col-sm-3">
        <div class="form-group form-group-sm is-required">
            {{ Form::label('zip_code', 'Código Postal') }}
            {{ Form::text('zip_code', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-sm-5">
        <div class="form-group form-group-sm is-required">
            {{ Form::label('city', 'Localidade') }}
            {{ Form::text('city', null, ['class' => 'form-control', 'required']) }}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group form-group-sm">
            {{ Form::label('country', 'País') }}
            {{ Form::select('country', trans('country'), Setting::get('app_country'), ['class' => 'form-control select2']) }}
        </div>
    </div>
</div>
<div class="row row-5">
    <div class="col-sm-6">
        <div class="form-group form-group-sm">
            {{ Form::label('contact_email', 'E-mail') }}
            {{ Form::text('contact_email', null, ['class' => 'form-control nospace lowercase']) }}
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group form-group-sm is-required">
            {{ Form::label('phone', 'Telefone') }}
            {{ Form::text('phone', null, ['class' => 'form-control nospace']) }}
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group form-group-sm is-required">
            {{ Form::label('mobile', 'Telemóvel') }}
            {{ Form::text('mobile', null, ['class' => 'form-control nospace']) }}
        </div>
    </div>
</div>
<div class="modal-footer" style="margin-left: -15px; margin-right: -15px; padding-bottom: 0">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}