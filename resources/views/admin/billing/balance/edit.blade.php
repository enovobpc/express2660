{{ Form::model($product, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('ref', 'Referência') }}
                {{ Form::text('ref', null, ['class' => 'form-control', 'maxlenght' => 10]) }}
            </div>
        </div>
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('cost_price', 'Preço de Custo') }}
                <div class="input-group">
                    {{ Form::text('cost_price', null, ['class' => 'form-control']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
            <div class="form-group is-required">
                {{ Form::label('price', 'Preço') }}
                <div class="input-group">
                    {{ Form::text('price', null, ['class' => 'form-control']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('promo_price', 'Preço Promoção') }}
                <div class="input-group">
                    {{ Form::text('promo_price', null, ['class' => 'form-control']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('stock', 'Stock Disponível') }}
                {{ Form::text('stock', null, ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('stock_min', 'Stock Mínimo de Compra') }}
                {{ Form::text('stock_min', empty($product->stock_min) ? 1 :null, ['class' => 'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('stock_warning', 'Alerta de Stock Mínimo') }}
                {{ Form::text('stock_warning', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('vat_rate', 'Taxa IVA') }}
                {{ Form::select('vat_rate', ['' => ''] + $vatRates, null, ['class' => 'form-control select2', 'required']) }}
            </div>
            <div class="form-group is-required">
                {{ Form::label('unity', 'Unidade') }}
                {{ Form::select('unity', ['' => ''] + trans('unities'), null, ['class' => 'form-control select2', 'required']) }}
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
    $(".select2").select2(Init.select2());
</script>

