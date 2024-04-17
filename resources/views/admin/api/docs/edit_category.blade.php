{{ Form::model($category, $formOptions) }}
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
                {{ Form::label('api_version', 'Versão API') }}
                {{ Form::text('api_version', null, ['class' => 'form-control lowercase', 'required', 'maxlength' => 5]) }}
            </div>
        </div>
        <div class="col-sm-5">
            <div class="form-group is-required">
                {{ Form::label('name', 'Nome') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 35]) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('slug', 'Slug') }}
                {{ Form::text('slug', null, ['class' => 'form-control nospace lowercase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('module', 'Módulo') }} {!! tip('Visivel só se o módulo estiver ativo') !!}
                {{ Form::text('module', null, ['class' => 'form-control nospace lowercase']) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('description', 'Descrição') }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'required', 'rows' => 3]) }}
            </div>
        </div>
<!--        <div class="col-sm-3">
            <div class="checkbox m-b-0 m-t-5">
                <label style="padding-left: 0 !important">
                    {{ Form::checkbox('sales_visible', 1, $category->exists ? null : true) }}
                    Visivel Vendas
                </label>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="checkbox m-b-0 m-t-5">
                <label style="padding-left: 0 !important">
                    {{ Form::checkbox('purchases_visible', 1, $category->exists ? null : true) }}
                    Visivel Compras
                </label>
            </div>
        </div>-->
    </div>
</div>
<div class="modal-footer">
<!--    <div class="pull-left">
        <div class="checkbox m-b-0 m-t-5">
            <label style="padding-left: 0 !important">
                {{ Form::checkbox('is_active', 1, $category->exists ? null : true) }}
                Ativo
            </label>
        </div>
    </div>-->
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
{{ Form::close() }}