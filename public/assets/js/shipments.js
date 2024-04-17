if (HAS_MODULE_LOGISTIC) {
    var skuAutocompleteConfig = {
        serviceUrl: ROUTE_SEARCH_SKU,
        minChars: 2,
        params: {
            index: $(this).attr('name')
        },
        onSearchStart: function () {
            $target = $(this).closest('tr');
            $target.find('[name="sku[]"],[name="serial_no[]"],[name="lote[]"],[name="product[]"]').val('');
            $target.find('.sku-feedback').hide();
            $target.find('.has-error').removeClass('has-error').css('border-color', '#ccc').css('color', '#555');
        },
        beforeRender: function (container, suggestions) {
            container.find('.autocomplete-suggestion').each(function (key, suggestion, data) {
                var warehouseName = '';
                if (suggestions[key].warehouse != '') {
                    warehouseName = ' - ' + suggestions[key].warehouse;
                }
                var stock = suggestions[key].stock_total;
                var color = stock > 0 ? '' : 'text-red';

                $(this).append('<div class="autocomplete-address ' + color + '">' + suggestions[key].sku + ' - ' + stock + ' unidades ' + warehouseName + '</div>')
            });
        },
        onSelect: function (suggestion) {
            var $target = $(this).closest('tr');
            var qty = parseInt($target.find('[name="qty[]"]').val()); //force to be 1 un

            $target.find('[name="product[]"]').val(suggestion.product);
            $target.find('[name="sku[]"]').val(suggestion.sku);
            $target.find('[name="serial_no[]"]').val(suggestion.serial_no);
            $target.find('[name="lote[]"]').val(suggestion.lote);
            $target.find('[name="stock[]"]').val(suggestion.stock_total);
            $target.find('[name="width[]"]').val(suggestion.width);
            $target.find('[name="height[]"]').val(suggestion.height);
            $target.find('[name="length[]"]').val(suggestion.length);
            $target.find('[name="box_weight[]"]').val(suggestion.weight);
            $target.find('[name="box_type[]"]').val(suggestion.box_type).trigger('change.select2');

            if (suggestion.stock_total <= 0) {
                $target.find('[name="qty[]"]').val(1).trigger('change')
            } else if (qty > parseInt(suggestion.stock_total)) {
                $target.find('[name="qty[]"]').val(1)
            }

            if (suggestion.serial_no != '') {
                $target.find('[name="qty[]"]').prop('disabled', true);
            } else {
                $target.find('[name="qty[]"]').prop('disabled', false);
            }

            if (suggestion.stock_status == 'blocked') {
                $target.find('.sku-feedback')
                    .show()
                    .addClass('text-red')
                    .html('<i class="fas fa-exclamation-triangle"></i> Artigo bloqueado!')
            } else {

                warehouseName = '';
                if (suggestion.warehouse) {
                    warehouseName = ' | ' + suggestion.warehouse;
                }

                html = '<small class="text-green">Ref: ' + suggestion.sku + ' | <b>' + suggestion.stock_total + '</b> Un. Stock' + warehouseName + '</small>';
                if (suggestion.stock_total <= 0) {
                    html = '<small class="text-red">Ref: ' + suggestion.sku + ' | <b>Sem Stock</b>' + warehouseName + '</small>';
                }

                $target.find('.sku-feedback')
                    .show()
                    .removeClass('text-red')
                    .html(html)
            }

            $('.search-sku').autocomplete('hide');
        },
    }

    $(document).on('change', '.modal [name="qty[]"]', function () {
        var $target = $(this).closest('tr');
        var stock = parseInt($target.find('[name="stock[]"]').val());
        var qty = parseInt($(this).val());

        $(this).css('border-color', '#ccc').css('color', '#555').removeClass('has-error');

        if (qty == 0) {
            $(this).css('border-color', 'red').css('color', 'red').addClass('has-error');
        } else if ($target.find('[name="sku[]"]').val() != '') {

            if (stock < qty) {
                $(this).css('border-color', 'red').css('color', 'red').addClass('has-error');
                Growl.error('<i class="fas fa-exclamation-triangle"></i> Stock Máximo: ' + stock)
            }
        }
    })
}

/*==============================================*/
/*============= SEARCH SENDER ==================*/
/*==============================================*/
//SEARCH SENDER
var SEARCH_SENDER_OPTIONS = {

    serviceUrl: ROUTE_SEARCH_RECIPIENT,
    minChars: 2,
    onSearchStart: function () {
        $('.box-sender-content:visible .shid').val('');
    },
    beforeRender: function (container, suggestions) {
        container.find('.autocomplete-suggestion').each(function (key, suggestion, data) {
            $(this).append('<div class="autocomplete-address">' + suggestions[key].address + ' - ' + suggestions[key].zip_code + ' ' + suggestions[key].city + '</div>')
        });
    },
    onSelect: function (suggestion) {
        $('.box-sender-content:visible .shid').val(suggestion.data)
        $('.box-sender-content:visible .shattn').val(suggestion.responsable);
        $('.box-sender-content:visible .shname').val(suggestion.name).trigger('change');
        $('.box-sender-content:visible .shaddr').val(suggestion.address);
        $('.box-sender-content:visible .zip-code').val(suggestion.zip_code);
        $('.box-sender-content:visible .shcity').val(suggestion.city).removeClass('has-error');
        $('.box-sender-content:visible .select2-country').val(suggestion.country).trigger('change');
        $('.box-sender-content:visible .phone').val(suggestion.phone);
        $('.box-sender-content:visible .shvat').val(suggestion.vat);

        if ($('[name="sender_agency_id"]').is(':visible')) {
            $('.box-sender-content:visible [name="sender_agency_id"]').val(suggestion.agency).trigger('change.select2');
        }

        $('.search-sender').autocomplete('hide');
        $('#box-sender .save-checkbox').hide();
        $('#box-sender input[name="save_sender"]').prop('checked', false);

        validatePhone($('.box-sender-content:visible .phone'));
    },
}
$('.search-sender').autocomplete(SEARCH_SENDER_OPTIONS);

//SELECT SEARCH SENDER RESULT
$('.form-shipment [name="sender_name"]').on('change', function () {
    if ($('.form-shipment [name="sender_id"]').val() == '') {
        $('#box-sender .save-checkbox').show();

        if ($('#box-sender input[name="default_save_sender"]').val() == '1') {
            $('#box-sender input[name="save_sender"]').prop('checked', true);
        } else {
            $('#box-sender input[name="save_sender"]').prop('checked', false);
        }

    } else {
        $('#box-sender .save-checkbox').hide();
        $('#box-sender input[name="save_sender"]').prop('checked', false);
    }
})

//CHANGE SENDER FIELDS
$(document).on('change', '#modal-remote-xl [name="sender_address"], #modal-remote-xl [name="sender_zip_code"],#modal-remote-xl [name="sender_city"],#modal-remote-xl [name="sender_country"],#modal-remote-xl [name="sender_phone"]', function () {
    if ($('#box-sender input[name="default_save_sender"]').val() == '1') {
        $('#box-sender input[name="save_sender"]').prop('checked', true);
    } else {
        $('#box-sender input[name="save_sender"]').prop('checked', false);
    }
    $('#box-sender .save-checkbox').show();

    var country = $('#modal-remote-xl [name="sender_country"]').val();
    if (country != "" && !EU_COUNTRIES.includes(country)) {
        $('.incoterms').show();
        $('[name=cod],[name=charge_price]').closest('.form-group').hide()
        $('[name=charge_price]').val('');
        $('[name=cod]').val('');
    } else {
        $('.incoterms').hide();
        $('[name=cod],[name=charge_price]').closest('.form-group').show()
    }
});

/*==============================================*/
/*============= SEARCH RECIPIENT ===============*/
/*==============================================*/
//SEARCH RECIPIENT
var SEARCH_RECIPIENT_OPTIONS = {
    serviceUrl: ROUTE_SEARCH_RECIPIENT,
    minChars: 2,
    onSearchStart: function () {
        $('.box-recipient-content:visible .shid').val('');
    },
    beforeRender: function (container, suggestions) {
        container.find('.autocomplete-suggestion').each(function (key, suggestion, data) {
            $(this).append('<div class="autocomplete-address">' + suggestions[key].address + ' - ' + suggestions[key].zip_code + ' ' + suggestions[key].city + '</div>')
        });
    },
    onSelect: function (suggestion) {

        $('.box-recipient-content:visible .shid').val(suggestion.data)
        $('.box-recipient-content:visible .shattn').val(suggestion.responsable);
        $('.box-recipient-content:visible .shname').val(suggestion.name).trigger('change');
        $('.box-recipient-content:visible .shaddr').val(suggestion.address);
        $('.box-recipient-content:visible .zip-code').val(suggestion.zip_code);
        $('.box-recipient-content:visible .shcity').val(suggestion.city).removeClass('has-error');
        $('.box-recipient-content:visible .select2-country').val(suggestion.country).trigger('change');
        $('.box-recipient-content:visible .phone').val(suggestion.phone);
        $('.box-recipient-content:visible .shvat').val(suggestion.vat);


        if (suggestion.email && $('.box-recipient-content.main-addr').is(':visible')) {
            enableRecipientEmail();
        }
        // else {
        //     disableRecipientEmail();
        // }

        if (suggestion.agency != '' && typeof suggestion.agency !== 'undefined') {
            $('.box-recipient-content:visible [name="recipient_agency_id"]').val(suggestion.agency).trigger('change.select2');
        }

        if (suggestion.obs && $('.modal [name=obs]').val() == '' && $('.box-recipient-content.main-addr').is(':visible')) {
            $('[name=obs]').val(suggestion.obs);
        }

        $('.search-recipient').autocomplete('hide');
        $('#box-recipient .save-checkbox').hide();
        $('#box-recipient input[name="save_recipient"]').prop('checked', false);

        validatePhone($('.box-recipient-content:visible .phone'));
    }
}

