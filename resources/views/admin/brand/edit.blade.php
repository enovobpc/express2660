{{ Form::model($brand, $formOptions) }}
<div class="modal-header">
    <button class="close" data-dismiss="modal" type="button">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    @if (!$brand->exists)
        <div class="alert alert-info">
            <h4 class="bold">Informação</h4>
            <p>Ao fim de gravar a marca poderá adicionar modelos.</p>
        </div>
    @endif

    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('obs', 'Observações') }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <div class="checkbox m-b-0 m-t-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('is_active', 1, $brand->exists ? null : true) }}
                Ativo
            </label>
        </div>
    </div>
    <button class="btn btn-default" data-dismiss="modal" type="button">Fechar</button>
    <button class="btn btn-primary" type="submit">Guardar</button>
</div>
{{ Form::close() }}
