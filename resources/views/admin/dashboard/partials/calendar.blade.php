<div class="box box-solid box-list dashboard-calendar">
    <div class="box-header bg-purple with-border">
        <a href="{{ route('admin.calendar.events.index') }}" class="btn btn-xs btn-primary pull-right">
            Ver Calendário
        </a>
        @if(!$calendarEvents->isEmpty())
        <a href="{{ route('admin.calendar.events.create') }}" class="btn btn-xs btn-sm btn-success pull-right m-r-5" data-toggle="modal" data-target="#modal-remote">
            <i class="fas fa-plus"></i> @trans('Novo')
        </a>
        @endif
        <h4 class="box-title">
            <i class="fas fa-calendar-alt"></i> @trans('Agenda')
        </h4>
    </div>
    <div class="box-body p-0 nicescroll">
        @if(!$calendarEvents->isEmpty())
            @foreach($calendarEvents as $date => $events)
            <h4>{{ human_date($date) }}</h4>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        {{--<th class="w-80px">Data</th>--}}
                        <th class="w-20px">@trans('Hora')</th>
                        <th>@trans('Evento')</th>
                        <th class="w-40px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                    <tr>
                        {{--<td>{{ $event->start->format('Y-m-d') }}</td>--}}
                        <td>{{ $event->start->format('H:i') == '00:00' ? '' : $event->start->format('H:i') }}</td>
                        <td>
                            <i class="fas fa-square" style="color:{{ $event->color }}"></i>
                            <a href="{{ route('admin.calendar.events.edit', $event->id) }}" data-toggle="modal" data-target="#modal-remote" class="event-title">
                                @if($event->concluded)
                                    <strike>{{ $event->title }}</strike>
                                @else
                                    {{ $event->title }}
                                @endif
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.calendar.events.conclude', $event->id) }}" class="event-mark-concluded" data-toggle="tooltip" title="Concluido/Por concluir" data-placement="left">
                                @if($event->concluded)
                                <i class="fas fa-check text-green"></i>
                                @else
                                <i class="fas fa-check text-muted"></i>
                                @endif
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endforeach
            {{--<a href="{{ route('admin.calendar.events.index') }}" class="calendar-show-more">
                <i class="fas fa-plus-circle"></i> Ver todos os eventos
            </a>--}}
        @else
            <div class="empty-event text-center">
                <h3>
                    <i class="fas fa-calendar-plus-o bigger-170"></i><br/>
                    @trans('Não há eventos agendados.')
                </h3>
                <a href="{{ route('admin.calendar.events.create') }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote">
                    @trans('Criar Evento')
                </a>
            </div>
        @endif
    </div>
</div>