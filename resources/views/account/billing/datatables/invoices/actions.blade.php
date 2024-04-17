<div class="text-center">
    <a href="{{ route('account.billing.invoices.download.invoice', [$row->id, 'key' => $row->api_key]) }}" class="btn btn-xs btn-default" target="_blank">
        <i class="fas fa-download"></i> Download
    </a>
</div>
