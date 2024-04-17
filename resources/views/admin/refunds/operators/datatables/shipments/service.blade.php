<div class="text-center">
    @if(@$row->service->display_code)

        @if($row->service->is_collection)

            <span data-toggle="tooltip" title="{{ @$row->service->name }}" class="bold">
            @if(count($row->service->zones) > 1)
                <i class="fas fa-cube"></i>{{ strtoupper(App\Models\Shipment::getBillingZone($row->sender_country, $row->recipient_country)) }}{{ @$row->service->display_code }}
            @else
                <i class="fas fa-cube"></i>{{ @$row->service->display_code }}
            @endif
        </span>
        @else
        <span data-toggle="tooltip" title="{{ @$row->service->name }}">
            @if(count($row->service->zones) > 1)
                {{ strtoupper(App\Models\Shipment::getBillingZone($row->sender_country, $row->recipient_country)) }}{{ @$row->service->display_code }}
            @else
                {{ @$row->service->display_code }}
            @endif
        </span>
        @endif
    @else
    <span class="text-red"><i class="fas fa-exclamation-triangle text-red"></i> N/A</span>
    @endif
    <br/>
    <span class="label" style="background: {{ @$row->provider->color }}">
        {{ @$row->provider->name }}
    </span>
</div>