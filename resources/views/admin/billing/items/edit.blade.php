@php
    $checkboxState = @$isFleetPart || $product->exists ? 'disabled' : '';
@endphp

{{ Form::model($product, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    @if (!$product->exists)
        <div class="alert alert-info">
            <h4 class="bold">Atenção</h4>
            <p>Ao fim de criado um artigo, já não é possível alterar se o mesmo é um serviço ou se contém stock.</p>
        </div>
    @endif

    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('reference', 'Referência') }}
                {{ Form::text('reference', null, ['class' => 'form-control text-uppercase', 'required']) }}
            </div>
        </div>
        <div class="col-sm-9">
            <div class="form-group is-required">
                {{ Form::label('name', 'Designação') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('provider_id', 'Fornecedor') }}
                {{ Form::select('provider_id', ['' => ''] + ($providers ?? []), null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('provider_reference', 'Ref. Fornecedor') }}
                {{ Form::text('provider_reference', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('price', 'Preço Compra') }}
                <div class="input-group">
                    {{ Form::text('price', null, ['class' => 'form-control']) }}
                    <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('sell_price', 'Preço Venda') }}
                <div class="input-group">
                    {{ Form::text('sell_price', null, ['class' => 'form-control']) }}
                    <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                </div>
            </div>
        </div>

        {{-- <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('type', 'Tipo') }}
                {{ Form::select('type', ['' => ''], null, ['class' => 'form-control select2']) }}
            </div>
        </div> --}}
    </div>

    <div class="row row-5">
        <div class="col-sm-3 has-stock-visible">
            <div class="form-group">
                {{ Form::label('unity', 'Unidade') }}
                {{ Form::select('unity', trans('admin/billing.items-unities'), null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('tax_rate', 'Taxa IVA') }}
                {{ Form::select('tax_rate', ['' => ''] + $taxRates, $product->tax_rate ?? 23, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('brand_id', 'Marca') }}&nbsp;<a href="{{ route('admin.brands.index') }}" target="_blank">(Gerir Marcas)</a>
                {{ Form::select('brand_id', ['' => ''] + $brands, null, ['class' => 'form-control select2']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('brand_model_id', 'Modelo') }}&nbsp;<i class="fas fa-spin fa-spinner" style="display: none"></i>
                {{ Form::select('brand_model_id', ['' => ''] + ($brandModels ?? []), null, ['class' => 'form-control select2']) }}
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group m-b-0">
                {{ Form::label('obs', 'Observações') }}
                {{ Form::text('obs', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>

    <div class="row row-5">
        <div class="col-sm-4">
            <div class="checkbox mt-t-15">
                <label style="padding-left: 0px !important">
                    @if (@$isFleetPart)
                    {{ Form::hidden('is_service', false) }}
                    @endif

                    {{ Form::checkbox('is_service', 1, @$isFleetPart ? false : null, [$checkboxState]) }}
                    Considerar Serviço <i class="fas fa-info-circle" data-toggle="tooltip" title="Ao ativar esta opção, este artigo será considerado um serviço em vez de um produto."></i>
                </label>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="checkbox mt-t-15">
                <label style="padding-left: 0px !important">
                    @if (@$isFleetPart)
                    {{ Form::hidden('has_stock', true) }}
                    @endif

                    {{ Form::checkbox('has_stock', 1, @$isFleetPart ? true : null, [$checkboxState]) }}
                    Contém stock?
                </label>
            </div>
        </div>
        @if (hasModule('fleet'))
        <div class="col-sm-4 has-stock-visible">
            <div class="checkbox mt-t-15">
                <label style="padding-left: 0px !important">
                    @if (@$isFleetPart)
                    {{ Form::hidden('is_fleet_part', true) }}
                    @endif

                    {{ Form::checkbox('is_fleet_part', 1, @$isFleetPart ? true : null, [$checkboxState]) }}
                    Considerar Peça <i class="fas fa-info-circle" data-toggle="tooltip" title="Ao ativar esta opção, este artigo fica disponível como uma peça para usar no módulo gestão de frota."></i>
                </label>
            </div>
        </div>
        @endif
    </div>

    <div class="row row-5">
        <div class="col-sm-2">
            <div class="checkbox mt-t-15">
                <label style="padding-left: 0px !important">
                    {{ Form::checkbox('is_active', 1, !$product->exists ? true : null) }}
                    Ativo
                </label>
            </div>
        </div>

        <div class="col-sm-10">
            <div class="checkbox mt-t-15">
                <label style="padding-left: 0px !important">
                    {{ Form::checkbox('is_customer_customizable', 1, null) }}
                    Permitir personalizar o preço de venda por cada cliente
                </label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options" style="width: 70%">
        @if(hasModule('invoices'))
        <div class="checkbox">
            <label>
                {{ Form::checkbox('update_billing') }}
                Alterar também no programa de faturação <i class="fas fa-info-circle" data-toggle="tooltip" title="Ao ativar esta opção, este artigo será atualizado no programa de faturação."></i>
            </label>
        </div>
        @else
            <div class="checkbox">
                <label>
                    {{ Form::checkbox('update_billing', 1, false, ['disabled']) }}
                    Alterar também no programa de faturação <i class="fas fa-info-circle" data-toggle="tooltip" title="Ao ativar esta opção, este artigo será atualizado no programa de faturação."></i>
                </label>
            </div>
        @endif
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary btn-submit">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal [data-toggle="tooltip"]').tooltip();

    $('[name="brand_id"]').on('change', function () {
        var $this = $(this);
        var $brandModel = $('[name="brand_model_id"]');
        var $icon = $brandModel.parent().find('.fa-spinner');

        $brandModel.empty();
        $brandModel.append('<option value=""></option>')

        $icon.show();
        $.get('{{ route('admin.brands.models.list', $product->brand_id) }}', {
            brand_id: $this.val(),
        }, function ({ data }) {
            $brandModel.select2({
                'data': data
            });
        }).always(function () {
            $icon.hide();
        });
    });

    $('[name="is_service"]').on('change', function () {
        var $this = $(this);
        if ($this.is(':checked')) {
            $('[name="has_stock"]').attr('disabled', true);
            $('[name="has_stock"]').parent().css('text-decoration', 'line-through');
            $('[name="has_stock"]').prop('checked', false);
        } else {
            $('[name="has_stock"]').attr('disabled', false);
            $('[name="has_stock"]').parent().css('text-decoration', 'none');
        }

        $('[name="has_stock"]').trigger('change');
    });

    $('[name="has_stock"]').on('change', function () {
        var $this = $(this);
        if ($this.is(':checked')) {
            $('.has-stock-visible').show();
        } else {
            $('.has-stock-visible').hide();
        }
    });

    // Triggers to update design
    @if ($checkboxState != 'disabled')
    $('[name="is_service"]').trigger('change');  
    @endif
    $('[name="has_stock"]').trigger('change');
</script>
