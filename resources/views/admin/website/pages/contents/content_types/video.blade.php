<div class="row">
    <div class="col-sm-8">
        <div class="form-group is-required">
            {{ Form::label('url', 'URL') }}
            <div class="input-group">
                {{ Form::text('url', null, ['class' => 'form-control required']) }}
                <span class="input-group-btn">
                    <button class="btn btn-default get-video" class="get-video" type="button"><i class="fas fa-sync-alt"></i></button>
                </span>
            </div>

        </div>
        <div class="error-label"></div>
        <div class="form-group">
            {{ Form::label('title', 'Título') }}
            {!! Form::textTrans('title', null, ['class' => 'form-control']) !!}
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    {{ Form::label('width', 'Comprimento') }}
                    <div class="input-group">
                        {{ Form::text('width', null, ['class' => 'form-control']) }}
                        <span class="input-group-addon">px</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {{ Form::label('height', 'Altura') }}
                    <div class="input-group">
                        {{ Form::text('height', null, ['class' => 'form-control']) }}
                        <span class="input-group-addon">px</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="checkbox m-t-25">
                    <label style="padding-left: 0 !important">
                        {{ Form::checkbox('autoplay', 1, null) }}
                        Reproduzir automáticamente
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <label>Pré-visualização</label>
        <div style="border: 1px solid #ddd; min-height: 150px; width: 100%">
            <img src="{{ $content->preview }}" class="img-result-content w-100"/>
        </div>
    </div>
</div>
{{ Form::hidden('preview') }}
{{ Form::hidden('embed') }}

<script>
    $(document).on('click', '.get-video', function(e){e.stopPropagation(); getURL(); })
    $(document).on('change', '.modal-content [name="url"]', function(){ getURL(); })

    function getURL(){
        var url = $('.modal-content [name="url"]').val();

        $('.error-label').html('<div class="text-center text-muted"><i class="fa fa-spin fa-circle-o-notch"></i> A carregar informação do vídeo...</div>');
        $.post('{{ route('admin.website.pages.sections.content.video.get') }}', { url : url }, function(data){

            if(data.result) {
                $('.img-result-content').attr('src', data.preview);
                $('[name="pt[title]').val(data.title);
                $('[name="preview"]').val(data.preview);
                $('[name="embed"]').val(data.embed);

                $('.error-label').html('');
            } else {
                $('.error-label').html('<div class="text-center text-red"><i class="fa fa-warning"></i> '+ data.feedback +'</div>');
            }

        }).error(function(){
            $('.error-label').html('<div class="text-center text-red"><i class="fa fa-warning"></i> Erro interno ao tentar obter a informação do vídeo</div>');
        })
    }
</script>