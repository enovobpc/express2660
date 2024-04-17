<div>
    <small>
    @if($row->min_volumes || $row->max_volumes)
        @if($row->min_volumes && $row->max_volumes)
            {{ $row->min_volumes }} a {{ $row->max_volumes }} vol
        @elseif($row->max_volumes)
            Max {{ $row->max_volumes }} vol
        @else
            Min {{ $row->min_volumes }} vol
        @endif
    @else
        Sem Limite Vols
    @endif
    </small>
</div>

<div>
    <small>
    @if($row->min_weight || $row->max_weight)
        @if($row->min_weight && $row->max_weight)
            {{ (int) $row->min_weight }} a {{ (int) $row->max_weight }}kg
        @elseif($row->max_weight)
            Max {{ (int) $row->max_weight }}kg
        @else
            Min {{ (int) $row->min_weight }}kg
        @endif
    @else
        Sem Limite KG
    @endif
    </small>
</div>

<div>
    <small>
    @if($row->max_dims)
        Max {{ $row->max_dims }}cm
    @endif
    </small>
</div>

<div>
    @if($row->allow_docs)
        <i class="fas fa-fw fa-envelope" data-toggle="tooltip" title="Permite Documentos"></i>
        &nbsp;
    @endif
    @if($row->allow_boxes)
        <i class="fas fa-fw fa-box-open" data-toggle="tooltip" title="Permite Caixas"></i>
        &nbsp;
    @endif
    @if($row->allow_pallets)
        <i class="fas fa-fw fa-pallet" data-toggle="tooltip" title="Permite Paletes"></i>
    @endif
</div>
