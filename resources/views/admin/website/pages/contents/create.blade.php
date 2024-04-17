{{ Form::open(['route' => ['admin.website.pages.sections.content.store', $pageId, $sectionId], 'method' => 'POST', 'class' => 'form-create-content']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Adicionar conteúdos ao bloco</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12 text-center">
            <h4 class="m-t-10 m-b-20">Que tipo de conteúdo quer adicionar a este bloco?</h4>
            @foreach(trans('admin/pages.content-types') as $key => $type)
            <button class="btn btn-app" type="button" data-id="{{ $key }}">
                <i class="fa {{ $type['icon'] }}"></i> {{ $type['text'] }}
            </button>
            @endforeach
            <select name="content_type" class="hide">
                <option></option>
                @foreach(trans('admin/pages.content-types') as $key => $type)
                    <option value="{{ $key }}">{{ $key }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::hidden('block', $block) }}
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Confirmar</button>
</div>
{{ Form::close() }}

<script>
    $('.form-create-content').on('submit', function(e){
        e.preventDefault();

        var $form = $(this).closest('form')
        var $button = $(this).find('button[type="submit"]');

        $button.button('loading');

        $.post($form.attr('action'), $form.serialize(), function(data){
            $('#modal-remote')
                .find('.modal-dialog')
                .addClass('modal-lg')
                .find('.modal-content')
                .html(data);
        }).fail(function (data) {
            Growl.error500()
            $button.button('reset');
        }).always(function(){
            $button.button('reset');
        })
    })

    $(document).on('click', '.btn-app', function(){
        $('.btn-app').removeClass('active');
        $(this).addClass('active');
        $('[name="content_type"]').val($(this).data('id'))
    })
</script>