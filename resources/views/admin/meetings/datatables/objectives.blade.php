@if($row->objectives)
    @foreach($row->objectives as $item)
        {{ trans('admin/meetings.objectives.'.$item) }}<br/>
    @endforeach
@endif