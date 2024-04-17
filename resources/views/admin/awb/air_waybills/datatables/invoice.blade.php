@if($row->invoice_id && $row->invoice_draft)
    <div class="text-center">
        {{ trans('admin/billing.types_code.' . $row->invoice_type) }} {{ $row->invoice_id }}
        <small><i class="text-muted">Rascunho</i></small>
    </div>
@elseif($row->invoice_id && !$row->invoice_draft)
    <div class="text-center">
        <a href="{{ route('admin.air-waybills.invoice.download', $row->id) }}" target="_blank">
            {{ trans('admin/billing.types_code.' . $row->invoice_type) }} {{ $row->invoice_id }}
        </a>
    </div>
@endif