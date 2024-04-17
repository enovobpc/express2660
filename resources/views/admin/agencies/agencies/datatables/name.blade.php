@if(Auth::user()->hasRole([config('permissions.role.admin')]) || (!Auth::user()->hasRole([config('permissions.role.admin')]) && in_array($row->id, $myAgencies)))
    <a href="{{ route('admin.agencies.edit', $row->id) }}"
       data-toggle="modal"
       data-target="#modal-remote">
        <b>{{ $row->print_name }}</b>
    </a>
@else
    <b>{{ $row->print_name }}</b>
@endif
<br/>
<span class="text-muted">
    {{ $row->address }}<br/>
    {{ $row->zip_code }} {{ $row->city }}
</span>