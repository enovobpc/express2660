@if($row->locale == 'en')
    <i class="flag-icon flag-icon-gb"></i> {{ strtoupper($row->locale) }}
@else
<i class="flag-icon flag-icon-{{ $row->locale }}"></i> {{ strtoupper($row->locale) }}
@endif