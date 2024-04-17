{{ Form::model($provider, $formOptions) }}
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
                {{ Form::label('iata_no', 'Nº IATA') }}
                {{ Form::text('iata_no', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('iata_code', 'Código IATA') }}
                {{ Form::text('iata_code', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('icao_code', 'Código ICAO') }}
                {{ Form::text('icao_code', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('airport', 'Aerporto Base') }}
                {{ Form::text('airport', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('address', 'Morada') }}
                {{ Form::text('address', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('zip_code', 'Cód. Postal') }}
                {{ Form::text('zip_code', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('city', 'Localidade') }}
                {{ Form::text('city', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('country', 'País') }}
                {{ Form::text('country', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group">
                {{ Form::label('email', 'E-mail') }}
                {{ Form::text('email', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('phone', 'Telefone') }}
                {{ Form::text('phone', null, ['class' => 'form-control']) }}
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

