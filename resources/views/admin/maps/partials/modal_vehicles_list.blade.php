<ul class="list-unstyled vehicles-data-list nicescroll">
    <?php
    $now = Date::now();
    ?>
    @foreach($vehicles as $vehicle)
        <?php $lastUpdate = new Date(@$vehicle->last_location); ?>

        @if(@$vehicle->latitude && @$vehicle->longitude)
            <li class="vehicle-location-item"
                data-lat="{{ $vehicle->latitude }}"
                data-lng="{{ $vehicle->longitude }}"
                data-id="{{ $vehicle->id }}"
                data-marker="{{ $vehicle->marker_icon }}"
                data-name="{{ $vehicle->name }}"
                data-html="{{ $vehicle->marker_html }}">
                <div class="user-details">
                    @if($vehicle->is_ignition_on)
                        @if($vehicle->speed > 0.00)
                            <i class="fas fa-circle text-green"></i>
                        @else
                            <i class="fas fa-circle text-orange"></i>
                        @endif
                    @else
                        <i class="fas fa-circle text-muted"></i>
                    @endif
                    <small class="pull-right">{{ $vehicle->last_location ? timeElapsedString($vehicle->last_location) : 'Nunca ativo' }}</small>
                    <b>{{ $vehicle->name }}</b>
                    <br/>
                    <div>
                        <ul class="list-inline m-0 vehicle-detail pull-right p-r-3">
                            <li style="width: 138px; overflow: hidden; position: relative">
                                <small>
                                @if($vehicle->gps_city)
                                    <i class="flag-icon flag-icon-{{ $vehicle->gps_country }}"></i> {{ $vehicle->gps_city }}
                                @else
                                    N/A
                                @endif
                                </small>
                            </li>
                            <li style="width:140px; text-align: right">
                                <small>
                                    <i class="fas fa-tachometer-alt"></i> {{ number($vehicle->speed, 0) }}km/h
                                    &nbsp;
                                    <i class="fas fa-gas-pump"></i> {{ $vehicle->fuel_level_html }}
                                </small>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="clearfix"></div>
            </li>
        @endif
    @endforeach
</ul>