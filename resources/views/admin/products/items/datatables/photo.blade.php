@if(!empty($row->filepath))
{{ HTML::image(@$row->filehost . $row->filepath, null, array('width' => '40px', 'class' => 'image-preview', 'data-img' => @$row->filehost . $row->filepath)) }}
@elseif(isset($row->avatar))
    {{ HTML::image(asset('assets/img/default/default.thumb.png'), null, array('width' => '40px', 'class' => 'image-preview', 'data-img' => asset('assets/img/default/avatar.png'))) }}
@else
{{ HTML::image(asset('assets/img/default/default.thumb.png'), null, array('width' => '40px', 'class' => 'image-preview', 'data-img' => asset('assets/img/default/default.thumb.png'))) }}
@endif
