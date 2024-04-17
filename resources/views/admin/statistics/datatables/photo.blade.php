@if (!empty($row->filepath))
{{ HTML::image(@$row->filehost . $row->getCroppa(200), null, array('style' => 'max-height: 40px; max-width: 150px', 'class' => 'image-preview', 'data-img' => @$row->filehost . $row->getCroppa(200))) }}
@endif

