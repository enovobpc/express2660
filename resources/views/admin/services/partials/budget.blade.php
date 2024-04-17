<div class="row">
    <div class="col-sm-2">
        {{ Form::label('image', 'Imagem/Logótipo', ['class' => 'form-label']) }}<br />
        <div class="fileinput {{ $service->filepath ? 'fileinput-exists' : 'fileinput-new' }}" data-provides="fileinput">
            <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                <img src="{{ asset('assets/img/default/default.thumb.png') }}">
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;">
                @if ($service->filepath)
                    <img src="{{ asset($service->getCroppa(200)) }}" onerror="this.src = '{{ img_broken(true) }}'" class="img-responsive">
                @endif
            </div>
            <div>
                <span class="btn btn-default btn-block btn-sm btn-file">
                    <span class="fileinput-new">Procurar...</span>
                    <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> Alterar</span>
                    <input type="file" name="image">
                </span>
                <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                    <i class="fas fa-close"></i> Remover
                </a>
            </div>
        </div>
    </div>
    <div class="col-sm-5">
        <div class="form-group">
            {{ Form::label('description', 'Informação Serviço') }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 20]) }}
        </div>
    </div>
    <div class="col-sm-5">
        <div class="form-group">
            {{ Form::label('description2', 'Informação Geral') }}
            {{ Form::textarea('description2', null, ['class' => 'form-control', 'rows' => 20]) }}
        </div>
    </div>
</div>