$('.search-recipient').autocomplete(SEARCH_RECIPIENT_OPTIONS)

//SELECT SEARCH RECIPIENT RESULT
$('.form-shipment [name="recipient_name"]').on('change', function () {
    if ($('.form-shipment [name="recipient_id"]').val() == '') {
        $('#box-recipient .save-checkbox').show();

        if ($('#box-recipient input[name="default_save_recipient"]').val() == '1') {
            $('#box-recipient input[name="save_recipient"]').prop('checked', true);
        } else {
            $('#box-recipient input[name="save_recipient"]').prop('checked', false);
        }
    } else {
        $('#box-recipient .save-checkbox').hide();
        $('#box-recipient input[name="save_recipient"]').prop('checked', false);
    }
})

//EDIT RECIPIENT FIELDS
$(document).on('change', '#modal-remote-xl [name="recipient_address"], #modal-remote-xl [name="recipient_zip_code"],#modal-remote-xl [name="recipient_city"],#modal-remote-xl [name="recipient_country"],#modal-remote-xl [name="recipient_phone"]', function () {
    if ($('#box-recipient input[name="default_save_recipient"]').val() == '1') {
        $('#box-recipient input[name="save_recipient"]').prop('checked', true);
    } else {
        $('#box-recipient input[name="save_recipient"]').prop('checked', false);
    }

    $('#box-recipient .save-checkbox').show();

    var country = $('#modal-remote-xl [name="recipient_country"]').val();
    if (country != "" && !EU_COUNTRIES.includes(country)) {
        $('.goods-price, .incoterms').show()
        $('[name=cod],[name=charge_price]').closest('.form-group').hide()
        $('[name=charge_price]').val('');
    } else {
        $('.goods-price, .incoterms').hide()
        $('[name=cod],[name=charge_price]').closest('.form-group').show()
    }
});



/*==============================================*/
/*========== TOGGLE SENDER / RECIPIENT =========*/
/*==============================================*/
$('#modal-remote-xl .toggle-sender').on('click',function(){
    var tmpName = $('#modal-remote-xl [name="sender_name"]').val();
    var tmpAddress = $('#modal-remote-xl [name="sender_address"]').val();
    var tmpZipCode = $('#modal-remote-xl [name="sender_zip_code"]').val();
    var tmpCity = $('#modal-remote-xl [name="sender_city"]').val();
    var tmpCountry = $('#modal-remote-xl [name="sender_country"]').val();
    var tmpPhone = $('#modal-remote-xl [name="sender_phone"]').val();
    var tmpAgency = $('#modal-remote-xl [name="sender_agency_id"]').val();
    var tmpVat = $('#modal-remote-xl [name="sender_vat"]').val();
    var tmpAttn = $('#modal-remote-xl [name="sender_attn"]').val();

    $('#modal-remote-xl [name="sender_name"]').val($('#modal-remote-xl [name="recipient_name"]').val());
    $('#modal-remote-xl [name="sender_address"]').val($('#modal-remote-xl [name="recipient_address"]').val());
    $('#modal-remote-xl [name="sender_zip_code"]').val($('#modal-remote-xl [name="recipient_zip_code"]').val());
    $('#modal-remote-xl [name="sender_city"]').val($('#modal-remote-xl [name="recipient_city"]').val()).removeClass('has-error');
    $('#modal-remote-xl [name="sender_country"]').val($('#modal-remote-xl [name="recipient_country"]').val());
    $('#modal-remote-xl [name="sender_phone"]').val($('#modal-remote-xl [name="recipient_phone"]').val());
    $('#modal-remote-xl [name="sender_agency_id"]').val($('#modal-remote-xl [name="recipient_agency_id"]').val());
    $('#modal-remote-xl [name="sender_vat"]').val($('#modal-remote-xl [name="recipient_vat"]').val());
    $('#modal-remote-xl [name="sender_attn"]').val($('#modal-remote-xl [name="recipient_attn"]').val());

    $('#modal-remote-xl [name="recipient_name"]').val(tmpName);
    $('#modal-remote-xl [name="recipient_address"]').val(tmpAddress);
    $('#modal-remote-xl [name="recipient_zip_code"]').val(tmpZipCode);
    $('#modal-remote-xl [name="recipient_city"]').val(tmpCity).removeClass('has-error');
    $('#modal-remote-xl [name="recipient_country"]').val(tmpCountry);
    $('#modal-remote-xl [name="recipient_phone"]').val(tmpPhone);
    $('#modal-remote-xl [name="recipient_email"]').val('');
    $('#modal-remote-xl [name="recipient_agency_id"]').val(tmpAgency);
    $('#modal-remote-xl [name="recipient_vat"]').val(tmpVat);
    $('#modal-remote-xl [name="recipient_attn"]').val(tmpAttn);

    $('#modal-remote-xl [name="sender_country"], #modal-remote-xl [name="recipient_country"]').trigger('change.select2');
    $('#modal-remote-xl [name="sender_agency_id"], #modal-remote-xl [name="recipient_agency_id"]').trigger('change.select2');
})

/*==============================================*/
/*================ ACTIVE EMAIL ================*/
/*==============================================*/
$('[name="active_email"],[name="send_email"]').on('change', function () {
    if($(this).is(':checked')) {
        enableRecipientEmail()
    } else {
        disableRecipientEmail()
    }
})

/*==============================================*/
/*================ CHANGE FIELDS ===============*/
/*==============================================*/
$('#modal-' + STR_HASH_ID + ' [name="services"]').on('change', function(){
    //input services para o caso do cliente não ter opção de escolha dos serviços,
    //o envio não fique sem serviços selecionado
    var serviceId = $(this).val();
    var $this     = $(this).find('option:selected');
    var unity     = $this.data('unity');

    $('.modal [name="service_id"]').val(serviceId);

    $('.input-km, .input-ldm').hide();
    $('.input-ldm input').prop('required', false);
    $('.goods-price input').prop('required', false);

    if(unity == 'km') {
        $('.input-km').show();
        $('.btn-auto-km').trigger('click');
    } else if(unity == 'ldm') {
        $('.input-ldm').show();
        $('.input-ldm input').prop('required', true);
    } else if(unity == 'advalor') {
        $('.input-ldm').hide();
        $('.goods-price').show();
        $('.goods-price input').prop('required', true);
    }

    //CHECK ALLOWS
    var serviceAllows = [
        { fields: ['[name="has_return[]"][value="rpack"]'], data: 'allow-return' },
        { fields: ['[name="charge_price"]'], data: 'allow-cod' },
        { fields: ['[name="without_pickup"]'], data: 'without-pickup' }
    ];

    serviceAllows.forEach(function (obj) {
        obj.fields.forEach(function (field) {
            var $el = $('#modal-' + STR_HASH_ID + ' ' + field)

            if ($this.data(obj.data)) {
                $el.attr('disabled', false);
                if ($el.attr('type') == 'checkbox') {
                    $el.parent().css('text-decoration', 'none');
                }
                return;
            }

            $el.attr('disabled', true);
            if ($el.attr('type') == 'checkbox') {
                $el.parent().css('text-decoration', 'line-through');
                $el.prop('checked', false);
                return;
            }
            
            $el.val('');
        });
    });

    /**
     * Check if service has required email active
     */
    if (!GLOBAL_EMAIL_REQUIRED && $('.modal [name="recipient_email"]').val() == '') {
        $('.modal [name="recipient_email"]').prop('required', false);
        $('.modal [name="recipient_email"]').removeClass('has-error');
        $('.modal [name="send_email"]').show();
        disableRecipientEmail();

        if ($this.data('email-required')) {
            $('.modal [name="recipient_email"]').prop('required', true);
            enableRecipientEmail();
            $('.modal [name="send_email"]').hide();
        }
    }
    /**-- */
    //--

    // Set default service hours
    if (FILL_HOURS) {
        $('.modal [name="start_hour_pickup"]').val($this.data('default-min-hour'));
        $('.modal [name="end_hour_pickup"]').val($this.data('default-max-hour'));
    }

    // if ($this.data('default-hour-from-task')) {
    //     $('.modal [name="start_hour_pickup"]').prop('disabled', true);
    //     $('.modal [name="end_hour_pickup"]').prop('disabled', true);
    // } else {
    //     $('.modal [name="start_hour_pickup"]').prop('disabled', false);
    //     $('.modal [name="end_hour_pickup"]').prop('disabled', false);
    // }
    //--

    /**
     * Filter hours based on service max and min hour
     */
    $('#modal-' + STR_HASH_ID + ' [name="start_hour_pickup"] option, #modal-' + STR_HASH_ID + ' [name="end_hour_pickup"] option').each(function () {
        $(this).prop('disabled', false);
    });
    $('#modal-' + STR_HASH_ID + ' [name="start_hour_pickup"], #modal-' + STR_HASH_ID + ' [name="end_hour_pickup"]').select2(Init.select2());
    
    $('#modal-' + STR_HASH_ID + ' [name="start_hour_pickup"] option, #modal-' + STR_HASH_ID + ' [name="end_hour_pickup"] option').each(function (index, el) {
        var $el = $(el);
        if (!$el.val()) { return };

        if ($el.val() < $this.data('min-hour') || $el.val() > $this.data('max-hour')) {
            $el.prop('disabled', true);
        }
    });
    /**-- */

    /**
     * Filter pack types
     */
    $('.shipment-dimensions [name="box_type[]"] > option').each(function (index, el) {
        var $el = $(el);
        if ($el.val() && !$this.data('pack-types').includes($el.val())) {
            $el.prop('disabled', true);
        } else {
            $el.prop('disabled', false);
        }
    });

    $('.shipment-dimensions [name="box_type[]"]').val("");
    $('.shipment-dimensions [name="box_type[]"]').select2(Init.select2());
    /**-- */

    validateTotalVolumes();
})

