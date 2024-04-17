@if(config('app.source') == 'activos24')
<a href="#" style="cursor: not-allowed">
    {{ $row->code }}
</a>
@else
    @if(in_array($row->status_id, [\App\Models\Logistic\ShippingOrderStatus::STATUS_PENDING, \App\Models\Logistic\ShippingOrderStatus::STATUS_PROCESSING]))
        <a href="{{ route('admin.logistic.shipping-orders.edit', $row->id) }}"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            {{ $row->code }}
        </a>
    @else
        <a href="{{ route('admin.logistic.shipping-orders.show', $row->id) }}"
           data-toggle="modal"
           data-target="#modal-remote-lg">
            {{ $row->code }}
        </a>
    @endif
@endif