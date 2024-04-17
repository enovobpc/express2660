@if(!@$notifications->isEmpty())
    @foreach($notifications as $notification)
        @if(strtolower($notification->source_class) != 'supportticket')
        <li class="{{ $notification->read ? '' : 'unread' }}">
            @include('admin.partials.notifications.read')
            @include('admin.partials.notifications.'.strtolower($notification->source_class))
        </li>
        @endif
    @endforeach
@else
    <li style="margin-top: 85px; text-align: center;" class="text-muted">
        <i class="fas fa-info-circle"></i> Não há notificações.
    </li>
@endif