@if($row->emails)
    {{ count(explode(',', $row->emails)) }}
@endif
