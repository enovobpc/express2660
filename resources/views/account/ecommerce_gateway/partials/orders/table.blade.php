<table class="table table-condensed table-hover" id="table-orders">
    <thead>
        <tr>
            <th class="w-20px">{{ trans('account/global.word.reference') }}</th>
            <th>{{ trans('account/global.word.recipient') }}</th>
            <th>{{ trans('account/global.word.remittance') }}</th>
            <th class="w-1"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            @php
                $createOptions = [
                    'source' => 'ecommerce_gateway',
                    'reference' => @$order['reference'],
                    'recipient_name' => @$order['recipient_name'],
                    'recipient_address' => @$order['recipient_address'],
                    'recipient_zip_code' => @$order['recipient_zip_code'],
                    'recipient_city' => @$order['recipient_city'],
                    'recipient_country' => @$order['recipient_country'],
                    'recipient_phone' => @$order['recipient_phone'],
                    'volumes' => @$webservice->settings['force_volumes_one'] ? 1 : @$order['volumes'],
                    'weight' => @$order['weight'],
                    'obs' => @$order['obs'],
                    'ecommerce_gateway_id' => @$webservice->id,
                    'ecommerce_gateway_order_code' => @$order['code'],
                    'pack_dimensions' => @$order['pack_dimensions'] ?? []
                ];
            @endphp

            <tr class="tr-order" data-code="{{ $order['code'] }}">
                <td>{{ $order['reference'] }}</td>
                <td>
                    {{ $order['recipient_name'] }}<br />
                    <small class="text-muted italic">
                        {{ $order['recipient_address'] }} | {{ $order['recipient_zip_code'] }} {{ $order['recipient_city'] }}
                    </small>
                </td>
                <td>
                    <div>{{ $createOptions['volumes'] }} <small class="text-muted">{{ $createOptions['volumes'] > 1 ? 'volumes' : 'volume' }}</small></div>
                    <div>{{ $order['weight'] }} <span class="text-muted">kg</span></div>
                </td>
                <td>
                    @if (!empty($submittedOrders[$order['code']]))
                        <a class="btn btn-info w-100" data-toggle="modal" data-target="#modal-remote-xl" href="{{ route('account.shipments.show', [$submittedOrders[$order['code']]]) }}">
                            {{ trans('account/global.word.details') }}
                        </a>
                    @else
                        <a class="btn btn-primary w-100" data-toggle="modal" data-target="#modal-remote-xl" href="{{ route('account.shipments.create', $createOptions) }}">
                            {{ trans('account/global.word.create-shipment') }}
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="row" id="table-orders-errors" style="display: {{ !empty($orders) ? 'none' : 'block' }}">
    <div class="col-xs-12 text-center">
        <p>{{ trans('account/ecommerce-gateway.feedback.orders.no-data') }}</p>
    </div>
</div>
