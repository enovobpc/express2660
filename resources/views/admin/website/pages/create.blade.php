{{ Form::model($page, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">Criar Página</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="row row-10">
                <div class="col-sm-9">
                    <div class="form-group is-required">
                        {{ Form::label('url', 'URL') }}
                        <div class="input-group">
                            <span class="input-group-addon">{{ config('app.url') }}/</span>
                            {{ Form::text('url', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('code', 'Código') }}
                        {{ Form::text('code', null, ['class' => 'form-control', 'required']) }}
                    </div>
                </div>
            </div>
            <hr/>
            <div class="form-group is-required">
                {{ Form::label('title', 'Título da Página') }}
                {{ Form::text('title', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
            </div>
            <div class="form-group">
                {{ Form::label('description', 'Descrição') }}<br/>
                {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) }}
            </div>

            <div class="form-group m-b-0">
                <label>
                    {{ Form::checkbox('show_title', '1', true, array('class' => 'flat-blue')) }}
                    Mostrar Título
                </label>
            </div>
            <div class="form-group m-b-0">
                <label>
                    {{ Form::checkbox('show_breadcrumb', '1', true, array('class' => 'flat-blue')) }}
                    Mostrar navegação até à página (por cima do título)
                </label>
            </div>
            <div class="form-group m-b-0">
                <label>
                    {{ Form::checkbox('published', '1', null, array('class' => 'flat-blue')) }}
                    Publicar Página
                </label>
            </div>
        </div>
        <div class="col-sm-6">
            <h4>Otimização para Motores de Pesquisa</h4>
            <hr/>
            <div class="row">
                <div class="col-sm-4">
                    {{ Form::label('image', 'Imagem SEO', array('class' => 'form-label')) }}<br/>
                    <div class="fileinput {{ $page->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                        <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                            <img src="{{ asset('assets/img/default/default.thumb.png') }}">
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;">
                            @if($page->filepath)
                                <img src="{{ asset($page->getCroppa(200)) }}">
                            @endif
                        </div>
                        <div>
                    <span class="btn btn-default btn-block btn-sm btn-file">
                        <span class="fileinput-new">Procurar...</span>
                        <span class="fileinput-exists"><i class="fa fa-refresh"></i> Alterar</span>
                        <input type="file" name="image">
                    </span>
                            <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                                <i class="fa fa-close"></i> Remover
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group">
                        {{ Form::label('meta_title', 'Título SEO') }}
                        {{ Form::text('meta_title', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('meta_description', 'Descrição SEO') }}<br/>
                        {{ Form::textarea('meta_description', null, ['class' => 'form-control', 'rows' => 5]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::hidden('delete_photo') }}
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}

<script>
    $('[data-dismiss="fileinput"]').on('click', function () {
        $('[name=delete_photo]').val(1);
    })
</script>