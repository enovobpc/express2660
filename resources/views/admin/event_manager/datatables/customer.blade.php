<a href="{{ route('admin.customers.edit', $row->customer_id) }}">
    {{ $row->customer->code ?? 'nd'}} - {{ $row->customer->billing_name ?? $row->customer->display_name ?? null }}
</a>
<br/>
<small><i class="text-muted">NIF: {{ $row->customer->vat ?? '' }}</i></small>