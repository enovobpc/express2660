@if(@$locations)
    @if($locations->isEmpty())
        <div class="text-center m-t-30 text-muted">
            <i class="fas fa-info-circle bigger-140"></i>
            <br/>
            Não há histórico para o operador e data selecionados.
        </div>
    @else
        <ul class="list-unstyled">
            <?php $totalRows = count($locations); ?>
            @foreach($locations as $key => $location)
                <li data-lat="{{ $location->latitude }}"
                    data-lng="{{ $location->longitude }}"
                    data-id="{{ $location->id }}"
                    data-html="<b><span class='marker-number'>{{ $totalRows }}</span> {{ @$location->operator->name }}</b><br/>{{ $location->date }}">
                    <i class="fas fa-map-marker-alt"></i> <span class="marker-number ">{{ $totalRows }}</span> {{ $location->date }}
                </li>
                <?php $totalRows-- ?>
            @endforeach
        </ul>
    @endif
@else
    @if(hasModule('gateway_gps'))
        <div class="text-center m-t-30 text-muted">
            <i class="fas fa-truck bigger-140"></i>
            <br/>
            Selecione uma viatura da lista.
        </div>
    @else
        <div class="text-center m-t-30 text-muted">
            <i class="fas fa-user bigger-140"></i>
            <br/>
            Selecione um motorista da lista.
        </div>
    @endif
@endif