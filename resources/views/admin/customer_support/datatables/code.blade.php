<a href="{{ route('admin.customer-support.show', $row->id) }}">
        {{ $row->code }}
</a>
<br/>
<div class="label label-{{ trans('admin/customers_support.categories-labels.'.$row->category) }}">
        {{ trans('admin/customers_support.categories.'.$row->category) }}
</div>