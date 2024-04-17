<a href="{{ route('admin.fleet.incidences.fix.edit', [$row->id, 'vehicle' => $row->vehicle_id]) }}" data-toggle="modal" data-target="#modal-remote">
@if($row->is_fixed)
    <span class="label label-success">@trans('Resolvido')</span>
@else
    <span class="label label-danger">@trans('Por Resolver')</span>
@endif
</a>