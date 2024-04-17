{{ Form::model($productSale, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('customer_id', 'Cliente') }}
                @if(!$productSale->exists)
                    {{ Form::select('customer_id', [], null, ['class' => 'form-control', 'required']) }}
                @else
                    {{ Form::select('customer_id', [$productSale->customer->id => $productSale->customer->code .' - '. $productSale->customer->name], null, ['class' => 'form-control', 'required']) }}
                @endif
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('product_id', 'Produto') }}
                @if(!$productSale->exists)
                    {{ Form::select('product_id', [],null, ['class' => 'form-control', 'required']) }}
                @else
                    {{ Form::text('product_id_text', $productSale->product->ref . ' - ' .$productSale->product->name, ['class' => 'form-control', 'readonly']) }}
                    {{ Form::hidden('product_id', null, ['class' => 'form-control']) }}
                @endif
            </div>
        </div>
    </div>
    <div class="row row-10">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('date', 'Data Venda') }}
                {{ Form::text('date', $productSale->exists ? null : date('Y-m-d'), ['class' => 'form-control datepicker']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required m-b-0">
                {{ Form::label('qty', 'Quantidade') }}
                 {{ Form::text('qty', null, ['class' => 'form-control', 'required']) }}
            </div>
            @if($productSale->exists)
                @if($productSale->product->stock > 0)
                <small class="m-0 italic stock-available"><span class="stock">{{ $productSale->product->stock }}</span> UN extra disponíveis</small>
                @else
                <small class="m-0 italic text-red stock-empty">Sem Stock Extra</small>
                @endif
            @else
                <small class="m-0 italic stock-available" style="display: none;"><span class="stock"></span> UN disponíveis</small>
                <small class="m-0 italic text-red stock-empty" style="display: none;">Sem Stock</small>
            @endif
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('price', 'Preço') }}
                <div class="input-group">
                    {{ Form::text('price', null, ['class' => 'form-control']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('subtotal', 'Subtotal') }}
                <div class="input-group">
                    {{ Form::text('subtotal', null, ['class' => 'form-control', 'readonly']) }}
                    <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('obs', 'Notas/Observações', ['data-content' => '']) }}
        {{ Form::textarea('obs', null, ['class' => 'form-control', 'maxlength' => 500, 'rows' => 3]) }}
    </div>
    <div class="qty-alert text-red"></div>
</div>
<div class="modal-footer">
    {{ Form::hidden('vat_rate') }}
    {{ Form::hidden('cost_price') }}
    {{ Form::hidden('allowed_stock', @$productSale->product->stock + @$productSale->qty) }}
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $(".select2").select2(Init.select2());
    $(".datepicker").datepicker(Init.datepicker());

    $("select[name=customer_id]").select2({
        ajax: {
            url: "{{ route('admin.products.sales.search.customer') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=customer_id] option').remove()
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $("select[name=product_id]").select2({
        ajax: {
            url: "{{ route('admin.products.sales.search.products') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=product_id] option').remove()
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    /**
     * Change customer
     */
    $('[name=product_id]').on('change', function () {
        var $this = $(this);
        var productId = $this.val();

        $.post("{{ route('admin.products.sales.get.product') }}", {id: productId}, function (data) {
            if(data.stock) {
                $('.stock-available').show();
                $('.stock-empty').hide();
                $('.stock').html(data.stock);
                $('[name="qty"]').prop('disabled', false);
            } else {
                $('.stock-available').hide();
                $('.stock-empty').show();
                $('.stock').html(data.stock);
                $('[name="qty"]').prop('disabled', true);
            }

            $('[name="price"]').val(data.price);
            $('[name="cost_price"]').val(data.cost_price);
            $('[name="vat_rate"]').val(data.vat_rate);
            $('[name="allowed_stock"]').val(data.stock);
        }).fail(function () {
            $('[name="product_id"]').closest('.form-group').append('<p class="text-red"><i class="fas fa-exclamation-circle"></i> Não foi possível carregar a informação do produto.')
        })
    });

    $('[name=qty],[name=price]').on('change', function () {
        var $this = $(this);
        var qty   = parseFloat($('.modal [name="qty"]').val());
        var price = parseFloat($('.modal [name="price"]').val());
        var maxQty = $('.modal [name="allowed_stock"]').val();

        $('.qty-alert').hide();
        if(qty <= 0 || qty > maxQty) {
            $this.css('border-color', 'red').css('color', 'red');

            if(qty <= 0) {
                $('.qty-alert').html('<i class="fas fa-exclamation-circle"></i> A quantidade não pode ser inferior ou igual a 0').show()
            } else if(qty > maxQty) {
                $('.qty-alert').html('<i class="fas fa-exclamation-circle"></i> Quantidade indisponível. Estão disponíveis apenas '+maxQty+' unidades.').show()
            }

            $('[name="subtotal"]').val('')
        } else {
            price = qty * price;
            $this.css('border-color', '#ccc').css('color', '#000');
            $('[name="subtotal"]').val(price.toFixed(2))
        }
    });

</script>

