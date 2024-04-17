{{ Form::model($reminder, $formOptions) }}
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
            <div class="form-group is-required">
                {{ Form::label('title', __('Lembrete')) }}
                {{ Form::text('title', null, ['class' => 'form-control', 'required', 'placeholder' => 'Ex: Revisão dos 135.000km']) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('vehicle_id', __('Viatura')) }}
                {{ Form::select('vehicle_id', count($vehicles) > 1 ? ['' => ''] + $vehicles : $vehicles, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-xs-6">
            <h4 class="text-primary">@trans('Lembrar numa data')</h4>
            <div class="row row-5">
                <div class="col-sm-7">
                    <div class="form-group is-required">
                        {{ Form::label('date', __('Data Limite')) }}
                        <div class="input-group">
                            {{ Form::text('date', $reminder->exists ? $reminder->date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'required']) }}
                            <span class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        {{ Form::label('days_alert', 'Emitir aviso') }}
                        <div class="input-group">
                            {{ Form::text('days_alert', $reminder->exists ? null : 30, ['class' => 'form-control number', 'maxlength' => 2]) }}
                            <span class="input-group-addon">
                                @trans('dias')
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <h4 class="text-primary">@trans('Lembrar aos KM')</h4>
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('km', __('Km Limite')) }}
                        <div class="input-group">
                            {{ Form::text('km', null, ['class' => 'form-control number']) }}
                            <span class="input-group-addon">
                                km
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-">
                    <div class="form-group">
                        {{ Form::label('km_alert', __('Emitir aviso')) }}
                        <div class="input-group">
                            {{ Form::text('km_alert', $reminder->exists ? null : 1000, ['class' => 'form-control number', 'maxlength' => 4]) }}
                            <span class="input-group-addon">
                                @trans('km antes')
                            </span>
                        </div>
                    </div>
                </div>
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
    $('.modal .select2').select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());
</script>

