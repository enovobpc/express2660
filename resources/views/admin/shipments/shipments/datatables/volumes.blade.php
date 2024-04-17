@php
$appMode = $appMode ?? Setting::get('app_mode');
 $service = @$servicesList[$row->service_id][0];
@endphp
<div data-vol="{{ $row->volumes }}"
    data-kg="{{ $row->weight }}"
    data-m3="{{ $row->fator_m3 }}"
    data-ldm="{{ $row->ldm }}">
@if(is_array($row->packaging_type) && !empty($row->packaging_type))
    @foreach($row->packaging_type as $type => $qty)
        <div>{{ $qty }} {{ @$packTypes[$type] }}</div>
    @endforeach
@elseif($row->volumes)
    @if($row->conferred_volumes)
        <div class="bold" data-toggle="tooltip" title="Conferido pelo operador: {{ $row->conferred_volumes }}">{{ $row->volumes }} Vol.</div>
    @else
        <div>{{ $row->volumes }} Vol.</div>
    @endif
@else
    <div class="text-muted">--- Vol.</div>
@endif

@if(@$service['unity'] == 'm3')
    {{ $row->volume_m3 }} m<sup>3</sup>
@elseif(@$service['unity'] == 'km')
    {{ $row->kms ? $row->kms : 0 }} km
@elseif(@$service['unity'] == 'hours')
    {{ $row->hours ? $row->hours : 0 }} h
@else
    @if($row->weight || $row->volumetric_weight)

        @if(config('app.source') == 'corridadotempo')
        {{ $row->weight }} kg
        @else
            <span style="cursor: default; font-weight: {{ $row->conferred_weight ? 'bold' : '' }}"
                data-toggle="tooltip"
                data-html="true"
                title="
                    {{ $row->conferred_weight ? 'Conferido motorista: '.money($row->conferred_weight) . 'kg'.'<br/>' : '' }}
                    {{ $row->customer_weight ? 'Cliente:  '.money($row->customer_weight) . 'kg<br/>' : '' }}
                        Real: {{ money($row->weight, 'kg') }}<br/>
                    {{ $row->volumetric_weight ? 'Volumétrico:  '.money($row->volumetric_weight) . 'kg' : '' }}">
                {{ $row->weight > $row->volumetric_weight ? $row->weight : $row->volumetric_weight }} kg
            </span>
        @endif
    @else
        <span class="text-muted">--- kg</span>
    @endif
@endif
</div>