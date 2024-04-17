{{ Form::model($warehouse, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group is-required m-0">
                {{ Form::label('code', __('Código')) }}
                {{ Form::text('code', null, ['class' => 'form-control uppercase nospace', 'required', 'maxlength' => 4]) }}
            </div>
        </div>
        <div class="col-sm-10">
            <div class="form-group is-required m-0">
                {{ Form::label('name', __('Designação')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
    <h4 class="form-divider">@trans('Localização e Contacto')</h4>
    <div class="row row-5">
        <div class="col-sm-8">
            <div class="form-group">
                {{ Form::label('company', __('Empresa responsável')) }}
                {{ Form::text('company', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('responsable', __('Pessoa responsável')) }}
                {{ Form::text('responsable', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('address', __('Morada')) }}
        {{ Form::text('address', null, ['class' => 'form-control']) }}
    </div>
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('zip_code', __('Código Postal')) }}
                {{ Form::text('zip_code', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('city', __('Localidade')) }}
                {{ Form::text('city', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('country', __('País')) }}
                {{ Form::select('country', trans('country'), null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('email', __('Localidade')) }}
                {{ Form::email('email', null, ['class' => 'form-control email']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('phone', __('Telefone')) }}
                {{ Form::text('phone', null, ['class' => 'form-control phone']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('mobile', __('Telemóvel')) }}
                {{ Form::text('mobile', null, ['class' => 'form-control phone']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}
<script>
    $('.select2').select2(Init.select2());
</script>

