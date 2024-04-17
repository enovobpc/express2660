<a href="{{ route('admin.products.sales.edit', $row->id) }}" data-toggle="modal" data-target="#modal-remote">
    {{ @$row->product->name }}
</a>
<br/>
<small class="text-muted">
    <i>{{ @$row->customer->name }}</i>
</small>