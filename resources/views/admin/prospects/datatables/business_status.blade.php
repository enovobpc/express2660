<div class="text-center">
    @if($row->business_status)
        <span class="label" style="background: {{ trans('admin/prospects.status-label.'.$row->business_status) }}">
            {{ trans('admin/prospects.status.' . $row->business_status) }}
        </span>
    @else
        <span class="label label-default">
            @trans('Pendente')
        </span>
    @endif
</div>