{{ Form::model($brand, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('type', __('Tipo')) }}
                {{ Form::select('type', ['' => ''] + trans('admin/fleet.brands.types'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
            <div class="form-group is-required">
                {{ Form::label('name', __('Designação')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-4 col-md-3 p-r-30">
            {{ Form::label('image', __('Logótipo'), array('class' => 'form-label')) }}<br/>
            <div class="fileinput {{ $brand->filepath ? 'fileinput-exists' : 'fileinput-new'}}"
                 data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                    <img src="{{ asset('assets/img/default/default.thumb.png') }}">
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail"
                     style="max-width: 150px; max-height: 150px;">
                    @if($brand->filepath)
                        <img src="{{ asset(@$brand->filehost . $brand->getCroppa(150)) }}">
                    @endif
                </div>
                @if(empty($brand->filehost) || (!empty($brand->filehost) && $brand->filehost == config('app.url')))
                    <div>
                        <span class="btn btn-default btn-block btn-sm btn-file">
                            <span class="fileinput-new">@trans('Procurar...')</span>
                            <span class="fileinput-exists"><i class="fas fa-sync-alt"></i> @trans('Alterar')</span>
                            <input type="file" name="image">
                        </span>
                        <a href="#" class="btn btn-danger btn-block btn-sm fileinput-exists" data-dismiss="fileinput">
                            <i class="fas fa-close"></i> @trans('Remover')
                        </a>
                    </div>
                @else
                    <p class="text-blue"><i class="fas fa-info-circle"></i> @trans('Apenas pode alterar a imagem desta agência no servidor') {{ $brand->filehost }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::hidden('delete_photo') }}
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());

    $('[data-dismiss="fileinput"]').on('click', function () {
        $('[name=delete_photo]').val(1);
    })
</script>

