<li data-id="{{ $image->id }}">
    <a href="{{ route('admin.website.sliders.destroy', $image->id) }}" class="btn btn-xs btn-danger" data-method="delete" data-confirm="Tem a certeza que prentende remover esta imagem?">
        <i class="fa fa-times"></i>
    </a>
    <a href="{{ route('admin.website.sliders.edit', $image->id) }}" data-toggle="modal" data-target="#modal-remote-lg">
        <img src="{{ asset($image->getCroppa(250, 110)) }}" alt="{{ $image->filename }}" width="300px">
    </a>
    <div class="tools tools-right">
        <div class="tags">
            <span class="label-holder">
                @if($image->visible)
                    <span class="label label-success"><i class="fa fa-eye fa-lg"></i> Visivel</span>
                @else
                    <span class="label label-danger"><i class="fa fa-eye-slash fa-lg"></i> Oculto</span>
                @endif

                <div class="locales">
                    @foreach(app_locales() as $key => $language)
                        @if(in_array($key, $image->locales))
                        <i class="flag-icon flag-icon-{{ $key }}" data-toggle="tooltip" title="{{ $language }}"></i>
                        @endif
                    @endforeach
                </div>

               {{-- @if($image->page)
                    <span class="label label-warning bigger-110 margin-left-5">{{ trans('sliders.pages.'.$image->page) }}</span>
                @endif--}}
                @if($image->filepath_xs)
                    <span class="label bg-blue bigger-110 margin-left-5"
                        style="padding: 2px; border: 1px solid #fff;"
                        data-toggle="tooltip"
                        title="Adaptado para telemóvel">
                        <i class="fa fa-fw fa-mobile-phone"></i>
                    </span>
                @endif
            </span>
        </div>
        <div class="text">
            <b>{!! $image->caption ? $image->caption : '<i class="text-muted">Sem título</i>' !!}</b><br/>
            <small>{!! $image->subcaption ? $image->subcaption : '<i class="text-muted">Sem subtítulo</i>' !!}</small>
        </div>
    </div>
</li>