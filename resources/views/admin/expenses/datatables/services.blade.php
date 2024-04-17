@if(empty($row->trigger_services))
    <span style="opacity: 0.4">Todos</span>
@else
    @foreach($row->trigger_services as $serviceId)
        <div>{{ @$services[$serviceId] }}</div>
    @endforeach
@endif