<div class="row">
    <div class="col-sm-8 col-md-9">
        <div class="form-group">
            {{ Form::label('title', 'Título') }}
            {!! Form::textTrans('title', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {{ Form::label('subtitle', 'Subtítulo') }}
            {!! Form::textTrans('subtitle', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {{ Form::label('url', 'Ao clicar, ir para URL') }}<br/>
            {!! Form::textTrans('url', null, ['class' => 'form-control']) !!}
        </div>
        <div class="checkbox margin-top-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('target_blank', 1, null) }}
                Abrir URL num novo separador
            </label>
        </div>
        <div class="checkbox margin-top-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('constrain_proportions', 1, null) }}
                Manter porporções da imagem
            </label>
        </div>
    </div>
    <div class="col-sm-4 col-md-3">
        {{ Form::label('image', 'Imagem', array('class' => 'form-label')) }}<br/>
        <div class="fileinput {{ $content->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
            <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                <img src="{{ asset('assets/img/default/default.thumb.png') }}">
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;">
                @if($content->filepath)
                    <img src="{{ asset($content->getCroppa(200)) }}">
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
<script>
    $('[data-dismiss="fileinput"]').on('click', function () {
        $('[name=delete_photo]').val(1);
    })
</script>