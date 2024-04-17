<?php
    $service = @$servicesList[$row->service_id][0];
?>
@if($row->volumes)
    {{ $row->volumes }} Vol.
@else
    <span class="text-muted">--- Vol.</span>
@endif
<br/>

@if(@$service['unity'] == 'm3')
    {{ $row->volume_m3 }} m<sup>3</sup>
@elseif(@$service['unity'] == 'km')
    {{ $row->kms ? $row->kms : 0 }} km
@elseif(@$service['unity'] == 'hours')
    {{ $row->hours ? $row->hours : 0 }} h
@else
    @if($row->weight || $row->volumetric_weight)
        {{ $row->weight > $row->volumetric_weight ? $row->weight : $row->volumetric_weight }} kg
    @else
        <span class="text-muted">--- kg</span>
    @endif
@endif

<br/>
@if($row->charge_price != 0.00)
    @if($row->total_price_for_recipient != 0.00)
    <span class="label bg-purple m-r-3" data-toggle="tooltip" title="Reembolso: {{ money($row->charge_price, Setting::get('app_currency')) }} + {{ money($row->total_price_for_recipient, Setting::get('app_currency')) }}">
    @else
    <span class="label bg-purple m-r-3" data-toggle="tooltip" title="Reembolso: {{ money($row->charge_price, Setting::get('app_currency')) }}">
    @endif
        <i class="fas fa-euro-sign"></i>
    </span>
@endif


@if($row->obs)
<span class="label bg-aqua m-r-3 p-l-6 p-r-6" data-toggle="tooltip" title="Obs: {{ $row->obs }}">
    <i class="fas fa-info"></i>
</span>
@endif

@if($row->obs_internal)
<span class="label bg-blue m-r-3 p-l-6 p-r-6" data-toggle="tooltip" title="Obs Internas: {{ $row->obs_internal }}">
    <i class="fas fa-info"></i>
</span>
@endif