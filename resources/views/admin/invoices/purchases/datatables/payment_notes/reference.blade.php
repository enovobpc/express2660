<a href="{{ route('admin.invoices.purchase.payment-notes.edit', $row->id) }}"
   data-toggle="modal"
   data-target="#modal-remote-xs">
    @if($row->reference)
    {{ $row->reference }}
    @else
    <i>----</i>
    @endif
</a>