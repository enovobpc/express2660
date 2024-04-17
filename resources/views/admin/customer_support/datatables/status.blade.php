<div class="text-center">
    <span class="label {{ trans('admin/customers_support.status-labels.'.$row->status) }}">
        {{ trans('admin/customers_support.status.'.$row->status) }}
    </span>
    <br/>
    <small>
        {{ $row->updated_at->format('Y-m-d') }}
    </small>
</div>