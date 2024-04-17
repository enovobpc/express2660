@if($row->provider_status)
<div class="text-center">
    <span class="label label-{{ trans('admin/budgets.provider-status-labels.'.$row->provider_status) }}">{{ trans('admin/budgets.provider-status.'.$row->provider_status) }}</span>
    <br/>
    <small>
        {{ $row->updated_at->format('Y-m-d') }}
    </small>
</div>
@endif