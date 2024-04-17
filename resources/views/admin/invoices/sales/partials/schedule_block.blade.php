@if(@$schedule)
    <div class="invoice-schedule-panel">
        <div class="row">
            <div class="col-sm-2" style="padding: 0;margin: 0;width: 250px;">
                <div class="form-group form-group-sm m-b-0" style="margin-right: 30px">
                    {{ Form::label('schedule_frequency', 'Repetir a cada', ['class' => 'col-sm-4 control-label text-right', 'style' => 'padding: 5px 5px 0 0; width: 120px;']) }}
                    <div class="row row-0" style="margin-right: -55px;">
                        <div class="col-sm-2">
                            {{ Form::text('schedule_repeat_every', @$schedule->repeat_every, ['class' => 'form-control text-center number nospace', 'maxlength' => 3]) }}
                        </div>
                        <div class="col-sm-4">
                            {{ Form::select('schedule_frequency', trans('admin/shipments.schedule.frequencies'), @$schedule->frequency, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 schedule-repeat form-group-sm p-0" style="{{ $schedule->repeat ? '' : 'display: none' }}">
                {{ Form::label('schedule_repeat', 'Emitir', ['class' => 'col-sm-4 control-label p-0 m-t-5 text-right']) }}
                <div class="col-sm-8 p-r-0 p-l-5">
                    {{ Form::select('schedule_repeat', trans('admin/shipments.schedule.month-frequencies'), @$schedule->repeat, ['class' => 'form-control select2']) }}
                </div>
            </div>
            <div class="col-sm-4 schedule-month-days form-group-sm p-0" style="{{ $schedule->repeat == 'day' ? '' : 'display: none' }}">
                {{ Form::label('schedule_month_days', 'Dias', ['class' => 'col-sm-1 control-label p-0 m-t-5 text-right']) }}
                <div class="col-sm-11 p-r-0 p-l-5">
                    {{ Form::selectRange('schedule_month_days[]', 1, 31 , @$schedule->month_days, ['class' => 'form-control select2', 'multiple', 'required']) }}
                </div>
            </div>
            <div class="col-sm-4 schedule-year-days form-group-sm p-0" style="{{ $schedule->frequency == 'year' ? '' : 'display: none' }}">
                {{ Form::label('schedule_year_days', 'Dias', ['class' => 'col-sm-2 control-label p-0 m-t-5 text-right']) }}
                <div class="col-sm-10 p-r-0 p-l-5">
                    {{ Form::select('schedule_year_days[]', getYearDaysArr() , @$schedule->year_days, ['class' => 'form-control select2', 'data-placeholder' => '', 'multiple', 'required']) }}
                </div>
            </div>
            <div class="col-sm-4 schedule-weekdays p-0" style="{{ ($schedule->frequency == 'year' || $schedule->frequency == 'day' ||  $schedule->repeat == 'day') ? 'display: none' : '' }}">
                <div class="form-group form-group-sm m-b-0 p-t-3">
                    {{ Form::label('schedule_weekdays', 'No dia', ['class' => 'col-sm-2 p-l-0 p-r-5 control-label text-right', 'style' => 'padding-top: 3px;']) }}
                    <div class="col-sm-10 p-0">
                        @for($weekday = 1 ; $weekday<= 7 ; $weekday++)
                            <div class="checkbox-inline" style="margin: 0; padding: 0">
                                <label>
                                    {{ Form::checkbox('schedule_weekdays[]', $weekday, (is_array($schedule->weekdays) && in_array($weekday, $schedule->weekdays) ? true : false)) }}
                                    {{ trans('datetime.weekday-tiny.' . $weekday) }}
                                </label>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group form-group-sm m-b-0" style="margin-right: -50px;">
                    {{ Form::label('schedule_end_time', 'Termina', ['class' => 'col-sm-2 control-label p-0 m-t-5 m-r-2 m-l-10']) }}
                    <div class="row row-0">
                        <div class="col-sm-4">
                            {{ Form::select('schedule_end_time', ['date' => 'Na data', 'after' => 'Depois de'], @$schedule->end_repetitions ? 'after' : 'date', ['class' => 'form-control select2']) }}
                        </div>
                        <div class="col-sm-5">
                            <div class="input-group" style="{{ $schedule->end_repetitions ? 'display: none' : '' }}">
                                {{ Form::text('schedule_end_date', @$schedule->end_date ? $schedule->end_date->format('Y-m-d') : null, ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar-alt"></i>
                                </div>
                            </div>
                            <div class="input-group" style="{{ $schedule->end_repetitions ? '' : 'display: none' }}">
                                {{ Form::text('schedule_end_repetitions', @$schedule->end_repetitions, ['class' => 'form-control nospace']) }}
                                <div class="input-group-addon">
                                    repetições
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif