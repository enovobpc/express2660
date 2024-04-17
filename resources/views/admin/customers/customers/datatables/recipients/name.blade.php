<a href="{{ route('admin.customers.recipients.edit', [$row->customer_id, $row->id]) }}" data-toggle="modal" data-target="#modal-remote">
    {{ $row->name }}
    @if($row->assigned_customer)
        <span class="label label-warning" data-toggle="tooltip" title="Associado ao cliente {{ @$row->assigned_customer->name }}">
            <i class="fas fa-link"></i> {{ @$row->assigned_customer->code }}
        </span>
    @endif
</a><br/>
@if($row->email)
{{ $row->email }}<br/>
@endif
{{ $row->phone }}