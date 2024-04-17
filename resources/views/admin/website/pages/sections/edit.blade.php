{{ Form::model($section, ['route' => ['admin.website.pages.sections.update', $section->page_id, $section->id], 'method' => 'PUT']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Editar secção</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('container', 'Alinhamento') }}
                {{ Form::select('container', ['container' => 'Centrado', 'container-fluid' => 'A todo o comprimento (com margem)', 'no-container' => 'A todo o comprimento (sem margem)'], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('block_spacer', 'Espaço entre Blocos') }}
                {{ Form::select('block_spacer', ['' => '15px (Por defeito)', 'row-10' => '10px', 'row-5' => '5px', 'row-0' => 'Sem Espaço'], null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('background', 'Côr do Fundo') }}
                {{ Form::text('background', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('sort', 'Posição') }}
                {{ Form::text('sort', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('padding_top', 'Espaçamento Interno') }}
                <div class="input-group">
                    <span class="input-group-addon width-30">Superior</span>
                    {{ Form::text('padding_top', null, ['class' => 'form-control']) }}
                    <span class="input-group-addon">px</span>
                </div>
                <div class="input-group">
                    <span class="input-group-addon width-30">Inferior</span>
                    {{ Form::text('padding_bottom', null, ['class' => 'form-control']) }}
                    <span class="input-group-addon">px</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('padding_top', 'Espaçamento entre secções') }}
                <div class="input-group">
                    <span class="input-group-addon width-30">Superior</span>
                    {{ Form::text('margin_top', null, ['class' => 'form-control']) }}
                    <span class="input-group-addon">px</span>
                </div>
                <div class="input-group">
                    <span class="input-group-addon width-30">Inferior</span>
                    {{ Form::text('margin_bottom', null, ['class' => 'form-control']) }}
                    <span class="input-group-addon">px</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-sm-12">
            <label>Estrutura dos blocos</label>
        </div>
        <div class="text-center">
        @foreach(trans('admin/pages.layouts') as $key => $layout)
            <div class="col-md-2">
                <div class="page-layout-preview {{ $section->layout == $key ? 'active' : '' }}" data-id="{{ $key }}">
                    <img src="{{ asset('assets/img/default/pages/'.$key.'.png') }}" style="height:43px"/>
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <div class="form-group m-b-0 m-t-5">
            <label style="font-weight: normal">
                {!! Form::checkbox('is_published', '1', null, array('class' => 'flat-blue')) !!}
                Publicar esta secção
            </label>
        </div>
    </div>
    {{ Form::select('layout', ['' => ''] + trans('admin/pages.layouts'), null, ['class' => 'hide', 'required']) }}
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