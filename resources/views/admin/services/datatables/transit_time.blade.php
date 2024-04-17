@if($row->transit_time || $row->transit_time_max)
    <div>
        @if($row->transit_time && $row->transit_time_max)
            {{ (int) $row->transit_time }}/{{ (int) $row->transit_time_max }}h
        @else
            {{ (int) $row->transit_time }}h
        @endif
    </div>
@endif
@if($row->delivery_hour)
    <small>
    Até {{ str_replace(':','h', $row->delivery_hour) }}
    </small>
@endif