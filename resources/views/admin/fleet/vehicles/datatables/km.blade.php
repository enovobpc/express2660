{{ money($row->km, '', 0) }}
@if(hasModule('gateway_gps'))
    @if($row->is_ignition_on)
        <span class="label label-success">@trans('Em andamento')</span>
    @else
        <span class="label" style="background: #999">@trans('Parada')</span>
    @endif
@endif