$('#modal-' + STR_HASH_ID + ' [name="department_id"]').on('change', function () {
    var $box = $(this).closest('.box-body');
    var isRecipientBlock = $(this).closest('.box-recipient-content').length;

    $box.find('.has-error').remove();

    $('label[for=sender_address] .fa-spin').removeClass('hide');

    $.post(ROUTE_GET_DEPARTMENT, {id: $(this).val()}, function (data) {

        if(isRecipientBlock) {
            $('#modal-remote-xl [name=recipient_name]').val(data.name);
            $('#modal-remote-xl [name=recipient_address]').val(data.address);
            $('#modal-remote-xl [name=recipient_zip_code]').val(data.zip_code);
            $('#modal-remote-xl [name=recipient_city]').val(data.city);
            $('#modal-remote-xl [name=recipient_phone]').val(data.phone);
            $('#modal-remote-xl [name=recipient_city]').val(data.city);
            $('#modal-remote-xl [name=recipient_country]').val(data.country).trigger("change");
        } else {
            $('#modal-remote-xl [name=sender_name]').val(data.name);
            $('#modal-remote-xl [name=sender_address]').val(data.address);
            $('#modal-remote-xl [name=sender_zip_code]').val(data.zip_code);
            $('#modal-remote-xl [name=sender_city]').val(data.city);
            $('#modal-remote-xl [name=sender_phone]').val(data.phone);
            $('#modal-remote-xl [name=sender_city]').val(data.city);
            $('#modal-remote-xl [name=sender_country]').val(data.country).trigger("change");
        }
    }).fail(function () {
        $box.append('<p class="text-red m-b-0 m-t-5 has-error"><i class="fas fa-exclamation-circle"></i> Ocorreu um erro ao obter os dados do departamento.</p>');
    }).always(function () {
        $('label[for=sender_address] i').addClass('hide');
        $('.save-checkbox').hide();
        $('[name="save_recipient"]').prop('checked', false);
        
    })
});

$('#modal-' + STR_HASH_ID + ' [name="is_collection"]').on('change', function () {
    var tmp;
    var changePosition = false;
    var $selectSearchCustomer = $('.select-search-customer');
    var $selectSearchRecipient = $('.select-search-recipient');
    var $senderPlace = $selectSearchCustomer.closest('.input-group');
    var $recipientPlace = $selectSearchRecipient.closest('.input-group');

    if ($(this).val() == 1) { //serviço de recolhas
        if ($('#box-sender .box-sender-content').length > 0) {
//                //troca posição das caixas
            $('#box-recipient').append($('.box-sender-content'));
            $('#box-sender').append($('.box-recipient-content'));
            $('label[for=recipient_id]').html('Remetente');
            $('label[for=customer_id]').html('Destinatário');
        }

        $('.services-shipments').hide().find('select').prop('required', false);
        $('.services-collections').show().find('select').prop('required', true);
        $('.services-shipments, .services-collections').find('select').val('').trigger('change');
        $('.hide-on-collection').hide();
        $('[name=print_guide]').prop('checked', false);
    } else { //serviços normais
        if (!$('#box-sender .box-sender-content').length > 0) {
            $('#box-sender').append($('.box-sender-content'));
            $('#box-recipient').append($('.box-recipient-content'));
            $('label[for=recipient_id]').html('Destinatário');
            $('label[for=customer_id]').html('Remetente');
        }

        $('.services-shipments').show().find('select').prop('required', true);
        $('.services-collections').hide().find('select').prop('required', false);
        $('.services-shipments, .services-collections').find('select').val('').trigger('change');
        $('.hide-on-collection').show();
        $('[name=print_guide]').prop('checked', true);
    }
})

$('#modal-' + STR_HASH_ID + ' [name="weight"]').on('change', function(){
    validateTotalVolumes()
});

$('#modal-' + STR_HASH_ID + ' [name="volumes"]').on('change', function(){
    var volumes = $(this).val();

    $('[name="fator_m3"], [name="volumetric_weight"]').val('');
    $('.helper-volumetric-weight').hide().prev().removeClass('col-sm-5').addClass('col-sm-8');
    $('.fator_m3').closest('small').hide();
    $('.helper-empty-service').hide();

    $tr = $('table.shipment-dimensions tbody tr:first');
    rowCount = $('table.shipment-dimensions tbody tr').length;

    validateTotalVolumes()
})

$('#modal-' + STR_HASH_ID + ' [name="cod"]').on('change', function(){

    var type = $(this).prop('type');
    if(type == 'checkbox') {
        if($(this).is(':checked')) {
            $('.shp-price').hide();
        } else {
            $('.shp-price').show();
        }
    } else {
        if($(this).val() == 'D' || $(this).val() == 'S') {
            $('.shp-price').hide();
        } else {
            $('.shp-price').show();
        }
    }
})




/*==============================================*/
/*================ DIMENSIONS ==================*/
/*==============================================*/
//show dimensions modal
$('[data-target="#modal-shipment-dimensions"]').on('click', function(){
    $('#modal-shipment-dimensions').addClass('in').show();

    var hash = $('.vol:visible').data('hash'); //get current volume field
    hash = typeof hash == 'undefined' ? 'master' : hash;
    $('table.shipment-dimensions tbody tr').hide(); //hide all rows
    $('table.shipment-dimensions tbody tr[data-hash="' + hash + '"]').show() //show only current rows

    $('table.shipment-dimensions tbody tr[data-hash="' + hash + '"]').each(function () {
        var sku = $(this).find('[name="sku[]"]').val();
        if (sku != '') {
            $(this).find('.search-sku').trigger('click')
        }
    })

    if(HAS_MODULE_LOGISTIC) {
        $('#modal-shipment-dimensions .search-sku').autocomplete(skuAutocompleteConfig);
    }
})

//duplica dimensões
$(document).on('click', '#modal-shipment-dimensions .copy-dimensions', function () { //show
    var $tr     = $(this).closest('tr');
    var $nextTr = $tr.next('tr');

    $nextTr.find('td').each(function (item) {
        lastTrVal = $tr.find('td:eq(' + item + ')').find('input, select').val();
        $(this).find('input, select').val(lastTrVal).trigger('change')
    })
})

//pre-preenche dimensoes
$(document).on('change', '#modal-shipment-dimensions [name="box_type[]"]', function (e) {

    var globalType = '';
    var lastType   = '';
    $('#modal-shipment-dimensions').find('[name="box_type[]"]').each(function () {
        var type = $(this).val();

        if (lastType == '') {
            lastType = type;
        }

        if (type != lastType) {
            globalType = 'multiple'
        }
    })

    if (globalType != 'multiple') {
        globalType = lastType;
    }

    $('[name="packaging_type"]').val(globalType)
    var $tr = $(this).closest('tr');

    var width  = $(this).find('option:selected').data('width');
    var lenght = $(this).find('option:selected').data('length');
    var height = $(this).find('option:selected').data('height');
    var weight = $(this).find('option:selected').data('weight');
    var description = $(this).find('option:selected').data('description');

    if (width != '' && typeof width != 'undefined') {
        $tr.find('[name="width[]"]').val(width).trigger('change');
    }

    if (lenght != '' && typeof lenght != 'undefined') {
        $tr.find('[name="length[]"]').val(lenght)
    }

    if (height != '' && typeof height != 'undefined') {
        $tr.find('[name="height[]"]').val(height)
    }

    if (weight != '' && typeof weight != 'undefined') {
        $tr.find('[name="box_weight[]"]').val(weight)
    }

    if (description != '' && typeof description != 'undefined') {
        $tr.find('[name="box_description[]"]').val(description)
    }

    $tr.find('[name="width[]"]').trigger('change');

    $tr.find('.bxtp').removeClass('has-error');
})

//editado peso linha
$(document).on('change', '#modal-shipment-dimensions [name="box_weight[]"]', function (e) {
    calcDimsTotals();
})

