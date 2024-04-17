<div class="section-block" id="{{ $content->block }}">
    <div class="block-options">
        @if($content->exists)
        <p class="pull-left"><i class="fas {{ trans('admin/pages.content-types.'.$content->content_type.'.icon') }}"></i> {{ trans('admin/pages.content-types.'.$content->content_type.'.text') }}</p>
        @else
            <p class="pull-left"><i class="fas fa-info-circle"></i> Bloco Vazio</p>
        @endif
            <div class="btn-group btn-group-xs pull-right" role="group">
            @if($content->exists)
                <a href="{{ route('admin.website.pages.sections.content.edit', [$pageId, $content->page_section_id, $content->block]) }}" class="btn btn-default" data-toggle="modal" data-target="#modal-remote-lg">
                    <i class="fas fa-pencil-alt"></i> Editar
                </a>
                <a href="{{ route('admin.website.pages.sections.content.destroy', [$pageId, $content->page_section_id, $content->block]) }}" class="btn btn-default" data-method="delete" data-confirm="Confirma a remoção do registo selecionado?">
                    <i class="fas fa-trash-alt"></i> Limpar
                </a>
            @else
                <a href="{{ route('admin.website.pages.sections.content.create', [$pageId, $content->page_section_id, 'block' => $content->block]) }}" class="btn btn-success" data-toggle="modal" data-target="#modal-remote">
                    <i class="fas fa-plus"></i> Criar Conteúdo
                </a>
            @endif
        </div>
        <div class="clearfix"></div>
    </div>
    {!! $blockContent !!}
</div>