<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">@trans('Detalhes do Evento')</h4>
</div>
<div class="modal-body">
    <div class="row row-10">
        <div class="col-sm-8">
            <div class="form-group">
                {{ Form::label('title', __('Título')) }}
                <p class="form-control-static">{{ $calendarEvent->title }}</p>
            </div>
            <div class="row row-5 event-dates">
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('start', __('Começa')) }}
                        <p class="form-control-static">{{ $calendarEvent->start->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('end', __('Acaba')) }}
                        <p class="form-control-static">{{ $calendarEvent->end->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="row row-5">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="alert_period"><i class="fas fa-bell"></i> @trans('Aviso')</label>
                        <p class="form-control-static">
                            @if($calendarEvent->alert_period)
                                {{ trans('admin/calendar.alert_periods.'.$calendarEvent->alert_period) }}
                            @else
                                @trans('Sem aviso')
                            @endif
                        </p>
                     </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="alert_period"><i class="fas fa-repeat"></i> @trans('Repetir')</label>
                        @if($calendarEvent->repeat_period)
                        <p class="form-control-static">{{ trans('admin/calendar.repeat_periods.'.$calendarEvent->repeat_period) }}</p>
                        @else
                        <p class="form-control-static">@trans('Sem repetição')</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            @if($calendarEvent->description)
                <div class="form-group">
                    {{ Form::label('description', __('Anotações')) }}
                    <p class="form-control-static" style="max-height: 160px; overflow: auto">
                        {!! nl2br($calendarEvent->description) !!}
                    </p>
                </div>
            @endif
        </div>
        <div class="col-sm-4">
            <div class="form-group m-0">
                {{ Form::label('created_by', __('Criado Por')) }}
                <p>
                    {{ @$calendarEvent->owner->name }}
                    @if(Auth::user()->hasRole(Config::get('permissions.role.admin')))
                        ({{ @$calendarEvent->owner->email }})
                    @endif
                </p>
            </div>
        </div>
        <div class="col-sm-8">
            @if(!$calendarEvent->participants->isEmpty())
            {{ Form::label('participants', __('Partilhado com')) }}
            <p>
                @foreach(@$calendarEvent->participants as $key => $participant)
                    @if($key == 0)
                    {{ $participant->name }}
                    @else
                    , {{ $participant->name }}
                    @endif
                @endforeach
            </p>
            @endif
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
</div>

