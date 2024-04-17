<a href="{{ route('admin.customers.edit', $row->customer_id) }}">
    {{ @$row->customer->code }} - {{ @$row->billing_name }}
</a>
<br/>
<small><i class="text-muted">NIF: {{ @$row->vat }}</i></small>