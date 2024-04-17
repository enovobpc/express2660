@if($row->doc_type != \App\Models\PurchaseInvoice::DOC_TYPE_SIND
&& $row->doc_type != \App\Models\PurchaseInvoice::DOC_TYPE_SINC)
<a href="{{ route('admin.invoices.purchase.edit', [$row->id]) }}"
    data-toggle="modal"
    data-target="#modal-remote-xl">
    {{ $row->code }}
    @if($row->deleted_at)
        <span class="label label-danger" data-toggle="tooltip" title="Anulado em {{ $row->deleted_at }} {{ @$row->deleted_by ? ' por ' . @$row->deletedBy->name : ''  }}">
                <i class="fas fa-exclamation-triangle"></i> Anulado
        </span>
    @endif
</a>
@else
    {{ $row->code }}
@endif
