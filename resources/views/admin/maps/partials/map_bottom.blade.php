<div class="maps-bottom">
    <table class="table table-condensed table-hover vehicles-list">
        <thead>
            <tr>
                <th class="w-1">
                    <button class="btn btn-sm btn-xs btn-primary btn-collapse-map-bottom"
                            data-toggle="tooltip"
                            data-placement="right"
                            title="Minimizar">
                        <i class="fas fa-angle-down"></i>
                    </button>
                </th>
                <th class="w-80px">Viatura</th>
                <th>Descrição</th>
                <th>Localização</th>
                <th class="w-140px">Último Registo</th>
                <th class="w-1"><i class="fas fa-gas-pump"></i></th>
                <th class="w-65px"><i class="fas fa-tachometer-alt"></i> Km/h</th>
                <th class="w-50px"><i class="fas fa-road"></i> Km</th>
                <th class="w-80px">L/100km</th>
                <th>Motorista</th>
                <th class="w-1">Eventos</th>
            </tr>
        </thead>
        <tbody>
        @foreach($vehicles as $vehicle)
            <?php $lastDate = new Date($vehicle->last_location) ?>
            <tr class="vehicle-row" data-html="{{ $vehicle->marker_html }}" data-on="{{ $vehicle->is_ignition_on ? '1' : '0' }}" data-id="{{ $vehicle->gps_id }}" data-lat="{{ $vehicle->latitude }}" data-lng="{{ $vehicle->longitude }}">
                <td>
                    @if($vehicle->is_ignition_on)
                        @if($vehicle->speed > 0.00)
                            <i class="fas fa-circle text-green"></i>
                        @else
                            <i class="fas fa-circle text-orange"></i>
                        @endif
                    @else
                        <i class="fas fa-circle text-muted"></i>
                    @endif
                </td>
                <td>{{ $vehicle->license_plate }}</td>
                <td>{{ $vehicle->name }}</td>
                <td><i class="flag-icon flag-icon-{{ $vehicle->gps_country }}"></i>{{ $vehicle->gps_zip_code }} {{ $vehicle->gps_city }}</td>
                <td>
                    <span data-toggle="tooltip" title="{{ $lastDate->format('Y-m-d H:i') }}">
                        {{ human_time($lastDate->format('Y-m-d H:i:s')) }}
                    </span>
                </td>
                <td>{{ $vehicle->fuel_level_html }}</td>
                <td class="text-center">{{ number($vehicle->speed, 0) }}</td>
                <td>{{ number($vehicle->counter_km, 0) }}</td>
                <td>{{ $vehicle->consumption_avg }}</td>
                <td>{{ @$vehicle->operator->name }}</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-default">Avisos</button>
                        <a href="{{ route('admin.fleet.vehicles.edit', $vehicle->id) }}" target="_blank" class="btn btn-default">Ver</a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>