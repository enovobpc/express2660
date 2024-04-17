<?php
    $service  = @$servicesList[$row->service_id][0];
    $provider = @$providersList[$row->provider_id][0];
?>


<div class="text-center">
    @if(@$service['display_code'])
        @if(@$service['is_collection'])
            <span style="white-space: nowrap" class="bold" data-toggle="tooltip" title="{{ @$service['name'] }}">
            @if($row->zone)
                    <i class="fas fa-cube"></i>{{ strtoupper($row->zone) }}
                @elseif(count(@$service['zones']) > 1)
                    <i class="fas fa-cube"></i>{{ strtoupper(App\Models\Shipment::getBillingZone($row->sender_country, $row->recipient_country)) }}
                @endif
                {{ @$service['display_code_alt'] }}
        </span>
        @else
            <span data-toggle="tooltip" title="{{ @$service['name'] }}">
            @if($row->zone)
                    <span>{{ strtoupper($row->zone) }}</span>
                @elseif(count(@$service['zones']) > 1)
                    <span>{{ strtoupper(App\Models\Shipment::getBillingZone($row->sender_country, $row->recipient_country)) }}</span>
                @endif
                {{ @$service['display_code_alt'] }}
        </span>
        @endif
    @else
        <span class="text-red"><i class="fas fa-exclamation-triangle text-red"></i> N/A</span>
    @endif
    <br/>
    <span class="label" style="background: {{ @$provider['color'] }}">
        {{ @$provider['name'] }}
    </span>
</div>
