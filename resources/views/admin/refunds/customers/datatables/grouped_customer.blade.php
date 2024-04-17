<a href="{{ route('admin.refunds.customers.show', [$row->customer_id, 'type' => 'devolved']) }}"
   data-toggle="modal"
    data-target="#modal-remote-xl">
    {{ @$row->customer->name }}
</a>
<br/>
<small class="italic text-muted">NIF: {{ @$row->customer->code }}</small>