<a href="{{ route('admin.shipments.index', ['customer' => $notification->source_id, 'status' => '1']) }}">
    <div class="pull-left">
        <img src="{{ asset(trans('admin/notifications.icons.'.strtolower($notification->source_class))) }}">
    </div>
    <h4>{{ $notification->message }}</h4>
    <p><i class="far fa-clock"></i> {{ $notification->alert_at->format('Y-m-d  |  H:i') }}</p>
</a>