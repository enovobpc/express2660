<table class="table table-condensed">
    <tr>
        <th>@trans('Evento')</th>
        <th class="w-90px">@trans('Data')</th>
        <th class="w-1"></th>
    </tr>
    @if($calendarEvents->isEmpty())
        <tr>
            <td class="text-muted" colspan="3">@trans('Não há eventos a apresentar.')</td>
        </tr>
    @else
        @foreach($calendarEvents as $event)
        <tr>
            <td>
                <i class="fas fa-square" style="color:{{ $event->color }}"></i>
                @if($event->concluded || ($event->end->lte(\Carbon\Carbon::today()) && $event->end->diffInDays(\Carbon\Carbon::now()) > 0))
                    <strike>
                        <a href="{{ route('admin.calendar.events.edit', $event->id) }}" data-toggle="modal" data-target="#modal-remote">
                            {{ $event->title }}
                        </a>
                    </strike>
                @else
                    <a href="{{ route('admin.calendar.events.edit', $event->id) }}" data-toggle="modal" data-target="#modal-remote">
                        {{ $event->title }}
                    </a>
                @endif
            </td>
            <td>{{ @$event->start->format('Y-m-d') }}</td>
            <td>
                @if($event->created_by == Auth::user()->id)
                <a href="{{ route('admin.calendar.events.destroy', array($event->id))}}" class="text-red"  data-toggle="delete-event">
                    <i class="fas fa-trash-alt"></i>
                </a>
                @endif
            </td>
        </tr>
        @endforeach
    @endif
</table>
<div id="events-loading" class="hide">
    <span class="fas fa-circle-notch fa-spin"></span> @trans('A carregar eventos...')'
</div>