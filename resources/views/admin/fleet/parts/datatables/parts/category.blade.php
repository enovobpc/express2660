@if($row->category)
    {{ trans('admin/fleet.parts.categories.' . $row->category) }}
@endif