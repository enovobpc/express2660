{{ Form::model($providerAgency, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('provider', 'Fornecedor') }}
                {{ Form::select('provider', [''=>''] + $providers, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('code', 'Código') }}
                {{ Form::text('code', null, ['class' => 'form-control uppercase nospace', 'required', 'maxlength' => 7]) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('name', 'Nome') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('status', 'Estado') }}
                {{ Form::select('status', [''=>''] + $status, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <hr style="margin: 0 0 10px 0"/>
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="row row-5">
                <div class="col-sm-8">
                    <div class="form-group">
                        {{ Form::label('company', 'Empresa') }}
                        {{ Form::text('company', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('district', 'Distrito') }}
                        {{ Form::text('district', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('address', 'Morada') }}
                {{ Form::text('address', null, ['class' => 'form-control']) }}
            </div>
            <div class="row row-5">
                <div class="col-sm-2">
                    <div class="form-group">
                        {{ Form::label('zip_code', 'C. Postal') }}
                        {{ Form::text('zip_code', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="form-group">
                        {{ Form::label('city', 'Localidade') }}
                        {{ Form::text('city', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('country', 'País') }}
                        {{ Form::select('country', trans('country'), null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('responsable', 'Responsável') }}
                        {{ Form::text('responsable', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('responsable_mobile', 'Telemóvel') }}
                        {{ Form::text('responsable_mobile', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
            <div class="form-group is-required">
                {{ Form::label('email', 'E-mail Principal') }}
                {{ Form::email('email', null, ['class' => 'form-control email', 'required']) }}
            </div>
            <div class="form-group is-required">
                {{ Form::label('web', 'Website') }}
                {{ Form::text('web', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <hr style="margin: 0 0 10px 0"/>
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('email_provider', 'E-mail Rede') }}
                {{ Form::email('email_provider', null, ['class' => 'form-control email', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('mobile', 'Telemóvel') }}
                {{ Form::text('mobile', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('phone', 'Telef. 1') }}
                {{ Form::text('phone', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('phone2', 'Telef. 2') }}
                {{ Form::text('phone2', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('phone3', 'Telef. 3') }}
                {{ Form::text('phone3', null, ['class' => 'form-control']) }}
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
    $('.modal .select2').select2(Init.select2());
</script>
