{{ Form::open(['route' => ['admin.website.pages.sections.store', $section->page_id], 'method' => 'POST']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Criar nova secção</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <h4 class="m-t-8 m-b-0">Escolha uma estrutura para aplicar nesta secção.</h4>
        </div>
    </div>
    <hr/>
    <div class="row row-5 text-center">
        @foreach(trans('admin/pages.layouts') as $key => $layout)
        <div class="col-md-3">
            <div class="page-layout-preview" data-id="{{ $key }}">
                <img src="{{ asset('assets/img/default/pages/'.$key.'.png') }}" class="h-70px"/>
            </div>
        </div>
        @endforeach
    </div>
</div>
<div class="modal-footer">
    <div class="hide">
        {{ Form::select('layout', ['' => ''] + trans('admin/pages.layouts'), null, ['class' => 'form-control select2 pull-right', 'required']) }}
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());

    $(document).on('click', '.page-layout-preview', function(){
        var layout = $(this).data('id');
        $('.page-layout-preview').removeClass('active');
        $(this).addClass('active');
        $('[name="layout"]').val(layout).trigger('change')
    })
</script>