<a href="{{ route('admin.invoices.purchase.payment-notes.download', $row->id) }}"
   target="_blank">
    {{ $row->code }}
    @if($row->deleted_at)
        <span class="label label-danger" data-toggle="tooltip" title="Anulado em {{ $row->deleted_at }} {{ @$row->deleted_by ? ' por ' . @$row->deletedBy->name : ''  }}">
                <i class="fas fa-exclamation-triangle"></i> Anulado
        </span>
    @endif
</a>