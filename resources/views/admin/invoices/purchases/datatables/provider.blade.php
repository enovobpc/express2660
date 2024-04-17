<a href="{{ route('admin.providers.edit', [$row->provider_id, 'tab' => 'purchase-invoices']) }}">
    {{ @$row->billing_name }}
</a>
<br/>
<small>
    <i class="text-muted">
        Código:
        <a href="{{ route('admin.providers.edit', $row->provider_id) }}" target="_blank">
            {{ @$row->provider->code ? @$row->provider->code : 'Apagado' }}
        </a>
        &nbsp;&bull;&nbsp;NIF: {{ @$row->vat }}</i>
</small>