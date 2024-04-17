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
                {{ Form::label('zip_code', 'Código Postal') }}
                {{ Form::text('zip_code', null, ['class' => 'form-control', 'required', $zipCode->exists ? 'disabled' : '']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('city', 'Localidade') }}
                {{ Form::text('city', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('country', 'País') }}
                {{ Form::select('country', ['' => ''] + trans('country'), $zipCode->exists ? null : Setting::get('app_country'), ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-9">
            <div class="form-group">
                {{ Form::label('agency_id', 'Associar Código Postal à Agência') }}
                {{ Form::select('agency_id', ['' => '- Não associar nenhuma Agência -'] + $agencies, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        {{--<div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('zone', 'Zona de Faturação') }}
                {{ Form::select('zone', ['' => ''] + trans('country'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>--}}
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('kms', 'Kms') }} <i class="fas fa-info-circle" data-toggle="tooltip" title="Distância da agência à localidade"></i>
                <div class="input-group">
                    {{ Form::text('kms', null, ['class' => 'form-control']) }}
                    <span class="input-group-addon">km</span>
                </div>

            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('provider_id', 'Associar ao fornecedor') }}
                {{ Form::select('provider_id', ['' => '- Não associar nenhum fornecedor -'] + $providers, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group">
                {{ Form::label('services[]', 'Serviços autorizados') }} {!! tip('Limite os serviços possíveis usar para este código postal. Por exemplo, pode impedir que serviços internacionais sejam selecionados para códigos postais nacionais.') !!}
                {{ Form::select('services[]', $services, array_map('intval', @$zipCode->services ? $zipCode->services : []), ['class' => 'form-control select2', 'multiple', 'data-placeholder' => 'Todos']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group m-0">
                <div class="checkbox m-0">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('is_regional', 1) }}
                        Este código postal é local/regional
                    </label>
                    {!! tip('Os códigos postais logais/regionais são todos os codigos postais abrangidos pelas suas próprias viaturas sem necessidade de subcontratação.') !!}
                </div>
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
