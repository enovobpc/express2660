<?php $modalHash = str_random(8) ?>
{{ Form::model($receptionOrder, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">@trans('Fechar')</span>
    </button>
    @if($receptionOrder->allow_edit)
    <h4 class="modal-title">@trans('Confirmar Recepção #'){{ $receptionOrder->code }}</h4>
    @else
    <h4 class="modal-title">@trans('Consultar Recepção #'){{ $receptionOrder->code }}</h4>
    @endif
</div>
<div class="modal-body {{ $modalHash }}">
    <div class="row row-5" style="background: #eee;
    border-bottom: 1px solid #ccc;
    margin: -15px -15px 14px -15px;
    padding: 10px;">
        <div class="col-sm-3">
            <div class="form-group is-required m-0">
                {{ Form::label('reception_order_id', __('Ordem de recepção')) }}
                @if($allowEdit)
                    {{ Form::select('reception_order_id', [@$receptionOrder->id => @$receptionOrder->code], null, ['class' => 'form-control reception-order', 'required', 'data-placeholder' => __('Procurar...')]) }}
                @else
                    {{ Form::text('', @$receptionOrder->code, ['class' => 'form-control', 'readonly']) }}
                @endif
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group is-required m-0">
                {{ Form::label('date', __('Data Recepção')) }}
                <div class="input-group">
                    <div class="input-group-addon" style="{{ $allowEdit ? '' : 'background: transparent' }}">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    @if($allowEdit)
                    {{ Form::text('received_date', $receptionOrder->exists ? $receptionOrder->received_date : date('Y-m-d'), ['class' => 'form-control datepicker', 'required']) }}
                    @else
                    {{ Form::text('', $receptionOrder->received_date, ['class' => 'form-control', 'readonly']) }}
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-4" style="border-left: 1px solid #999; height: 53px;">
            <h5 class="m-0">
                <small>@trans('Cliente')</small><br/>
                <div class="customer-name">
                    @if(@$receptionOrder->customer->name)
                        {{ @$receptionOrder->customer->code }} - {{ @$receptionOrder->customer->name }}
                    @else
                        @trans('N/A')
                    @endif
                </div>
            </h5>
        </div>
    </div>
    <div class="overlay" style="{{ !$receptionOrder->exists && !$receptionOrder->reception_order_id ? 'display:block' : 'display:none' }}">
        <i class="fas fa-search fs-30"></i>
        <h4 class="m-b-15">@trans('Para iniciar, pesquise na  a ordem de saída ou o código da expedição')</h4>
    </div>
    <div class="row">
        <div class="col-sm-5">
            <h4 class="pull-left" style="margin-top: 15px">@trans('Artigos previstos')</h4>
            @if($allowEdit)
            <button type="button" class="btn btn-xs btn-primary pull-right m-t-15 btn-confirm-all">@trans('Confirmar todos artigos') <i class="fas fa-angle-right"></i></button>
            @endif
            <div class="clearfix"></div>
            <div style="height: 300px;border: 1px solid #999;border-radius: 3px;">
                @include('admin.logistic.reception_orders.partials.confirmation_table_products')
            </div>
        </div>
        <div class="col-sm-7">
            @if($allowEdit)
            <div style="
    border: 1px solid #999;
    border-radius: 3px;">
                <div class="row row-0" style="    background: #999;
    padding: 4px;
    border-radius: 2px 2px 0 0;">
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fas fa-barcode fa-fw"></i>
                                {{ Form::checkbox('pick-barcode', false) }}
                            </div>

                            {{ Form::select('location_id', [''=>''] + $locations, null, ['class' => 'form-control select2', 'data-placeholder'=>__('Local. Destino')]) }}
                            {{ Form::text('location_code', null, ['class' => 'form-control hidden', 'placeholder'=>'Local. Destino']) }}
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="form-group m-b-0">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fas fa-barcode"></i>
                                </div>
                                {{ Form::text('product_barcode', null, ['class' => 'form-control', 'disabled', 'placeholder' => __('Código de Barras ou SKU do artigo...')]) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <button type="button" class="btn btn-sm btn-block btn-success btn-add-product">
                            <i class="fas fa-plus"></i> @trans('Add')
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div style="height: 300px; overflow-y: auto" class="table-devolutions-lines">
                            @include('admin.logistic.reception_orders.partials.confirmation_table_received')
                        </div>
                    </div>
                </div>
            </div>
            @else
                <h4 class="pull-left" style="margin-top: 15px">@trans('Artigos recebidos')</h4>
                <div class="clearfix"></div>
                <div style="height: 300px;border: 1px solid #999;border-radius: 3px;">
                    @include('admin.logistic.reception_orders.partials.confirmation_table_received')
                </div>
            @endif
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
    @if($allowEdit)
        <button type="submit" class="btn btn-default" data-loading-text="@trans('A gravar...')">Gravar</button>
        <button type="button" class="btn btn-primary btn-conclude" data-loading-text="@trans('A finalizar...')">@trans('Finalizar')</button>
        {{ Form::hidden('readall', 0) }}
        {{ Form::hidden('conclude', 0) }}
    @endif
</div>

@if($allowEdit)
<div class="modal" id="modal-conclude">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@trans('Concluir Recepção')</h4>
            </div>
            <div class="modal-body">
                <div class="msg-dft-message">
                    <h4 class="m-t-0 m-b-10"><b>@trans('Confirma a finalização da recepção?')</b></h4>
                    <p>@trans('Após concluir, não será possível editar de novo.')</p>
                </div>
                <div class="msg-qty-unsatisfied" style="display: none">
                    <h4 class="m-t-0 m-b-10"><b>@trans('Existem artigos que não foram recebidos na totalidade.')<br/>@trans('Pretende concluir mesmo assim?')</b></h4>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">@trans('Cancelar')</button>
                    <button type="button" class="btn btn-default" data-answer="1">@trans('Concluir')</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
{{ Form::close() }}

<style>
    .overlay{
        position: absolute;
        display: table-cell;
        text-align: center;
        left: 0;
        right: 0;
        bottom: 0;
        top: 74px;
        background: rgba(255,255,255,1);
        z-index: 10;
        color: #777;
        padding-top: 130px;
    }

    .rw-green {
        background: #a8f99f;
    }

    .rw-red {
        background: #fdd0d0;
    }
</style>

@if($allowEdit)
<script>
    var MODAL = ".{{ $modalHash }}"
    $(MODAL + ' .datepicker').datepicker(Init.datepicker());
    $(MODAL + ' .select2').select2(Init.select2());




    $(document).on('change', MODAL + ' [name="reception_order_id"]', function () {
        var receptionOrderId = $(MODAL + ' [name="reception_order_id"]').val()

        if(receptionOrderId == '' || receptionOrderId == null) {
            $('.customer-name, .shipment-trk, .table-products, .table-devolutions').html('');
            $('.overlay').show()
        } else {
            $('#modal-remote-xl .modal-content').html('<div class="text-center p-30 fs-20"><i class="fas fa-spin fa-circle-notch"></i> Aguarde...</div>');
            $.get('{{ route('admin.logistic.reception-orders.create') }}', {order: receptionOrderId}, function (data) {
                if (data.result) {
                    $('#modal-remote-xl .modal-content').html(data.html);
                } else {
                    Growl.error(data.feedback)
                }
            }).fail(function () {
                Growl.error500();
            })
        }
    })

    $(MODAL + ' [name="location_id"], ' + MODAL + ' [name=location_code]').on('change', function() {
        if($(this).val() != '') {
            $(MODAL + ' [name="product_barcode"]').prop('disabled', false).focus()
        } else {
            $(MODAL + ' [name="product_barcode"]').prop('disabled', true)
        }
    })

    $(MODAL + ' [name=location_code]').keypress(function (ev) {
        if (ev.keyCode === 10 || ev.keyCode === 13) {
            ev.preventDefault();
        }
    });

    $(MODAL + ' [name=pick-barcode]').on('change', function () {
        var $this   = $(this);
        var $select = $(MODAL + ' select[name=location_id]').next('.select2-container');
        var $input  = $(MODAL + ' input[name=location_code]');

        if ($this.is(':checked')) {
            $(MODAL + ' [name="product_barcode"]').prop('disabled', !$input.val());
            $input.removeClass('hidden');
            $select.addClass('hidden');
        } else {
            $(MODAL + ' [name="product_barcode"]').prop('disabled', ($(MODAL + ' [name="location_id"]').val() == ''));
            $select.removeClass('hidden');
            $input.addClass('hidden');
        }
    });

    $(MODAL + ' [name="product_barcode"]').on('keydown', function(e){
        if (e.keyCode == 13) {
            e.preventDefault();
            addItem();
        }
    });

    $(document).on('click', MODAL + ' .btn-add-product', function(e){
        addItem();
    });

    $(document).on('click', MODAL + ' .btn-auto-read', function (e) {
        var sku = $(this).data('sku');
        var $location = $(MODAL + ' [name="pick-barcode"]').is(':checked') ? $(MODAL + ' [name="location_code"]') : $(MODAL + ' [name="location_id"]');

        if($location.val().trim() == '') {
            $(this).html('<i class="fas fa-angle-right"></i>')
            Growl.error('Deve selecionar a localização para onde vai mover o artigo.')
        } else {
            $(this).html('<i class="fas fa-spin fa-circle-notch"></i>')
            $('[name="product_barcode"]').val(sku);
            $('.btn-add-product').trigger('click');
        }
    })

    $(document).on('click', MODAL + ' .btn-confirm-all', function(e) {
        $('[name="readall"]').val(1);
        addItem()
    })

    $(document).on('change', MODAL + ' .table-devolutions-lines [name="qty"]', function (e) {
        updateItem($(this));
    })

    $(document).on('change', MODAL + ' .table-devolutions-lines [name="status"]', function (e) {
        updateItem($(this));
    })


    function addItem() {
        var orderId  = $(MODAL + ' [name="reception_order_id"]').val();
        var barcode  = $(MODAL + ' [name="product_barcode"]').val();
        var location = $(MODAL + ' [name="pick-barcode"]').is(':checked') ? $(MODAL + ' [name="location_code"]').val() : $(MODAL + ' [name="location_id"]').val();
        var date     = $(MODAL + ' [name="reception_date"]').val();
        var readall  = $('[name="readall"]').val();
        $('[name="readall"]').val(0) //repoe valor
        readall = readall == '1' ? true : false;

        data = {
            readall: readall,
            orderId: orderId,
            barcode: barcode,
            location: location,
            date: date
        }

        console.log(readall);
        if(barcode != "" || readall) {
            $.post("{{ route('admin.logistic.reception-orders.confirmation.line.store', [$receptionOrder->id]) }}", data, function(data) {

                if(data.result) {
                    $(MODAL + ' .table-devolutions-lines').html(data.html_devolved)
                    $(MODAL + ' .table-products').html(data.html_products)
                    $(MODAL + ' .table-devolutions-lines .select2').select2(Init.select2());
                } else {
                    Growl.error(data.feedback)
                }

            }).fail(function () {
                Growl.error500();
            }).always(function () {
                $(MODAL + ' .btn-auto-read').html('<i class="fas fa-angle-right"></i>')
                $(MODAL + ' [name="product_barcode"]').val('').focus()
                $(MODAL + ' [name="readall"]').val(0);
            })
        }
    }

    function updateItem($obj) {

        var $target  = $obj.closest('tr');
        var url      = $target.data('url');
        var qty      = $target.find('[name="qty"]').val();
        var status   = $target.find('[name="status"]').val();

        data = {
            qty: qty,
            status: status
        }

        if(qty <= 0) {
            Growl.error('A quantidade tem de ser igual ou superior a 1')
        } else {
            $.post(url, data, function(data) {
                if(!data.result) {
                    Growl.error(data.feedback)
                }

                if(data.html_devolved) {
                    $(MODAL + ' .table-devolutions-lines').html(data.html_devolved)
                    $(MODAL + ' .table-products').html(data.html_products)
                    $(MODAL + ' .table-devolutions-lines .select2').select2(Init.select2());
                }

            }).fail(function () {
                Growl.error500();
            }).always(function () {
                $(MODAL + ' [name="product_barcode"]').val('').focus()
            })
        }
    }

    /**
     * Remove devolution item
     */
    function removeItem(thisElement) {

        $.ajax({
            url: thisElement.attr('href'),
            type: 'delete',
            success: function (data) {

                if (data.result) {
                    $(MODAL + ' .table-devolutions-lines').html(data.html_devolved)
                    $(MODAL + ' .table-products').html(data.html_products)
                    $(MODAL + ' .table-devolutions-lines .select2').select2(Init.select2());
                    Growl.success(data.feedback)
                } else {
                    Growl.error(data.feedback);
                }
            }
        }).fail(function () {
            Growl.error500()
        });
    }

    $('#modal-conclude [data-answer]').on('click', function(){
        if($(this).data('answer') == '1') {
            $('.modal [name="conclude"]').val(1);
            $('.form-confirmation').submit();
            $('.btn-conclude').button('loading')
        }
        $(this).closest('.modal').removeClass('in').hide();
    });

    $('.btn-conclude').on('click', function (e) {
        e.preventDefault();
        $('#modal-conclude').addClass('in').show();

        if($('.table-products tr.rw-red').length > 0 || $('.table-products .text-red').length > 0) {
            $('#modal-conclude .msg-qty-unsatisfied').show();
            $('#modal-conclude .msg-dft-message').hide();
        } else {
            $('#modal-conclude .msg-qty-unsatisfied').hide();
            $('#modal-conclude .msg-dft-message').show();
        }
    })

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-confirmation').on('submit', function(e){
        e.preventDefault();

        var $form   = $(this);
        var $button = $form.find('button[type="submit"], .btn-conclude');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function(data){
            if(data.result) {
                oTable.draw(); //update datatable
                Growl.success(data.feedback);
                $('#modal-remote-xl, #modal-remote-lg, #modal-remote').modal('hide');
            } else {
                Growl.error(data.feedback);
            }

        }).fail(function () {
            Growl.error500();
        }).always(function() {
            $button.button('reset');
        })
    });
</script>
@endif

