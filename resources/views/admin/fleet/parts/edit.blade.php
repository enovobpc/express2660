{{ Form::model($part, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-10">
        <div class="col-sm-4">
            <div class="form-group is-required">
                {{ Form::label('reference', __('Referência')) }}
                {{ Form::text('reference', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group is-required">
                {{ Form::label('name', __('Designação')) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
    <div class="row row-10">
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('brand_id', __('Marca')) }} <a href="{{ route('admin.brands.index') }}" target="_blank">(Gerir Marcas)</a>
                {{ Form::select('brand_id', ['' => ''] + $brands, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('brand_model_id', __('Modelo')) }}&nbsp;<i class="fas fa-spin fa-spinner" style="display: none"></i>
                {{ Form::select('brand_model_id', ['' => ''] + (@$brandModels ?? []), null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('stock_total', __('Stock Disponivel')) }}
                {{ Form::text('stock_total', null, ['class' => 'form-control decimal', 'disabled' => hasModule('purchase_invoices')]) }}
            </div>
        </div>
    </div>
    <div class="row row-10">
        {{-- <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('category', 'Categoria') }}
                {{ Form::select('category', ['' => ''] + $categories, null, ['class' => 'form-control select2', 'required'] ) }}
            </div>
        </div> --}}
    </div>
    
    <hr/>
    <div class="row row-5">
        <div class="col-sm-5">
            <div class="form-group">
                {{ Form::label('provider_id', __('Fornecedor')) }}
                {{ Form::select('provider_id', [''=>''] + $providers, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('price', __('Preço Custo')) }}
                <div class="input-group">
                    {{ Form::text('price', null, ['class' => 'form-control decimal', 'disabled' => hasModule('purchase_invoices')]) }}
                    <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::label('purchase_invoice', __('Nº Fatura')) }}
                {{ Form::text('purchase_invoice', null, ['class' => 'form-control', 'disabled' => hasModule('purchase_invoices')]) }}
            </div>
        </div>
    </div>
    <div class="form-group m-0">
        {{ Form::label('obs', __('Anotações')) }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => '3'] ) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::close() }}
<script>
    $('.modal .select2').select2(Init.select2());

    $('[name="brand_id"]').on('change', function () {
        var $this = $(this);
        var $brandModel = $('[name="brand_model_id"]');
        var $icon = $brandModel.parent().find('.fa-spinner');

        $brandModel.empty();
        $brandModel.append('<option value=""></option>')

        $icon.show();
        $.get('{{ route('admin.brands.models.list', $part->brand_id) }}', {
            brand_id: $this.val(),
        }, function ({ data }) {
            $brandModel.select2({
                'data': data
            });
        }).always(function () {
            $icon.hide();
        });
    });
</script>