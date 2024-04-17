{{ Form::model($section, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="font-size-32px" aria-hidden="true">&times;</span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('api_version', 'Versão') }}
                {{ Form::text('api_version', null, ['class' => 'form-control lowercase', 'required', 'maxlength' => 15]) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('category_id', 'Categoria') }}
                {{ Form::select('category_id', ['' => ''] + $categories, null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('name', 'Nome Secção') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 35]) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('slug', 'Slug') }}
                {{ Form::text('slug', null, ['class' => 'form-control lowecase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('icon', 'Icone') }}
                {{ Form::text('icon', null, ['class' => 'form-control lowecase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('description', 'Descrição') }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'row' => 4]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2())
</script>




