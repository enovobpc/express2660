<a href="{{ route('admin.logistic.products.show', $row->id) }}">
    <b>{{ @$row->name }}</b>
</a>
<br/>
<small class="text-muted"><i>{{ @$row->customer->name }}</i></small>