<div class="published">
@if($row->is_published)
    <i class="fas fa-check-circle text-green" data-toggle="tooltip" title="Publicado"></i>
@else
    <i class="fas fa-times-circle text-red" data-toggle="tooltip" title="Não Publicado"></i>
@endif
</div>