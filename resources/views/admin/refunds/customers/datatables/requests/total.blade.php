<a href="{{ route('admin.refunds.requests.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg" class="bold">
    {{ money($row->total, Setting::get('app_currency')) }}
</a>