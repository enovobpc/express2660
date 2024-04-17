@if($row->filepath)
    <a href="{{ asset($row->filepath) }}"
        data-src="{{ asset($row->filepath) }}"
        data-thumb="{{ asset($row->getCroppa(200,200)) }}"
        class="preview-img">
        <img src="{{ asset($row->getCroppa(200,200)) }}" style="height: 40px; width: 40px" onerror="this.src='{{ asset('assets/img/default/broken.thumb.png') }}'"/>
    </a>
@else
    <img src="{{ asset('assets/img/default/default.thumb.png') }}" style="height: 40px; width: 40px"/>
@endif
