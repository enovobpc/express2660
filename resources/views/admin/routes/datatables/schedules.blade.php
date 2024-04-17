@foreach ($row->schedules as $schedule)
    <div>
        <span class="label label-default">{{ $schedule['min_hour'] }} - {{ $schedule['max_hour'] }}</span>
        @if ($schedule['operator'])
            <span class="m-r-2">{{ @$operators[$schedule['operator']]['name'] }}</span>
        @endif

        @if ($schedule['provider'])
            @php
                $provider = @$providers[$schedule['provider']];
            @endphp
            <span class="label" style="background-color: {{ @$provider['color'] }}">{{ @$provider['name'] }}</span>
        @endif
    </div>
@endforeach