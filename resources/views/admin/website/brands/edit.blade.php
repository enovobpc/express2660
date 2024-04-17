{{ Form::model($brand, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-8 col-md-9">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
            <div class="row row-5">
                <div class="col-sm-8">
                    <div class="form-group">
                        {{ Form::label('url', 'Ao clicar na marca, ir para URL') }}
                        {{ Form::url('url', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group m-t-25">
                        <label style="font-weight: normal">
                            {{ Form::checkbox('target_blank', '1') }}
                            Abrir em novo separador
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('description', 'Descrição') }}<br/>
                {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
            <div class="form-group m-b-0">
                <label style="font-weight: normal">
                    {{ Form::checkbox('is_visible', '1', $brand->exists ? null : true) }}
                    Marca visivel no site
                </label>
            </div>
        </div>
        <div class="col-sm-4 col-md-3">
            {{ Form::label('image', 'Logótipo', array('class' => 'form-label')) }}<br/>
            <div class="fileinput {{ $brand->filepath ? 'fileinput-exists' : 'fileinput-new'}}" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                    <img src="{{ asset('assets/img/default/default.thumb.png') }}">
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 150px; max-height: 150px;">
                    @if($brand->filepath)
                    <img src="{{ asset($brand->getCroppa(200)) }}">
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
    $('[data-dismiss="fileinput"]').on('click', function () {
        $('[name=delete_photo]').val(1);
    })
</script>