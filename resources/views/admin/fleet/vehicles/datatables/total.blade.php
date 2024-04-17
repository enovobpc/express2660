<b data-total="{{ $row->total }}"> {{ $row->total == '0.00' ? : money($row->total, Setting::get('app_currency')) }}</b>
@if($row->assigned_invoice_id)
    <br/>
    <small>
        <a href="{{ route('admin.invoices.purchase.download', $row->assigned_invoice_id) }}" target="_blank">
            <i class="fas fa-file-alt"></i> {{ @$row->invoice->reference }}
        </a>
    </small>
@endif