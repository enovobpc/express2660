@if($date)
    {{ $date->format('Y-m-d') }} <span>{{ $date->format('H:i') }}</span>
@endif