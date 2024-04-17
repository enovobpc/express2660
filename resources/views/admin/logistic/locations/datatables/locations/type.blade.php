@if($row->type_id)
<span class="image-preview" data-img="{{ asset(@$row->type->filepath) }}">
    {{ @$row->type->name }} <i class="fas fa-info-circle"></i>
</span>
@endif