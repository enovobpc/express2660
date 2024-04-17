<a href="{{ route('account.departments.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote-lg">{{ $row->name }}</a><br/>
<small>
{{ $row->address }}<br/>
{{ $row->zip_code }} {{ $row->city }} ({{ trans('country.'.$row->country) }})
</small>