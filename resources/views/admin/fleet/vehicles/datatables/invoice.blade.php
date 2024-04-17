@if($row->assigned_invoice_id)
    <div class="text-center">
    <a href="{{ route('admin.invoices.purchase.download', $row->assigned_invoice_id) }}"
       target="_blank">
        <span class="label bg-blue"><i class="fas fa-file-alt"></i> {{ @$row->invoice->reference }}</span>
    </a>
    </div>
@endif
