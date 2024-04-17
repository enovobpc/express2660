{{ Form::model($condition, $formOptions) }}
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
                {{ Form::text('code', null, ['class' => 'form-control lowercase', 'required', 'maxlength' => 5]) }}
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 35]) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('days', 'Dias') }}
                {{ Form::text('days', null, ['class' => 'form-control number', 'required', 'maxlength' => 4]) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="checkbox m-b-0 m-t-5">
                <label style="padding-left: 0 !important">
                    {{ Form::checkbox('sales_visible', 1, $condition->exists ? null : true) }}
                    Visivel Vendas
                </label>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="checkbox m-b-0 m-t-5">
                <label style="padding-left: 0 !important">
                    {{ Form::checkbox('purchases_visible', 1, $condition->exists ? null : true) }}
                    Visivel Compras
                </label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <div class="checkbox m-b-0 m-t-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('is_active', 1, $condition->exists ? null : true) }}
                Ativo
            </label>
        </div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}