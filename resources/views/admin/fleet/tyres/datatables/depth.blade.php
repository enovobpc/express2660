@if($row->depth >= 1.6) 
<span class="label label-success">{{ $row->depth }} mm</span>
@elseif($row->depth >= 1.0 && $row->depth < 1.6) 
<span class="label label-warning">{{ $row->depth }} mm</span>
@elseif($row->depth > 0.0 && $row->depth < 1.0) 
<span class="label label-danger">{{ $row->depth }} mm</span>
@else
<span class="label label-default">-- mm</span>
@endif