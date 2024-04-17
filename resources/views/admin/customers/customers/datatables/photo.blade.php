@if (!empty($row->filepath))
{{ HTML::image(@$row->filehost . $row->getCroppa(200), null, array('width' => '40px', 'class' => 'image-preview', 'data-img' => @$row->filehost . $row->getCroppa(200))) }}
@else
{{ HTML::image(asset('assets/img/default/default.thumb.png'), null, array('width' => '40px', 'class' => 'image-preview', 'data-img' => asset('assets/img/default/default.thumb.png'))) }}
@endif

