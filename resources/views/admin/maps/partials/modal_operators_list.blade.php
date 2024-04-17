<ul class="list-unstyled operators-data-list nicescroll">
    <?php
    $now = Date::now();
    ?>
    @foreach($operators as $operator)
        <?php $lastUpdate = new Date(@$operator->last_location->created_at); ?>

        @if(@$operator->location_enabled && @$operator->last_location->latitude)
            <li class="operator-location-item {{ $operator->getLocationStatus() }}"
                data-lat="{{ $operator->last_location->latitude }}"
                data-lng="{{ $operator->last_location->longitude }}"
                data-id="{{ $operator->id }}"
                data-marker="{{ $operator->getLocationMarker() }}"
                data-name="{{ $operator->name }}"
                data-html="<b>{{ $operator->name }}</b><br/>
                                        Há {{ timeElapsedString($operator->last_location->created_at) }}<br/><button class='btn btn-xs btn-primary m-t-5 btn-show-route-history' data-id='{{ $operator->id }}'><i class='fas fa-route'></i> Ver histórico de trajeto</button>">
                <img src="{{ $operator->filehost }}{{ $operator->filepath ? $operator->getCroppa(64, 64) : asset('assets/img/default/avatar.png') }}"/>
                <div class="user-details">
                    <b>{{ $operator->name }}</b><br/>
                    {{ $operator->last_location ? timeElapsedString($operator->last_location->created_at) : 'Nunca ativo' }}
                </div>
                <div class="clearfix"></div>
            </li>
        @else
            <li class="operator-location-item" data-lat="" data-lng="">
                <img src="{{ $operator->filepath ? $operator->getCroppa(64, 64) : asset('assets/img/default/avatar.png') }}"/>
                <div class="user-details">
                    <b>{{ $operator->name }}</b><br/>
                    @if($operator->location_denied)
                        <span class="label label-danger">Localização Bloqueada</span> &bull;
                    @elseif(!$operator->location_enabled)
                        <span class="label label-default">Localização Inativa</span> &bull;
                    @else
                        <span class="label label-default">Sem localização</span> &bull;
                    @endif
                    {{ @$operator->last_location->created_at ? timeElapsedString(@$operator->last_location->created_at) : 'Nunca ativo' }}
                </div>
                <div class="clearfix"></div>
            </li>
        @endif
    @endforeach
</ul>