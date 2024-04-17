<a href="#" class="btn btn-sm btn-success" data-toggle="modal" data-target="#upload-multimedia">
    <i class="fa fa-upload"></i> Carregar ficheiro
</a>
<div class="box box-default box-solid m-t-15">
    <div class="box-header with-border">
        <h4 class="box-title">{{ count($multimedia) }} Ficheiros no servidor</h4>
        <label class="pull-right" style="margin-top: -2px">
            {{ Form::checkbox('select-all', '', null, ['class' => 'select-all', 'data-target' => '.image-select']) }}
            Selecionar Tudo
        </label>
    </div>
    <div class="box-body">
        <div class="page-media-navigator">
            @foreach($multimedia as $file)
                <div class="file-preview" data-toggle="tooltip" title="Clique para copiar URL" data-copy="{{ asset('/uploads/pages/' . $file->getBasename()) }}">
                    <div class="actions">
                        <div class="pull-right">
                            <a href="{{ route('admin.website.multimedia.destroy', [$file->getBasename()]) }}" data-method="delete" data-confirm="Confirma a remoção do ficheiro selecionado?" class="text-red">
                                <i class="fa fa-trash bigger-120"></i>
                            </a>
                        </div>
                        <div class="pull-left">
                            {{ Form::checkbox('image-select', $file->getBasename(), null, ['class' => 'image-select']) }}
                        </div>
                    </div>
                    @if(in_array($file->getExtension(), ['png', 'jpeg', 'jpg', 'gif', 'bmp']))
                        <img src="{{ asset('uploads/pages/' . $file->getBasename()) }}" onerror="this.src='http://energy.test/assets/img/default/img_broken.png'" class="img-responsive"/>
                    @else
                        <img src="{{ asset('assets/img/icons/'.$file->getExtension() . '.svg') }}" onerror="this.src='http://energy.test/assets/img/default/img_broken.png'" class="img-responsive"/>
                    @endif
                    <p>{{ $file->getFilename() }}</p>
                </div>
            @endforeach
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="selected-images-action hide">
    <div class="text-muted italic padding-bottom-5">
        Com os registos selecionados:
    </div>
    <div>
        {{ Form::open(array('route' => 'admin.website.multimedia.selected.destroy')) }}
        <button class="btn btn-sm btn-danger" data-action="confirm" data-title="Apagar selecionados"><i class="fa fa-trash"></i> Apagar</button>
        {{ Form::close() }}
    </div>
</div>
@include('admin.website.pages.partials.upload_multimedia')