//calcula M3 de cada linha
$(document).on('change', '#modal-shipment-dimensions [name="qty[]"], #modal-shipment-dimensions [name="width[]"], #modal-shipment-dimensions [name="height[]"], #modal-shipment-dimensions [name="length[]"]', function () {

    var $tr    = $(this).closest('tr');
    var width  = $tr.find('[name="width[]"]').val();
    var height = $tr.find('[name="height[]"]').val();
    var length = $tr.find('[name="length[]"]').val();
    var volume = calcVolume(width, height, length, VOLUMES_MESURE_UNITY);

    $tr.find('[name="fator_m3_row[]"]').val(volume);

    calcDimsTotals();
})

//add dimension row
$('.modal .btn-new-dim-row').on('click', function(e) {
    e.preventDefault()

    var hash = $(this).data('hash');
    hash = typeof hash == 'undefined' ? 'master' : hash;
    var $tr = $('table.shipment-dimensions tbody tr:first');

    clonedRow = $tr.clone();
    clonedRow.attr('data-hash', hash);
    clonedRow.find('input').val('');
    clonedRow.find('input[name="dim_src[]"]').val(hash == 'master' ? '' : hash);
    clonedRow.find('input[name="qty[]"]').val('1');
    clonedRow.find('span.select2').remove()
    clonedRow.find('.select2').select2(Init.select2());
    clonedRow.find('.sku-feedback, .m3lbl').hide();
    $('table.shipment-dimensions tbody').append(clonedRow);

    $('table.shipment-dimensions tbody tr').hide(); //hide all rows
    $('table.shipment-dimensions tbody tr[data-hash="' + hash + '"]').show() //show only current rows

    if (HAS_MODULE_LOGISTIC) {
        $('#modal-shipment-dimensions .search-sku').autocomplete(skuAutocompleteConfig);
    }
})

//apaga linha
$(document).on('click', '#modal-shipment-dimensions .btn-del-dim-row', function () {
    if($('[name="qty[]"]').length > 1) {
        $(this).closest('tr').remove();
        calcDimsTotals();
    } else {
        Growl.error('Não pode remover esta linha.')
    }
})

//confirma dimensoes
$('.confirm-dimensions').on('click', function(){

    var dimLines = validateDimensionLines();

    var vols = parseInt($('.modal [name="volumes"]').val());
    if (isNaN(vols)) {
        vols = 1;
        $('.modal [name="volumes"]').val(1);
    }
    $('.modal [name="volumes"]').trigger('change');

    if ($('#modal-shipment-dimensions .has-error').length) {
        Growl.error('<i class="fas fa-exclamation-triangle"></i> Corrija os campos a vermelho antes de confirmar.')
    } else if (dimLines > vols) {
        $('#modal-confirm-vols').addClass('in').show().find('.cvol').html(dimLines);
    } else {
        $('#modal-shipment-dimensions').removeClass('in').hide();

        var dims = calcDimsTotals()
        $('.modal-xl [name="fator_m3"]').val(dims.m3);
        $('.modal-xl [name="volumes"]').val(dims.volumes);

        if(dims.weight > 0.00) {
            $('.modal-xl [name="weight"]').val(dims.weight);
        }

        $('.modal-xl [name="weight"]').trigger('change');
    }

})

$('#modal-confirm-vols [data-answer]').on('click', function(){
    if($(this).data('answer') == '1') {
        var dims = calcDimsTotals();
        $('.modal-xl [name="fator_m3"]').val(dims.m3);
        $('.modal-xl [name="volumes"]').val(dims.volumes);

        if(dims.weight > 0.00) {
            $('.modal-xl [name="weight"]').val(dims.weight)
        }

        $('.modal-xl [name="weight"]').trigger('change');
    }
    $(this).closest('.modal').removeClass('in').hide();
    $('#modal-shipment-dimensions').removeClass('in').hide();

    if(APP_SOURCE == 'hunterex') {
        var val;
        var fatorM3 = 0;
        $('#modal-shipment-dimensions [name="fator_m3_row[]"]').each(function(){
            val = $(this).val() == "" ? 0 : $(this).val();
            val = parseFloat(val);
            qty = parseInt($(this).closest('tr').find('[name="qty[]"]').val());
            fatorM3+= (val * qty);
        })

        var weight  = 0;
        $('#modal-shipment-dimensions [name="box_weight[]"]').each(function(){
            val = $(this).val() == "" ? 0 : $(this).val();
            val = parseFloat(val);
            qty = parseInt($(this).closest('tr').find('[name="qty[]"]').val());
            weight+= (val * qty);
        })


        if(weight != 0) {
            weight = weight.toFixed(2);
            $('[name="weight"]').val(weight);
        }

        $('.modal [name="fator_m3"]').val(fatorM3);
        $('.fator_m3').html(fatorM3).closest('small').show();
    }
});

/*==============================================*/
/*================== TAB OBS ===================*/
/*==============================================*/
$('[data-toggle="tabobs"]').on('click', function(e){
    e.preventDefault();

    $('.nav-obs li, .tab-obs').removeClass('active');
    $(this).closest('li').addClass('active');

    $target = $(this).data('target');
    $($target).addClass('active');
})

/*==============================================*/
/*=================== PRICES ===================*/
/*==============================================*/
$('.btn-refresh-prices').on('click', function () {

    var html= '<div class="text-center fs-16 m-t-30 m-b-30">' +
        '<i class="fas fa-spin fa-circle-notch"></i> A calcular preço...' +
        '</div>';

    $('#modal-shipment-price-details').addClass('in').show();
    $('#modal-shipment-price-details .modal-body').html(html)
    $('.modal [name="volumes"]').trigger('change');
})

$('#modal-shipment-price-details .btn-close').on('click', function(e){
    e.preventDefault();
    $('#modal-shipment-price-details').removeClass('in').hide();
})

$('#modal-shipment-price-details .btn-close').on('click', function(e){
    e.preventDefault();
    $('#modal-shipment-price-details').removeClass('in').hide();
})

