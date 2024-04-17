@if(@$row->services)
    @foreach($row->services as $serviceId)
    <span style="white-space: nowrap">{{ @$services[$serviceId] }}</span>
    @endforeach
@else
    Todos
@endif