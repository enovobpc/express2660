<div class="thumbnail">
    <img src="{{ asset($content->filepath) }}" alt="{{ $content->title }}" class="w-100"/>
    <div class="caption">
        @if($content->title)
            <h3>{{ $content->title }}</h3>
        @endif
        @if($content->content)
            <p>{{ $content->content }}</p>
        @endif

        @if($content->btn_primary_title)
        <a href="{{ $content->btn_primary_url }}" class="btn btn-primary"
           style="{{ $content->btn_primary_color ? 'color:' . $content->btn_primary_color : ''}}
           {{ $content->btn_primary_background ? 'background:' . $content->btn_primary_background : ''}}" role="button">{{ $content->btn_primary_title }}</a>
        @endif

        @if($content->btn_secundary_title)
            <a href="{{ $content->btn_secundary_url }}" class="btn btn-default"
               style="{{ $content->btn_secundary_color ? 'color:' . $content->btn_secundary_color : ''}}
               {{ $content->btn_secundary_background ? 'background:' . $content->btn_secundary_background : ''}}" role="button">{{ $content->btn_secundary_title }}</a>
        @endif

    </div>
</div>