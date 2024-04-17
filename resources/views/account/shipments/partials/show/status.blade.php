<table class="table">
    <tr>
        <th class="w-100px" style="border-top: none">{{ trans('account/global.word.date') }}</th>
        <th class="w-70px" style="border-top: none">{{ trans('account/global.word.hour') }}</th>
        <th class="w-180px" style="border-top: none">{{ trans('account/global.word.status') }}</th>
        <th class="w-220px" style="border-top: none">{{ trans('account/global.word.local') }}</th>
        @if(Setting::get('tracking_show_operator_name'))
            <th class="w-160px" style="border-top: none">{{ trans('account/global.word.operator') }}</th>
        @endif
        <th style="border-top: none">{{ trans('account/global.word.obs') }}</th>
        <th class="w-1" style="border-top: none"></th>
    </tr>
    @foreach($shipmentHistory as $history)
    <?php
        $nameTrans = 'name';
        if (in_array($locale, ['en', 'fr', 'es'])) {
            $nameTrans = 'name_' . $locale;
        }
    ?>
    <tr>
        <td>{{ $history->created_at->format('Y-m-d') }}</td>
        <td>{{ $history->created_at->format('H:i') }}</td>
        <td>
            <span class="label" style="background: {{ @$history->status->color }}">
                {{ !empty(@$history->status->{$nameTrans}) ? @$history->status->{$nameTrans} : @$history->status->name }}
            </span>
        </td>
        <td>
        </td>
        @if(Setting::get('tracking_show_operator_name'))
            <td>
                <div>{{ @$history->operator->name }}</div>
                <?php $phone = @$history->operator->professional_mobile ? @$history->operator->professional_mobile : @$history->operator->phone ?>
                @if(Setting::get('tracking_show_operator_phone') && $phone)
                    <div class="text-muted"><small><i class="fas fa-mobile-alt"></i> {{ $phone }}</small></div>
                @endif
            </td>
        @endif
        <td>
            @if($history->receiver)
                {{ trans('account/global.word.received-by') }}: {{ $history->receiver }}
            @endif

            @if($history->incidence)
                <small>{{ trans('account/global.word.incidence-reason') }}</small><br/>{{ $history->incidence->{$nameTrans} }}
            @endif

            @if($history->obs)
                @if($history->incidence || $history->receiver)
                    <br/>
                @endif
                {!! nl2br($history->obs) !!}
            @endif
        </td>
        <th>
            @if($history->status_id == \App\Models\ShippingStatus::DELIVERED_ID && in_array($shipment->webservice_method, ['envialia', 'tipsa']))
                <a href="{{ route('account.shipments.get.pod', $shipment->id) }}" target="_blank" class="btn btn-xs btn-default">
                    <i class="fas fa-fw fa-file"></i> {{ trans('account/global.word.pod') }}
                </a>
            @endif

            @if($history->signature || $history->receiver)
                <div>
                    <a href="{{ route('account.shipments.get.pod', $shipment->id) }}" target="_blank" class="btn btn-xs btn-default">
                        <i class="fas fa-fw fa-signature"></i> {{ trans('account/global.word.pod') }}
                    </a>
                </div>
            @endif

            @if(Setting::get('tracking_location_active') && $history->latitude && $history->longitude)
                <div class="m-t-2">
                    <a href="http://maps.google.com/maps?q={{ $history->latitude }},{{ $history->longitude }}" target="_blank" class="btn btn-xs btn-default">
                        <i class="fas fa-fw fa-map-marker-alt"></i> Consultar Localização
                    </a>
                </div>
            @endif

            @if($history->filepath)
                <a href="{{ asset($history->filepath) }}" target="_blank" class="btn btn-xs btn-default">
                    <i class="fas fa-file"></i> {{ trans('account/global.word.scanning') }}
                </a>
            @endif
        </th>
    </tr>
    @endforeach
</table>

<a class="btn btn-black btn-xs" href="{{ route('tracking.index', ['tracking' => $shipment->tracking_code]) }}" target="__blank">
    {{ trans('account/global.word.public-tracking') }}
</a>