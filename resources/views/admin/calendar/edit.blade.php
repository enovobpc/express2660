{{ Form::model($calendarEvent, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-10">
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('title', __('Título')) }}
                {{ Form::text('title', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
            </div>
            <div class="row row-5 event-dates {{ $calendarEvent->exists && $calendarEvent->created_by != Auth::user()->id ? 'hide' : '' }}">
                <div class="col-sm-6">
                    <div class="date-input">
                        <div class="form-group is-required">
                            {{ Form::label('start', __('Começa')) }}
                            {{ Form::text('start', $calendarEvent->start->format('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                        </div>
                    </div>
                    <div class="hour-input">
                        <div class="form-group">
                            {{ Form::label('start_hour', '&nbsp;') }}
                            {{ Form::select('start_hour', ['' => ''] + $hours, $calendarEvent->exists ? $calendarEvent->start->format('H:i') : $lastStartHour, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="date-input">
                        <div class="form-group is-required">
                            {{ Form::label('end', __('Acaba')) }}
                            {{ Form::text('end', $calendarEvent->end ? $calendarEvent->end->format('Y-m-d') : $calendarEvent->start->format('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                        </div>
                    </div>
                    <div class="hour-input">
                        <div class="form-group">
                            {{ Form::label('end_hour', '&nbsp;') }}
                            {{ Form::select('end_hour', ['' => ''] + $hours, $calendarEvent->exists ? $calendarEvent->end->format('H:i') : $endHour, ['class' => 'form-control select2']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('description', __('Anotações')) }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="alert_period"><i class="fas fa-bell"></i> @trans('Aviso')</label>
                {{ Form::select('alert_period', ['' => __('Sem aviso')] + trans('admin/calendar.alert_periods'), null, ['class' => 'form-control select2']) }}
            </div>
            <div class="form-group {{ $calendarEvent->created_by != Auth::user()->id ? 'hide' : '' }}">
                <label for="alert_period"><i class="fas fa-repeat"></i> @trans('Repetir')</label>
                {{ Form::select('repeat_period', ['' => __('Não se repete')] + trans('admin/calendar.repeat_periods'), null, ['class' => 'form-control select2']) }}
            </div>
            <div class="form-group m-0">
                {{ Form::label('color', __('Identificador')) }}<br/>
                {{ Form::select('color', $colors) }}
            </div>
        </div>
        <div class="col-sm-12 {{  $calendarEvent->exists && $calendarEvent->created_by != Auth::user()->id ? 'hide' : '' }}">
            <div class="form-group">
                <a href="#" class="pull-right select-all">@trans('Selecionar Todos')</a>
                {{ Form::label('participants[]', __('Partilhar este evento com...')) }}
                {{ Form::select('participants[]', $operators, @$participants, ['class' => 'form-control select2 event-participants', 'multiple']) }}
            </div>
        </div>
        <div class="col-sm-4 {{  $calendarEvent->exists && $calendarEvent->created_by != Auth::user()->id ? 'hide' : '' }}" data-toggle="tooltip" title="Ative esta opção se pretende que este lembrete seja mostrado na faturação a clientes correspondente à data que indicou.">
            <div class="form-group">
                <div class="h-15px blng" style="{{ $calendarEvent->type == 'billing' ? '' : 'display: none' }}"></div>
                <div class="checkbox">
                    <label style="display: inline-block; padding: 0">
                        {{ Form::checkbox('type', 'billing', @$calendarEvent->type == 'billing' ? true : false, ['class' => 'billing-event']) }}
                        @trans('Lembrar na faturação?')'
                    </label>
                </div>
            </div>

        </div>
        <div class="col-sm-8">
            <div class="form-group m-b-0 blng" style="{{ $calendarEvent->type == 'billing' ? '' : 'display: none' }}">
                {{ Form::label('customer_id', __('Lembrar na faturação mensal do cliente...')) }}
                {{ Form::select('customer_id', [@$calendarEvent->customer_id => @$calendarEvent->customer->name], null, ['class' => 'form-control', 'data-placeholder' => '']) }}
            </div>
        </div>
    </div>
    <span class="repeat-alert text-yellow hide"><i class="fas fa-exclamation-triangle"></i> @trans('Ao alterar a periodicidade, só serão afetados eventos com data posterior à data do evento atual.')</span>
</div>
<div class="modal-footer">
    {{--<div class="extra-options pull-left w-65">
        <div class="checkbox">
            <label style="display: inline-block">
                {{ Form::checkbox('type', 'billing', @$calendarEvent->type == 'billing' ? true : false) }}
                Lembrete de Faturação
            </label>
            <span style="display: inline-block; margin-left: -10px">{!! tip('Ative esta opção se pretende que este lembrete seja mostrado na faturação a clientes correspondente à data que indicou.') !!}</span>
        </div>
    </div>--}}
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}

{{ Html::script('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.js') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker.css') }}
{{ Html::style('vendor/jquery-simplecolorpicker/jquery.simplecolorpicker-fontawesome.css') }}
<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker());

    $(document).ready(function () {
        $('select[name="color"]').simplecolorpicker({theme: 'fontawesome'});
    })

    $('[name="repeat_period"]').on('change', function(){
        $('.repeat-alert').removeClass('hide');
    })

    $('.select-all').on('click', function(e){
        e.preventDefault();
        $('.event-participants option').prop('selected', true);
        $('.event-participants').trigger('change')
    })

    $(".modal select[name=customer_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.shipments.search.customer') }}")
    });

    $('.billing-event').on('change', function(){
        $('.blng').hide()
        if($(this).is(':checked')) {
            $('.blng').show()
        }
    })
</script>
