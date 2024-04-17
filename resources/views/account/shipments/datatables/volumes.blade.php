@if(($auth->id == '1443' || $auth->customer_id == '1443') && config('app.source') == 'corridadotempo')
    @if(!empty($row->custom_fields))
        @foreach($row->custom_fields as $key => $field)
        {{ $field }}<br/>
        @endforeach
    @endif
@else

    @if($row->packaging_type)
        @foreach((array)$row->packaging_type as $type => $qty)
            <div>{{ $qty }} <small class="text-muted">{{ @$packTypes[$type] }}</small></div>
        @endforeach
    @elseif($row->volumes)
        <div>{{ $row->volumes }} <small class="text-muted">{{ $row->volumes > 1 ? 'volumes' : 'volume' }}</small></div>
    @endif

{{--    @if($row->volumes)
        {{ $row->volumes }} <small class="text-muted">{{ $row->volumes > 1 ? 'volumes' : 'volume' }}</small><br/>
    @endif--}}

    @if(@$row->service->unity == 'm3')
        {{ $row->volume_m3 }} m<sup>3</sup>
    @elseif(@$row->service->unity == 'km')
        {{ $row->kms ? $row->kms : 0 }} km
    @else
        @if($row->weight || $row->volumetric_weight)
            {{ $row->weight > $row->volumetric_weight ? $row->weight : $row->volumetric_weight }} <small class="text-muted">kg</small>
        @else
            <span class="text-muted">--- kg</span>
        @endif
    @endif
@endif