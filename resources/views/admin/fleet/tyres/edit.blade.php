{{ Form::model($tyre, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-7">
            <h4 class="form-divider no-border" style="margin-top: 0;">@trans('Informação do Pneu')</h4>
            <div class="row row-5">
                <div class="col-sm-12">
                    <div class="form-group is-required">
                        {{ Form::label('vehicle_id', __('Viatura')) }}
                        {{ Form::select('vehicle_id', count($vehicles) > 1 ? ['' => ''] + $vehicles : $vehicles, null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('kms', __('Km Iniciais')) }}
                        <div class="input-group input-group-money">
                            {{ Form::text('kms', null, ['class' => 'form-control number', 'required']) }}
                            <span class="input-group-addon">km</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('date', __('Data Montagem')) }}
                        <div class="input-group input-group-money">
                            {{ Form::text('date', $tyre->exists && $tyre->date ? $tyre->date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'required']) }}
                            <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('end_km', __('Km Finais')) }}
                        <div class="input-group input-group-money">
                            {{ Form::text('end_km', null, ['class' => 'form-control number']) }}
                            <span class="input-group-addon">km</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('end_date', __('Data Abate')) }}
                        <div class="input-group input-group-money">
                            {{ Form::text('end_date', null, ['class' => 'form-control datepicker']) }}
                            <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('reference', __('Referência')) }}
                        {{ Form::text('reference', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('size', __('Tamanho/Medida')) }}
                        {{ Form::text('size', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('position_id', __('Posição pneu')) }}
                        {{ Form::select('position_id', ['' => ''] + $positions, null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
            </div>
            <hr style="margin-top: 0;"/>
            <div class="row row-5">
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('brand', __('Marca')) }}
                        {{ Form::text('brand', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('model', __('Modelo')) }}
                        {{ Form::text('model', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('status', __('Estado Compra')) }}
                        {{ Form::select('status', ['' => __('Novo'), '2' => __('Recauchetado')], null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('operator_id', __('Colaborador')) }}
                        {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('provider_id', __('Fornecedor')) }}
                        {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('total', __('Total')) }}
                        <div class="input-group">
                            {{ Form::text('total', null, ['class' => 'form-control decimal']) }}
                            <span class="input-group-addon">
                                {{ Setting::get('app_currency') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        {{ Form::label('obs', __('Observações')) }}
                        {{ Form::textarea('obs',null, ['class' => 'form-control', 'rows' => 3]) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <h4 class="form-divider no-border" style="margin-top: 0;">@trans('Histórico Aferições')</h4>
            <table class="table table-condensed m-0">
                <tr>
                    <th class="bg-gray-light w-110px">@trans('Data')</th>
                    <th class="bg-gray-light w-60px"><span data-toggle="tooltip" title="@trans('Profundidade (mm)')"></span>@trans('Prof.')</th>
                    <th class="bg-gray-light">@trans('Notas')</th>
                </tr>
                @for ($i=0 ; $i<=8 ; $i++)
                <tr>
                    <td>{{ Form::text('measurements['.$i.'][date]', @$tyre->measurements[$i]['date'], ['class' => 'form-control datepicker']) }}</td>
                    <td>{{ Form::text('measurements['.$i.'][size]', @$tyre->measurements[$i]['size'], ['class' => 'form-control decimal']) }}</td>
                    <td>{{ Form::text('measurements['.$i.'][obs]', @$tyre->measurements[$i]['obs'], ['class' => 'form-control']) }}</td>
                </tr>
                @endfor
            </table>
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
    $('.datepicker').datepicker(Init.datepicker())
</script>

