<i class="fas fa-circle" style="color: {{ @$row->provider->color }}"></i> {{ @$row->provider->name }}
@if($row->filepath)
    <a href="{{ asset($row->filepath) }}" data-target="_blank">
        <i class="fas fa-paperclip"></i>
    </a>
@endif