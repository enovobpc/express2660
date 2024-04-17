<a href="{{ route('account.recipients.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">{{ $row->name }}</a>
<br/>
<small class="lh-1-2" style="display: block">
    {{ $row->address }}<br/>
    {{ $row->zip_code }} {{ $row->city }} ({{ trans('country.'.$row->country) }})
</small>