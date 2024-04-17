<?php
$hasDate = $date;
$today = \Jenssegers\Date\Date::today();
$date  = new \Jenssegers\Date\Date($date);

$daysLeft = $date->diffInDays($today);

$color = '#12a501';
$late = false;
if($date->lt($today)) {
    $color = 'red';
    $late = true;
} else if($daysLeft <= 10) {
    $color = '#fb2300';
} else if($daysLeft <= 30) {
    $color = '#fba300';
}

?>
@if($hasDate)
    <span style="color: {{ $color }}">
        {{ $date->format('Y-m-d') }}
        <br/>
        <small>
            @if($late)
                <i class="fas fa-exclamation-triangle"></i>
                {{ $daysLeft }}d @trans('atrás')
            @else
            {{ $daysLeft }} @trans('dias')
            @endif
       </small>
    </span>
@endif