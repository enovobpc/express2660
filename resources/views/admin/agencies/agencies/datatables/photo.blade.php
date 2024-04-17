@if (empty($row->filepath))
    <div class="text-center text-red" style="width:80px">
        <i class="fas fa-image text-muted fs-18"></i><br/>
        <small><i class="fas fa-exclamation-triangle"></i> Em Falta</small>
    </div>
@else
    {{ HTML::image(@$row->filehost . $row->getCroppa(200), null, array('style' => 'min-height: 20px; max-height: 40px; max-width: 150px; min-width: 50px', 'class' => 'image-preview', 'data-img' => @$row->filehost . $row->getCroppa(200))) }}
@endif

