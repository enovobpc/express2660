{{ Form::model($packType, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('code', 'Código') }}
                {{ Form::text('code', null, ['class' => 'form-control', 'required', 'maxlength' => 4]) }}
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('type', 'Tipo') }}
                {{ Form::select('type', ['' => ''] + trans('admin/global.packs-types'), null, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('description', 'Descrição') }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('width', 'Comprimento') }}
                <div class="input-group">
                    {{ Form::text('width', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">
                        {{ Setting::get('shipments_volumes_mesure_unity') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('length', 'Largura') }}
                <div class="input-group">
                    {{ Form::text('length', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">
                        {{ Setting::get('shipments_volumes_mesure_unity') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('height', 'Altura') }}
                <div class="input-group">
                    {{ Form::text('height', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">
                        {{ Setting::get('shipments_volumes_mesure_unity') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('weight', 'Peso Máx.') }}
                <div class="input-group">
                    {{ Form::text('weight', null, ['class' => 'form-control decimal']) }}
                    <div class="input-group-addon">Kg</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group m-t-25">
                <div class="checkbox m-0">
                    <label style="padding-left: 0">
                        {{ Form::checkbox('is_active', 1, $packType->exists ? null : true) }}
                        Ativo
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2());
</script>
