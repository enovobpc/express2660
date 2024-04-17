@if($row->occurrences)
    @foreach($row->occurrences as $item)
        {{ trans('admin/meetings.occurrences.'.$item) }}<br/>
    @endforeach
@endif