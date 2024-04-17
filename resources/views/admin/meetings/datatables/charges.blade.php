@if($row->charges)
    @foreach($row->charges as $item)
        {{ trans('admin/meetings.charges.'.$item) }}<br/>
    @endforeach
@endif