<div class="text-center">
    <span class="label label-{{ trans('admin/budgets.status-labels.'.$row->status) }}">{{ trans('admin/budgets.status.'.$row->status) }}</span>
    <br/>
    <small>
        {{ $row->updated_at->format('Y-m-d') }}
    </small>
</div>