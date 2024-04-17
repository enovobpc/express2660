{{ Form::model($webserviceMethod, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-10">
        <div class="col-sm-6">
            <div class="form-group is-required m-b-5">
                {{ Form::label('name', 'Designação no sistema') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required m-b-5">
                {{ Form::label('method', 'Método') }}
                {{ Form::text('method', null, ['class' => 'form-control lowercase nospace', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="checkbox m-t-25 m-b-0">
                <label style="padding-left: 0 !important">
                    {{ Form::checkbox('enabled', 1) }}
                    Método ativo
                </label>
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
    $('.select2').select2(Init.select2());
</script>

