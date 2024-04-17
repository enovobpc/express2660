<?php 
$currencySymbol = $invoice->currency ? $invoice->currency : Setting::get('app_currency');
$canEdit = $invoice->payment_notes->isEmpty() ? true : false;
?>
{{ Form::open($formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="tabbable-line m-b-15">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-info" data-toggle="tab">
                Detalhes Fatura
            </a>
        </li>
        <li class="{{ @$tab == 'status' ? 'active' : '' }}">
            <a href="#tab-linked" data-toggle="tab">
                Imputar ou Distribuir Despesa
            </a>
        </li>
        @if($invoice->exists)
        <li class="{{ @$tab == 'attachments' ? 'active' : '' }}">
            <a href="#tab-attachments" data-toggle="tab">
                Documentos Anexos
            </a>
        </li>
        @else
            <li class="disabled" style="opacity: 0.4"
                data-toggle="tooltip"
                title="Pode adicionar outros anexos após gravar o documento.">
                <a href="#tab-attachments" >
                    Documentos Anexos
                </a>
            </li>
        @endif
    </ul>
</div>
<div class="modal-body p-t-0 p-b-0 modal-shipment modal-shipment-detail">
    <div class="tab-content m-b-0" style="padding-bottom: 10px;">
        <div class="tab-pane active" id="tab-info">
            @include('admin.invoices.purchases.partials.tabs.details')
        </div>
        <div class="tab-pane" id="tab-linked">
            @include('admin.invoices.purchases.partials.tabs.linked')
        </div>
        @if($invoice->exists)
        <div class="tab-pane" id="tab-attachments">
            @include('admin.invoices.purchases.partials.tabs.attachments')
        </div>
        @endif
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options">
       
        <ul class="list-inline pull-left m-t-5 m-b-0">
            @if($canEdit)
            <li>
                <div class="checkbox m-b-0 m-t-5">
                    <label>
                        {{ Form::checkbox('ignore_stats', '1', $invoice->ignore_stats ? true : false) }}
                        Esta despesa já está contemplada em sistema {!! tip('Assinale esta opção caso esta despesa ja esteja contemplada nos custos do sistema. Esta opção vai evitar que o sistema considere este custo 2x.') !!}
                    </label>
                </div>
            </li>
            @else
            <li>
                <p class="text-blue"><i class="fas fa-info-circle"></i> Este documento não pode ser editado porque tem pagamentos associados.</p>
            </li>
            @endif
        </ul>
        
        <div class="clearfix"></div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    @if($canEdit)
    <button type="submit"
            class="btn btn-primary btn-store-invoice"
            data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A gravar...">
        Gravar
    </button>
    @endif
</div>
{{ Form::hidden('invoice_id', $invoice->id) }}
{{ Form::close() }}
<div class="modal" id="modal-confirm-empty-vat">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Emitir fatura sem NIF</h4>
            </div>
            <div class="modal-body">
                <h4 class="m-0"><span class="empty-nif">Não existe NIF associado.<br/></span>A fatura vai ser emitida como Consumidor Final. Pretende continuar?</h4>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-answer="0">Não</button>
                    <button type="button" class="btn btn-default" data-answer="1">Sim</button>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    tr:hover .billing-remove-row {
        display: block;
    }

    tr .billing-remove-row {
        display: none;
        position: absolute;
        left: 4px;
        padding: 6px 0;
        width: 40px;
        position: absolute;
        z-index: 1;
    }

    tr .billing-remove-row > i {
        cursor: pointer;
    }
</style>

<script>
    $('.modal .datepicker').datepicker(Init.datepicker());
    $('.modal .select2').select2(Init.select2());
    $('.modal [data-toggle="tooltip"]').tooltip();

    @if(!$canEdit)
    $('.modal .form-billing .billing-remove-row').hide();
    $('.modal .form-billing input, .modal .form-billing select, .modal .form-billing textarea').prop('disabled', true);
    @endif
    /**
     * Assign value
     */
    assignValues();

    $('[name="doc_type"]').on('change', function(){
        if($(this).val() == 'provider-invoice-receipt') {
            $('.payment-receipt').show();
        } else {
            $('.payment-receipt').hide();
        }
    })

    function assignValues() {
        $('.assigned-amount').on('change', function(){
            //var docTotal = parseFloat($('.modal [name="total"]').val());
            var docTotal = parseFloat($('.modal [name="subtotal"]').val());
            var totalAssigned = 0;

            $(document).find('.assigned-amount').each(function (row) {
                value = parseFloat($(this).val());
                value = isNaN(value) ? 0 : value;
                totalAssigned+= value;
            })

            if(totalAssigned > docTotal) {
                unsigned = parseFloat($('.total-unsigned').html()).toFixed(2);

                $(this).css('border-color', 'red');
                Growl.error('Valor máximo aceite '  + unsigned + '€')
            } else {
                $(this).css('border-color', '#ccc');

                unsigned = (docTotal - totalAssigned).toFixed(2);
                $('.total-unsigned').html(unsigned)

                if(unsigned > 0) {
                    $('.total-unsigned').closest('h3').removeClass('text-red');
                } else {
                    $('.total-unsigned').closest('h3').addClass('text-red');
                }
            }
        })
    }
    /**
     * Change currency
     */
    $(document).on('change', '[name="currency"]', function(){
        $('.currency').html($(this).val());
    })

    /**
     * Change tax rate
     */
    $(document).on('change', '.modal-xl .tax-rate', function(){
        updateTotals();
    })

    /**
     * Enable final consumer
     */
    $(document).on('change', '[name="final_consumer"]',function(){

        if($(this).is(':checked')) {
            var totalVat   =  parseFloat($('[name=total_month_vat]').val());
            var totalNoVat = parseFloat($('[name=total_month_no_vat]').val());
            $('[name=total_month_vat]').val((totalVat + totalNoVat).toFixed(2));
            $('[name=total_month_no_vat]').val('0.00');
        } else {
            var totalVat =  parseFloat($('[name=total_month_vat]').val());
            var totalNoVat = parseFloat($('[name=total_month_no_vat_saved]').val());
            $('[name=total_month_vat]').val((totalVat - totalNoVat).toFixed(2));
            $('[name=total_month_no_vat]').val(totalNoVat.toFixed(2));
        }

    })

    /**
     * Change qty, price or discount
     */
    $('.input-qty, .input-price, .input-discount').on('change', function() {
        var qty      = parseFloat($(this).closest('tr').find('.input-qty').val());
        var price    = parseFloat($(this).closest('tr').find('.input-price').val());
        var discount = parseFloat($(this).closest('tr').find('.input-discount').val());
        var subtotal = (qty * price);

        subtotal = subtotal - (subtotal * (discount/100));

        $(this).closest('tr').find('.input-subtotal').val(subtotal.toFixed(2));
        $(this).closest('tr').find('.tax-rate').trigger('change');

        updateTotals()
    });

    $(document).on('change', '.input-subtotal, [name="total_discount"], [name="irs_tax"], [name=rounding_value]', function() {
        var value = $(this).val();

        if(value == '') {
            $(this).val('0.00');
        } else {
            $(this).val(parseFloat(value).toFixed(2));
        }

        updateTotals();
    })

    $('.add-billing-address').on('click', function () {
        $('#add-billing-address').toggle();
    })

    function updateTotals() {

        var subtotalDoc    = totalVat = 0;
        var totalDiscount  = 0;
        var roundingValue  = parseFloat($('[name="rounding_value"]').val());
        var totalIRS       = parseFloat($('[name="irs_tax"]').val());
        var globalDiscount = parseFloat($('[name="total_discount"]').val());

        $('.input-subtotal').each(function(){
            var $tr      = $(this).closest('tr');
            var subtotal = parseFloat($(this).val().replace(',', '.'));
            var taxRate  = $tr.find('.tax-rate').val()
            var discount = parseFloat($tr.find('.input-discount').val());

            subtotalDoc+= subtotal;
            totalDiscount+= discount;

            if (taxRate.indexOf('M') > -1) {
            } else {
                vat = subtotal * (taxRate/100)
                totalVat+= vat;
            }
        })


        var subtotalDoc = subtotalDoc - globalDiscount;
        var totalDoc    = (subtotalDoc + totalVat + roundingValue);

        //APLICA IRS
        var totalDoc = ((subtotalDoc - (subtotalDoc * (totalIRS / 100))) + totalVat);


        $('[name="subtotal"]').val(subtotalDoc.toFixed(2))
        $('[name="vat_total"]').val(totalVat.toFixed(2))
        $('[name="total"]').val(totalDoc.toFixed(2))
    }

    $('[name="vat"]').on('change', function(){
        if($(this).val() == '' || $(this).val() == '999999990') {
            $('[name="empty_vat"]').val(1);
        } else {
            $('[name="empty_vat"]').val(0);
            var vat = $('.form-billing [name="vat"]').val();

            //verifica entidade associada ao NIF
            $('[for="vat"]').append('<i class="fas fa-spin fa-circle-notch vat-loading"></i>')
            $.post('{{ route('admin.invoices.search.customers.vat') }}', {vat:vat}, function(data){
                if(data.exists) {
                    $('.form-billing [name="vat"]').closest('.form-group').removeClass('has-error')
                    $('.form-billing [name="billing_code"]').val(data.code);
                    $('.form-billing [name="billing_name"]').val(data.name);
                    $('.form-billing [name="billing_address"]').val(data.address);
                    $('.form-billing [name="billing_city"]').val(data.city);
                    $('.form-billing [name="billing_zip_code"]').val(data.zip_code);
                    $('.form-billing [name="billing_country"]').val(data.country).trigger('change.select2');
                    $('.form-billing [name="agency_id"]').val(data.agency_id).trigger('change');
                    $('.form-billing [name="billing_email"]').val(data.email);
                    if(data.is_particular) {
                        $('.form-billing [name="final_consumer"]').prop('checked', true);
                    } else {
                        $('.form-billing [name="final_consumer"]').prop('checked', false);
                    }

                    Growl.success('O NIF é válido.')
                }
            }).always(function(){
                $('.vat-loading').remove();
            })
        }
    });

    $('#modal-confirm-empty-vat [data-answer]').on('click', function(){
        if($(this).data('answer') == '1') {
            $('[name="empty_vat"]').val(0);
            $('.form-billing').submit();
        }
        $(this).closest('.modal').removeClass('in').hide();
    });

    /*$('#modal-confirm-submit [data-answer]').on('click', function(){
        if($(this).data('answer') == '1') {
            $('[name="submit_confirmed"]').val('1')
            $('.form-billing').submit();
        }
        $(this).closest('.modal').removeClass('in').hide();
    });*/


    /**
     * SEARCH PRODUCT
     * ajax method
     */
    $('.search-product').autocomplete({
        serviceUrl: "{{ route('admin.invoices.sales.search.item', ['is_purchase' => true]) }}",
        onSearchStart: function () {},
        beforeRender: function (container, suggestions) {
            container.find('.autocomplete-suggestion').each(function(key, suggestion, data){
                $(this).prepend('<span class="autocomplete-address">' + suggestions[key].reference + ' - </span>');
                if (suggestions[key].has_stock) {
                    $(this).append(' | ' + suggestions[key].stock_total_html);
                }
            });
        },
        onSelect: function (suggestion) {
            var $this = $(this);
            $this.closest('tr').find('.label-reference').html(suggestion.reference)
            $this.closest('tr').find('.input-reference').val(suggestion.reference)
            $this.closest('tr').find('.input-id').val(suggestion.data)
            $this.val(suggestion.name)
            $this.closest('tr').find('input, .input-group-addon').css('color', '#555')

            if(suggestion.price != '0.00') {
                $this.closest('tr').find('.input-price').val(suggestion.price);
                $this.closest('tr').find('.input-price').trigger('change');
            }
        },
    });

    $(document).on('change', '.search-product', function(){
        var $tr = $(this).closest('tr');

        if($(this).val() == '') {
            $tr.find('.label-reference').html('')
            $tr.find('.input-reference').val('')
            $tr.find('.input-id').val('')
        } else if($tr.find('.input-reference').val() == '') {
            //$tr.find('input, .input-group-addon').css('color', 'red') //colca linhas de faturação a vermelho se não tiverem referencia
        } else {
            $tr.find('input, .input-group-addon').css('color', '#555')
        }
    })

    $('.btn-add-product-row').on('click', function() {
        $(this).prev().show();
        $(this).prev().find('tbody tr:hidden:first').show();
        updateTotals();
    })

    //remove row
    $('.billing-remove-row > i').on('click', function($q){
        var $table = $(this).closest('table');
        var $tr = $(this).closest('tr');
        $tr.find('.input-id').val('');
        $tr.find('.label-reference').html('');
        $tr.find('.input-reference, .search-product').val('');
        $tr.find('.input-price').val(0).trigger('change');
        if($table.find('tbody tr:visible').length >= 2) {
            $tr.css('display', 'none')
        }

        $tr.appendTo($table);
    })

    /**
     * SEARCH PROVIDER
     * ajax method
     */
    var users;
    users = $('.search-provider').autocomplete({
        serviceUrl: '{{ route('admin.invoices.purchase.search.providers') }}',
        minChars: 2,
        onSearchStart: function () {
            $('.form-billing [name="provider_id"]').val('');
            $('.form-billing [name="empty_vat"]').val(1);
        },
        beforeRender: function (container, suggestions) {
            container.find('.autocomplete-suggestion').each(function(key, suggestion, data){
                $(this).append('<div class="autocomplete-address">' + suggestions[key].address + ' - ' + suggestions[key].zip_code + ' ' +  suggestions[key].city + '</div>')
            });
        },
        onSelect: function (suggestion) {
            $('.form-billing [name="billing_code"]').val(suggestion.code);
            $('.form-billing [name="provider_id"]').val(suggestion.data);
            $('.form-billing [name="billing_name"]').val(suggestion.name).trigger('change');
            $('.form-billing [name="billing_address"]').val(suggestion.address);
            $('.form-billing [name="billing_zip_code"]').val(suggestion.zip_code);
            $('.form-billing [name="billing_city"]').val(suggestion.city);
            $('.form-billing [name="billing_country"]').val(suggestion.country).trigger('change.select2');
            $('.form-billing [name="vat"]').val(suggestion.vat);
            $('.form-billing [name="billing_email"]').val(suggestion.email);
            $('.form-billing [name="category_id"]').val(suggestion.category).trigger('change');
            $('.form-billing [name="payment_condition"]').val(suggestion.payment_condition).trigger('change');
            $('.form-billing [name="empty_vat"]').val(0);
            $('.form-billing [name="type_id"]').trigger('change');
        },
    });

    $('.search-provider').on('change', function(){
        if($('.form-billing [name="provider_id"]').val() == '') {
            $('.form-billing [name="billing_code"]').val('{{ @$newProviderCode }}');
            $('.form-billing [name="vat"]').val('');
            $('.form-billing [name="billing_address"]').val('');
            $('.form-billing [name="billing_zip_code"]').val('');
            $('.form-billing [name="billing_city"]').val('');
            $('.form-billing [name="category_id"]').val('').trigger('change.select2');
            $('.form-billing [name="billing_country"]').val('pt').trigger('change.select2');
            $('.form-billing [name="payment_condition"]').val('').trigger('change.select2');
            $('#add-billing-address').show();
        } else {
            $('#add-billing-address').hide();
        }

        $('.ft-nm').html($(this).val());
    })

    $("[name=docdate]").on('change', function(){
        var date    = $(this).val();
        var duedate = new Date(date);
        var days    = $(".modal [name=payment_condition]").val();
        var docType = $(".modal [name=doc_type]").val();

        if(typeof days === "undefined" || days == '' || days == 'sft' || days == 'prt') {
            days = 0;
        } else if (days == 'dbt') {
            days = 30;
        } else {
            days = days.replace("d", "");
            days = parseInt(days);
            if(isNaN(days)) {
                days = 30;
            }
        }

        $("[name=duedate]").prop('readonly', false);
        if(docType == 'provider-invoice-receipt' || docType == 'provider-simplified-invoice') {
            $("[name=duedate]").prop('readonly', true);
            days = 0;
        }

        duedate.setDate(duedate.getDate() + days);

        var dd = duedate.getDate();
        var mm = duedate.getMonth() + 1;
        var y  = duedate.getFullYear();

        duedate = y + '-' + ("0" + mm).slice(-2) + '-' + ("0" + dd).slice(-2);

        $('[name=duedate]').datepicker('remove')
        $("[name=duedate]").val(duedate)

        if(date != '') {
            $('[name=duedate]').datepicker({
                format: 'yyyy-mm-dd',
                language: 'pt',
                todayHighlight: true,
                startDate: date
            });
        }

        $('.ft-dt').html(date);

        if($("[name=received_date]").val() == '') {
            $("[name=payment_until]").val(duedate);
        }
    })


    $("[name=received_date]").on('change', function(){
        var date    = $(this).val();

        if(date != '') {
            var duedate = new Date(date);
            var days = $(".modal [name=payment_condition]").val();

            if (typeof days === "undefined" || days == '' || days == 'sft' || days == 'prt') {
                days = 0;
            } else if(days == 'dbt') {
                days = 30;
            } else {
                days = days.replace("d", "");
                days = parseInt(days);
                if (isNaN(days)) {
                    days = 30;
                }
            }

            $("[name=payment_until]").prop('readonly', false);

            duedate.setDate(duedate.getDate() + days);

            var dd = duedate.getDate();
            var mm = duedate.getMonth() + 1;
            var y = duedate.getFullYear();

            duedate = y + '-' + ("0" + mm).slice(-2) + '-' + ("0" + dd).slice(-2);

            $('[name=payment_until]').datepicker('remove')
            $("[name=payment_until]").val(duedate)

            if (date != '') {
                $('[name=payment_until]').datepicker({
                    format: 'yyyy-mm-dd',
                    language: 'pt',
                    todayHighlight: true,
                    startDate: date
                });
            }
        }
    })

    $("[name=payment_condition]").on('change', function(){
        $("[name=docdate]").trigger('change')
        $("[name=received_date]").trigger('change')
    })


    /**
     * Change type
     */

    $('.modal [name="type_id"]').on('change', function(e){
        getAssignSources()
    })

    $('.modal [name="provider_id"]').on('change', function(e){
        var type = $('.modal [name="type_id"]').find('option:checked').data('target-type');
        if(type == 'Shipment') {
            getAssignSources()
        }
    })

    function getAssignSources() {
        var typeId     = $('.modal [name="type_id"]').find('option:checked').data('target-type');
        var invoiceId  = $('.modal [name="invoice_id"]').val()
        var providerId = $('.modal [name="provider_id"]').val()

        $('[href="#tab-linked"]').hide();
        if(typeId != '') {
            $('[href="#tab-linked"]').show();
        }

        $.post('{{ route('admin.invoices.purchase.assign.source') }}', {type:typeId, invoiceId:invoiceId, providerId:providerId}, function (data) {
            $('.modal #tab-linked').html(data)
            assignValues()
        }).fail(function(){
        })
    }

    $('[href="#tab-linked"]').on('click', function(){
        $('#tab-linked').find('.assigned-amount:first-child').trigger('change')
    })

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-billing').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $form = $(this);
        var form  = $(this)[0];
        var formData = new FormData(form);
        var $btn = $form.find('button[type=submit]');

        var isScheduled = $('[name="is_scheduled"]').length
        var emptyProducts = false;


        if ($('.modal [name="doc_type"]').val() == '') {
            Growl.error('Não selecionou o tipo de documento a emitir.')
        } else if ($('[name="billing_code"]').val() == '' && $('[name="empty_vat"]').val() == '0') {
            Growl.error('O campo "Código" do fornecedor deve estar preenchido.')
        } else if ($('.modal [name="category_id"]').val() == '') {
            Growl.error('O campo "Tipo Fornecedor" deve estar preenchido.')
            $('.modal .add-billing-address').trigger('click')
        } else if (emptyProducts) {
            Growl.error('Não selecionou nenhum artigo a faturar.')
        } else if ($('[name="empty_vat"]').val() == '1') {
            $('.empty-nif').show();
            if ($('[name="vat"]').val() == '999999990') {
                $('.empty-nif').hide();
            }
            $('#modal-confirm-empty-vat').addClass('in').show();
        } else if ($('[name="total_month"]').val() == '0.00' || $('[name="total_month"]').val() == '') {
            Growl.error('Não pode gravar uma despesa sem valor.')
        } else {

            $btn.button('loading');

            $.ajax({
                url: $form.attr('action'),
                data: formData,//$form.serialize(),
                type: 'POST',
                contentType: false,
                processData: false,
                success: function(data) {
                    if(data.result) {
                        if(isScheduled) {
                            oTableScheduled.draw(false); //update datatable
                        } else {
                            try {
                                oTable.draw(false);
                            }catch(err) {}

                            try {
                                oTableInvoices.draw(false);
                            }catch(err) {}
                        }

                        Growl.success(data.feedback);
                        $('#modal-remote-xl').modal('hide');
                        $('.billing-header').html(data.html_header)
                        $('.billing-sidebar').html(data.html_sidebar)

                        //update current account
                        if(data.balanceUpdate) {
                            $.post(data.balanceUpdate, function(data){});
                        }

                    } else {
                        Growl.error(data.feedback);
                    }
                }
            }).fail(function () {
                Growl.error500();
            }).always(function () {
                $btn.button('reset');
            });
        }
    });
</script>

