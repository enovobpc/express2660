<div>
    @if($row->master_location)
        {{ @$row->master_location }}
    @else
        @foreach($row->product_location as $productLocation)
            <span class="bold">
                <span class="label label-default" data-toggle="tooltip" title="{{ @$productLocation->location->warehouse->name }}">{{ @$productLocation->location->warehouse->code }}</span>
                {!! @$productLocation->getLocationCode() !!}
            </span>
            <br/>
        @endforeach
    @endif
</div>
@if($row->warehouse_id)
    <div>{{ @$row->warehouse->name }}</div>
@endif