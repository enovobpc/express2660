<?php
    $count = DB::table('customers_assigned_messages')->where('message_id', $row->id)->count();
?>
<a href="{{ route('admin.customers.messages.show', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
    {{ $count }} @trans('Destinatários')
</a>