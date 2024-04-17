{{ Form::model($testimonial, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="font-size-32px" aria-hidden="true">&times;</span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12 col-md-8">
            <div class="form-group is-required">
                {{ Form::label('message', 'Mensagem') }}
                {!! Form::textareaTrans('message', null, ['class' => 'form-control', 'rows' => 6]) !!}
            </div>
            <div class="row row-5">
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('author', 'Nome Autor') }}
                        {{ Form::text('author', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('company', 'Nome Empresa') }}
                        {{ Form::text('company', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {{ Form::label('author_role', 'Cargo do Autor') }}
                        {!! Form::textTrans('author_role', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 col-md-2">
            {{ Form::label('image', 'Logotipo', array('class' => 'form-label')) }}<br/>
            <div class="fileinput {{ $testimonial->brand_filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                    <img src="{{ asset('assets/img/default/default.thumb.png') }}">
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;">
                    @if($testimonial->brand_filepath)
                        <img src="{{ asset($testimonial->brand_filepath) }}">
                    @endif
                </div>
                <div>
                    <span class="btn btn-default btn-block btn-sm btn-file">
                        <span class="fileinput-new">Procurar...</span>
                        <span class="fileinput-exists"><i class="fa fa-refresh"></i> Alterar</span>
                        <input type="file" name="brand_image">
                    </span>
                    <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                        <i class="fa fa-close"></i> Remover
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-4 col-md-2">
            {{ Form::label('image', 'Fotografia', array('class' => 'form-label')) }}<br/>
            <div class="fileinput {{ $testimonial->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                    <img src="{{ asset('assets/img/default/default.thumb.png') }}">
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;">
                    @if($testimonial->filepath)
                        <img src="{{ asset($testimonial->getCroppa(200)) }}">
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
    </div>
</div>
<div class="modal-footer">
    {{ Form::hidden('delete_photo') }}
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());

    $('[data-dismiss="fileinput"]').on('click', function () {
        $('[name=delete_photo]').val(1);
    })
</script>