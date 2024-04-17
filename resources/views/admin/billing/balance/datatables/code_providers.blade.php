<a href="{{ route('admin.billing.balance.show', [$row->id, 'source' => 'providers']) }}" data-toggle="modal" data-target="#modal-remote-xl">
    {{ $row->code }}
</a>