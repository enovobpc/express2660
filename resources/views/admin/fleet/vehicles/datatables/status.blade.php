@if($row->status)
<span class="label {{ trans('admin/fleet.vehicles.status-color.'. $row->status) }}">
    {{ trans('admin/fleet.vehicles.status.'. $row->status) }}
</span>
@endif