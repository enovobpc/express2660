{{ Form::model($absence, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        @if(@$operators)
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('user_id', __('Colaborador')) }}
                {{ Form::select('user_id', ['' => ''] + $operators, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        @endif
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('type_id', __('Tipo de ausência')) }}
                {!! Form::selectWithData('type_id', $types, null, ['class' => 'form-control select2', 'required']) !!}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('period', __('Período')) }}
                {{ Form::select('period', trans('admin/users.absences-periods'), @$absence->period == 'days' && @$absence->duration == '0.5' ? '1-2day' : null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-6">
            <div class="form-group is-required">
                {{ Form::label('start_date', __('Data Início')) }}
                <div class="input-group">
                    {{ Form::text('start_date', $absence->exists ? $absence->start_date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'required']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group is-required period-time" data-period="days" style="{{ (@$absence->period == 'days' && @$absence->duration > '0.5') || empty($absence->period) ? '' : 'display:none' }}">
                {{ Form::label('end_date', __('Data Fim')) }}
                <div class="input-group">
                    {{ Form::text('end_date', $absence->exists ? $absence->end_date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'required']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
            <div class="form-group period-time" data-period="1-2day" style="{{ @$absence->period == 'days' && @$absence->duration == '0.5' ? '' : 'display:none' }}">
                {{ Form::label('period_day', __('Periodo Dia')) }}
                {{ Form::select('period_day', ['' => '', 'm' => 'Manhã', 't' => 'Tarde'], null, ['class' => 'form-control select2']) }}
            </div>
            <div class="form-group period-time" data-period="hours" style="{{ @$absence->period == 'hours' ? '' : 'display:none' }}">
                {{ Form::label('duration', __('Horas')) }}
                <div class="input-group">
                    {{ Form::text('duration', null, ['class' => 'form-control number', 'maxlength' => '2']) }}
                    <div class="input-group-addon">
                        @trans('horas')
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="form-group m-b-0">
        {{ Form::label('obs', __('Observações')) }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 3]) }}
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <div class="checkbox m-t-5 m-b-8">
            <label style="padding-left: 0">
                {{ Form::checkbox('calendar', 1, $absence->exists ? false : true) }}
                @trans('Registar no calendário')
            </label>
        </div>
    </div>
    <div class="pull-right">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker());

    $(document).on('change', '[name="period"]', function(){
        var period = $(this).val();

        $('.period-time').removeClass('is-required').hide();
        $('.period-time').find('input, select').prop('required', false);
        $('[data-period="'+period+'"]').show();
        $('[data-period="'+period+'"]').addClass('is-required');
        $('[data-period="'+period+'"]').find('input, select').prop('required', true);
    })

    $(".modal [name=start_date]").on('change', function(){
        var date = $(this).val()
        $('.modal [name=end_date]').datepicker('remove')
        $(".modal [name=end_date]").val(date)

        if(date != '') {
            $('.modal [name=end_date]').datepicker({
                format: 'yyyy-mm-dd',
                language: 'pt',
                todayHighlight: true,
                startDate: date
            });
        }
    })
</script>