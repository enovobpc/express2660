<div class="m-b-3">
    {{ @$row->serviceGroup->name }}
</div>
<div data-toggle="tooltip" title="Regra de Cálculo de Preço">
        <span class="label label-info">
                <i class="fas fa-euro-sign"></i>
        </span>
        &nbsp;
        <small class="bold">
            @if($row->unity == 'weight')
                Peso KG
            @elseif($row->unity == 'volume')
                Nº Volumes
            @elseif($row->unity == 'internacional')
                País de Destino
            @elseif($row->unity == 'm3')
                Metros Cúbicos
            @elseif($row->unity == 'pallet')
                Nº Paletes
            @elseif($row->unity == 'km')
                Total KM
            @elseif($row->unity == 'hours')
                Total Horas
            @elseif($row->unity == 'advalor')
                AdValor
            @elseif($row->unity == 'costpercent')
                % Custo
            @endif
        </small>
</div>