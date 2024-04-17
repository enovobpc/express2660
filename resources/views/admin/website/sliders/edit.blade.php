{{ Form::model($slider, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span class="font-size-32px" aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-8">
            <div class="form-group">
                {{ Form::label('caption', 'Título')}}
                {!! Form::textTrans('caption', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {{ Form::label('subcaption', 'Subtítulo')}}
                {!! Form::textTrans('subcaption', null, ['class' => 'form-control']) !!}
            </div>
            <div class="row row-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('caption_posy', 'Legenda - Posição Vertical')}}
                        {{ Form::select('caption_posy', ['top' => 'Ao cimo', 'middle' => 'Ao centro', 'bottom' => 'Em baixo'], null, ['class' => 'form-control select2'])  }}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('caption_posy', 'Legenda - Posição Horizontal')}}
                        {{ Form::select('caption_posy', [ 'left' => 'À esquerda', 'center' => 'Ao meio', 'right' => 'À direita'], null, ['class' => 'form-control select2'])  }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-8">
                    <div class="form-group">
                        {{ Form::label('url', 'Ao clicar na imagem, ir para o URL')}}
                        {!! Form::textTrans('url', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="height-30"></div>
                    <div class="form-group m-b-0">
                        <label style="font-weight: normal;">
                            {{ Form::checkbox('target_blank', '1') }}
                            Abrir em novo separador
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group margin-0 is-required">
                {{ Form::label('locales[]', 'Mostrar slider nas seguintes línguas')}}
                {{ Form::select('locales[]', app_locales(), $slider->locales ? null : array_keys(app_locales()), ['class' => 'form-control select2', 'multiple', 'required'])  }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('imagem', 'Imagem (Tablet e Computador)')}}
                <div class="clearfix"></div>
                @if($slider->exists)
                    <div class="fileinput" style="width: 100%;">
                        <div class="fileinput-preview thumbnail" style="
                                height: 100px;
                                width: 100%;
                                background-repeat: no-repeat;
                                background-position: center;
                                background-size: contain;
                                background-image: url({{ asset($slider->getCroppa(250)) }})
                                ">
                        </div>
                    </div>

                @else
                    <div class="fileinput fileinput-new" data-provides="fileinput" style="width: 100%">
                        <div class="fileinput-preview thumbnail preview-xs" data-trigger="fileinput" style="width: 250px; max-height: 100px; min-height: 100px;"></div>
                        <div>
                        <span class="btn btn-default btn-file">
                            <span class="fileinput-new">Selecionar</span>
                            <span class="fileinput-exists">Alterar</span>
                            <input type="file" name="imagem" accept="image/*" required>
                        </span>
                            <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remover</a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="form-group m-b-0">
                {{ Form::label('imagem', 'Imagem (Telemóvel)')}}
                <div class="clearfix"></div>
                @if($slider->exists)
                    <div class="fileinput" style="width: 100%;">
                        <div class="fileinput-preview thumbnail" style="
                                height: 175px;
                                width: 100%;
                                background-repeat: no-repeat;
                                background-position: center;
                                background-size: contain;
                                background-image: url({{ asset($slider->getCroppa(250, null, null, 'filepath_xs')) }})
                                ">
                        </div>
                    </div>
                @else
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 250px; min-width: 100px; min-height: 100px;"></div>
                    <div>
                        <span class="btn btn-default btn-file">
                            <span class="fileinput-new">Selecionar</span>
                            <span class="fileinput-exists">Alterar</span>
                            <input type="file" name="imagem_xs" accept="image/*">
                        </span>
                        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remover</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
<div class="modal-footer">
    <div class="pull-left">
        <div class="form-group m-b-0 m-t-5">
            <label style="font-weight: normal;">
                {{ Form::checkbox('visible', 1, $slider->exists ? $slider->visible : true) }}
                Apresentar imagem nos slides
            </label>
        </div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary" data-loading-text="Guardar...">Gravar</button>
</div>
{{ Form::close() }}
<script>
    $('.select2').select2(Init.select2());
</script>
