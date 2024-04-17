<a href="{{ route('admin.customers.recipients.edit', [$row->customer_id, $row->id]) }}" data-toggle="modal" data-target="#modal-remote">
    {{ $row->name }}
    @if($row->assigned_customer)
        <span class="label label-warning" data-toggle="tooltip" title="Associado ao cliente {{ @$row->assigned_customer->name }}">
            <i class="fas fa-link"></i> {{ @$row->assigned_customer->code }}
        </span>
    @endif
</a>
@if($row->responsable)
    <br/><i class="text-muted">@trans('A/C:')' {{ $row->responsable }}</i>
@endif
@if($row->vat)
    <br/><i class="text-muted">@trans('NIF:')' {{ $row->vat }}</i>
@endif
