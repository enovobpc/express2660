<a href="{{ route('admin.customers.departments.edit', [$row->customer_id, $row->id]) }}" data-toggle="modal" data-target="#modal-remote-lg">{{ $row->name }}</a><br/>
{{ $row->address }}<br/>
{{ $row->zip_code }} {{ $row->city }}<br/>
{{ trans('country.'.$row->country) }}