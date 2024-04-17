@if($row->volumes)
    {{ $row->volumes }} Vol.
@else
    <span class="text-muted">--- Vol.</span>
@endif
<br/>
@if(@$row->service->unity == 'm3')
    {{ $row->volume_m3 }} m<sup>3</sup>
@else
    @if($row->weight || $row->volumetric_weight)
        {{ $row->weight > $row->volumetric_weight ? $row->weight : $row->volumetric_weight }} kg
    @else
        <span class="text-muted">--- kg</span>
    @endif
@endif