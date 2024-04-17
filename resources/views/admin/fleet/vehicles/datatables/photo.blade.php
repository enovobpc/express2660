@if (!empty($row->filepath))
    {{ HTML::image(@$row->filehost . $row->getThumb(), null, array('width' => '40px', 'class' => 'image-preview', 'data-img' => @$row->filehost . $row->getCroppa(200))) }}
@else
    <?php $url = '/uploads/fleet_brands/' . str_slug($row->brand->name) . '.jpg'; ?>
    @if(File::exists(public_path().$url))
        {{ HTML::image(asset($url), null, array('width' => '40px', 'class' => 'image-preview', 'data-img' => asset($url))) }}
    @else
        {{ HTML::image(asset('assets/img/default/default.thumb.png'), null, array('width' => '40px', 'class' => 'image-preview', 'data-img' => asset($url))) }}
    @endif
@endif