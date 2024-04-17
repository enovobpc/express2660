@if($notification->read)
<span class="pull-right btn-notification-read" data-toggle="tooltip" title="Marcar como não lido" data-placement="left" data-href="{{ route('admin.notifications.read', [$notification->id]) }}">
    <i class="fas fa-circle-o text-blue"></i>
</span>
@else
<span class="pull-right btn-notification-read" data-toggle="tooltip" title="Marcar como lido" data-placement="left" data-href="{{ route('admin.notifications.read', [$notification->id]) }}">
    <i class="fas fa-circle text-blue"></i>
</span>
@endif