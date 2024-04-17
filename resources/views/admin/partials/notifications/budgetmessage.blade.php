<a href="{{ route('admin.budgets.show', $notification->source_id) }}">
    <div class="pull-left">
        <img src="{{ asset(trans('admin/notifications.icons.'.strtolower($notification->source_class))) }}">
    </div>
    <h4>{{ $notification->message }}</h4>
    <p><i class="far fa-clock"></i> {{ $notification->alert_at->format('Y-m-d  |  H:i') }}</p>
</a>