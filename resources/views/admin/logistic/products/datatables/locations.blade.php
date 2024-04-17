<div>
    @if($row->master_location)
        {{ @$row->master_location }}
    @else
        @foreach($row->locations as $location)
            <span class="bold">
                <span class="label label-default" data-toggle="tooltip" title="{{ @$location->warehouse->name }}">{{ @$location->warehouse->code }}</span>
                {{ @$location->code }}
            </span>
            {{--<span>({{ $location->pivot->stock }})</span>--}}
            @if(@$location->pivot->stock_available)
            <span>({{ @$location->pivot->stock_available }})</span>
            @endif
            <br/>
        @endforeach
    @endif
</div>
@if($row->warehouse_id)
    <div>{{ @$row->warehouse->name }}</div>
@endif