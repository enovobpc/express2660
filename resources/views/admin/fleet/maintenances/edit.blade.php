{{ Form::model($maintenance, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-7">
            <div class="row row-5">
                <div class="col-sm-5">
                    <div class="form-group is-required">
                        {{ Form::label('vehicle_id', __('Viatura')) }}
                        {{ Form::select('vehicle_id', count($vehicles) > 1 ? ['' => ''] + $vehicles : $vehicles, null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('provider_id', __('Fornecedor')) }}
                        {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group is-required">
                        {{ Form::label('date', __('Data')) }}
                        <div class="input-group">
                            {{ Form::text('date', $maintenance->exists ? $maintenance->date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                            <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-12">
                    <div class="form-group is-required">
                        {{ Form::label('title', __('Descrição do Serviço'), ['data-content'=>'']) }}
                        {{ Form::text('title', null, ['class' => 'form-control ucwords autocomplete', 'required', 'placeholder' => __('Ex.: Mudança de óleo e filtros.'), 'maxlength' => 50]) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="row row-5">
                        <div class="col-sm-6">
                            <div class="form-group is-required">
                                {{ Form::label('km', __('Km viatura')) }}
                                <div class="input-group">
                                    {{ Form::text('km', null, ['class' => 'form-control number', 'required']) }}
                                    <span class="input-group-addon">km</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            @if(hasModule('purchase_invoices'))
                                <div class="form-group" data-toggle="tooltip" title="@trans('Este preço é meramente indicativo. Só são contabilizados para análise de custos os valores inseridos no registo de compras.')">
                                    {{ Form::label('total', __('Total sem IVA')) }}
                                    <div class="input-group">
                                        {{ Form::text('total', null, ['class' => 'form-control decimal']) }}
                                        <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="form-group is-required">
                                    {{ Form::label('total', __('Total sem IVA')) }}
                                    <div class="input-group">
                                        {{ Form::text('total', null, ['class' => 'form-control decimal', 'required']) }}
                                        <span class="input-group-addon">{{ Setting::get('app_currency') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('operator_id', __('Operador')) }}
                        {{ Form::select('operator_id', ['' => ''] + $operators, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    @if(hasModule('purchase_invoices') && hasPermission('purchase_invoices'))
                        <div class="form-group">
                            {{ Form::label('assigned_invoice_id', __('Fatura Compra Associada')) }}
                            {{ Form::select('assigned_invoice_id', $maintenance->exists ? [$maintenance->assigned_invoice_id => @$maintenance->invoice->reference] : [], null, ['class' => 'form-control', 'data-placeholder' => '']) }}
                        </div>
                    @else
                        <div  data-toggle="tooltip" title="{{ hasModule('purchase_invoices') ? '' : __('A sua licença não inclui o módulo de faturas de despesas.')}}">
                            <div class="form-group">
                                {{ Form::label('assigned', __('Fatura Compra Associada')) }}
                                {{ Form::select('assigned', [], null, ['class' => 'form-control', 'disabled']) }}
                            </div>
                        </div>
                    @endif
                    {{--<div class="form-group" style="display: {{ ($maintenance->exists && $maintenance->filepath) ?  'none' : 'block' }};" }}>
                        {{ Form::label('name', 'Ficheiro a anexar') }}
                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                            <div class="form-control" data-trigger="fileinput">
                                <i class="fas fa-file fileinput-exists"></i>
                                <span class="fileinput-filename"></span>
                            </div>
                            <span class="input-group-addon btn btn-default btn-file">
                                <span class="fileinput-new">Procurar...</span>
                                <span class="fileinput-exists">Alterar</span>
                                <input type="file" name="file">
                            </span>
                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remover</a>
                        </div>
                    </div>--}}
                </div>
                <div class="col-sm-12">
                    <div class="form-group" style="display: {{ ($maintenance->exists && $maintenance->filepath) ?  'block' : 'none' }};" }}>
                        {{ Form::label('name', __('Ficheiro a anexar')) }}
                        <div>
                            <a href="{{ asset($maintenance->filepath) }}" target="_blank" class="">
                                <i class="fas fa-file"></i> {{ $maintenance->filename }}
                            </a>
                        </div>
                        <button class="btn btn-danger btn-xs m-t-10 btn-delete">
                            <i class="fas fa-trash-alt"></i> @trans('Eliminar o anexo') {{ $maintenance->filename }}
                        </button>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group m-b-0">
                        {{ Form::label('description', __('Observações e Detalhes do serviço'), ['data-content'=>'']) }}
                        {{ Form::textarea('description', null, ['class' => 'form-control', 'maxlength' => 250, 'rows' => 7]) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <a href="{{ route('admin.fleet.parts.index') }}" target="_blank" class="pull-right">
                @trans('Gerir Peças')
            </a>
            {{ Form::label('name', 'Peças associadas') }}
            <div class="checkbox-list-area {{ $partsList->isEmpty() ? 'empty' : '' }}" style="border: 1px solid #ccc; height: 345px; overflow-x: hidden;">
                <div style="position: relative;
                    left: -1px;
                    top: -2px;
                    right: 0;
                    height: 35px;
                    width: 105%;">
                   {{-- <button class="btn btn-sm btn-default" type="button" style="position: absolute;right: 21px; top: 1px;padding: 7px 10px 6px; border-radius: 0 !important;">
                        <i class="fas fa-plus"></i>
                    </button>--}}
                    {{ Form::text('filter_box', null, ['class' => 'form-control search-parts', 'placeholder' => 'Procurar uma peça da lista...']) }}
                   {{-- <div style="position:absolute; z-index: 3">
                    {{ Form::text('part_id') }}
                    {{ Form::text('part_ref') }}
                    {{ Form::text('part_name') }}
                    {{ Form::text('part_brand') }}
                    {{ Form::text('part_stock') }}
                    </div>--}}
                </div>
                <div style="margin-top: -3px;
                    position: relative;
                    z-index: 1;
                    background: #fff;">
                    <table class="table table-hover table-condensed table-parts m-0">
                        <thead>
                            <tr>
                                <th class="bg-gray w-1"></th>
                                <td class="bg-gray">@trans('Artigo')</td>
                                <td class="bg-gray w-1">@trans('Stk')</td>
                                <td class="bg-gray w-50px">@trans('Qtd')</td>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($partsList as $part)
                            <tr>
                                <td><i class="fas fa-times text-red btn-remove-part"></i></td>
                                <td>
                                    {{ @$part->product->name }}<br/>
                                    <small class="italic text-muted">
                                        {{ @$part->product->reference }} - {{ @$part->product->brand->name }}
                                    </small>
                                </td>
                                <td class="text-right">{{ number_format(@$part->product->stock_total + abs($part->qty), 2) }}</td>
                                <td>
                                    {{ Form::hidden('part_id[]', $part->billing_product_id) }}
                                    {{ Form::text('part_qty[]', abs($part->qty), ['class'=>'form-control decimal', 'data-price' => @$part->product->price ?? 0, 'data-stock' => @$part->product->stock_total + abs($part->qty), 'required'])}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{--<div class="checkbox-list-area" style="border: 1px solid #ccc; height: 345px; overflow-x: hidden;">
                <div style="position: relative;
                    left: -1px;
                    top: -2px;
                    right: 0;
                    height: 35px;
                    width: 105%;">
                    {{ Form::text('filter_box', null, ['class' => 'form-control', 'placeholder' => 'Encontrar na lista...']) }}
                </div>
                <div style="margin-top: -11px;
                    position: relative;
                    z-index: 1;
                    background: #fff;">
                    @foreach($partsList as $category => $parts)
                        <div class="filter-category-group clearfix">
                            <div class="filter-divider">
                                {{ $category ? trans('admin/fleet.parts.categories.' . $category) : 'Todas as Categorias' }}
                            </div>
                            @foreach($parts as $part)
                            <div class="col-sm-6" data-filter-text="{{ strtolower($part->name) }} {{ strtolower(removeAccents($part->name)) }}">
                                <div class="checkbox pull-left m-t-3 m-b-0" style="padding-left: 0px">
                                    <label style="padding-left: 0">
                                        @if(in_array($part->id, $selectedParts))
                                            {{ Form::checkbox('parts[]', $part->id, true) }}
                                        @else
                                            {{ Form::checkbox('parts[]', $part->id, false) }}
                                        @endif
                                        {{ $part->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>--}}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary">@trans('Gravar')</button>
</div>
{{ Form::hidden('delete_file') }}
{{ Form::close() }}

<style>
    .filter-divider {
        padding: 5px;
        margin-top: 7px;
        border-top: 1px solid #ccc;
        background: #eee;
        text-transform: uppercase;
        font-weight: bold;
    }

    .checkbox-list-area.empty:before {
        content: 'Sem peças adicionadas';
        position: absolute;
        left: 0;
        right: 0;
        top: 50%;
        text-align: center;
        color: #999;
    }

    .table-parts td {
        vertical-align: middle;
    }

    .table-parts td.has-error input {
        border-color: red;
        color: red;
    }

    .table-parts tr input {
        height: 24px;
        text-align: center;
        margin: -3px 0;
    }
</style>

<script>
    $('.modal .select2').select2(Init.select2());
    $('.modal .datepicker').datepicker(Init.datepicker());

    $(".modal select[name=assigned_invoice_id]").select2({
        minimumInputLength: 2,
        allowClear: true,
        ajax: Init.select2Ajax("{{ route('admin.invoices.purchase.search.invoice') }}")
    });

    $('.autocomplete').autocomplete({
        serviceUrl: '{{ route('admin.fleet.maintenances.search.services') }}',
        /*lookup: countries,*/
        onSelect: function (suggestion) {
            //alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
        }
    });


    $('.btn-delete').on('click', function(e){
        e.preventDefault();
        $(this).closest('.form-group').hide();
        $(this).closest('.form-group').prev().show();
        $('[name="delete_file"]').val(1);
    })

    $('[name="filter_box"]').on('keyup', function(){
        var value = $(this).val().toLowerCase();
        var regex = new RegExp(value + '\\w*\\b');
        $('.filter-category-group').show();
        $('[data-filter-text]').hide().filter(function () {
            return regex.test($(this).data('filter-text'));
        }).show();

        $('.filter-category-group').each(function(){
            if($(this).find('[data-filter-text]:visible').length <= 0) {
                $(this).hide()
            }
        })
    })


    var skuAutocompleteConfig = {

        serviceUrl: "{{ route('admin.fleet.maintenances.search.parts') }}",
        minChars: 2,
        extraParams: {
            index: $(this).attr('name')
        },
        onSearchStart: function () {
           /* $target = $(this).closest('tr');
            $target.find('[name="sku[]"],[name="serial_no[]"],[name="lote[]"],[name="product[]"]').val('');
            $target.find('.sku-feedback').hide();
            $target.find('.has-error').removeClass('has-error').css('border-color', '#ccc').css('color', '#555');*/
        },
        beforeRender: function (container, suggestions) {
            container.find('.autocomplete-suggestion').each(function (key, suggestion, data) {
                var brandName = '';
                if(suggestions[key].brand_name != '') {
                    brandName = ' - ' + suggestions[key].brand_name;
                }
                var stock = suggestions[key].stock_total;
                var ref   = suggestions[key].reference;
                var color = stock > 0 ? '' : 'text-red';

                if(typeof ref !== 'undefined') {
                    $(this).append('<div class="autocomplete-address ' + color + '">' + ref + ' - ' + stock + ' un <b>' + brandName + '</b></div>')
                }

            });
        },
        onSelect: function (suggestion) {
            var $target = $('.modal .checkbox-list-area table tbody');


            if($target.find('[value="'+suggestion.part+'"]').length > 0) {
                Growl.warning('Esta peça já foi adicionada à lista.');
            } else {

                if (suggestion.stock_total > 0) {
                    /* $target.find('[name="part_id"]').val(suggestion.part);
                     $target.find('[name="part_name"]').val(suggestion.name);
                     $target.find('[name="part_brand"]').val(suggestion.brand_name);
                     $target.find('[name="part_ref"]').val(suggestion.reference);
                     $target.find('[name="part_stock"]').val(suggestion.stock_total);*/

                    var html = '<tr>' +
                        '<td><i class="fas fa-times text-red btn-remove-part"></i></td>' +
                        //'<td>' + suggestion.reference + '</td>' +
                        '<td>' + suggestion.name + '<br/><small class="italic text-muted">' + suggestion.reference + ' - ' + suggestion.brand_name + '</small></td>' +
                        '<td class="text-right">' + suggestion.stock_total + '</td>' +
                        '<td>' +
                        '<input type="hidden" name="part_id[]" value="' + suggestion.part + '">' +
                        '<input type="text" name="part_qty[]" value="1" data-stock="' + suggestion.stock_total + '" data-price="'+ suggestion.price +'" required class="form-control decimal">' +
                        '</td>' +
                        '</tr>';

                    $target.prepend(html);
                    $('.checkbox-list-area').removeClass('empty');

                    updatePrice();
                } else {
                    Growl.warning('Não pode adicionar peças sem stock.');
                }
            }

            $('.search-parts').val('');
            $('.search-parts').autocomplete('hide');
        },
    }
    $('.modal .search-parts').autocomplete(skuAutocompleteConfig);

    $('.modal .btn-remove-part').on('click', function(){
        $(this).closest('tr').remove();
        if(!$('.modal .btn-remove-part').length) {
            $('.checkbox-list-area').addClass('empty');
        }
    })

    $('.modal [name="part_qty[]"]').on('change', function() {
        var value = parseFloat($(this).val());
        var stockMax = parseFloat($(this).data('stock'));

        if(value > stockMax) {
            $(this).closest('td').addClass('has-error');
            Growl.error('Stock máximo ' + stockMax)
        } else if(value <= 0) {
            $(this).closest('td').addClass('has-error');
            Growl.error('Stock deve ser superior a 0')
        } else {
            updatePrice();
            $(this).closest('td').removeClass('has-error');
        }
    })

    function updatePrice() {
        // Only update price if the maintenance doesn't have a purchase invoice assigned
        if ($('.modal [name="assigned_invoice_id"]').length) {
            return;
        }

        var total = 0;
        $('.modal [name="part_qty[]"]').each(function (el) {
            var $this = $(this);
            var qty = parseFloat($this.val());
            var price = parseFloat($this.data('price'));
            total += qty * price;
        });

        $('.modal [name="total"]').val(total.toFixed(2));
    }
</script>