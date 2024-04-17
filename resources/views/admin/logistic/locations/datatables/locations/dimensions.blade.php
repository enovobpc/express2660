@if($row->width && $row->height && $row->length)
{{ @$row->width }} x {{ @$row->height }} x {{ @$row->length }}
@endif