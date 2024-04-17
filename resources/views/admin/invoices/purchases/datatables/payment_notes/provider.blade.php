<a href="{{ route('admin.providers.edit', [$row->provider_id, 'tab' => 'purchase-invoices']) }}">
    @if(@$row->billing_name)
        {{ @$row->billing_name }}
    @else
        @if(@$row->provider->name)
            {{ @$row->provider->name }}
        @else
            <i class="text-red">Fornecedor Apagado</i>
        @endif
    @endif
</a>
<br/>
<small><i class="text-muted">NIF: {{ @$row->vat }}</i></small>