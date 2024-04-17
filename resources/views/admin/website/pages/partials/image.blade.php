@if($content->url)
<a href="{{ $content->url }}" {{ $content->target_blank ? 'target="_blank"' : '' }}>
@endif
    <div class="page-image">
        @if(isExtension($content->filepath, 'mp4'))
            <video controls autoplay>
                <source src="{{ asset($content->filepath) }}" type="video/mp4">
                O seu browser não suporta a execução de vídeo
            </video>
        @else
            <div class="image-hover"></div>
            @if($content->constrain_proportions)
                <img src="{{ asset($content->filepath) }}" class="width-100"/>
            @else
                <div class="image" style="background-image: url('{{ asset($content->getLarge()) }}')"></div>
            @endif

            @if($content->title || $content->subtitle)
                <div style="position: absolute;left: 0; right: 0;">
                    <div class="caption">
                        @if($content->title)
                            <h1>{{ $content->title }}</h1>
                        @endif

                        @if($content->subtitle)
                            <h3>{{ $content->subtitle }}</h3>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
@if($content->url)
</a>
@endif
