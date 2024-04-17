{{ Form::model($category, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {!! Form::textTrans('name', null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('name', 'Icone') }}
                <div class="input-group iconpicker-container">
                    {{ Form::text('icon', null, ['class' => 'form-control iconpicker']) }}
                    <span class="input-group-addon" style="padding: 9px 10px;">
                        <i class="fa {{ $category->icon ? $category->icon : 'fa-angle-right' }}"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group m-0">
        <label style="font-weight: normal !important">
            {!! Form::checkbox('is_visible', 1, $category->exists ? null : true) !!}
            Categoria Visivel
        </label>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}
{{ Html::style('/vendor/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.css') }}
{{ Html::script('/vendor/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.js') }}
<script>
    $('.iconpicker').iconpicker();
</script>

