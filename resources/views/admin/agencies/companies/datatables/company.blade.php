<b>
    <a href="{{ route('admin.companies.edit', $row->id) }}"
       data-toggle="modal"
       data-target="#modal-remote-lg">
        {{ $row->name }}
    </a>
</b>
<br/>
{{ $row->address }}<br/>
{{ $row->zip_code }} {{ $row->city }}