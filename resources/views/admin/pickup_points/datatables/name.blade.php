<a href="{{ route('admin.pickup-points.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
    {{ $row->name }}
</a>
<br/>
<small>
    {{ $row->address }}<br/>
    {{ $row->zip_code }} {{ $row->city }}
</small>