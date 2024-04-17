@if(@$locations)
    @if(empty($locations))
        <div class="text-center m-t-30 text-muted">
            <i class="fas fa-info-circle bigger-140"></i>
            <br/>
            Não há histórico para a viatura e data selecionadas.
        </div>
    @else
        <ul class="list-unstyled">
            <?php $totalRows = count($locations); ?>
            @foreach($locations as $key => $location)
                <li data-lat="{{ $location['latitude'] }}"
                    data-lng="{{ $location['longitude'] }}"
                    data-id="{{ $location['id'] }}"
                    data-html="<b><span class='marker-number'>{{ $totalRows }}</span> {{ @$vehicle->name }}</b><br/>{{ $location['gps_city'] }}">
                    <div>
                        <i class="fas fa-circle {{ $location['is_ignition_on'] ? 'text-green' : 'text-muted' }}"></i>
                        {{--<span class="marker-number">{{ $totalRows }}</span> --}}
                        {{ $location['last_location']->format('H:i') }} | {{ $location['gps_city'] }}, {{ strtoupper($location['gps_country']) }}
                    </div>
                    <div>
                        <ul class="list-inline m-0 route-detail">
                            <li>
                                <i class="fas fa-tachometer-alt"></i> {{ $location['speed'] }}km/h
                            </li>
                            <li>
                                <i class="fas fa-gas-pump"></i> {{ $location['fuel_level'] }}%
                            </li>
                        </ul>
                    </div>
                </li>
                <?php $totalRows-- ?>
            @endforeach
        </ul>
    @endif
@else
    <div class="text-center m-t-30 text-muted">
        <i class="fas fa-user bigger-140"></i>
        <br/>
        Selecione uma viatura e data da lista.
    </div>
@endif