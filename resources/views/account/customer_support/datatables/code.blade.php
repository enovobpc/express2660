<a href="{{ route('account.customer-support.show', $row->code) }}">
    {{ $row->code }}
</a>
<div class="label label-{{ trans('admin/customers_support.categories-labels.'.$row->category) }}">
    {{ trans('admin/customers_support.categories.'.$row->category) }}
</div>