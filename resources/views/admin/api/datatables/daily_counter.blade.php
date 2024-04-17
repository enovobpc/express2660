@if($row->daily_counter > $row->daily_limit)
    <span class="text-red">
    {{ $row->daily_counter }}/<b>{{ $row->daily_limit }}</b>
    </span>
@else
    {{ $row->daily_counter }}/<b>{{ $row->daily_limit }}</b>
@endif