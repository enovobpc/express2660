<?php
$disabled = $receptionOrder->exists ? '' : 'disabled';
$customerDisabled =  @$receptionOrder->lines->count() > 0 ? 'disabled' : '';
$hash = str_random();
?>
{{ Form::model($receptionOrder, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body modal-{{ $hash }}">
    <div class="row row-5">
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('customer_id', __('Cliente')) }}
                {{ Form::select('customer_id', @$receptionOrder->customer->exists ? [$receptionOrder->customer->id => $receptionOrder->customer->name] : ['' => ''], null, ['class' => 'form-control', 'required', $customerDisabled]) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('document', __('Documento ou Referência')) }}
                {{ Form::text('document', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('status_id', __('Estado')) }}
                {{ Form::select('status_id', ['' => ''] + $status, @$receptionOrder->exists ? null : \App\Models\Logistic\ReceptionOrderStatus::STATUS_REQUESTED, ['class' => 'form-control select2', 'required']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required">
                {{ Form::label('requested_date', __('Previsão Recepção')) }}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    {{ Form::text('requested_date', $receptionOrder->exists ? null : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                </div>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                {{ Form::label('boxs', __('Caixas')) }}
                {{ Form::text('boxs', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                {{ Form::label('pallets', __('Paletes')) }}
                {{ Form::text('pallets', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                {{ Form::label('price', __('Preço')) }}
                {{ Form::text('price', null, ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <h4 class="m-b-10 m-t-0 bold"><i class="fas fa-boxes"></i> @trans('Artigos a receber')</h4>
    <div class="row row-5">
        <div style="padding: 2px 0 5px;
    background: #e0e5e8;
    border: 1px solid #ccc;
    border-radius: 3px;
    margin: 0 5px 0 5px;">
            <div class="col-sm-12">
                <div class="row row-5">
                    <div class="col-sm-9" style="width: 73%">
                        <div class="form-group m-0">
                            {{ Form::label('add_product', __('Artigo a adicionar')) }}
                            {{ Form::select('add_product', [],null, ['class' => 'form-control search-product', 'data-placeholder' => __('Procurar por nome, sku, lote, ...'), $disabled]) }}
                        </div>
                    </div>
                    {{--<div class="col-sm-2" style="width: 20%">
                        <div class="form-group m-0">
                            {{ Form::label('add_location', 'Localização') }}
                            {{ Form::select('add_location', [], null, ['class' => 'form-control add-location select2', $disabled]) }}
                        </div>
                    </div>--}}
                    <div class="col-sm-1" style="width: 10%">
                        <div class="form-group m-0">
                            {{ Form::label('add_qty', __('Qtd Receber')) }}
                            {{ Form::text('add_qty', null, ['class' => 'form-control number', $disabled]) }}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-sm btn-block btn-success btn-add-product m-t-19" {{ $disabled }}
                        data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> Aguarde...">
                            <i class="fas fa-plus"></i> @trans('Adicionar Artigo')
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 table-products">
                @include('admin.logistic.reception_orders.partials.product_table')
            </div>
            <div class="clearfix"></div>
        </div>

    </div>
    <div class="row row-5">

    </div>

    {{--<div class="row">
        <div class="col-sm-6">
            <div class="form-group m-t-10 m-b-0">
                {{ Form::label('obs', 'Observações internas') }}
                {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 12]) }}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group m-t-10 m-b-0">
                {{ Form::label('recipient_name', 'Destinatário') }}
                {{ Form::text('recipient_name', null, ['class' => 'form-control']) }}
            </div>
            <div class="form-group m-t-10 m-b-0">
                {{ Form::label('recipient_address', 'Morada') }}
                {{ Form::text('recipient_address', null, ['class' => 'form-control']) }}
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group m-t-10 m-b-0">
                        {{ Form::label('recipient_zip_code', 'Código Postal') }}
                        {{ Form::text('recipient_zip_code', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group m-t-10 m-b-0">
                        {{ Form::label('recipient_city', 'Localidade') }}
                        {{ Form::text('recipient_city', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group m-t-10 m-b-0">
                        {{ Form::label('recipient_country', 'País') }}
                        {{ Form::select('recipient_country', trans('country'), null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="form-group m-t-10 m-b-0">
                        {{ Form::label('recipient_phone', 'Telefone') }}
                        {{ Form::text('recipient_phone', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>--}}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default btn-close">@trans('Fechar')</button>
    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">@trans('Gravar')</button>
    {{--<button type="button" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">Gravar e Criar Expedição</button>--}}
</div>
{{ Form::hidden('id', $receptionOrder->id) }}
{{ Form::close() }}

<style>
    .select2-results__option .s2-custom-result h4{
        font-weight: 500;
        margin: 0 0 2px;
        font-size: 14px;
    }

    .select2-results__option .s2-custom-result p {
        margin: 0;
        color: #777;
    }

    .select2-results__option--highlighted  p {
        color: #ddd !important;
    }
</style>

<script>
    var MODAL = '.modal-{{ $hash }}';

    $(MODAL + ' .datepicker').datepicker(Init.datepicker());
    $(MODAL + ' .select2').select2(Init.select2());

    $(MODAL + " select[name=customer_id]").select2({
        ajax: {
            url: "{{ route('admin.logistic.reception-orders.search.customer') }}",
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


    function formatResult(result) {
        if (!result.id) return result.text;

        var markup = '<div class="s2-custom-result clearfix">' +
            '<div class="s2-custom-res-title">' + result.text + '</div>' +
            '<p><i class="fas fa-circle '+result.stock_class+'"></i> '+ result.stock +'UN &bull; Ref: ' + result.sku +' ' + result.lote + '</p>' +
            '</div>';

        return $(markup);
    }

    function formatSelection(result) {
        return result.full_name || result.text;
    }


    $(MODAL + ' .search-product').select2({
        ajax: {
            url: "{{ route('admin.logistic.reception-orders.search.product') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                var customerId = $('[name="customer_id"]').val()
                return {
                    q: params.term,
                    customer: customerId
                };
            },
            processResults: function (data) {
                $('.search-product option').remove()

                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        templateResult: formatResult,
        templateSelection: formatSelection
    }).on('select2:select', function (e) {
        /*var data  = e.params.data;

        $('[name=add_qty]').prop('placeholder', '');

        if(data.stock <= 0) {
            Growl.error('Não existe stock disponível neste artigo.');
            $('.search-product option').remove()
            $('[name=add_location] option').remove()
        } else if(data.locations === null) {
            Growl.error('Este artigo não se encontra disponível em armazém.');
            $('.search-product option').remove()
            $('[name=add_location] option').remove()
        } else {
            $(MODAL + ' .search-product option:selected').data('stock', data.stock)
            $('[name=add_location] option').remove()
            $('[name=add_location]').select2({data: data.locations});
            $('[name=add_qty]').prop('placeholder', 'Max ' + data.stock);
            $('[name=add_qty]').data('max', data.stock);
        }*/
    })

    $('.btn-add-product').on('click', function(e) {
        e.preventDefault();

        var receptionOrder = $('.modal [name="id"]').val();
        var customer   = $(MODAL + ' [name="customer_id"]').val();
        var status     = $(MODAL + ' [name="status_id"]').val();
        var date       = $(MODAL + ' [name="date"]').val();
        var document   = $(MODAL + ' [name="document"]').val();
        var product    = $(MODAL + ' [name="add_product"]').val();
        var qty        = parseInt($(MODAL + ' [name="add_qty"]').val());
        var $btn       = $(this);

        var data = {
            id: receptionOrder,
            customer_id: customer,
            status_id: status,
            date: date,
            document: document,
            product_id: product,
            qty: qty
        };

        if(product == '') {
            Growl.error('Deve selecionar um artigo a adicionar.')
        } else if(qty == '' || qty == 0 || isNaN(qty)) {
            Growl.error('Deve indicar a quantidade a receber')
        } else {

            $btn.button('loading');
            $.post('{{ route('admin.logistic.reception-orders.product.add') }}', data, function(data){

                if(data.result) {
                    $(MODAL).closest('form').find('[name="id"]').val(data.id);
                    $(MODAL + ' .table-products').html(data.html)
                    $(MODAL + ' [name="add_product"], [name="add_qty"]').val('').trigger('change');
                    $(MODAL + ' [name="customer_id"]').prop('disabled', true)
                } else {
                    Growl.error(data.feedback)
                }

            }).fail(function(){
                Growl.error500()
            }).always(function(){
                $btn.button('reset');
            })
        }
    });

    $(document).on('click', MODAL + ' .btn-delete-product', function (e) {
        e.preventDefault()
        var $btn = $(this).find('i');
        var $tr  = $(this).closest('tr');
        var url  = $(this).attr('href');

        $btn.removeClass('fa-times').addClass('fa-spin fa-circle-notch');

        $.post(url, function(data){

            if(data.result) {
                $tr.remove();
                Growl.success(data.feedback)
            } else {
                Growl.error(data.feedback)
            }

        }).fail(function(){
            Growl.error500()
        }).always(function(){
            $btn.removeClass('fa-spin fa-circle-notch').addClass('fa-times');
        })
    })

    /**
     * UPDATE
     **/
    $(document).on('change', MODAL + ' [name="product_qty[]"],'+ MODAL + ' [name="product_price[]"]', function (e) {
        e.preventDefault()
        var $target = $(this).closest('tr').find('[name="product_qty[]"]');
        var url  = $target.data('url');
        var qty  = parseInt($target.val());
        var price= $(this).closest('tr').find('[name="product_price[]"]').val();


        if(qty == '' || qty == 0 || isNaN(qty) || qty < 0) {
            Growl.error('A quantidade tem de ser superior a 0');
            $target.val($target.data('qty'));
        } else {
            $.post(url, {qty: qty, price:price}, function (data) {
                if (data.result) {
                    Growl.success(data.feedback)
                    $target.data('qty', qty)
                } else {
                    $target.val(data.qty)
                    Growl.error(data.feedback)
                }

            }).fail(function () {
                Growl.error500()
            }).always(function () {
                //$btn.removeClass('fa-spin fa-circle-notch').addClass('fa-times');
            })
        }
    })

    /**
     * Change Customer
     */
    $(document).on('change', '[name="customer_id"]', function () {
        $('[name="add_product"], [name="add_location"], [name="add_qty"], .btn-add-product').prop('disabled', false);
    });

    $('.btn-close').on('click', function (e) {
        e.preventDefault();
        $('.modal').modal('hide');
        oTable.draw();
    })

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    /*$('.form-product-reception').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw(); //update datatable
                Growl.success(data.feedback)
                $('#modal-remote-xl, #modal-remote-lg, #modal-remote').modal('hide');
            } else {
                Growl.error(data.feedback)
            }

        }).fail(function () {
            Growl.error500()
        }).always(function(){
            $button.button('reset');
        })
    });*/
</script>