$('.modal-xl .trigger-price').on('change', function() {

   //console.log('TRIGGER = ' + $(this).attr('name'));

    var triggerField = $(this).attr('name');
    var serviceId    = $('.modal-xl [name="service_id"]').val();

    //if(CUSTOMER_SHOW_PRICES && serviceId) {

        var fields = $('.form-shipment :not(input[name=_method]').serialize();
        fields+='&trigger_field='+triggerField;

        if(triggerField == 'sender_zip_code' ||
            triggerField == 'sender_city' ||
            triggerField == 'sender_country') {
            $('.modal [for="sender_address"] i').removeClass('hide');
        } else if(triggerField == 'recipient_zip_code' ||
            triggerField == 'recipient_city' ||
            triggerField == 'recipient_country') {
            $('.modal [for="recipient_address"] i').removeClass('hide');
        }

        $('.loading-prices').addClass('fa-spin fa-circle-notch').removeClass('fa-info-circle');
        $('.modal [name="waint_ajax"]').val(1);

        $.post(ROUTE_GET_PRICE, fields, function (data) {

            if (data.errors && data.errors.length) {
                Growl.error(data.errors[0]);
            }

            if (data.prices) {
                //faturação
                $('.modal [name="zone"]').val(data.prices.zone);
                $('.modal [name="pickup_zone"]').val(data.prices.pickup_zone);
                $('.modal [name="base_price"]').val(data.prices.base_price);
                $('.modal [name="shipping_price"]').val(data.prices.shipping);
                $('.modal [name="expenses_price"]').val(data.prices.expenses);
                $('.modal [name="expenses_sum"]').val((parseFloat(data.prices.expenses) + parseFloat(data.prices.fuel_price)).toFixed(2))
                $('.modal [name="fuel_tax"]').val(data.prices.fuel_tax);
                $('.modal [name="fuel_price"]').val(data.prices.fuel_price);
                $('.modal [name="extra_price"]').val(data.prices.extra_price);
    
                $('.modal [name="billing_pickup_zone"]').val(data.prices.pickup_zone);
                $('.modal [name="billing_zone"]').val(data.prices.zone);
                $('.modal [name="billing_subtotal"]').val(data.billing.subtotal);
                $('.modal [name="billing_vat"]').val(data.billing.vat);
                $('.modal [name="billing_total"]').val(data.billing.total);
                $('.modal [name="billing_item"]').val(data.billing.billing_item);
                $('.modal [name="vat_rate"]').val(data.billing.vat_rate).trigger('change.select2');
                $('.modal [name="vat_rate_id"]').val(data.billing.vat_rate_id);
    
                //pesos
                $('.modal [name="extra_weight"]').val(data.parcels.extra_weight);
                $('.modal [name="taxable_weight"]').val(data.parcels.taxable_weight);
                $('.modal [name="provider_taxable_weight"]').val(data.parcels.provider_taxable_weight);
                $('.modal [name="volumetric_weight"]').val(data.parcels.volumetric_weight);
                $('.modal [name="fator_m3"]').val(data.parcels.fator_m3);
    
                if(data.parcels.volumetric_weight && data.parcels.volumetric_weight > 0.00) {
                    $('.helper-volumetric-weight').show();
                    $('.helper-volumetric-weight b').html(data.parcels.volumetric_weight);
                    $('.weight-col').removeClass('col-sm-8').addClass('col-sm-5');
                }
    
                //labels html
                $('.modal .billing-subtotal').html(data.billing.subtotal+ data.billing.currency);
                $('.modal .billing-vat').html(data.billing.vat + data.billing.currency);
                $('.modal .billing-vat-rate').html(data.billing.vat_rate);
                $('.modal .billing-total').html(data.billing.total + data.billing.currency);
                $('.modal .fuel-tax').html(data.prices.fuel_tax);
                $('.modal .extra-weight').html(data.parcels.extra_weight)//verificar
    
                if(data.service.provider_id) {
                    $('.modal-xl [name="provider_id"]').prop('disabled', true).val(data.service.provider_id).trigger('change.select2')
                } else {
                    $('.modal-xl [name="provider_id"]').prop('disabled', false);
                }
    
                //tags
                if(data.shipment.tags.length) {
                    $('.modal [name="tags"]').val(data.shipment.tags.join(','));
                } else {
                    $('.modal [name="tags"]').val('');
                }
                
                if (data.field_errors.length) {
                    data.field_errors.forEach(error => {
                        error.fields.forEach(field => {
                            var $field = $('.modal ' + field);
                            if (!$field.hasClass('has-error')) {
                                $field.addClass('has-error');
                            }
    
                            if (error.parent) {
                                var $parent = $(error.parent);
                                if (!$parent.find(field).length && !$parent.find('div:contains("'+ error.feedback +'")').length) {
                                    $parent.append('<div class="dynamic-alert '+ field.replace('.', '') +'"><i class="fas fa-info-circle"></i> ' + error.feedback + '</div>');
                                    $parent.show();
                                }
                            } else {
                                $field.each(function () {
                                    $field.tooltip('destroy');
                                    $field.data('toggle', 'tooltip');
                                    $field.tooltip({
                                        title: error.feedback
                                    });
                                });
                            }
                        });
                    });
                }
            }
            

            //modal details
            $('#modal-shipment-price-details .modal-body').html(data.modal_html)

            //enable all optional fields
            //$('[name*="optional_fields"]').prop('disable', false).trigger('update');
            if(data.zones.blocked) {
                Growl.error('O serviço está indisponível para este fornecedor e este destino.');
                
                $('.modal [name=service_id]').val('').trigger('change.select2'); //reset escolha do serviço para impedir
            } else if(triggerField == 'sender_zip_code' || triggerField == 'sender_country') {
                
                if ($('.modal [name="services"]').val()) {
                    if(!data.agency.service_allowed) {
                        Growl.error('O serviço escolhido está indisponível para este envio.')
                    }
                }

                setAgency(data.agency, 'sender');
                filterServicesList(data.agency.services);
            } else if(triggerField == 'recipient_zip_code' || triggerField == 'recipient_country') {
                if ($('.modal [name="services"]').val()) {
                    if(!data.agency.service_allowed) {
                        Growl.error('O serviço escolhido está indisponível para este envio.')
                    }
                }
                
                setAgency(data.agency, 'recipient')
                filterServicesList(data.agency.services);
            }


        }).fail(function(){
            var html = '<div class="text-red text-center fs-16 m-t-30 m-b-30">' +
                '<i class="fas fa-exclamation-triangle"></i> Erro interno. Não foi possível calcular preços' +
                '</div>'
            $('#modal-shipment-price-details .modal-body').html(html)
        }).always(function () {
            $('.modal [name="waint_ajax"]').val(0);
            $('.modal [name="calc_price"]').val(1); //volta a indicar que pode calcular preços
            $('.btn-refresh-prices i').removeClass('fa-spin');
            $('.modal [for="sender_address"] i, .modal [for="recipient_address"] i').addClass('hide');
            //$('.loading-prices').hide();
            $('.loading-prices').removeClass('fa-spin fa-circle-notch').addClass('fa-info-circle');
        })
    //}
});

/*==============================================*/
/*==================== PUDO ====================*/
/*==============================================*/
//SHOW PUDO
$('#modal-remote-xl [name="pudo_pickup"], #modal-remote-xl  [name="pudo_delivery"]').on('change', function(){
    if($(this).is(':checked')) {
        enablePudoFields($(this));
    } else {
        disablePudoFields($(this));
    }
})

//SELECT PUDO
$('#modal-remote-xl [name="recipient_pudo_id"], #modal-remote-xl  [name="sender_pudo_id"]').on('change', function(){
    var pudoId  = $(this).val();
    var address = $(this).find('option:selected').data('address');
    var zipCode = $(this).find('option:selected').data('zip-code');
    var city    = $(this).find('option:selected').data('city');

    if($(this).attr('name') == 'recipient_pudo_id') {
        $('#modal-remote-xl [name="recipient_address"]').val(address)
        $('#modal-remote-xl [name="recipient_zip_code"]').val(zipCode)
        $('#modal-remote-xl [name="recipient_city"]').val(city)
    } else {
        $('#modal-remote-xl [name="sender_address"]').val(address)
        $('#modal-remote-xl [name="sender_zip_code"]').val(zipCode)
        $('#modal-remote-xl [name="sender_city"]').val(city)
    }
})


/*========================================*/
/*============= STATE FIELDS =============*/
/*========================================*/
$('[name="sender_state"], [name="recipient_state"]').trigger('select2:select')

$('[name="sender_state"], [name="recipient_state"]').on('select2:select', function(){
    var value = $(this).find('option:selected').val();
    if(typeof value != "undefined" && value != "") {
        value = value.toUpperCase();
    }
    $(this).next().find('.select2-selection__rendered').html(value)
})

/*==============================================*/
/*=================== GET KMS ==================*/
/*==============================================*/
$('.modal-xl [name="sender_zip_code"], .modal-xl [name="sender_city"], .modal-xl [name="sender_country"], .modal-xl [name="recipient_zip_code"], .modal-xl [name="recipient_city"], .modal-xl [name="recipient_country"]').on('change', function () {
    getAutoKm();
})

$('.modal-xl .btn-auto-km').on('click', function () {
    if ($('.modal-xl [name="sender_zip_code"]').val() == '' || $('.modal-xl [name="sender_city"]').val() == '' || $('.modal-xl [name="sender_country"]').val() == ''
        || $('.modal-xl [name="recipient_zip_code"]').val() == '' || $('.modal-xl [name="recipient_city"]').val() == '' || $('.modal-xl [name="recipient_country"]').val() == '') {
        Growl.warning('Preencha primeiro as informações do remetente e destinatário.');
    } else {
        getAutoKm();
    }
});

/*========================================*/
/*========= PAGAMENTO AUTOMATICO =========*/
/*========================================*/
/* MOVED TO ACCOUNT.SHIPMENTS.MODALS.PAYMENT */

/*==============================================*/
/*=============== OPTIONAL FIELDS ==============*/
/*==============================================*/
$('#modal-' + STR_HASH_ID).on('change', '[name*="optional_fields"]', function (e) {

    $('#modal-' + STR_HASH_ID+' [name="weight"]').trigger('change');

    /*var $modal    = $('#modal-' + STR_HASH_ID);
    var $this     = $(this);
    var qty       = $this.val();
    var expenseId = $(this).data('id');
    var fields    = $('.form-shipment :not(input[name=_method]').serialize();
    fields+='&expense_id='+expenseId+'&expense_qty='+qty;


    if(expenseId != '') {
        $('.modal [name="waint_ajax"]').val(1);

        $modal.find('.loading-prices').show();

        $.post(ROUTE_GET_EXPENSE_PRICE, fields, function (data) {

            if(data.billing != null) {
                $tr.find('[name="expense_unity[]"]').val(data.expense.unity);

                $tr.find('[name="expense_qty[]"]').val(data.billing.qty);
                $tr.find('[name="expense_price[]"]').val(data.billing.price);
                $tr.find('[name="expense_subtotal[]"]').val(data.billing.subtotal);
                $tr.find('[name="expense_vat[]"]').val(data.billing.vat);
                $tr.find('[name="expense_total[]"]').val(data.billing.total);
                $tr.find('[name="expense_vat_rate[]"]').val(data.billing.vat_rate);
                $tr.find('[name="expense_vat_rate_id[]"]').val(data.billing.vat_rate_id);

                $tr.find('[name="expense_provider_id[]"]').val(data.cost.provider_id);
                $tr.find('[name="expense_cost_price[]"]').val(data.cost.price);
                $tr.find('[name="expense_cost_subtotal[]"]').val(data.cost.subtotal);
                $tr.find('[name="expense_cost_vat[]"]').val(data.cost.vat);
                $tr.find('[name="expense_cost_total[]"]').val(data.cost.total);
                $tr.find('[name="expense_cost_vat_rate[]"]').val(data.cost.vat_rate);
                $tr.find('[name="expense_cost_vat_rate_id[]"]').val(data.cost.vat_rate_id);

                if(data.expense.unity == 'euro') {
                    $tr.find('[name="expense_price[]"]').closest('.input-group-money').find('.input-group-addon').html('€')
                } else {
                    $tr.find('[name="expense_price[]"]').closest('.input-group-money').find('.input-group-addon').html('%')
                }

                updateExpensesTotal();
            } else {
                $tr.find('[name="expense_price[]"]').closest('.input-group-money').find('.input-group-addon').html('€')
            }

        }).always(function () {
            $tr.find('.update-expenses i').removeClass('fa-spin');

            if($tr.find('[name="is_fast_expense"]').length) {
                $('.modal [name="agency_id"]').trigger('change'); //atualiza todos o preço
                $tr.find('[name="is_fast_expense"]').remove();
            }

            $('.modal [name="waint_ajax"]').val(0);
        })
    }*/
})

/*========================================*/
/*===== VALIDAR E SUBMETER FORMULÁRIO ====*/
/*========================================*/
$(".form-shipment [required]").on('change', function(){
    $(this).removeClass('has-error');
    if($(this).is('select')) {
        $(this).next().removeClass('has-error');
    }

    if ($(this).hasClass('dynamic-tooltip')) {
        $(this).tooltip('destroy');
    }
})

$(document).on('change', '[class*="has-error"]', function () {
    $(this).removeClass('has-error');
    $(this).tooltip('destroy');
})

$('.modal [name="services"], .modal [name="date"], .modal .shipment-start-hour, .modal .shipment-end-hour').on('change', function () {
    $('.modal [name="date"]').removeClass('has-error');
    $('.dynamic-alert.shipment-date').remove();

    if ($('.modal .shipment-start-hour').hasClass('has-error')) {
        $('.modal .shipment-start-hour').removeClass('has-error').tooltip('destroy');

        $('.dynamic-alert.shipment-start-hour').remove();
    }

    if ($('.modal .shipment-end-hour').hasClass('has-error')) {
        $('.modal .shipment-end-hour').removeClass('has-error').tooltip('destroy');

        $('.dynamic-alert.shipment-end-hour').remove();
    }

    $('#alert-date').hide();
});


$('.btn-submit').on('click', function (e) {
    if($('.modal [name="waint_ajax"]').val() == 1) {
        Growl.warning('<i class="fas fa-clock"></i> Atualização de dados a decorrer. Aguarde.');
        return false;
    } else if(0) {
        $('#modal-shipment-payment').addClass('in').show();
        return false;
    } else {

        emptyFields = $(".form-shipment [required]").filter(function () {
            return !$(this).val();
        });

        countEmptyFields = emptyFields.length;

        emptyFields.each(function(){

            $(this).addClass('has-error')
            if($(this).is('select')) {
                $(this).next().addClass('has-error')
            }
        })

        if (countEmptyFields) {
            Growl.warning('Preencha os campos a vermelho antes de gravar.')
            return false;
        }
    }
})

$('.form-shipment').on('submit', function(e){
    e.preventDefault();

    var $form           = $(this);
    var $button         = $('button[type=submit],.btn-submit');
    var hasDims         = parseFloat($form.find('[name="fator_m3"]').val())
    var openModalBtn    = $('#open-payment-modal');

    hasDims              = hasDims != '' && hasDims > 0.000001 ? true : false;

    if($(document).find('.has-error').length) {
        Growl.error("<i class='fas fa-exclamation-circle'></i> Corrija os campos a vermelho antes de gravar.");
    } else if($(document).find('[name="services"] option:selected').data('dim-required') == '1' && !hasDims) {
        $('#modal-shipment-dimensions').addClass('in').show(); //abre modal dimensões
        Growl.warning("<i class='fas fa-exclamation-circle'></i> É obrigatório indicar a mercadoria e dimensões antes de gravar.");
    } else {

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function (data) {

            if (data.trkid) {
                $(document).find('[name="trkid"]').remove();
                $('.form-shipment').append('<input type="hidden" name="trkid" value="' + data.trkid + '"/>');
            }

            if(data.result && !data.isPaid) {
                
                modalShipmentPayment.show(data.trkid, data.subtotal, data.vat, data.total, function () {
                    // PAIED
                    $('.modal').hide();
                    $(".modal-backdrop").remove();

                    if (typeof oTable !== "undefined") {
                        oTable.draw(false);
                    }
                }, function () {
                    // CANCEL (later)
                    $('.modal').hide();
                    $(".modal-backdrop").remove();

                    if (typeof oTable !== "undefined") {
                        oTable.draw(false);
                    }
                });

            } else {
                var removeEcommerceOrder = true;
                var ecommerceOrderCode   = $('.form-shipment [name="ecommerce_gateway_order_code"]').val();

                if (data.result && !data.payment) {
                    $('#modal-confirm-payment-error').addClass('in').show();
                    $('#modal-confirm-payment-error').find('span.total').html(data.total.toFixed(2))
                    $('#modal-confirm-payment-error .btn-confirm-ok').on('click', function (e) {
                        if (typeof oTable !== "undefined") {
                            oTable.draw(false); //update datatable without change pagination
                        }
                        Growl.success('Envio gravado com sucesso.');
                        $('#modal-remote-xl').modal('hide');
                    })
                } else if (data.result && !data.syncError) {
                    if (typeof oTable !== "undefined") {
                        oTable.draw(false); //update datatable without change pagination
                    }


                    Growl.success(data.feedback);
                    if (data.printGuide || data.printLabel || data.printCmr) {

                        if (data.printGuide) {
                            if (window.open(data.printGuide, '_blank')) {
                                if ($('#modal-remote-xlg').is(':visible')) {
                                    $('#modal-remote-xlg').modal('hide');
                                } else {
                                    $('#modal-remote-xl').modal('hide');
                                }
                            } else {
                                $('#modal-remote-xl').find('.modal-xl').removeClass('modal-xl').find('.modal-content').html(data.html);
                            }
                        }

                        if (data.printLabel) {
                            if (window.open(data.printLabel, '_blank')) {
                                if ($('#modal-remote-xlg').is(':visible')) {
                                    $('#modal-remote-xlg').modal('hide');
                                } else {
                                    $('#modal-remote-xl').modal('hide');
                                }
                            } else {
                                $('#modal-remote-xl').find('.modal-xl').removeClass('modal-xl').find('.modal-content').html(data.html);
                            }
                        }

                        if (data.printCmr) {
                            if (window.open(data.printCmr, '_blank')) {
                                if ($('#modal-remote-xlg').is(':visible')) {
                                    $('#modal-remote-xlg').modal('hide');
                                } else {
                                    $('#modal-remote-xl').modal('hide');
                                }
                            } else {
                                $('#modal-remote-xl').find('.modal-xl').removeClass('modal-xl').find('.modal-content').html(data.html);
                            }
                        }

                    } else {

                        if ($('#modal-remote-xlg').is(':visible')) {
                            $('#modal-remote-xlg').modal('hide');
                        } else {
                            $('#modal-remote-xl').modal('hide');
                        }
                    }

                } else if (data.syncError) {

                    $('#modal-confirm-sync-error').find('.error-msg').html(data.feedback)
                    $('#modal-confirm-sync-error').find('.error-provider').html($('[name="provider_id"] option:selected').text())
                    $('#modal-confirm-sync-error').addClass('in').show();

                    $('#modal-confirm-sync-error .btn-confirm-no').on('click', function (e) {
                        $('#modal-confirm-sync-error').removeClass('in').hide();
                    })

                    $('#modal-confirm-sync-error .btn-confirm-yes').on('click', function () {
                        if (typeof oTable !== "undefined") {
                            oTable.draw(false); //update datatable without change pagination
                        }
                        Growl.success('Envio gravado com sucesso.');
                        $('#modal-remote-xl').modal('hide');
                    })

                } else {
                    removeEcommerceOrder = false;
                    Growl.error(data.feedback)
                }

                if (removeEcommerceOrder && ecommerceOrderCode && typeof modalEcommerceGatewayOrders == 'object') {
                    modalEcommerceGatewayOrders.remove(ecommerceOrderCode);
                }
            }

            if (data.debug) {
                window.open(data.debug, '_blank')
            }

        }).fail(function () {
            Growl.error500();
        }).always(function () {
            $button.button('reset');
        })
    }


    $('#modal-remote-xl').on('hidden.bs.modal', function(){
        $('.search-sender').autocomplete('dispose')
        $('.search-recipient').autocomplete('dispose')
        $('[name="sender_city"]').autocomplete('dispose')
        $('[name="recipient_city"]').autocomplete('dispose')
    })
});

/*========================================*/
/*============ FUNÇÕES GLOBAIS ===========*/
/*========================================*/
function enablePudoFields($obj) {

    $target = $obj.closest('.box-body')

    $target.find('.pudo-select').show();
    $target.find('.pudo-select select').val('').trigger('change.select2');

    if($target.attr('id') == 'box-sender') {
        $('#modal-remote-xl [name="sender_address"], #modal-remote-xl [name="sender_zip_code"], #modal-remote-xl [name="sender_city"]')
            .val('')
            .prop('readonly', true);
    } else {
        $('#modal-remote-xl [name="recipient_address"], #modal-remote-xl [name="recipient_zip_code"], #modal-remote-xl [name="recipient_city"]')
            .val('')
            .prop('readonly', true);
    }


    $target.find('.pudo-loading').show();

    //obtem os pontos PUDO
    $.post(ROUTE_GET_PUDOS, function (data) {

        if (data.length === 0) {
            $target.find('.pudo-error').html('<span class="text-red">Não existem pontos de entrega disponíveis.</span>').show()
            $target.find('.pudo-select select').html('').select2(Init.select2())
        } else {
            $target.find('.pudo-error').hide();

            $target.find('.pudo-select select').select2("destroy");
            $target.find('.pudo-select select').html(data).select2({
                templateResult: function (data) {
                    return data;
                },
            }).select2(Init.select2());
            //$('[name=recipient_pudo_id]').empty().select2({data:data}).trigger('change');
        }
    }).always(function () {
        $target.find('.pudo-loading').hide();
    })
}

function disablePudoFields($obj) {

    $target = $obj.closest('.box-body')

    $target.find('.pudo-select, .pudo-loading, .pudo-error').hide();
    $target.find('.pudo-select select').val('').trigger('change.select2');


    if($target.attr('id') == 'box-sender') {
        $('#modal-remote-xl [name="sender_address"], #modal-remote-xl [name="sender_zip_code"], #modal-remote-xl [name="sender_city"]')
            .val('')
            .prop('readonly', false);

    } else {
        $('#modal-remote-xl [name="recipient_address"], #modal-remote-xl [name="recipient_zip_code"], #modal-remote-xl [name="recipient_city"]')
            .val('')
            .prop('readonly', false);
    }


    $('.pudo-delivery, .pudo-loading, .pudo-error').hide();
    $('.pudo-delivery select').val('').trigger('change.select2');
    $('#modal-remote-xl [name="recipient_address"], #modal-remote-xl [name="recipient_zip_code"], #modal-remote-xl [name="recipient_city"]')
        .val('')
        .prop('readonly', false);
}


function enableRecipientEmail() {
    $('[name="send_email"]').prop('checked', true);
    $('[name="active_email"]').closest('.checkbox').hide();
    $('.input-group-email').show()
}

function disableRecipientEmail() {
    $('[name="active_email"]').prop('checked', false);
    $('[name="active_email"]').closest('.checkbox').show();
    $('[name="recipient_email"]').val('');
    $('.input-group-email').hide()
}

function showOrHideGoodsPrice() {
    var country = $('[name="recipient_country"]').val();

    $('.goods-price').show();

    if($('.opt-field[data-type="insurance"]').is(':checked') || $('[name="services"] option:selected').data('unity') == 'advalor') {
        $('.goods-price').show();
        $('.goods-price input').prop('required', true);
    } else if(country != "" && !EU_COUNTRIES.includes(country)) {
        $('.goods-price').show();
        $('.goods-price input').prop('required', false);
    } else {
        $('.goods-price').hide();
        $('.goods-price input').prop('required', false).val('');
    }
}

function toggleStateField(objList, defaultState, target) {

    if(Object.keys(objList).length >= 1) {

        $('.modal [name="'+target+'"]').select2('destroy').empty().select2({data:objList}).val(defaultState);
        $('.modal [name="'+target+'"]').prop('required', true).trigger('select2:select')
        $('.select2-selection__rendered').val();

        if(target == 'recipient_state') {
            $('#box-recipient .row-state > div').removeClass('col-sm-12').addClass('col-sm-7');
            $('#box-recipient .row-state .col-sm-5').removeClass('hide');
            $('#box-recipient .col-zip, #box-recipient .col-city, #box-recipient label[for="sender_city"], #box-recipient label[for="sender_phone"], #box-recipient label[for="recipient_city"], #box-recipient label[for="recipient_phone"]').removeClass('ignore');
        } else {
            $('#box-sender .row-state > div').removeClass('col-sm-12').addClass('col-sm-7');
            $('#box-sender .row-state .col-sm-5').removeClass('hide');
            $('#box-sender .col-zip, #box-sender .col-city, #box-sender label[for="sender_city"], #box-sender label[for="sender_phone"], #box-sender label[for="recipient_city"], #box-sender label[for="recipient_phone"]').removeClass('ignore');
        }

    } else {

        $('.modal [name="'+target+'"]').prop('required', false).empty();

        if(target == 'recipient_state') {
            $('#box-recipient .row-state > div').removeClass('col-sm-7').addClass('col-sm-12');
            $('#box-recipient .row-state .col-sm-5').addClass('hide');
            $('#box-recipient .col-zip, #box-recipient .col-city, #box-recipient label[for="sender_city"], #box-recipient label[for="sender_phone"], #box-recipient label[for="recipient_city"], #box-recipient label[for="recipient_phone"]').addClass('ignore');
        } else {
            $('#box-sender .row-state > div').removeClass('col-sm-7').addClass('col-sm-12');
            $('#box-sender .row-state .col-sm-5').addClass('hide');
            $('#box-sender .col-zip, #box-sender .col-city, #box-sender label[for="sender_city"], #box-sender label[for="sender_phone"], #box-sender label[for="recipient_city"], #box-sender label[for="recipient_phone"]').addClass('ignore');
        }
    }
}

function getAutoKm() {

    if (ROUTE_GET_DISTANCE_KM
        && SHIPMENT_CALC_AUTO_KM
        && $('.modal-xl .btn-auto-km').length
        && $('[name=services] option:selected').data('unity') == 'km') { //so calcula automatico se autorizado


        var triangulation = $('.modal-xl [name="km_agency"]:checked').length

        var returnBack = 0; //quando ha retorno nao precisa de duplicar a distancia porque o envio gerado já vai ter a distancia
        if (SHIPMENT_KM_RETURN_BACK) {
            returnBack = 1;
        }

        var agencyZp        = $.trim($('.modal-xl [name="agency_zp"]').val());
        var agencyCity      = $.trim($('.modal-xl [name="agency_city"]').val());

        var originZp        = $.trim($('.modal-xl [name="sender_zip_code"]').val());
        var originCity      = $.trim($('.modal-xl [name="sender_city"]').val());
        var originCountry   = $.trim($('.modal-xl [name="sender_country"]:selected').text())

        var destZp          = $.trim($('.modal-xl [name="recipient_zip_code"]').val());
        var destCity        = $.trim($('.modal-xl [name="recipient_city"]').val());
        var destCountry     = $.trim($('.modal-xl [name="recipient_country"]:selected').text());

        originCountry       = originCountry == '' ? 'Portugal' : originCountry;
        destCountry         = destCountry == '' ? 'Portugal' : destCountry

        var origin          = originZp + ' ' + originCity + ',' + originCountry;
        var destination     = destZp + ' ' + destCity + ',' + destCountry;
        var agency          = agencyZp + ' ' + agencyCity + ',pt';


        if (originZp != '' && originCity != '' && destZp != '' && destCity != '') {

            var $icon = $('.modal-xl .btn-auto-km').find('.fas');
            $icon.addClass('fa-spin');

            $('.modal [name="waint_ajax"]').val(1);

            $.get(ROUTE_GET_DISTANCE_KM, {
                source: APP_SOURCE,
                origin: origin,
                destination: destination,
                agency: agency,
                origin_zp: originZp,
                destination_zp: destZp,
                agency_zp: agencyZp,
                origin_city: originCity,
                destination_city: destCity,
                agency_city: agencyCity,
                origin_country: originCountry,
                destination_country: destCountry,
                agency_country: 'pt',
                triangulation: triangulation,
                return: returnBack
            }, function (data) {
                if (data.result) {
                    distance = parseFloat(data.distance_value);
                    distance = distance.toFixed(2);
                    $('.modal-xl [name="kms"]').val(distance).trigger('change');
                } else {
                    Growl.error('Não foi possível calcular a distância. Verifique os códigos postais e localidades inseridas.')
                }
            }).always(function () {
                $icon.removeClass('fa-spin');
                $('.modal [name="waint_ajax"]').val(0);
            })
        }

    }
}


function filterServicesList(servicesArr) {
 
    if(servicesArr) {
        if($.inArray(parseInt($('.modal [name=services]').val()), servicesArr) === -1) {
            $('[name=services]').val('').trigger('change.select2'); //remove serviço selecionado
        }

        var countEnabled = 0;
        $('.modal [name=services] option').each(function(item){
            if($.inArray(parseInt($(this).val()), servicesArr) !== -1) {
                $(this).prop('disabled', false);
                countEnabled++;
            } else {
                $(this).prop('disabled', 'disabled');
            }
        })

        if(countEnabled == 1) {
            $('.modal [name=services] option:not(:disabled)').prop('selected', true); //seleciona automatico se apenas 1 opção disponível
            $('.modal [name=services]').trigger('change');
        }

    } else {
        $('.modal [name=services] option').each(function(){
            $(this).prop('disabled', false);
        });
    }

    $(document).find('.modal [name=services]').select2(Init.select2());
}

function validateTotalVolumes(){

    var maxValue  = $('.modal-xl [name=services]').find(':selected').data('max');
    var maxWeight = parseFloat($('.modal-xl [name=services]').find(':selected').data('max-weight'));
    var volumes   = $('.modal-xl [name=volumes]').val();
    var weight    = parseFloat($('.modal-xl [name=weight]').val());

    if(volumes > maxValue) {
        $('.helper-max-volumes').show();
        $('.modal-xl [name=volumes]').css('border-color', 'red');
        $('button[type=submit]').prop('disabled', true);
        $('.lbl-total-vol').html(maxValue);
    } else {
        $('.helper-max-volumes').hide();
        $('.modal-xl [name=volumes]').css('border-color', '#dddddd');
        $('button[type=submit]').prop('disabled', false);
    }

    if(weight > maxWeight) {
        $('.helper-max-weight').show();
        $('.modal-xl [name=weight]').css('border-color', 'red');
        $('button[type=submit]').prop('disabled', true);
        $('.lbl-total-kg').html(maxWeight);
    } else {
        $('.helper-max-weight').hide();
        $('.modal-xl [name=weight]').css('border-color', '#dddddd');
        $('button[type=submit]').prop('disabled', false);
    }
}

function calcVolume(width, height, length) {
    var width  = width == "" ? 0 : width;
    var length = length == "" ? 0 : length;
    var height = height == "" ? 0 : height;
    return (parseFloat(width) * parseFloat(height) * parseFloat(length)) / 1000000;
}

function validateDimensionLines() {

    var totalQty = 0;
    $('.shipment-dimensions tbody tr').each(function(){
        var $tr = $(this);
        var qty     = parseInt($tr.find('[name="qty[]"]').val());
        var type    = $tr.find('[name="box_type[]"]').val();
        var desc    = $tr.find('[name="box_description[]"]').val();
        var m3      = $tr.find('[name="fator_m3_row[]"]').val();
        var weight  = $tr.find('[name="box_weight[]"]').val();

        if(type == '' && (desc != '' || m3!='' || weight != '')) {
            $tr.find('.bxtp').addClass('has-error');
            return false;
        } else if(type != '' && (desc != '' || m3!='' || weight != '')) {
            totalQty+= qty
        }
    })
    return totalQty
}

function calcDimsTotals() {

    var val;
    var totalM3  = 0;
    var totalQty = 0;
    var totalWeight = 0;

    $('#modal-shipment-dimensions [name="fator_m3_row[]"]').each(function(){
        val = $(this).val() == "" ? 0 : $(this).val();
        val = parseFloat(val);
        qty = parseInt($(this).closest('tr').find('[name="qty[]"]').val());
        totalM3+= (val * qty);
    })

    $('#modal-shipment-dimensions [name="box_weight[]"]').each(function(){
        var $tr = $(this).closest('tr');

        var qty    = parseInt($tr.find('[name="qty[]"]').val());
        var type   = $tr.find('[name="box_type[]"]').val();
        var desc   = $tr.find('[name="box_type[]"]').val();
        var m3     = $tr.find('[name="fator_m3_row[]"]').val();

        var weight = $(this).val() == "" ? 0 : $(this).val();
        weight = parseFloat(weight);
        totalWeight+= (weight * qty);

        if(type != '' && (weight!='' || desc!='' || m3!='')) {
            totalQty+= qty; //so soma os totais para linhas preenchidas
        }
    })

    var totalQty = 0;
    $('.shipment-dimensions tbody tr').each(function(){
        var $tr = $(this);
        var qty     = parseInt($tr.find('[name="qty[]"]').val());
        var type    = $tr.find('[name="box_type[]"]').val();
        var desc    = $tr.find('[name="box_description[]"]').val();
        var m3      = $tr.find('[name="fator_m3_row[]"]').val();
        var weight  = $tr.find('[name="box_weight[]"]').val();

        if(type != '' && (desc != '' || m3!='' || weight != '')) {
            totalQty+= qty
        }
    })

    if(totalWeight != 0) {
        totalWeight = totalWeight.toFixed(2);
    }

    totalQty = totalQty == 0 ? 1 : totalQty;

    $('.dims-ttl-vols').html(totalQty);
    $('.dims-ttl-weight').html(totalWeight);
    $('.dims-ttl-m3').html(totalM3.toFixed(3));

    return {
        'volumes': totalQty,
        'weight':totalWeight,
        'm3':totalM3
    }
}

function validateZipCode(target){

    $inputZipCode = $('.modal [name="'+target+'_zip_code"]');
    $inputCity    = $('.modal [name="'+target+'_city"]');
    $inputCountry = $('.modal [name="'+target+'_country"]');

    $inputZipCode.removeClass('has-error');
    $inputZipCode.closest('.form-group').removeClass('has-error')
    $inputCity.closest('.form-group').removeClass('has-error');

    if($inputZipCode.val() != '' && $inputCountry.val() != '') {
        //valida o codigo postal
        if (!ZipCode.validate($inputCountry.val(), $inputZipCode.val())) {
            $inputZipCode.addClass('has-error');
            $inputZipCode.closest('.form-group').addClass('has-error');
        }
    }
}

function setAgency(data, target) {

    if (senderZipCodeAutocompleteSelected) {
        senderZipCodeAutocompleteSelected = false;
        $('.modal [name="sender_country"]').trigger('change');
        return;
    }

    if (recipientZipCodeAutocompleteSelected) {
        recipientZipCodeAutocompleteSelected = false;
        $('.modal [name="recipient_country"]').trigger('change');
        return;
    }

    multipleCities = true;
    zipCodeKms  = typeof data.kms === "undefined" || isNaN(data.kms) || data.kms == null ? 0 : parseFloat(data.kms);
    customerKms = parseFloat($('.modal [name="customer_km"]').val());
    customerKms = isNaN(customerKms) ? 0 : customerKms;
    totalKms    = parseFloat(customerKms) + zipCodeKms;

    var $inputZipCode = $('.modal [name="'+target+'_zip_code"]');
    var $inputCity    = $('.modal [name="'+target+'_city"]');
    var $inputCountry = $('.modal [name="'+target+'_country"]');
    var $inputAgency  = $('.modal [name="'+target+'_agency_id"]');
    var $inputKms     = $('.modal [name="kms"]');
    var zipCode       = $inputZipCode.val();

    $inputCity.autocomplete('dispose')
    if (target == 'sender' || (target == 'recipient' && !recipientZipCodeAutocompleteSelected)) {
        $inputCity.val('').closest('.form-group').removeClass('has-error');
    }
    $inputZipCode.removeClass('has-error');
    $inputZipCode.closest('.form-group').removeClass('has-error')
    $inputCountry.val(data.country).trigger("change.select2");
    $inputAgency.html(data.agenciesHtml);

    if(!SHIPMENT_CALC_AUTO_KM) {
        $inputKms.val(totalKms);
    }

    validateZipCode(target);

    if (data.cities.length > 1) {
        multipleCities = true;
        $inputCity.autocomplete({
            lookup: data.cities,
            minChars: 0,
            onSelect: function (suggestion) {
                $inputCity.val(suggestion.data).trigger('change');

                if (suggestion.country != $inputCountry.val()) {
                    $inputCountry.val(suggestion.country).trigger('change');
                }

                $inputCity.closest('.form-group').removeClass('has-error');
                if (!ZipCode.validate($inputCountry.val(), zipCode)) {
                    $inputZipCode.closest('.form-group').addClass('has-error');
                }
            },
        });
    } else if (data.cities.length == 1) {
        $inputCity.val(data.cities[0]['value']).removeClass('has-error');
    } else if(data.cities.length == 0 && (data.country == 'pt' || data.country == 'es')) {
        $inputCity.val('');
        $inputZipCode.addClass('has-error');
        $inputZipCode.closest('.form-group').addClass('has-error')
        //Growl.error('<i class="fas fa-exclamation-circle"></i> Código postal incorreto para o país selecionado.')
    }

    //focus no campo city quando multiplas cidades
    if(multipleCities && $inputCity.val() == '') {
        $inputCity.focus();
    }

    //força o fornecedor para a zona
    if (data.provider_id) {
        $('.modal [name="provider_id"]').val(data.provider_id).trigger("change.select2");
    }

    //força agencia
    if (data.agency_id) {
        $inputAgency.val(data.agency_id).trigger('change.select2');
    } else {
        $inputAgency.val($('.modal [name="agency_id"]').val()).trigger("change.select2");
    }

    //calcula os KM automaticamente
    //getAutoKm();

    toggleStateField(data.states_select, data.state, target + '_state');
}

var senderZipCodeAutocompleteSelected = false;
var recipientZipCodeAutocompleteSelected = false;
if (SEARCH_ZIP_CODE) {
    ZipCode.searchInputAutocomplete(
        '.modal [name="sender_zip_code"]',
        '.modal [name="sender_city"]',
        $('.modal [name="sender_country"]').val(),
        function () {
            senderZipCodeAutocompleteSelected = true;
        });

    $('.modal [name="sender_country"]').on('change', function () {
        ZipCode.searchInputAutocomplete(
            '.modal [name="sender_zip_code"]',
            '.modal [name="sender_city"]',
            $('.modal [name="sender_country"]').val(),
            function () {
                senderZipCodeAutocompleteSelected = true;
            });
    });


    ZipCode.searchInputAutocomplete(
        '.modal [name="recipient_zip_code"]',
        '.modal [name="recipient_city"]',
        $('.modal [name="recipient_country"]').val(),
        function () {
            recipientZipCodeAutocompleteSelected = true;
        });

    $('.modal [name="recipient_country"]').on('change', function () {
        ZipCode.searchInputAutocomplete(
            '.modal [name="recipient_zip_code"]',
            '.modal [name="recipient_city"]',
            $('.modal [name="recipient_country"]').val(),
            function () {
                recipientZipCodeAutocompleteSelected = true;
            });
    });
}