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
            <i class="fas fa-info-circle"></i> Selecionou {{ count($shipmentsIds) }} serviços para criar o mapa.
            <div class="pull-right input-sm" style="width: 200px;margin-top: -11px;">
                {{ Form::label('status_id', 'Alterar estado para', ['style' => 'float: left;display: block;position: absolute;margin-left: -50px;margin-top: 5px; font-weight: normal; margin-left: -115px;']) }}
                {{ Form::select('status_id', [''=>'Não mudar'] + $status, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        {{ Form::hidden('ids', implode(',', $shipmentsIds)) }}
    @endif
    <div class="row">
        <div class="col-sm-5">
            <h4 class="form-divider no-border bold" style="margin: -10px 0 10px;"><i class="fas fa-sign-out-alt"></i> Início Rota</h4>
            <div class="row row-5">
                <div class="col-sm-7">
                    {{ Form::label('delivery_date', 'Data/Hora', ['class' => 'control-label']) }}
                    <div class="row row-0">
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                {{ Form::text('pickup_date', $trip->pickup_date ? $trip->pickup_date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'required', 'style' => 'padding: 0 0 0 5px;']) }}
                            </div>
                        </div>
                        <div class="col-sm-4" style="margin-left: -1px">
                            {{ Form::select('start_hour', [''=>''] + listHours(5), $trip->start_hour, ['class' => 'form-control select2', 'required']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('start_kms', 'Kms Início') }}
                        <div class="input-group">
                            {{ Form::text('start_kms', null, ['class' => 'form-control decimal']) }}
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
                        {{ Form::label('end_location', 'Localidade Arranque', ['class' => 'control-label']) }}
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
                                {{ Form::select('start_country', ['' => '']+trans('country'), null, ['class' => 'form-control select2', 'required']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <h4 class="form-divider no-border bold" style="margin: -10px 0 10px;"><i class="fas fa-sign-in-alt"></i> Fim Rota</h4>
            <div class="row row-5">
                <div class="col-sm-7">
                    {{ Form::label('delivery_date', 'Data/Hora', ['class' => 'control-label']) }}
                    <div class="row row-0">
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                {{ Form::text('delivery_date', null, ['class' => 'form-control datepicker', 'style' => 'padding: 0 0 0 5px;']) }}
                            </div>
                        </div>
                        <div class="col-sm-4" style="margin-left: -1px">
                            {{ Form::select('end_hour', [''=>''] + listHours(5), $trip->end_hour, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('end_kms', 'Kms Finais') }}
                        <div class="input-group">
                            {{ Form::text('end_kms', null, ['class' => 'form-control decimal']) }}
                            <div class="input-group-addon">km</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group m-b-0">
                        {{ Form::label('end_location', 'Localidade de Termo', ['class' => 'control-label']) }}
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
                                {{ Form::select('end_country', ['' => '']+trans('country'), null, ['class' => 'form-control select2', 'required']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <h4 class="form-divider no-border bold" style="margin: -10px 0 10px;"><i class="fas fa-box-open"></i> Entrega</h4>
            <div class="form-group">
                {{ Form::label('period_id', 'Período') }}
                {!! Form::text('period_id', [''=>''] + $periods, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('avg_delivery_time', 'Ø Tempo Entrega', ['class' => 'control-label']) }}
                {{ Form::select('avg_delivery_time', [''=>''] + $deliveryTimes, $trip->avg_delivery_time, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="sp-15"></div>
    <div class="row">
        <div class="col-sm-3">
            <h4 class="form-divider no-border bold" style="margin-top: 0"><i class="fas fa-users"></i> Motorista</h4>
            <div class="form-group">
                {{ Form::label('operator_id', 'Motorista', ['class' => 'control-label']) }}
                {!! Form::select('operator_id', [''=>''] + $operators, null, ['class' => 'form-control select2']) !!}
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('assistants[]', 'Assistentes/Auxiliares', ['class' => 'control-label']) }}
                {!! Form::select('assistants[]', $operators, $trip->assistants, ['class' => 'form-control select2', 'multiple']) !!}
            </div>
        </div>
        <div class="col-sm-3">
            <h4 class="form-divider no-border bold" style="margin-top: 0"><i class="fas fa-truck"></i> Viatura</h4>
            <div class="form-group">
                {{ Form::label('vehicle', 'Viatura') }}
                {{ Form::select('vehicle', ['' => ''] + $vehicles, null, ['class' => 'form-control select2']) }}
            </div>
            @if($trailers)
                <div class="form-group">
                    {{ Form::label('trailer', 'Reboque') }}
                    {{ Form::select('trailer', ['' => ''] + $trailers, null, ['class' => 'form-control select2']) }}
                </div>
            @endif
        </div>
        <div class="col-sm-3">
            <h4 class="form-divider no-border bold" style="margin-top: 0"><i class="fas fa-road"></i> Rota e Viagem</h4>
            <div class="form-group">
                {{ Form::label('provider_id', 'Subcontrato') }}
                {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
            </div>
            <div class="form-group">
                {{ Form::label('delivery_route_id', 'Rota') }}
                {{ Form::select('delivery_route_id', ['' => ''] + $routes, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <h4 class="form-divider no-border bold" style="margin-top: 0"><i class="fas fa-hand-holding-usd"></i> Ajudas Custo</h4>
            <div class="form-group">
                {{ Form::label('allowances_price', 'Ajudas Custo') }}
                <div class="input-group">
                    {{ Form::text('allowances_price', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('weekend_price', 'Fim de Semana') }}
                <div class="input-group">
                    {{ Form::text('weekend_price', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
        {{--@if(!$trip->shipments->isEmpty())
            <div class="col-sm-12">
                <h4 class="form-divider no-border bold" style="margin-top: 0; color: orange"><i class="fas fa-exclamation-triangle"></i> Atenção! Mapa com cargas atribuidas.</h4>

            </div>
            <div class="col-sm-3">

                <div class="form-group is-required">
                    {{ Form::label('date', 'Data', ['class' => 'control-label']) }}
                    <div class="input-group">
                        {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                        <div class="input-group-addon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group is-required">
                    {{ Form::label('hour', 'Hora', ['class' => 'control-label']) }}
                    {{ Form::time('hour', date('H:i'), ['class' => 'form-control hourpicker', 'required']) }}
                </div>
            </div>
        <div class="col-sm-7">
            <div class="form-group">
                {{ Form::label('status_id', 'Alterar estado das cargas para...') }}
                {{ Form::select('status_id', ['' => ''] + $status, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        @endif--}}
        <div class="col-sm-12">
            <h4 class="form-divider no-border bold" style="margin-top: 0"><i class="fas fa-info-circle"></i> Observações</h4>
            <div class="form-group m-0">
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
    </div>
    @if(!$trip->exists && empty($shipmentsIds))
    <div class="alert alert-info m-0 m-t-10">
        <i class="fas fa-info-circle"></i> Os serviços a entregar são adicionados após gravar.
    </div>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::hidden('type', null) }}
{{ Form::hidden('parent_id', null) }}
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

    $('#modal-operator-declaration .btn-confirm-yes').on('click', function () {
        $('#modal-operator-declaration').removeClass('in').hide();
    })
</script>
