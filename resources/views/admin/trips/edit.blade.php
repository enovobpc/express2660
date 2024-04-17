{{ Form::model($trip, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    @if(!empty($shipmentsIds))
        <div class="alert alert-info" style="margin: -16px -15px 15px; padding: 15px; border-radius: 0;">
            <i class="fas fa-info-circle"></i> @trans('Selecionou :total serviços para criar o mapa.', ['total' => count($shipmentsIds)])
            <div class="pull-right input-sm" style="width: 200px;margin-top: -11px;">
                {{ Form::label('status_id', __('Alterar estado para'), ['style' => 'float: left;display: block;position: absolute;margin-left: -50px;margin-top: 5px; font-weight: normal; margin-left: -115px;']) }}
                {{ Form::select('status_id', ['' => __('Não mudar')] + $status, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        {{ Form::hidden('ids', implode(',', $shipmentsIds)) }}
    @endif
    <div class="row" style="background: #f2f2f2;
    padding: 20px 0;
    margin: -16px -15px 15px;
    border-bottom: 1px solid #ccc;
    box-shadow: 0 2px 3px #ddd;">
        <div class="col-sm-5">
            <h4 class="form-divider no-border bold" style="margin: -10px 0 10px;"><i class="fas fa-sign-out-alt"></i> @trans('Início Volta')</h4>
            <div class="row row-5">
                <div class="col-sm-7">
                    {{ Form::label('start_date', __('Data/Hora'), ['class' => 'control-label']) }}
                    <div class="row row-0">
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                {{ Form::text('start_date', null, ['class' => 'form-control datepicker trigger-allowances', 'required', 'style' => 'padding: 0 0 0 5px;']) }}
                            </div>
                        </div>
                        <div class="col-sm-4" style="margin-left: -1px">
                            {{ Form::select('start_hour', [''=>''] + listHours(5), $trip->start_hour, ['class' => 'form-control select2 trigger-allowances', 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('start_kms', __('Kms Início')) }}
                        <div class="input-group input-group-money">
                            {{ Form::text('start_kms', null, ['class' => 'form-control decimal trigger-allowances']) }}
                            <div class="input-group-addon">km</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    {{--<div class="form-group">
                        {{ Form::label('start_location', 'Local Arranque', ['class' => 'control-label']) }}
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            {{ Form::text('start_location', null, ['class' => 'form-control']) }}
                        </div>
                    </div>--}}
                    <div class="form-group m-b-0">
                        {{ Form::label('end_location', __('Localidade Início'), ['class' => 'control-label']) }}
                        <div class="row row-0">
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    {{ Form::text('start_location', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                {{ Form::select('start_country', ['' => '']+trans('country'), null, ['class' => 'form-control select2 trigger-allowances', 'required']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <h4 class="form-divider no-border bold" style="margin: -10px 0 10px;"><i class="fas fa-sign-in-alt"></i> @trans('Fim Volta')</h4>
            <div class="row row-5">
                <div class="col-sm-7">
                    {{ Form::label('end_date', __('Data/Hora'), ['class' => 'control-label']) }}
                    <div class="row row-0">
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                {{ Form::text('end_date', null, ['class' => 'form-control datepicker trigger-allowances', 'style' => 'padding: 0 0 0 5px;']) }}
                            </div>
                        </div>
                        <div class="col-sm-4" style="margin-left: -1px">
                            {{ Form::select('end_hour', [''=>''] + listHours(5), $trip->end_hour, ['class' => 'form-control select2 trigger-allowances']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('end_kms', __('Kms Finais')) }}
                        <div class="input-group input-group-money">
                            {{ Form::text('end_kms', null, ['class' => 'form-control decimal trigger-allowances']) }}
                            <div class="input-group-addon">km</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group m-b-0">
                        {{ Form::label('end_location', __('Localidade de Termo'), ['class' => 'control-label']) }}
                        <div class="row row-0">
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    {{ Form::text('end_location', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                {{ Form::select('end_country', ['' => '']+trans('country'), null, ['class' => 'form-control select2 trigger-allowances', 'required']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <h4 class="form-divider no-border bold" style="margin: -10px 0 10px;"><i class="fas fa-box-open"></i> @trans('Entrega')</h4>
            <div class="form-group">
                {{ Form::label('period_id', __('Período')) }}
                {!! Form::selectWithData('period_id', $periods, null,['class' => 'form-control select2']) !!}
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('avg_delivery_time', __('Ø Tempo Entrega'), ['class' => 'control-label']) }}
                {{ Form::select('avg_delivery_time', [''=>''] + $deliveryTimes, $trip->avg_delivery_time, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <h4 class="form-divider no-border bold" style="margin-top: 0"><i class="fas fa-users"></i> @trans('Motorista')</h4>
            <div class="form-group">
                <div class="pull-right lbl-declaration-loading text-blue" style="display: none"><small><i class="fas fa-spin fa-circle-notch"></i></small></div>
                <div class="pull-right lbl-declaration text-blue" style="display: none"><small>@trans('Última distribuição') <span></span> @trans('dias')</small></div>
                <div class="pull-right lbl-declaration-unfinished text-blue" style="display: none"><small>@trans('Em distribuição') <span class="last-manifest"></span></small></div>
                {{ Form::label('operator_id', __('Motorista'), ['class' => 'control-label']) }}
                {!! Form::select('operator_id', [''=>''] + $operators, null, ['class' => 'form-control select2']) !!}
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('assistants[]', __('Acompanhantes'), ['class' => 'control-label']) }}
                {!! Form::select('assistants[]', $operators, $trip->assistants, ['class' => 'form-control select2', 'multiple']) !!}
            </div>
        </div>
        <div class="col-sm-3">
            <h4 class="form-divider no-border bold" style="margin-top: 0"><i class="fas fa-truck"></i> @trans('Viatura')</h4>
            <div class="form-group">
                {{ Form::label('vehicle', __('Viatura')) }}
                {{ Form::select('vehicle', ['' => ''] + $vehicles, null, ['class' => 'form-control select2']) }}
            </div>
            @if($trailers)
                <div class="form-group">
                    {{ Form::label('trailer', __('Reboque')) }}
                    {{ Form::select('trailer', ['' => ''] + $trailers, null, ['class' => 'form-control select2']) }}
                </div>
            @endif
        </div>
        <div class="col-sm-3">
            <h4 class="form-divider no-border bold" style="margin-top: 0"><i class="fas fa-road"></i> @trans('Subcontrato')</h4>
            <div class="form-group">
                {{ Form::label('provider_id', __('Fornecedor')) }}
                {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
               {{-- <div class="checkbox">
                    <label style="padding: 0;">
                        {{ Form::checkbox('update_provider', 1, true) }}
                        Atualizar todos serviços {!! tip('Altera todos os serviços do mapa para este fornecedor') !!}
                    </label>
                </div>--}}
            </div>
            <div class="row row-0">
                <div class="col-sm-7">
                    <div class="form-group m-0">
                        {{ Form::label('cost_price', __('Preço Custo')) }}
                        {!! tip('O valor será distribuido uniformemente pelos serviços associados a este mapa') !!}
                        <div class="input-group input-group-money">
                            {{ Form::text('cost_price', null, ['class' => 'form-control decimal']) }}
                            <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group m-0">
                        {{ Form::label('vat_rate_id', __('IVA')) }}
                        {{ Form::select('vat_rate_id', $vatRates, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">

            <h4 class="form-divider no-border bold" style="margin-top: 0"><i class="fas fa-hand-holding-usd"></i> @trans('Rota')</h4>
            <div class="row row-5">
                <div class="col-sm-12">
                    {{-- <div class="form-group">
                        {{ Form::label('allowances_price', 'Ajudas Custo') }}
                        <div class="pull-right lbl-allowances-loading text-blue" style="display: none"><small><i class="fas fa-spin fa-circle-notch"></i></small></div>
                        <div class="input-group input-group-money">
                            {{ Form::text('allowances_price', null, ['class' => 'form-control decimal']) }}
                            <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                        </div>
                    </div> --}}
                    <div class="form-group">
                        {{ Form::label('delivery_route_id', __('Rota')) }}
                        {{ Form::select('delivery_route_id', [''=>''] + $routes, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        {{ Form::label('fuel_consumption', __('Consumo')) }}
                        <div class="input-group input-group-money">
                            {{ Form::text('fuel_consumption', null, ['class' => 'form-control decimal']) }}
                            <div class="input-group-addon">lt</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <h4 class="form-divider no-border bold" style="margin-top: 0"><i class="fas fa-info-circle"></i> @trans('Observações')</h4>
            <div class="form-group m-0">
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
    </div>
    @if(!$trip->exists && empty($shipmentsIds))
    <div class="alert alert-info m-0 m-t-10">
        <i class="fas fa-info-circle"></i> @trans('Os serviços a entregar são adicionados após gravar.')'
    </div>
    @endif
</div>
<div class="modal-footer">
    <div class="pull-left">
        <p class="m-t-10 text-yellow lh-1-0 alert-provider" style="display: none">
            <i class="fas fa-info-circle"></i> @trans('ATENÇÃO: Todos os envios do mapa serão afetados por esta alteração.')'
        </p>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary btn-save" data-loading-text="A gravar...">@trans('Gravar')</button>
</div>

{{ Form::hidden('type', null) }}
{{ Form::hidden('parent_id', null) }}
{{ Form::hidden('declaration_required', 0) }}
{{ Form::hidden('original_operator', $trip->operator_id) }}
{{ Form::hidden('original_vehicle', $trip->vehicle) }}
{{ Form::hidden('original_trailer', $trip->trailer) }}
{{ Form::close() }}


<style>
    .modal .select2-selection__rendered {
        padding: 0;
    }

</style>
<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal .select2-multiple').select2(Init.select2Multiple())
    $('.modal .datepicker').datepicker(Init.datepicker());

    $('.modal [name="period_id"]').on('change', function() {
        var start = $(this).find('option:selected').data('start');
        var end   = $(this).find('option:selected').data('end');

        $('.modal [name="start_hour"]').val(start).trigger('change');
        $('.modal [name="end_hour"]').val(end).trigger('change');
    });


    $('.modal ').on('change', function(){
        $('.modal .alert-provider').show();
    })

    $(".form-trip [required]").on('change', function(){
        $(this).removeClass('has-error');
        if($(this).is('select')) {
            $(this).next().removeClass('has-error')
        }
    })

    $('.form-trip .btn-save').on('click', function(e) {
        var $btn = $(this);

        emptyFields = $(".form-trip [required]").filter(function () {
            return !$(this).val();
        });

        countEmptyFields = emptyFields.length;
        emptyFields.each(function(){
            $(this).addClass('has-error')
            if($(this).is('select')) {
                $(this).next().addClass('has-error')
            }
        })

        if (countEmptyFields) {
            Growl.warning(@trans('Preencha os campos a vermelho antes de gravar.'))
            return false;
        }

        if ($('[name="declaration_required"]').val() == "1") {
            $btn.button('reset');
            $('#modal-operator-declaration').addClass('in').show();
            return false;
        }
    });
</script>