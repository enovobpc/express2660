@if(hasModule('shipment_attachments'))
    <div class="attachments-content">
        @include('admin.shipments.shipments.partials.show.attachments_content')
    </div>
@else
    <div class="module-denied">
        @include('admin.partials.denied_message')
    </div>
@endif