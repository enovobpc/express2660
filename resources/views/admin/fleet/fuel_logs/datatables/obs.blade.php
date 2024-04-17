{{ $row->obs }}
@if($row->filepath)
    @if($row->obs)
        <br/>
    @endif
    <a href="{{ asset($row->filepath) }}" data-target="_blank">
        <i class="fas fa-paperclip"></i>
    </a>
@endif