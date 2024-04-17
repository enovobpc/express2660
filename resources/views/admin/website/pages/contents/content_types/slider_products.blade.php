<div class="row">
    <div class="col-sm-12">
        <div class="form-group is-required">
            {{ Form::label('include_view', 'Conteúdo do Slider') }}
            {{ Form::select('include_view', ['products.sliders.new_products' => 'Produtos Novos', 'products.sliders.promo_products' => 'Produtos em Promoção'], null, ['class' => 'form-control required']) }}
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
        $.post('{{ route('admin.pages.sections.content.video.get') }}', { url : url }, function(data){

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