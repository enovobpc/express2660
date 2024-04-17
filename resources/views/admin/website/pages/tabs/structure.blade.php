<div class="row pages">
    <div class="col-xs-12">
        <ul class="list-unstyled sortable">
        @foreach($page->sections as $section)
        <li style="margin-bottom: 15px" data-id="{{ $section->id }}">
            <div class="section-container" style="background: {{ $section->background }}">
                <div class="btn-group-vertical remove-section-btn" role="group" aria-label="...">
                    <a href="{{ route('admin.website.pages.sections.edit', [$page->id, $section->id]) }}" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-remote">
                        <span data-toggle="tooltip" title="Personalizar esta secção">
                            <i class="fa fa-paint-brush"></i>
                        </span>
                    </a>
                    <button type="button" class="btn btn-sm btn-default">
                        <span data-toggle="tooltip" title="Arrastar para ordenar">
                            <i class="fa fa-sort"></i>
                        </span>
                    </button>
                    <a href="{{ route('admin.website.pages.sections.destroy', [$page->id, $section->id]) }}" class="btn btn-sm btn-danger" data-method="delete" data-confirm="Confirma a remoção desta secção?">
                        <span data-toggle="tooltip" title="Eliminar esta secção">
                            <i class="fa fa-trash"></i>
                        </span>
                    </a>
                    @if($section->is_published)
                        <div class="text-center m-t-5" data-toggle="tooltip" title="Secção Publicada (Visivel ao Público)">
                            <i class="fa fa-circle text-green"></i>
                        </div>
                    @else
                        <div class="text-center m-t-5" data-toggle="tooltip" title="Secção não Publicada (Invisivel ao Público)">
                            <i class="fa fa-circle text-red"></i>
                        </div>
                    @endif
                </div>
                {!! \App\Models\Website\PageSection::loadContent($section->page_id, $section->layout, $section->id, true) !!}
            </div>
        </li>
        @endforeach
        </ul>
        <div class="row">
            <div class="col-xs-12">
                <a href="{{ route('admin.website.pages.sections.create', $page->id) }}" class="new-section-btn text-center" data-toggle="modal" data-target="#modal-remote">
                    <h3 class="m-0">
                        <i class="fa fa-plus-circle"></i>
                        Adicionar Secção
                    </h3>
                </a>
            </div>
        </div>
    </div>
</div>