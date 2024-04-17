
{{ Form::model($trip, $formOptions) }}

<div class="row row-5">
    <div class="col-sm-4">
        <h4 class="bold text-blue m-t-0 m-b-20">@trans('Geral')</h4>
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('date', 'Data realização', ['class' => 'control-label']) }}
                <div class="input-group">
                    {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required', 'style' => 'padding: 0 0 0 5px;']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('period_id', 'Período') }}
                {!! Form::selectWithData('period_id', $periods, null, ['class' => 'form-control select2']) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('start_location', 'Local Início', ['class' => 'control-label']) }}
                {{ Form::text('start_location', $trip->exists ? null : Setting::get('company_zip_code'), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('end_location', 'Local Termo', ['class' => 'control-label']) }}
                {{ Form::text('end_location', $trip->exists ? null : Setting::get('company_zip_code'), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group m-b-0 is-required">
                {{ Form::label('start_hour', 'Hora Saída', ['class' => 'control-label']) }}
                {{ Form::select('start_hour', [''=>''] + listHours(5), $trip->start_hour, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group m-b-0 is-required">
                {{ Form::label('end_hour', 'Hora Chegada', ['class' => 'control-label']) }}
                {{ Form::select('end_hour', [''=>''] + listHours(5), $trip->end_hour, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group m-b-0">
                {{ Form::label('avg_delivery_time', 'Tempo Entrega', ['class' => 'control-label']) }}
                {!! tip('Estabelece o tempo médio de entrega de cada serviço.') !!}
                {{ Form::select('avg_delivery_time', [''=>''] + $deliveryTimes, $trip->avg_delivery_time, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <h4 class="bold text-blue">Motorista e Ajudantes</h4>
        <div>
            <div class="col-sm-7">
                <div class="form-group is-required">
                    {{ Form::label('operator_id', 'Motorista', ['class' => 'control-label']) }}
                    {!! Form::select('operator_id', $operators, null, ['class' => 'form-control select2']) !!}
                </div>
            </div>
            <div class="col-sm-5">
                <div class="form-group">
                    {{ Form::label('vehicle', 'Viatura') }}
                    {{ Form::select('vehicle', ['' => ''] + $vehicles, null, ['class' => 'form-control select2']) }}
                </div>
            </div>
            @if($trailers)
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('trailer', 'Reboque') }}
                        {{ Form::select('trailer', ['' => ''] + $trailers, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            @endif
            <div class="col-sm-12">
                <div class="form-group is-required">
                    {{ Form::label('operator_id', 'Ajudantes', ['class' => 'control-label']) }}
                    {!! Form::select('operator_id', $operators, null, ['class' => 'form-control select2', 'multiple']) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <h4 class="bold text-blue m-t-0 m-b-20">Rota e Viagem</h4>

        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('provider_id', 'Subcontrato') }}
                {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('delivery_route_id', 'Rota') }}
                {{ Form::select('delivery_route_id', ['' => ''] + $routes, null, ['class' => 'form-control select2']) }}
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('start_kms', 'Kms Início') }}
                <div class="input-group">
                    {{ Form::text('start_kms', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">km</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('end_kms', 'Kms Fim') }}
                <div class="input-group">
                    {{ Form::text('end_kms', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">km</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-12">
    <hr style="margin: 5px 0 10px"/>
    <button type="submit" class="btn btn-sm btn-primary">Gravar</button>
</div>
<div class="clearfix"></div>
{{ Form::close() }}