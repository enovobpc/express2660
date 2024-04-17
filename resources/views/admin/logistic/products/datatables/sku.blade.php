<a href="{{ route('admin.logistic.products.show', $row->id) }}">
    {{ @$row->sku }}
</a>
<br/>
<small>{{ @$row->customer_ref }}</small>