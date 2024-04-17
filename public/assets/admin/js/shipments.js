function initShipmentScripts() {
//var returnTypes = ["return", "rcheck", "rpack", "rguide", "out_hour", "weekend", "night"];

function skuAutocompleteConfig() {
    return {
        serviceUrl: ROUTE_SEARCH_SKU + ($('.modal-xl [name="customer_id"]').val() ? '?customer=' + $('.modal-xl [name="customer_id"]').val() : ''),
        minChars: 2,
        extraParams: {
            customer: $('.modal-xl [name="customer_id"]').val(),
            index: $(this).attr('name')
        },
        onSearchStart: function () {
            //console.log($('.modal-xl select[name="customer_id"]').val());
            $target = $(this).closest('tr');
            $target.find('[name="sku[]"],[name="serial_no[]"],[name="lote[]"],[name="product[]"]').val('');
            $target.find('.sku-feedback').hide();
            $target.find('.has-error').removeClass('has-error').css('border-color', '#ccc').css('color', '#555');
        },
        beforeRender: function (container, suggestions) {
            container.find('.autocomplete-suggestion').each(function (key, suggestion, data) {
                var warehouseName = '';
                if(suggestions[key].warehouse != '') {
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
                if( suggestion.warehouse) {
                    warehouseName = ' | '+ suggestion.warehouse;
                }
    
                html = '<small class="text-green">Ref: ' + suggestion.sku + ' | <b>' + suggestion.stock_total + '</b> Un. Stock'+warehouseName+'</small>';
                if (suggestion.stock_total <= 0) {
                    html = '<small class="text-red">Ref: ' + suggestion.sku + ' | <b>Sem Stock</b>'+warehouseName+'</small>';
                }
                $target.find('.sku-feedback')
                    .show()
                    .removeClass('text-red')
                    .html(html)
            }
    
    
            $('.search-sku').autocomplete('hide');
        },
    }
}

$('.modal .select2').select2(Init.select2());
$('.modal .select2-country').select2(Init.select2Country());
$('.modal .datepicker').datepicker(Init.datepicker());

$('.modal .select2').on('select2:open', function (event) {
    $('.select2-dropdown').css('min-width', $(this).next().css('width'))
})

$('.modal [name="sender_state"], .modal [name="recipient_state"]').trigger('select2:select')


//EVENTS WHEN SHIPMENT ALREADY EXISTS
if (SHIPMENT_EXISTS == 1) {
    $('#modal-' + STR_HASH_ID + ' [name=customer_id]').on('change',function (e) {
        e.preventDefault();
        $('#modal-confirm-change-customer').addClass('in').show();
    });

    $('#modal-confirm-change-customer .btn-confirm-no').on('click', function (e) {
        $('#modal-confirm-change-customer').removeClass('in').hide();
    })

    $('#modal-confirm-change-customer .btn-confirm-yes').on('click', function () {
        $('#modal-confirm-change-customer').removeClass('in').hide();
        var $this = $('[name=customer_id]');
        updateCustomer($this, true);
        getAutoKm()
    })
}

/*==============================================*/
/*================= MODAL TABS =================*/
/*==============================================*/
$('.form-shipment').on('click', '#modal-' + STR_HASH_ID + ' [data-toggle="shptab"]', function(e){
    e.preventDefault();

    var lastOpenedTab = $('.form-shipment .nav-tabs li.active a').attr('href');
    var tabToOpen     = $(this).attr('href');

    openShpTab(tabToOpen); //abre nova tab

    $('#modal-' + STR_HASH_ID + ' .btn-submit').prop('disabled', false).show();
    $('#modal-' + STR_HASH_ID + ' .btn-save-submit').prop('disabled', false).show();
    
    $('#modal-' + STR_HASH_ID + ' .btn-confirm-dimensions').hide()
    if(tabToOpen == '#tab-shp-goods') {
        $('#modal-' + STR_HASH_ID + ' .btn-submit').prop('disabled', true).hide();
        $('#modal-' + STR_HASH_ID + ' .btn-save-submit').prop('disabled', true).hide();
        $('#modal-' + STR_HASH_ID + ' .btn-confirm-dimensions').show()
    }

    if(lastOpenedTab == '#tab-shp-goods') {
        $('.confirm-dimensions').trigger('click');
        $('#modal-confirm-vols [data-answer]').attr('data-next-tab', tabToOpen);
        $('#modal-' + STR_HASH_ID + ' [name="volumes"]').trigger('change'); //força atualização de preços
    }
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
/*=============== UPDATE CUSTOMER ==============*/
/*==============================================*/
//SEARCH CUSTOMER
$(".modal select[name=customer_id]").select2({
    ajax: {
        url: ROUTE_SEARCH_CUSTOMER,
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

//SELECT SEARCH RESULT
$('[name=customer_id], [name=department_id]').on('select2:select', function (e) {
    var data = e.params.data;
    var $this = $(this);

    if ($('[name=department_id]').val() == '') {
        $this = $('[name=customer_id]');
    }

    if (data.blocked > 0) {
        $('[name=customer_id], [name=department_id]').val('').trigger('change.select2');
        $('#modal-customer-blocked').addClass('in').show();
        if (data.blocked_reason == 'credit') {
            $('#modal-customer-blocked').find('.blocked-credit').show();
            $('#modal-customer-blocked').find('.blocked-days').hide();
        } else {
            $('#modal-customer-blocked').find('.blocked-credit').hide();
            $('#modal-customer-blocked').find('.blocked-days').show();
            $('#modal-customer-blocked').find('.limitdays').html(data.blocked);
        }
    } else {

        if ((data.payment != null && data.payment != '') || data.unpaid_invoices != '') {
            $('#modal-customer-payment-condition').addClass('in').show();

            if(data.payment != null && data.payment != '') {
                $('#modal-customer-payment-condition').find('.paymenttext').html(data.payment);
                $('.punpdinvc,.unpdinvces').hide();
                $('.pcndt,.hpgto').show()
            }

            if (data.unpaid_invoices != '') {
                $('#modal-customer-payment-condition').find('.unpdinvces').html(data.unpaid_invoices);
                $('.punpdinvc, .unpdinvces').show();
                $('.pcndt').hide();
            }
        }

        if (data.unpaid_invoices != '') {

        }

        $this.find('option:selected')
            .attr('data-name', data.name)
            .attr('data-vat', data.vat)
            .attr('data-address', data.address)
            .attr('data-zip_code', data.zip_code)
            .attr('data-city', data.city)
            .attr('data-country', data.country)
            .attr('data-phone', data.phone)
            .attr('data-agency', data.agency)
            .attr('data-obs', data.obs)
            .attr('data-departments', data.departments)
            .attr('data-kms', data.kms);

        if (data.km_from_agency) {
            $('.modal-xl [name="km_agency"]').prop('checked', true);
        } else {
            $('.modal-xl [name="km_agency"]').prop('checked', false);
        }

        $('[name=agency_zp]').val(data.origin_zp);
        $('[name=agency_city]').val(data.origin_city);


        if ($this.attr('name') != 'department_id') {
            if (data.departments !== null) {
                $('.select-customer').addClass('has-department');
                $('.select-department').removeClass('hide');
                $('[name=department_id]').select2({ data: data.departments });
            } else {
                $('.select-department').addClass('hide');
                $('.select-customer').removeClass('has-department');
                $('[name=department_id]').val('');
            }
        }

        if (SHIPMENT_EXISTS == 0) {
            updateCustomer($this, true)
        } else {
            updateCustomer($this, false)
        }
    }
})

$('#modal-customer-blocked .btn-confirm-no').on('click', function () {
    $('#modal-customer-blocked').removeClass('in').hide();
})

$('#modal-customer-payment-condition .btn-confirm-no').on('click', function () {
    $('#modal-customer-payment-condition').removeClass('in').hide();
})

/*==============================================*/
/*=============== CREATE CUSTOMER ==============*/
/*==============================================*/
$('[data-target="#modal-create-customer"]').on('click', function () { //show
    $('#modal-create-customer').addClass('in').show();
});

$('#modal-create-customer .cancel-create-customer').on('click', function () { //hide
    resetModalCreateCustomer();
    $('#modal-create-customer').removeClass('in').hide();
});

$('#modal-create-customer .confirm-create-customer').on('click', function () {

    var $form    = $(this).closest('form');
    var formData = $form.serialize();
    var $btn     = $(this);

    countEmptyFields = $("#modal-create-customer [required]").filter(function(){
        return !$(this).val();
    }).length;


    if(countEmptyFields) {
        Growl.error('Preencha todos os campos obrigatórios.');
    } else {
        $btn.button('loading');

        $.post($form.attr('action'), formData, function (data) {
            if (data.result) {

                $('.form-shipment [name="sender_name"]').val(data.customer.name);
                $('.form-shipment [name="sender_address"]').val(data.customer.address);
                $('.form-shipment [name="sender_zip_code"]').val(data.customer.zip_code);
                $('.form-shipment [name="sender_city"]').val(data.customer.city);
                $('.form-shipment [name="sender_state"]').val(data.customer.state);
                $('.form-shipment [name="sender_country"]').val(data.customer.country);
                $('.form-shipment [name="sender_phone"]').val(data.customer.mobile);
                $('.form-shipment [name="sender_vat"]').val(data.customer.vat);
                $('.form-shipment [name="sender_agency_id"]').val(data.customer.agency_id).trigger('change.select2');

                $('.form-shipment [name="customer_id"]').val(data.customer.id);
                $('.form-shipment [name="customer_id"]').html('<option value="'+data.customer.id+'">'+data.customer.code+' - '+data.customer.name+'</option>');

                resetModalCreateCustomer();
                $('#modal-create-customer').removeClass('in').hide();
            } else {
                Growl.error(data.feedback)
            }

        }).always(function () {
            $btn.button('reset');
        }).error(function () {
            Growl.error500();
        })
    }
});

/*==============================================*/
/*================== SCHEDULE ==================*/
/*==============================================*/
$('[name="schedule_frequency"]').on('change', function () {
    var frequency = $(this).val();

    if (frequency == 'day') {
        $('.schedule-repeat').hide();
        $('.schedule-weekdays').hide();
        $('.schedule-month-days').hide();
    } else if (frequency == 'week') {
        $('.schedule-repeat').hide();
        $('.schedule-weekdays').show();
        $('.schedule-month-days').hide();
    } else if (frequency == 'month') {
        $('.schedule-repeat').show();
        $('.schedule-weekdays').hide();
        $('.schedule-month-days').show();
    } else if (frequency == 'year') {

    }
})

$('[name="schedule_repeat"]').on('change', function () {
    var repeat = $(this).val();

    if (repeat == 'day') {
        $('.schedule-weekdays').hide();
        $('.schedule-month-days').show();
    } else {
        $('.schedule-weekdays').show();
        $('.schedule-month-days').hide().find('input').val('');
    }
})

$('[name="schedule_end_time"]').on('change', function () {
    var type = $(this).val();

    if (type == 'date') {
        $('[name="schedule_end_date"]').val('').prop('required', true).closest('.input-group').show();
        $('[name="schedule_end_repetitions"]').val('').prop('required', false).closest('.input-group').hide();
    } else {
        $('[name="schedule_end_date"]').val('').prop('required', false).closest('.input-group').hide();
        $('[name="schedule_end_repetitions"]').val('').prop('required', true).closest('.input-group').show();
    }
})


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

        if ($('[name="sender_agency_id"]').is(':visible')) {
            $('.box-sender-content:visible [name="sender_agency_id"]').val(suggestion.agency).trigger('change.select2');
        }

        $('.box-sender-content:visible .shattn').val(suggestion.responsable);
        $('.box-sender-content:visible .shname').val(suggestion.name).trigger('change');
        $('.box-sender-content:visible .shaddr').val(suggestion.address);
        $('.box-sender-content:visible .zip-code').val(suggestion.zip_code);
        $('.box-sender-content:visible .shcity').val(suggestion.city).removeClass('has-error');
        $('.box-sender-content:visible .select2-country').val(suggestion.country).trigger('change');
        $('.box-sender-content:visible .phone').val(suggestion.phone);
        $('.box-sender-content:visible .shvat').val(suggestion.vat);

        if(suggestion.obs && $('.modal [name=obs]').val() == '') {
            $('[name=obs]').val(suggestion.obs);
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

    updateShipperName();
})

//CHANGE SENDER FIELDS
$(document).on('change', '#modal-remote-xl [name="sender_address"], #modal-remote-xl [name="sender_zip_code"],#modal-remote-xl [name="sender_city"],#modal-remote-xl [name="sender_country"],#modal-remote-xl [name="sender_phone"]', function () {
    $('#box-sender input[name="save_sender"]').prop('checked', true);
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
        } else {
            disableRecipientEmail();
        }

        if (suggestion.agency != '' && typeof suggestion.agency !== 'undefined') {
            $('.box-recipient-content:visible [name="recipient_agency_id"]').val(suggestion.agency).trigger('change.select2');
        }

        if (suggestion.obs && $('.modal [name=obs]').val() == '' && $('.box-recipient-content.main-addr').is(':visible')) {
            if($('[name=obs_delivery]').lenght) {
                $('[name=obs_delivery]').val(suggestion.obs);
            } else {
                $('[name=obs]').val(suggestion.obs);
            }
        }

        $('.search-recipient').autocomplete('hide');
        $('#box-recipient .save-checkbox').hide();
        $('#box-recipient input[name="save_recipient"]').prop('checked', false);

        validatePhone($('.box-recipient-content:visible .phone'));

        //valida zip code para moradas adicionais
        /*if(!$('.box-recipient-content:visible .main-addr').is(':visible')) {
            var country = $('.box-recipient-content:visible .select2-country').val();
            var zipCode = $('.box-recipient-content:visible .zip-code').val();
            validateZipCode(zipCode, country)
        }*/
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

    updateReceiverName();
})

//EDIT RECIPIENT FIELDS
$(document).on('change', '#modal-remote-xl [name="recipient_address"], #modal-remote-xl [name="recipient_zip_code"],#modal-remote-xl [name="recipient_city"],#modal-remote-xl [name="recipient_country"],#modal-remote-xl [name="recipient_phone"]', function () {

    $('#box-recipient input[name="save_recipient"]').prop('checked', true);
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
/*=========== EDIT SHIPPER / RECEIVER ==========*/
/*==============================================*/
$('.btn-confirm-shipper').on('click', function () {
    updateShipperName();
    updateReceiverName();
});

/*==============================================*/
/*========== TOGGLE SENDER / RECIPIENT =========*/
/*==============================================*/
$('.toggle-sender').on('click', function () {
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

    $('.btn-refresh-prices').trigger('click');
});


/*==============================================*/
/*=========== RESET SENDER / RECIPIENT =========*/
/*==============================================*/
$('.reset-sender').on('click', function () {
    resetSender();
});

$('.reset-recipient').on('click', function () {
    resetRecipient();
});

/*==============================================*/
/*================= EDIT SHIPPER ===============*/
/*==============================================*/
$('.form-group-shipper .form-control, .btn-edit-shipper').on('click', function () {
    $('#modal-edit-shipper').addClass('in').show();
});

$('#modal-edit-shipper [data-answer]').on('click', function () {
    $(this).closest('.modal').removeClass('in').hide();
});


/*==============================================*/
/*================ CHANGE FIELDS ===============*/
/*==============================================*/
$(document).find('#modal-' + STR_HASH_ID).ready(function () {
    $('#modal-shipment-dimensions .search-sku').autocomplete(skuAutocompleteConfig());
});

$('#modal-' + STR_HASH_ID + ' [name=customer_id]').on('change', function () {
    $('#modal-shipment-dimensions .search-sku').autocomplete(skuAutocompleteConfig());
});

$('#modal-' + STR_HASH_ID + ' .btn-confirm-dimensions').on('click', function (e) {
    e.preventDefault();
    $('#modal-' + STR_HASH_ID + ' [href="#tab-shp-info"]').trigger('click');
});

$(document).on('change', '#modal-' + STR_HASH_ID + ' .vol', function () {
    var volumes = $(this).val();
    var hash = $(this).data('hash');
    hash = typeof hash == 'undefined' ? 'master' : hash;

    /*if ($('.modal-xl [name="service_id"]').find(':selected').data('unity') == 'pallet') {
        $('table.shipment-pallets tbody tr:gt(0)').html('');
    } else {*/
        $tr = $('table.shipment-dimensions tbody tr:first');
        rowCount = $('table.shipment-dimensions tbody tr[data-hash="' + hash + '"]').length;
        $('.modal-xl [name="fator_m3"]').val('');
    /*}*/

    var i, html;

    if ($('.modal-xl [name=service_id]').find(':selected').data('unity') == 'pallet') {
        for (i = 1; i <= volumes; i++) {
            html = '<tr>';
            html += '<td>' + i + '</td>';
            html += '<td><div class="input-group"><input class="form-control input-sm m-0" name="pallet_weight[]" type="text"><div class="input-group-addon">kg</div></div></td>';
            html += '<td><input class="form-control input-sm m-0" name="pallet_qty[]" type="text"></td>';
            html += '<td><input class="form-control input-sm m-0" name="pallet_cost[]" type="text" readonly></td>';
            html += '<td><input class="form-control input-sm m-0" name="pallet_price[]" type="text" readonly></td>';
            html += '</tr>';

            $('table.shipment-pallets').append(html);
        }
    }

    validateTotalVolumes()
})

$('#modal-' + STR_HASH_ID + ' [name="shipping_price"], #modal-' + STR_HASH_ID + ' [name="cost_shipping_price"]').on('change', function () {
    $('#modal-' + STR_HASH_ID + ' [name="prv_shipping_price"]').val($(this).val()).trigger('change');
    $('#modal-' + STR_HASH_ID + ' [name="base_price"]').val($(this).val()).trigger('change');
    $('#modal-' + STR_HASH_ID + ' [name="extra_price"]').val('0.00').trigger('change');

    var isPriceFixed = $('#modal-' + STR_HASH_ID + ' [name="price_fixed"]').is(':checked');

    //atualiza todos os campos manuais de taxa de percentagem
    if($('#modal-' + STR_HASH_ID + ' .row-expenses [value="percent"]').length) {
        $('#modal-' + STR_HASH_ID + ' .row-expenses [value="percent"]').each(function(){
            $(this).closest('tr').find('.update-expenses').trigger('click');
        })
    }

    //se o preço está bloqueado, força a sincronização de novo.
    //se nao tiver esta validação, o preço estará sempre a atualizar em loop.
    if(isPriceFixed) {
        $('#modal-' + STR_HASH_ID + ' .btn-refresh-prices').trigger('click')
    } else {
        //emitir modal de aviso se pretende bloquear o preço
        //Growl.info('Preco alterado manualmente. Deve bloquear o preço para não ser alterado.')
        $('.price-lock').trigger('click')
    }
})

$('#modal-' + STR_HASH_ID + ' [name="weight"]').on('focus', function () {
    $('.provider-weight').show();
    validateTotalVolumes()
})

$('#modal-' + STR_HASH_ID + ' [name="weight"]').on('keyup click', function(){
    prefillProviderWeight($(this).val());
});

$('#modal-' + STR_HASH_ID + ' [name="cost"]').on('change', function () {
    $('[name="cost_price"]').val($(this).val());
});

$('#modal-' + STR_HASH_ID + ' [name="operator_id"]').on('change', function () {
    var vehicle  = $(this).find(':selected').data('vehicle');
    var provider = $(this).find(':selected').data('provider');
    $('.form-shipment  [name="vehicle"]').val(vehicle).trigger('change');

    if (typeof provider !== "undefined") {
        $('.form-shipment  [name="provider_id"]').val(provider).trigger('change');
    }
})

$('#modal-' + STR_HASH_ID + ' [name="vehicle"]').on('change', function () {
    $('.dlvr-vehicle').html($(this).val());
})

$('#modal-' + STR_HASH_ID + ' [name="trailer"]').on('change', function () {
    $('.dlvr-trailer').html($(this).val());
})

$('#modal-' + STR_HASH_ID + ' [name=service_id]').on('change', function () {

    //var tmp;
    //var changePosition = false;
    var $selectSearchCustomer = $('.select-search-customer');
    var $selectSearchRecipient = $('.select-search-recipient');
    //var $senderPlace = $selectSearchCustomer.closest('.input-group');
    //var $recipientPlace = $selectSearchRecipient.closest('.input-group');
    var $selected = $(this).find(':selected');

    if (IS_PICKUP == "1" || $selected.data('import')) { //serviço de recolhas
        if ($('#box-sender .box-sender-content').length > 0) {
            $('input[name="volumes"]').prop('required', false);
            $('input[name="weight"]').prop('required', false);

            if ($selected.data('import')) {
                $('[name="is_import"]').val(1);
            }
        }

    } else { //serviços normais

        if (!($('#box-sender .box-sender-content').length > 0)) {
            $('input[name="volumes"], input[name="weight"]').prop('required', true);
            $('[name="is_import"]').val(0);
        }
    }

    if ($selected.data('unity') == 'm3') { //services M3
        $('[for="volumes"]').html('Volumes');
        $('[name="weight"]').val('').prop('required', false).prop('readonly', false);
        $('[name="volume_m3"]').prop('required', true);
        $('.form-group-volume-m3, .btn-set-dimensions').show();
        $('.form-group-weight, .btn-set-pallets').hide();
        $('.form-group-kms, .form-group-hours, .form-group-ldm').hide();
        $('[name=kms],[name=hours],[name=ldm],[name=goods_price]').val('').prop('required', false);
    } else if ($selected.data('unity') == 'pallet') { //services pallet
        $('[for="volumes"]').html('Paletes');
        $('[name="weight"]').val('').prop('required', true).prop('readonly', true);
        $('[name="volume_m3"]').val('').prop('required', false);
        $('.form-group-volume-m3, .btn-set-dimensions').hide();
        $('.form-group-weight, .btn-set-pallets').show();
        $('.btn-set-dimensions').data('target', '#modal-shipment-pallets');
        $('.form-group-kms, .form-group-hours, .form-group-ldm').hide();
        $('[name=kms],[name=hours],[name=ldm],[name=goods_price]').val('').prop('required', false);
    } else if ($selected.data('unity') == 'km') { //services km
        $('.form-group-kms').show();
        $('.form-group-hours, .form-group-ldm').hide();
        $('[name=kms]').val('').prop('required', true);
        $('[name=hours],[name=ldm],[name=goods_price]').val('').prop('required', false);
        $('.modal .btn-auto-km').trigger('click');//atualiza kms
    } else if ($selected.data('unity') == 'hours') { //services hours
        $('.form-group-kms, .form-group-ldm').hide();
        $('.form-group-hours').show();
        $('[name=kms],[name=ldm],[name=goods_price]').val('').prop('required', false);
        $('[name=hours]').val('').prop('required', true);
    } else if ($selected.data('unity') == 'ldm') { //services LDM
        $('.form-group-kms, .form-group-hours').hide();
        $('.form-group-ldm').show();
        $('[name=kms],[name=hours]').val('').prop('required', false);
        $('[name=ldm]').val('').prop('required', true);
    } else { //all other services
        $('[for="volumes"]').html('Volumes');
        $('[name="volume_m3"]').val('').prop('required', false).prop('readonly', false);
        $('[name="weight"]').prop('required', true).prop('readonly', false);
        $('.form-group-volume-m3, .btn-set-pallets').hide();
        $('.form-group-weight, .btn-set-dimensions').show();
        $('.btn-set-dimensions').data('target', '#modal-shipment-dimensions');
        $('.form-group-kms, .form-group-hours, .form-group-ldm').hide();
        $('[name=kms],[name=hours],[name=ldm],[name=goods_price]').val('').prop('required', false);
    }

    if ($selected.data('unity') == 'advalor') { //services AdValue
        //$('.goods-price').show();
        $('[name="goods_price"]').val('').prop('required', true);
    }

    if (APP_MODE == 'cargo' || APP_MODE == 'freight') {
        $('.form-group-ldm').show();
        $('[name=ldm]').prop('required', false);
    }

    if (IS_PICKUP == "1") {
        $('[name="volumes"], [name="weight"]').prop('required', false);
    }

    validateTotalVolumes();

    calcDeliveryDate();

    validateZipCode('sender');
    validateZipCode('recipient');
})

$('#modal-' + STR_HASH_ID + ' [name="date"]').on('changeDate', function (e) {
    calcDeliveryDate();
    calcDeliveryHours();
});

$('#modal-' + STR_HASH_ID + ' [name="delivery_date"]').on('changeDate', function (e) {
    calcDeliveryHours();
});

$('#modal-' + STR_HASH_ID + ' [name=start_hour]').on('change', function () {
    calcDeliveryDate()
    calcDeliveryHours()
});

$('#modal-' + STR_HASH_ID + ' [name=end_hour]').on('change', function () {
    calcDeliveryHours()
});

$('#modal-' + STR_HASH_ID + ' [name=kms]').on('change', function () {
    $(this).css('border-color', '#ccc').css('color', '#555').removeClass('has-error');
});

$('#modal-' + STR_HASH_ID + ' [name="has_assembly"]').on('change', function () {
    if($(this).is(':checked')) {
        addTag('assembly')
    } else {
        removeTag('assembly')
    }
});

$('#modal-' + STR_HASH_ID + ' .price-lock').on('click', function () {
    if($('#modal-' + STR_HASH_ID + ' [name="price_fixed"]').is(':checked')) {
        $('#modal-' + STR_HASH_ID + ' [name="weight"]').trigger('change');
    }
});

//codigo postal moradas adicionais destino
$(document).on('change', '#modal-' + STR_HASH_ID + ' .box-recipient-content.new-addr .zip-code, #modal-' + STR_HASH_ID + ' .box-recipient-content.new-addr .select2-country', function(){
    $target = $(this).closest('#modal-' + STR_HASH_ID + ' .box-recipient-content.new-addr');
    getAgencyAdicionalAddress($target);
})

//codigo postal moradas adicionais remetente
$(document).on('change', '#modal-' + STR_HASH_ID + ' .box-sender-content.new-addr .zip-code, #modal-' + STR_HASH_ID + ' .box-sender-content.new-addr .select2-country', function(){
    $target = $(this).closest('#modal-' + STR_HASH_ID + ' .box-sender-content.new-addr');
    getAgencyAdicionalAddress($target);
})

//change dimensions qty
$(document).on('change', '#modal-' + STR_HASH_ID + ' [name="qty[]"]', function () {
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


/*==============================================*/
/*==================== PUDO ====================*/
/*==============================================*/
$('#modal-remote-xl [name="pudo_pickup"], #modal-remote-xl  [name="pudo_delivery"]').on('change', function () {
    if ($(this).is(':checked')) {
        enablePudoFields($(this));
    } else {
        disablePudoFields($(this));
    }
})

$('#modal-remote-xl [name="recipient_pudo_id"], #modal-remote-xl  [name="sender_pudo_id"]').on('change', function () {
    var pudoId = $(this).val();
    var address = $(this).find('option:selected').data('address');
    var zipCode = $(this).find('option:selected').data('zip-code');
    var city = $(this).find('option:selected').data('city');

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
    $('[name="volumes"]').trigger('change');
})

$('.modal-xl .trigger-price').on('change', function() {

    //ver eventos a disparar
    console.log('TRIGGER = ' + $(this).attr('name'));

    var triggerField   = $(this).attr('name');
    var serviceId      = $('.modal-xl [name="service_id"]').val();
    var customerId     = $('.modal-xl [name="customer_id"]').val();
    var pricePerKg     = $('#modal-' + STR_HASH_ID + ' [name="price_kg"]').val();
    var isBilled       = $('.modal-xl .shpprc').is('[readonly]');
    var isPriceBlocked = $('.modal-xl [name="price_fixed"]:checked').length;
    
    //if(!isBilled && !isPriceBlocked && serviceId && customerId) { //a variavel isBilled impedia disparar o atualizador de preços quando o preço estava bloqueado
    if(!isBilled && serviceId && customerId) {

        if (pricePerKg != '' && typeof pricePerKg != 'undefined' && pricePerKg != '0.00') {
            calcPricePerTon();
        } else {

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

            $('.btn-refresh-prices i').addClass('fa-spin');
            $('.loading-prices').show();
            $('.modal [name="waint_ajax"]').val(1);

            $.post(ROUTE_GET_PRICE, fields, function (data) {

                if (data.errors && data.errors.length) {
                    Growl.error(data.errors[0]);
                }

                //faturação
                $('.modal [name="zone"]').val(data.prices.zone);
                $('.modal [name="pickup_zone"]').val(data.prices.pickup_zone);
                $('.modal [name="base_price"]').val(data.prices.base_price);
                $('.modal [name="shipping_price"]').val(data.prices.shipping);
                $('.modal [name="prv_shipping_price"]').val(data.prices.shipping);
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
                $('.modal [name="vat_rate_id"]').val(data.billing.vat_rate_id).trigger('change.select2');
                $('.modal [name="vat_rate"]').val(data.billing.vat_rate);

                //custos
                $('.modal [name="cost_shipping_price"]').val(data.costs.shipment);
                $('.modal [name="cost_expenses_price"]').val(data.costs.expenses);
                $('.modal [name="cost_billing_subtotal"]').val(data.costs.subtotal);
                $('.modal [name="cost_billing_vat"]').val(data.costs.vat);
                $('.modal [name="cost_billing_total"]').val(data.costs.total);
                $('.modal [name="cost_billing_zone"]').val(data.costs.zone);
                $('.modal [name="cost_fuel_price"]').val(data.costs.fuel_price);
                $('.modal [name="cost_fuel_tax"]').val(data.costs.fuel_tax);

                //pesos e mercadoria
                $('.modal [name="extra_weight"]').val(data.parcels.extra_weight);
                $('.modal [name="taxable_weight"]').val(data.parcels.taxable_weight);
                $('.modal [name="provider_taxable_weight"]').val(data.parcels.provider_taxable_weight);
                $('.modal [name="volumetric_weight"]').val(data.parcels.volumetric_weight);
                $('.modal [name="fator_m3"]').val(data.parcels.fator_m3);
                $('.modal [name="has_sku"]').val(data.parcels.has_sku == true ? 1 : 0);
                $('.modal .lbl-vkg').html(data.parcels.volumetric_weight)

                //labels html
                $('.modal .billing-subtotal').html(data.billing.subtotal+ data.billing.currency);
                $('.modal .billing-vat').html(data.billing.vat + data.billing.currency);
                $('.modal .billing-vat-rate').html(data.billing.vat_rate);
                $('.modal .billing-total').html(data.billing.total + data.billing.currency);
                $('.modal .fuel-tax').html(data.prices.fuel_tax);
                $('.modal .extra-weight').html(data.parcels.extra_weight)//verificar
                $('.modal .cost-billing-subtotal').html(data.costs.subtotal+ data.billing.currency);
                $('.modal .cost-billing-vat').html(data.costs.vat + data.billing.currency);
                $('.modal .cost-billing-total').html(data.costs.total+ data.billing.currency);

                //inativa o campo fornecedor
               /* if(data.service.provider_id) {
                    $('.modal-xl [name="provider_id"]').prop('disabled', true).val(data.service.provider_id).trigger('change.select2')
                } else {
                    $('.modal-xl [name="provider_id"]').prop('disabled', false);
                }*/

                //tags
                if(data.shipment.tags.length) {
                    $('.modal [name="tags"]').val(data.shipment.tags.join(','));
                } else {
                    $('.modal [name="tags"]').val('');
                }

                //ganhos
                if(data.balance.value > 0.00) {
                    $('.modal .billing-balance').html('<span class="text-green"><i class="fas fa-caret-up"></i> '+round(data.balance.value).toFixed(2)+data.billing.currency+'</span>');
                } else {
                    $('.modal .billing-balance').html('<span class="text-red"><i class="fas fa-caret-down"></i> '+round(data.balance.value).toFixed(2)+data.billing.currency+'</span>');
                }

                //insere ou atualiza taxas adicionais
                insertOrUpdateExpenses(data);

                if (data.prices.is_pvp) {
                    $('.pvp').html('(PVP)')
                } else {
                    $('.pvp').html('')
                }

                //enable all optional fields
                //$('[name*="optional_fields"]').prop('disable', false).trigger('update');
                if(data.zones.blocked) {
                    Growl.error('O serviço está indisponível para este fornecedor e este destino.');
                    $('.modal [name=service_id]').val('').trigger('change.select2'); //reset escolha do serviço para impedir
                } else if(triggerField == 'sender_zip_code' || triggerField == 'sender_country') {

                    setAgency(data.agency, 'sender');

                    if(!data.agency.service_allowed) {
                        Growl.error('O serviço está indisponível para esta origem ou destino.')
                    }

                    filterServicesList(data.agency.services);
                } else if(triggerField == 'recipient_zip_code' || triggerField == 'recipient_country') {
                    setAgency(data.agency, 'recipient')
                    filterServicesList(data.agency.services);

                    if (!data.agency.service_allowed) {
                        Growl.error('O serviço escolhido está indisponível para este envio.')
                    }
                }


            }).always(function () {
                $('.modal [name="waint_ajax"]').val(0);
                $('.btn-refresh-prices i').removeClass('fa-spin');
                $('.modal [for="sender_address"] i, .modal [for="recipient_address"] i').addClass('hide');
                $('.loading-prices').hide();
            })
        }
    }
});

/*==============================================*/
/*================== EXPENSES ==================*/
/*==============================================*/
//SHOW EXPENSES MODAL
$('[data-target="#modal-shipment-expenses"]').on('click', function () {
    $('#modal-shipment-expenses').addClass('in').show();

    //atualiza contadores de totais da modal expenses
    updateExpensesTotal();
})

//CLOSE EXPENSES MODAL
$('.confirm-expenses').on('click', function () {
    $('.loading-expenses').addClass('hide');
    $('#modal-shipment-expenses').removeClass('in').hide();

    //atualiza contadores de totais da modal expenses
    updateExpensesTotal();

    //dispara atualizador de preços
    $('.modal [name="weight"]').trigger('change');
})

$('.btn-expenses-costs').on('click', function(e){
    e.preventDefault();
    if($('.expense-provider-detail').is(':visible')) {
        $('.expense-provider-detail').hide();
    } else {
        $('.expense-provider-detail').show();
    }
})

//ADD EXPENSE ROW
$('#modal-shipment-expenses .btn-add-expenses').on('click', function(){

    var $tr = $('.table-expenses tr:last').clone();

    $tr.find('.select2-container').remove();

    $tr.attr('data-auto', '0');
    $tr.find('[name="expense_auto[]"]').val('');
    $tr.find('[name="expense_id[]"]').val('');
    $tr.find('[name="expense_billing_item[]"]').val('');
    $tr.find('[name="expense_qty[]"]').val(1);
    $tr.find('[name="expense_price[]"]').val('');
    $tr.find('[name="expense_subtotal[]"]').val('');
    $tr.find('[name="expense_vat[]"]').val('');
    $tr.find('[name="expense_total[]"]').val('');
    $tr.find('[name="expense_vat_rate_id[]"]').val('');

    $tr.find('[name="expense_provider_id[]"]').val('');
    $tr.find('[name="expense_cost_price[]"]').val('');
    $tr.find('[name="expense_cost_subtotal[]"]').val('');
    $tr.find('[name="expense_cost_vat[]"]').val('');
    $tr.find('[name="expense_cost_total[]"]').val('');
    $tr.find('[name="expense_cost_vat_rate_id[]"]').val('');

    $tr.find('.select2').select2(Init.select2());

    $('.table-expenses').append($tr)
});

//UPDATE EXPENSE ROW VALUES
$(document).off('change', '.table-expenses [name="expense_qty[]"], .table-expenses [name="expense_price[]"], .table-expenses [name="expense_vat_rate_id[]"], .table-expenses [name="expense_cost_price[]"]');
$(document).on('change', '.table-expenses [name="expense_qty[]"], .table-expenses [name="expense_price[]"], .table-expenses [name="expense_vat_rate_id[]"], .table-expenses [name="expense_cost_price[]"]', function () {
    var $tr = $(this).closest('tr');
    $tr.find('[name="expense_id[]"]').trigger('change');
    // updateExpenseRow($tr);
    // updateExpensesTotal(); //atualiza contadores de totais da modal expenses
})

//força o valor da taxa a ficar com 2 casas decimais
$(document).on('focusout', '#modal-' + STR_HASH_ID + ' .table-expenses [name="expense_price[]"], #modal-' + STR_HASH_ID + ' .table-expenses [name="expense_cost_price[]"]', function () {
    value = $(this).val();
    if(value != '') {
        $(this).val(parseFloat(value).toFixed(2));
    }
})

//REMOVE EXPENSE ROW
$(document).on('click', '.modal .table-expenses .remove-expenses', function () {

    var $tr = $(this).closest('tr');

    if(!$tr.data('auto') == '1' && ($(document).find('.table-expenses tr[data-auto=""]').length > 1 || $(document).find('.table-expenses tr[data-auto="0"]').length > 1)) {
        $tr.remove();
    }
});

//BUTTON SYNC EXPENSE
$(document).on('click', '#modal-' + STR_HASH_ID + ' .table-expenses .update-expenses', function () {
    $(this).closest('tr').find('[name="expense_id[]"]').trigger('change');
});

//Obtem preço base a partir da base de dados
$(document).on('change', '#modal-' + STR_HASH_ID + ' [name="expense_id[]"]', function(event){

    var isModalExpensesOpen = $(this).closest('.row-expenses').is(':visible');
    var $tr = $(this).closest('tr');
    var qty   = $tr.find('[name="expense_qty[]"]').val();
    var price = $tr.find('[name="expense_price[]"]').val();
    var costPrice = $tr.find('[name="expense_cost_price[]"]').val();
    var unity = $tr.find('[name="expense_unity[]"]').val();
    var expenseId = $(this).val();
    var fields = $('.form-shipment :not(input[name=_method]').serialize();
    fields+='&expense_id='+expenseId+'&expense_qty='+qty+'&expense_unity='+unity+'&expense_price='+price+'&expense_cost_price='+costPrice;

    if(expenseId != '') {
        $('.modal [name="waint_ajax"]').val(1);

        /**
         * When a manual expense is selected remove the auto expense if it exists
         * This way it's possible to customize auto expenses
         */
        var $autoExpenses = $('#modal-' + STR_HASH_ID + ' .row-expenses[data-auto="1"] > td > select[name="expense_id[]"]');
        $autoExpenses.each(function () {
            var $this = $(this);
            if ($this.val() != expenseId)
                return;

            if ($tr[0] != $this.closest('tr')[0])
                $this.parent().parent().remove();
        });
        /**-- */

        $tr.find('.update-expenses i').addClass('fa-spin');
        $tr.find('[name="expense_price[]"]').closest('.input-group-money').find('.input-group-addon').html('<i class="fas fa-spin fa-circle-notch"></i>')

        $tr.find('[name="expense_billing_item[]"]').val('');
        $tr.find('[name="expense_unity[]"]').val('euro');
        $tr.find('[name="expense_qty[]"]').val(qty);
        $tr.find('[name="expense_price[]"]').val('');
        $tr.find('[name="expense_subtotal[]"]').val('');
        $tr.find('[name="expense_vat[]"]').val('');
        $tr.find('[name="expense_total[]"]').val('');
        $tr.find('[name="expense_vat_rate[]"]').val('');
        $tr.find('[name="expense_vat_rate_id[]"]').val('');

        $tr.find('[name="expense_provider_id[]"]').val('');
        $tr.find('[name="expense_cost_price[]"]').val('');
        $tr.find('[name="expense_cost_subtotal[]"]').val('');
        $tr.find('[name="expense_cost_vat[]"]').val('');
        $tr.find('[name="expense_cost_total[]"]').val('');
        $tr.find('[name="expense_cost_vat_rate[]"]').val('');
        $tr.find('[name="expense_cost_vat_rate_id[]"]').val('');


        $.post(ROUTE_GET_EXPENSE_PRICE, fields, function (data) {

            if(data.billing != null) {
                $tr.find('[name="expense_billing_item[]"]').val(data.fillable.billing_item_id);
                $tr.find('[name="expense_unity[]"]').val(data.expense.unity);

                $tr.find('[name="expense_qty[]"]').val(data.billing.qty);
                $tr.find('[name="expense_price[]"]').val(data.billing.price);
                $tr.find('[name="expense_subtotal[]"]').val(data.billing.subtotal);
                $tr.find('[name="expense_vat[]"]').val(data.billing.vat);
                $tr.find('[name="expense_total[]"]').val(data.billing.total);
                $tr.find('[name="expense_vat_rate[]"]').val(data.billing.vat_rate);
                $tr.find('[name="expense_vat_rate_id[]"]').val(data.billing.vat_rate_id).trigger('change.select2');

                $tr.find('[name="expense_provider_id[]"]').val(data.cost.provider_id);
                $tr.find('[name="expense_cost_price[]"]').val(data.cost.price);
                $tr.find('[name="expense_cost_subtotal[]"]').val(data.cost.subtotal);
                $tr.find('[name="expense_cost_vat[]"]').val(data.cost.vat);
                $tr.find('[name="expense_cost_total[]"]').val(data.cost.total);
                $tr.find('[name="expense_cost_vat_rate[]"]').val(data.cost.vat_rate);
                $tr.find('[name="expense_cost_vat_rate_id[]"]').val(data.cost.vat_rate_id).trigger('change.select2');

                if(data.expense.unity == 'euro') {
                    $tr.find('[name="expense_price[]"]').closest('.input-group-money').find('.input-group-addon').html('€')
                } else {
                    $tr.find('[name="expense_price[]"]').closest('.input-group-money').find('.input-group-addon').html('%')
                }

                updateExpensesTotal();

                //se a modal de taxas não está aberta, significa que este evento foi dispultado
                //por um trigger. É necessário atualizar o preço global do envio.
                if(!isModalExpensesOpen) {

                    //memoriza a informação se o preço do envio estava bloqueado.
                    var isPriceFixed = $('#modal-' + STR_HASH_ID + ' [name="price_fixed"]').is(':checked');

                    //coloca o preço bloqueado para que permita ao sistema enviar o preço da caixa de preço
                    //sem o modificar, para o caso de ter sido um preço inserido manualmente mas que não foi ativo o bloqueio de preço
                    $('#modal-' + STR_HASH_ID + ' [name="price_fixed"]').prop('checked', true);
                    $('#modal-' + STR_HASH_ID + ' .btn-refresh-prices').trigger('click');

                    //se o preço não estava bloqueado inicialmente, volta a desbloquear o preço
                    if(!isPriceFixed) {
                        $('#modal-' + STR_HASH_ID + ' [name="price_fixed"]').prop('checked', false);
                    }
                }
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
    }
})


/*========================================*/
/*========== Preço por tonelada ==========*/
/*========================================*/
$('#modal-' + STR_HASH_ID).on('change', '[name="price_kg"], [name="price_kg_unity"]', function (e) {
    calcPricePerTon();
})

/*========================================*/
/*============ PRICES COMPARE ============*/
/*========================================*/
$('.btn-compare-prices').on('click', function () {

    var customerId       = $('#modal-' + STR_HASH_ID + ' [name="customer_id"]').val();
    var serviceId        = $('#modal-' + STR_HASH_ID + ' [name="customer_id"]').val();
    var senderZipCode    = $('#modal-' + STR_HASH_ID + ' [name="sender_zip_code"]').val();
    var recipientZipCode = $('#modal-' + STR_HASH_ID + ' [name="sender_zip_code"]').val();
    var volumes          = $('#modal-' + STR_HASH_ID + ' [name="volumes"]').val();
    var weight           = $('#modal-' + STR_HASH_ID + ' [name="weight"]').val();

    if (IS_PICKUP == '1') {

        service = assignedService;

        if (volumes == '' || weight == '') {
            volumes = weight  = 1;
        }
    }

    if (customerId != ''
        && serviceId != ''
        && senderZipCode != ''
        && recipientZipCode != ''
        && volumes != ''
        && weight != '') {

        var fields = $('.form-shipment').find('[name!=_method]').serialize();
        var html = '<div class="cost-prices-comparation">';
        html += '<i class="fas fa-spin fa-circle-notch"></i> A calcular preços...';
        html += '</div>';

        $.post(ROUTE_COMPARE_PRICES, fields, function (data) {
            $(document).find('.cost-prices-comparation').html(data.html)
        }).fail(function () {
            $(document).find('.cost-prices-comparation').html('<span class="text-red"><i class="fas fa-exclamation-triangle"></i> Ocorreu um erro ao calcular os preços.</span>')
        })

    } else {
        var html = '<div class="cost-prices-comparation">';
        html += '<i class="fas fa-info-circle"></i> Preencha mais informação do envio para poder calcular os preços.';
        html += '</div>';
    }

    $(this).closest('.input-group-btn').before(html);
})

$(document).on('click', '.close-comparator', function () {
    $(document).find('.cost-prices-comparation').remove();
})

$(document).on('click', '.cost-prices-comparation [data-provider]', function () {
    var id = $(this).data('provider');
    $('[name="provider_id"]').val(id).trigger('change');
    $(document).find('.cost-prices-comparation').remove();
})

/*==============================================*/
/*================ DIMENSIONS ==================*/
/*==============================================*/
$('[data-target="#modal-shipment-dimensions"]').on('click', function () {

    //mostra modal shipment dimensions
    //$('#modal-shipment-dimensions').addClass('in').show();
    $('[href="#tab-shp-goods"]').trigger('click');

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

    $('#modal-shipment-dimensions .search-sku').autocomplete(skuAutocompleteConfig());
})

//duplica dimensões
$(document).on('click', '#modal-' + STR_HASH_ID + ' .copy-dimensions', function () { //show
    var $tr     = $(this).closest('tr');
    var $nextTr = $tr.next('tr');

    $nextTr.find('td').each(function (item) {
        lastTrVal = $tr.find('td:eq(' + item + ')').find('input, select').val();
        $(this).find('input, select').val(lastTrVal).trigger('change')
    })
})

//pre-preenche dimensoes
$(document).on('change', '#modal-' + STR_HASH_ID + ' [name="box_type[]"]', function (e) {

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
})

//calcula M3 de cada linha
$(document).on('change', '[name="width[]"], [name="height[]"], [name="length[]"]', function () {

    var $tr    = $(this).closest('tr');
    var width  = $tr.find('[name="width[]"]').val();
    var height = $tr.find('[name="height[]"]').val();
    var length = $tr.find('[name="length[]"]').val();
    var volume = calcVolume(width, height, length, VOLUMES_MESURE_UNITY);

    $tr.find('[name="fator_m3_row[]"]').val(volume);
})

//Add new dimension row
$('.btn-new-dim-row').on('click', function (e) {
    e.preventDefault()

    //var hash = $(this).data('hash');

    if(ADICIONAL_ADDR_MODE == 'pro_fixed' || ADICIONAL_ADDR_MODE == 'pro') {
        var hash = $('.table-addrs tr.active').data('hash');
    } else {
        var hash = $('.address-tabs [data-action="pnladdr"].active').data('id');
    }

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

    $('#modal-shipment-dimensions .search-sku').autocomplete(skuAutocompleteConfig());
})

//delete dimension row
$(document).on('click', '#modal-shipment-dimensions .btn-del-dim-row', function () {
    if($('[name="qty[]"]').length > 1) {
        $(this).closest('tr').remove();
    } else {
        Growl.error('Não pode remover esta linha.')
    }
})

$('.confirm-dimensions').on('click', function () { //hide

    //var vols     = parseInt($('.modal [name="volumes"]').val());
    var vols     = parseInt($('.vols-panel .vol').val());
    var totalQty = countTotalQtyDimensions();

    if (isNaN(vols)) {
        vols = 1;
        
        $('.vols-panel .vol:visible').val(1);
        //$('.modal [name="volumes"]').val(1);
    }

    if ($('#modal-shipment-dimensions .has-error').length) {
        Growl.error('<i class="fas fa-exclamation-triangle"></i> Corrija os campos a vermelho antes de confirmar.')
        openShpTab('#tab-shp-goods');
    } else if (totalQty > vols) {
        openShpTab('#tab-shp-goods')
        $('#modal-confirm-vols').addClass('in').show().find('.cvol').html(totalQty);
    } else {
        //Oculta modal
        //$('#modal-shipment-dimensions').removeClass('in').hide();

        var hash = $('.vol:visible').data('hash');
        hash = typeof hash == 'undefined' ? 'master' : hash;

        var m3;
        var somaDimensoes = 0;
        var weightVal;
        var fatorM3 = 0;
        var totalWeight = 0;
        var totalCost = 0;
        var totalPrice = 0;
        var totalGoodsPrice = 0;
        var columnPriceExists = false;
        $('.fator-m3').hide();


        if ($('#modal-shipment-dimensions tr[data-hash="' + hash + '"] [name="box_total_price[]"]').length) {
            $('#modal-shipment-dimensions tr[data-hash="' + hash + '"] [name="box_total_price[]"]').each(function () {
                columnPriceExists = true;
                price = parseFloat($(this).val());
                cost = parseFloat($(this).closest('td').find('[name="box_cost[]"]').val());
                price = isNaN(price) ? 0 : price;
                cost = isNaN(cost) ? 0 : cost;

                totalPrice += price;
                totalCost += cost;
            })
        }

        $('#modal-shipment-dimensions tr[data-hash="' + hash + '"] [name="fator_m3_row[]"]').each(function () {
            m3 = $(this).val() == "" ? 0 : $(this).val();
            m3 = parseFloat(m3);
            qty = parseInt($(this).closest('tr').find('[name="qty[]"]').val());
            fatorM3 += (m3 * qty);
        })

        $('#modal-shipment-dimensions tr[data-hash="' + hash + '"] [name="box_weight[]"]').each(function () {
            var weightVal = $(this).val() == "" ? 0 : parseFloat($(this).val());
            weightVal = parseFloat(weightVal);
            qty = parseInt($(this).closest('tr').find('[name="qty[]"]').val());
            totalWeight += (weightVal * qty);

            goodsPrice = parseFloat($(this).closest('tr').find('[name="box_price[]"]').val());
            goodsPrice = isNaN(goodsPrice) ? 0 : goodsPrice;
            totalGoodsPrice += goodsPrice;
        })

        if (hash == 'master') {
            $('[name="fator_m3"]').val(fatorM3);
        } else {
            $('[name="addr[' + hash + '][fator_m3]"').val(fatorM3);
        }

        if (totalWeight > 0) {
            if (hash == 'master') {
                $('[name="weight"]').val(totalWeight);
            } else {
                $('[name="addr[' + hash + '][weight]"').val(totalWeight);
            }
        }

        if (fatorM3 != "" || fatorM3 != "0") {
            if (hash == 'master') {
                $('.fator-m3').show().find('span').html(fatorM3);
            } else {
                $('.fator-m3').show().find('span').html(fatorM3);
            }
        }

        if (columnPriceExists) {
            $('.modal [name="total_price"]').val(totalPrice.toFixed(2)).trigger('change');
            $('.modal [name="cost"]').val(totalCost.toFixed(2));
        }

        if ($('[name="service_id"] option:selected').data('unity') == 'm3') {
            $('[name="volume_m3"]').val(fatorM3.toFixed(2))
        }

        $('[name="goods_price"]').val(round(totalGoodsPrice).toFixed(2));
    }
})

$('#modal-confirm-vols [data-answer]').on('click', function () {

    if ($(this).data('answer') == '1') {
        var totalQty = countTotalQtyDimensions();
        $('.vols-panel .vol').val(totalQty).trigger('change');
    }

    $(this).closest('.modal').removeClass('in').hide();

    openShpTab($(this).data('next-tab'))

    //$('.form-shipment .nav-tabs li').removeClass('active');
    //$('[href="#tab-shp-info"]').trigger('click')
    //oculta modal
    //$('#modal-shipment-dimensions').removeClass('in').hide();
});

/*==============================================*/
/*=================== GET KMS ==================*/
/*==============================================*/
$('.modal-xl [name="sender_zip_code"], .modal-xl [name="sender_city"], .modal-xl [name="sender_country"], .modal-xl [name="recipient_zip_code"], .modal-xl [name="recipient_city"], .modal-xl [name="recipient_country"], .modal-xl [name="km_agency"]').on('change', function () {
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
/*=========== CHANGE PROVIDER ============*/
/*========================================*/
$('.modal-xl [name=provider_id]').on('change', function () {

    var sync     = $('[name=sync_agencies]').val();
    var provider = $(this).val();
    var zipCode  = $('.modal-xl [name=recipient_zip_code]').val();

    if (sync == '1') {
        $('[name=sync_agencies]').val('');
    } else {
        $('.recipient-agency-loading').show();
        $.post(ROUTE_GET_AGENCY, {
            zipCode: zipCode,
            provider: provider
        }, function (data) {
            $('[name=recipient_agency_id]').html(data.agenciesHtml);

            if (data.agency_id) {
                $('[name=recipient_agency_id]').val(data.agency_id).trigger('change.select2');
            } else {
                $('[name=recipient_agency_id]').val($('#modal-remote-xl select[name=agency_id]').val()).trigger('change.select2');
            }
        }).always(function () {
            $('.recipient-agency-loading').hide();
        })
    }

    if ($('#modal-remote-xl  [name="pudo_delivery"]').is(':checked')) {
        enablePudoFields($('#modal-remote-xl  [name="pudo_delivery"]'));
    }

    if ($('#modal-remote-xl  [name="pudo_pickup"]').is(':checked')) {
        enablePudoFields($('#modal-remote-xl  [name="pudo_pickup"]'));
    }
});

$('.modal-xl [name=provider_id]').on('select2:opening', function (e) {
    if ($('#modal-confirm-change-provider').length) {
        e.preventDefault();
        $('#modal-confirm-change-provider').addClass('in').show();
        return false;
    }
});

$('#modal-confirm-change-provider .btn-confirm-yes').on('click', function () {
    $this = $(this);
    $this.button('loading')
    $.post(ROUTE_SYNC_RESET, { delete_provider: 1 }, function (data) {
        if (data.result) {
            $('#modal-confirm-change-provider').remove();
            Growl.success(data.feedback);
        } else {
            Growl.error(data.feedback);
        }
    }).fail(function () {
        Growl.error500();
    }).always(function () {
        $this.button('reset')
    })
})

$('#modal-confirm-change-provider .btn-confirm-no').on('click', function () {
    $('#modal-confirm-change-provider').removeClass('in').hide();
})


/*========================================*/
/*============= STATE FIELDS =============*/
/*========================================*/
$('[name="sender_state"], [name="recipient_state"]').on('select2:select', function () {
    var value = $(this).find('option:selected').val();
    if (typeof value != "undefined" && value != "") {
        value = value.toUpperCase();
    }
    $(this).next().find('.select2-selection__rendered').html(value)
})

/*====================================================*/
/*========== MORADAS ADICIONAIS - PRO FIXED ==========*/
/*====================================================*/
if(ADICIONAL_ADDR_MODE == 'pro_fixed' || ADICIONAL_ADDR_MODE == 'pro') {

    initAddrModeProFixed();

    //change fields on main modal and update on selected row
    $(document).on('change', '#modal-' + STR_HASH_ID + ' .modal-new-addr .zip-code',function(){
        var $target  = $(this).closest('.box')
        getAgencyAdicionalAddress($target);
    })

    $(document).on('change', '#modal-' + STR_HASH_ID + ' .modal-new-addr .select2-country',function(){
        var $target  = $(this).closest('.box')
        validateZipCode($target);
    })

    //change fields on main modal and update on selected row
    $('#modal-' + STR_HASH_ID + ' [modal-addr]').on('change', function(){
        var target = $(this).attr('modal-addr');
        $tr = $('.table-addrs tr.active');
        $tr.find(target).val($(this).val());
        updateAddressRowValues($tr);
    })

    //select address row
    $(document).on('click', '#modal-' + STR_HASH_ID + ' .table-addrs tr[data-target]', function (e) {
        selectAddressRow($(this));
    });

    //button add new address row
    $('#modal-' + STR_HASH_ID + ' [data-action="add-addr-row"]').on('click', function(){
        addAddressRow();
    })

    //button edit selected address row
    $('#modal-' + STR_HASH_ID + ' [data-action="edit-addr-row"]').on('click', function() {
        openAddressRowModal($('.table-addrs').find('.active'));
    })

    //button replicate address row
    $(document).on('click', '#modal-' + STR_HASH_ID + ' .table-addrs [data-action="copy-addr"]', function (e) {
        var $tr = $(this).closest('tr');
        bootbox.dialog({
            animate: false,
            title: 'Duplicar transporte',
            message: '<h4>Confirma a duplicação da linha selecionada?</h4>',
            buttons: {
                cancel: { label: 'Cancelar' },
                main: {
                    label: 'Duplicar',
                    className: 'btn-success',
                    callback: function(result) { 
                        copyAddressRow($tr);
                    }
                }
            }
        });
    });

    //button remove address row
    $(document).on('click', '#modal-' + STR_HASH_ID + ' .table-addrs [data-action="del-addr"]', function(){
        var $tr = $(this).closest('tr');
        bootbox.dialog({
            animate: false,
            title: 'Apagar transporte',
            message: '<h4>Confirma a remoção da linha selecionada?</h4>',
            buttons: {
                cancel: { label: 'Cancelar'},
                main: {
                    label: 'Apagar',
                    className: 'btn-danger',
                    callback: function(result) { 
                        removeAddressRow($tr);
                     }
                }
            }
        });
    });

    //open address row edition modal
    $(document).on('dblclick', '#modal-' + STR_HASH_ID + ' .table-addrs tr[data-target]', function (e) {
        openAddressRowModal($(this));
    });

    //close edition modal - confirm
    $(document).on('click', '#modal-' + STR_HASH_ID + ' .modal-new-addr [data-answer="1"]', function (e) {
        e.preventDefault();

        var $tr    = $(this).closest('tr');
        var $modal = $(this).closest('.modal-new-addr');

        if(hasEmptyFields($modal)) {
            Growl.warning('Preencha os campos a vermelho antes de gravar.')
            return false;
        } else {
            updateAddressRowValues($tr);
            $modal.removeClass('in').hide();

            if($tr.index() == 1) { //primeira linha
                updateShipperName();
                updateReceiverName();
            }
        }
    });

    //close edition modal - cancel
    $(document).on('click', '#modal-' + STR_HASH_ID + ' .modal-new-addr [data-answer="0"]', function (e) {
        e.preventDefault();

        var $tr    = $(this).closest('tr');
        var $modal = $(this).closest('.modal');
        var senderName    = $modal.find('.box-sender-content .shname').val();
        var recipientName = $modal.find('.box-recipient-content .shname').val();
       
        if(senderName == '' && recipientName == '') {
            removeAddressRow($tr);
        } else {
            $modal.find('[data-answer="1"]').trigger('click');
        }
    });
}

/*=======================================================*/
/*========== MORADAS ADICIONAIS - PRO/AVANÇADO ==========*/
/*=======================================================*/
if(ADICIONAL_ADDR_MODE == 'pro') {

    $('#modal-remote-xl [data-action="add-addr"]').on('click', function (e) {
        $('.row-destinations-default').hide();
        $('.row-destinations-advanced').show();
        $('.vols-panel').addClass('vols-panel-addr');
        $('.nav-obs').addClass('nav-obs-addr');
        var $tr = $('.table-addrs [data-target="#modal-main"]');

        //migra dados da janela pai para a janela main da primeira linha de moradas
        //obtem o nome do campo atual e coloca o mesmo valor no campo com o mesmo nome precedido de underscore.
        $('.row-destinations-default .box-sender-content input, .row-destinations-default .box-sender-content select, .row-destinations-default .box-sender-content textarea, .row-destinations-default .box-recipient-content input, .row-destinations-default .box-recipient-content select, .row-destinations-default .box-recipient-content textarea').each(function(){
            var fieldName  = $(this).attr('name');
            var fieldValue = $(this).val();
            $('#modal-main').find('[name="__'+fieldName+'"]').val(fieldValue);
        })

        multipleAddrModeDisableOriginalFields();
        updateAddressRowValues($tr)
    });

    $('#modal-remote-xl [data-action="rem-addr"]').on('click', function (e) {
        $('.row-destinations-default').show();
        $('.row-destinations-advanced').hide();
        $('.vols-panel').removeClass('vols-panel-addr');
        $('.nav-obs').removeClass('nav-obs-addr');

        //migra dados da linha para a janela main da primeira linha de moradas
        //obtem o nome do campo atual e coloca o mesmo valor no campo com o mesmo nome precedido de underscore.
        $('#modal-main .box-sender-content input, #modal-main .box-sender-content select, #modal-main .box-sender-content textarea, #modal-main .box-recipient-content input, #modal-main .box-recipient-content select, #modal-main .box-recipient-content textarea').each(function(){
            var fieldName  = $(this).attr('name');
            var fieldValue = $(this).val();
            $('.row-destinations-default').find('[name="'+fieldName+'"]').val(fieldValue);
        })

        updateShipperName();
        updateReceiverName();
        multipleAddrModeEnableOriginalFields()
    });
}

/*========================================================*/
/*========== MORADAS ADICIONAIS - BI DIRECIONAL ==========*/
/*========================================================*/
if(ADICIONAL_ADDR_MODE == 'bidir') {
    $('#modal-remote-xl [data-action="add-addr"]').on('click', function (e) {
        var hash     = Str.random(5)
        var $mainObj = $('#modal-remote-xl .row-destinations-default .box');
        var $element = $('#modal-remote-xl .address-tabs');
        var totalTab = parseInt($element.find('[data-action="pnladdr"]').length);
        var tabHtml  = '';

        if (!totalTab) {
            tabHtml += '<li data-action="pnladdr" data-id="main-addr">Local 1</li>';
            tabHtml += '<li class="active" data-action="pnladdr" data-id="' + hash + '">Local 2 <span><i class="fas fa-times fs-10"></i></span></li>';
        } else {
            tabHtml += '<li class="active" data-action="pnladdr" data-id="' + hash + '">Local ' + ((totalTab/2) + 1) + '<span><i class="fas fa-times fs-10"></i></span></li>';
        }

        $element.find('[data-action="pnladdr"]').removeClass('active');
        $mainObj.find('.new-addr, .main-addr').hide();

        $element.find('li:last-child').before(tabHtml);

        $mainObj.each(function() {
            $targetObj = $(this).find('.box-body>div.main-addr').clone();
            $targetObj.removeClass('main-addr')
                .addClass('new-addr')
                .addClass(hash)
                .show();
        
            $targetObj.find('input, select').each(function (item) {
                $(this).attr('name', 'addr[' + hash + '][' + $(this).attr('name') + ']');
                $(this).val('')
            });
        
            $targetObj.find('.select2-country').val('pt').trigger('change.select2')
            $targetObj.find('.search-sender').autocomplete(SEARCH_SENDER_OPTIONS)
            $targetObj.find('.search-recipient').autocomplete(SEARCH_RECIPIENT_OPTIONS);
        
            $targetObj.find('.select2-container').remove();
            $targetObj.find('.select2').select2(Init.select2())
            $targetObj.find('.select2-country').select2(Init.select2Country())
            $(this).find('.box-body').append($targetObj.append());
        })
        
        //volumes
        var $volumes = $('.vols-panel [name="volumes"]').clone();
        $volumes.attr('name', 'addr[' + hash + '][volumes]')
            .val('')
            .attr('data-hash', hash)
            .addClass(hash);
        $('.vol').hide();
        $volumes.show();
        $('.vols-panel [name="volumes"]').after($volumes);

        //weight
        var $weight = $('.vols-panel [name="weight"]').clone();
        $weight.attr('name', 'addr[' + hash + '][weight]')
            .val('')
            .attr('data-hash', hash)
            .addClass(hash);
        $('.kg').hide();
        $weight.show();
        $('.vols-panel [name="weight"]').after($weight);

        //volumetric weight
        var $volumetricWeight = $('.vols-panel [name="volumetric_weight"]').clone();
        $volumetricWeight.attr('name', 'addr[' + hash + '][volumetric_weight]')
            .val('')
            .attr('data-hash', hash)
            .addClass(hash);
        $('.vkg').hide();
        $volumetricWeight.show();
        $('.vols-panel [name="volumetric_weight"]').after($volumetricWeight);
        
        //fator m3
        var $fatorM3 = $('.vols-panel [name="fator_m3"]').clone();
        $fatorM3.attr('name', 'addr[' + hash + '][fator_m3]')
            .val('')
            .attr('data-hash', hash)
            .addClass(hash);
        $('.fm3').hide();
        $fatorM3.show();
        $('.vols-panel [name="fator_m3"]').after($fatorM3);

    
    })

    //Enable address tab
    $(document).on('click', '#modal-remote-xl [data-action="pnladdr"]', function () {
        var hash = $(this).data('id');
        $('li[data-id="'+hash+'"]').closest('.address-tabs').find('[data-action="pnladdr"]').removeClass('active');
        $('li[data-id="'+hash+'"]').addClass('active');
        $('li[data-id="'+hash+'"]').closest('.box').find('.box-body>div:not(.form-group-shipper)').hide();

        if (hash == 'main-addr') {
            $(document).find('.vol, .kg, .vkg, .fm3').hide();
            $('[name="volumes"]').show();
            $('[name="weight"]').show();
            $('[name="volumetric_weight"]').show();
            $('[name="fator_m3"]').show();
        } else {
            $(document).find('.vol, .kg, .vkg, .fm3').hide();
        }

        $(document).find('.' + hash).show();
        showDimensionsRows(hash);
    })

    //Remove adicional address
    $(document).on('click', '#modal-remote-xl [data-action="pnladdr"] span', function (e) {
        e.preventDefault();
        var $mainObj = $('#modal-remote-xl .box');
        var $element = $(this).closest('.address-tabs');
        var $li = $(this).closest('li');
        var hash = $li.data('id');

        if (hash != 'main-addr') {
            $('#modal-remote-xl .box').find('.' + hash).remove();
            $('li[data-id="'+hash+'"]').remove();

            if ($element.find('[data-action="pnladdr"]').length == 1) {
                $mainObj.find('[data-action="pnladdr"]').remove();
                $('.address-right, .address-left').show();
            } else {
                var count = 1;
                $element.find('[data-action="pnladdr"]').each(function () {
                    var curHash = $(this).data('id');
                    $('li[data-id="'+curHash+'"]').html('Local ' + count + ' <span><i class="fas fa-times fs-10"></i></span>');
                    count++;
                })
            }
            $element.find('[data-action="pnladdr"]').removeClass('active');
            $element.find('[data-action="pnladdr"]:first-child').addClass('active');

            $mainObj.each(function(){
                $(this).find('.new-addr').hide();
                $(this).find('.main-addr').show();
            })
        }
    })
}

/*========================================================*/
/*========== MORADAS ADICIONAIS - UNIDIRECIONAL ==========*/
/*========================================================*/
if(ADICIONAL_ADDR_MODE == 'udir' || ADICIONAL_ADDR_MODE == '') {

    $('#modal-remote-xl [data-action="add-addr"]').on('click', function (e) {
        var hash     = Str.random(5)
        var $mainObj = $(this).closest('.box');
        var $element = $(this).closest('.address-tabs');
        var totalTab = parseInt($element.find('[data-action="pnladdr"]').length);
        var tabHtml  = '';
    
        $('.address-left').on('click', function () {
            $('.address-right').hide();
        })
    
        $('.address-right').on('click', function () {
            $('.address-left').hide();
        })
    
        if (!totalTab) {
            tabHtml += '<li data-action="pnladdr" data-id="main-addr">Local 1</li>';
            tabHtml += '<li class="active" data-action="pnladdr" data-id="' + hash + '">Local 2 <span><i class="fas fa-times fs-10"></i></span></li>';
        } else {
            tabHtml += '<li class="active" data-action="pnladdr" data-id="' + hash + '">Local ' + (totalTab + 1) + '<span><i class="fas fa-times fs-10"></i></span></li>';
        }
    
        $element.find('[data-action="pnladdr"]').removeClass('active');
        $mainObj.find('.new-addr, .main-addr').hide();
    
        $element.find('li:last-child').before(tabHtml);
    
        $targetObj = $mainObj.find('.box-body>div.main-addr').clone();
        $targetObj.removeClass('main-addr')
            .addClass('new-addr')
            .addClass(hash)
            .show();
    
        $targetObj.find('input, select').each(function (item) {
            $(this).attr('name', 'addr[' + hash + '][' + $(this).attr('name') + ']');
            $(this).val('')
        });
    
        $targetObj.find('.select2-country').val('pt').trigger('change.select2')
        $targetObj.find('.search-sender').autocomplete(SEARCH_SENDER_OPTIONS)
        $targetObj.find('.search-recipient').autocomplete(SEARCH_RECIPIENT_OPTIONS);
    
        //volumes
        var $volumes = $('.vols-panel [name="volumes"]').clone();
        $volumes.attr('name', 'addr[' + hash + '][volumes]')
            .val('')
            .attr('data-hash', hash)
            .addClass(hash);
        $('.vol').hide();

        $volumes.show();
        $('.vols-panel [name="volumes"]').after($volumes);
    
        //weight
        var $weight = $('.vols-panel [name="weight"]').clone();
        $weight.attr('name', 'addr[' + hash + '][weight]')
            .val('')
            .attr('data-hash', hash)
            .addClass(hash);
        $('.kg').hide();
        $weight.show();
        $('.vols-panel [name="weight"]').after($weight);
    
        //volumetric weight
        var $volumetricWeight = $('.vols-panel [name="volumetric_weight"]').clone();
        $volumetricWeight.attr('name', 'addr[' + hash + '][volumetric_weight]')
            .val('')
            .attr('data-hash', hash)
            .addClass(hash);
        $('.vkg').hide();
        $volumetricWeight.show();
        $('.vols-panel [name="volumetric_weight"]').after($volumetricWeight);
        
        //fator m3
        var $fatorM3 = $('.vols-panel [name="fator_m3"]').clone();
        $fatorM3.attr('name', 'addr[' + hash + '][fator_m3]')
            .val('')
            .attr('data-hash', hash)
            .addClass(hash);
        $('.fm3').hide();
        $fatorM3.show();
        $('.vols-panel [name="fator_m3"]').after($fatorM3);
    
        $targetObj.find('.select2-container').remove();
        $targetObj.find('.select2').select2(Init.select2())
        $targetObj.find('.select2-country').select2(Init.select2Country())
        $mainObj.find('.box-body').append($targetObj.append());
        showDimensionsRows(hash);
    })
    
    //Enable address tab
    $(document).on('click', '#modal-remote-xl [data-action="pnladdr"]', function () {
        var hash = $(this).data('id');
        $(this).closest('.address-tabs').find('[data-action="pnladdr"]').removeClass('active');
        $(this).addClass('active');
        $(this).closest('.box').find('.box-body>div:not(.form-group-shipper)').hide();
    
        if (hash == 'main-addr') {
            $(document).find('.vol, .kg, .vkg, .fm3').hide();
            $('[name="volumes"]').show();
            $('[name="weight"]').show();
            $('[name="volumetric_weight"]').show();
            $('[name="fator_m3"]').show();
        } else {
            $(document).find('.vol, .kg, .vkg, .fm3').hide();
        }
    
        $(document).find('.' + hash).show();
        showDimensionsRows(hash);
    })
    
    //Remove adicional address
    $(document).on('click', '#modal-remote-xl [data-action="pnladdr"] span', function (e) {
        e.preventDefault();
        var $mainObj = $(this).closest('.box');
        var $element = $(this).closest('.address-tabs');
        var $li = $(this).closest('li');
        var hash = $li.data('id');
    
        if (hash != 'main-addr') {
            $(this).closest('.box').find('.' + hash).remove();
            $(this).closest('li').remove();
    
            if ($element.find('[data-action="pnladdr"]').length == 1) {
                $element.find('[data-action="pnladdr"]').remove();
                $('.address-right, .address-left').show();
            } else {
                var count = 1;
                $element.find('[data-action="pnladdr"]').each(function () {
                    $(this).html('Local ' + count + ' <span><i class="fas fa-times fs-10"></i></span>');
                    count++;
                })
            }
            $element.find('[data-action="pnladdr"]').removeClass('active');
            $element.find('[data-action="pnladdr"]:first-child').addClass('active');
            $mainObj.find('.new-addr').hide();
            $mainObj.find('.main-addr').show();
        }
    })
}

/*========================================*/
/*========== calculo de rota GPS =========*/
/*========================================*/
$('[href="#tab-shp-map"]').on('click', function(){
    makeShipmentRoute();
})

$('.shp-dlvr-route-options [name="optimize"],.shp-dlvr-route-options [name="return_back"],.shp-dlvr-route-options [name="waypoint_agency"]').on('change', function(){
    makeShipmentRoute();
})

$('.shp-dlvr-route-options [name="avoid_highways"], .shp-dlvr-route-options [name="avoid_tolls"]').on('change', function(){
    initDeliveryDirections();
})


/*==============================================*/
/*=============== OPTIONAL FIELDS ==============*/
/*==============================================*/
$('#modal-' + STR_HASH_ID).on('change', '[name*="optional_fields"]', function (e) {
    insertOrUpdateFastExpense($(this));
})

/*========================================*/
/*===== VALIDAR E SUBMETER FORMULÁRIO ====*/
/*========================================*/
$(document).on('change','#modal-' + STR_HASH_ID + ' [required]', function(){
    $(this).removeClass('has-error');
    if($(this).is('select')) {
        $(this).next().removeClass('has-error')
    }
})

$('.btn-save-submit').on('click', function(e){
    e.preventDefault();
    $('#modal-' + STR_HASH_ID + ' [name="save_other"]').val(1);
    $('.btn-submit').trigger('click');
})


$('.btn-submit').on('click', function (e) {

    if($('.modal [name="waint_ajax"]').val() == 1) {
        Growl.warning('<i class="fas fa-clock"></i> Atualização de dados a decorrer. Aguarde.');
        return false;
    } else {
        /*countEmptyFields = $(".form-shipment [required]").filter(function () {
            return !$(this).val();
        }).addClass('has-error').length;*/

        emptyFields = $(".form-shipment [required]:not([disabled])").filter(function () {
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
            openShpTab('#tab-shp-info');
            Growl.warning('Preencha os campos a vermelho antes de gravar.')
            return false;
        }
    }
})

$('.form-shipment').on('submit', function (e) {
    e.preventDefault();

    var $form = $(this);
    var $button = $('button[type=submit],.btn-submit');
    var shpType = $('.modal [name="shp_type"]').val();

    var hasDims = parseFloat($form.find('[name="fator_m3"]').val())
    hasDims = hasDims != '' && hasDims > 0.000001 ? true : false;

    if ($(document).find('.has-error').length) {
        Growl.error("<i class='fas fa-exclamation-circle'></i> Corrija os campos a vermelho antes de gravar.");
    } else if ($form.find('[name="service_id"] option:selected').data('dim-required') == '1' && !hasDims) {

        $('[href="#tab-shp-goods"]').trigger('click')

        //mostra modal dimensions
        //$('#modal-shipment-dimensions').addClass('in').show(); //abre modal dimensões
        Growl.warning("<i class='fas fa-exclamation-circle'></i> É obrigatório indicar a mercadoria e dimensões antes de gravar.");
    } else {

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function (data) {
            if (data.result && !data.syncError) {

                if (typeof oTable !== "undefined" && !data.saveOther) {
                    oTable.draw(false); //update datatable without change pagination
                }

                if($('.trip-content .shipments-table').length) {
                    tripRefreshShipmentsList(); //atualiza a lista de envios do delivery manifest se estiver na pagina
                }

                Growl.success(data.feedback);
                
                if (data.printGuide || data.printLabel || data.printCmr) {

                    if(data.saveOther) {

                        if (data.printGuide) {
                            window.open(data.printGuide, '_blank');
                        }

                        if (data.printLabel) {
                            window.open(data.printLabel, '_blank');
                        }

                        if (data.printCmr) {
                            window.open(data.printCmr, '_blank');
                        }

                        resetShipmentModal();

                    } else {
                        if (data.printGuide) {
                            if (window.open(data.printGuide, '_blank')) {
                                if ($('#modal-remote-xlg').is(':visible')) {
                                    $('#modal-remote-xlg').modal('hide');
                                } else {
                                    $('#modal-remote-xl').modal('hide');
                                }
                            } else {
                                $('#modal-remote-xl').modal('show');
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
                                $('#modal-remote-xl').modal('show');
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
                                $('#modal-remote-xl').modal('show');
                                $('#modal-remote-xl').find('.modal-xl').removeClass('modal-xl').find('.modal-content').html(data.html);
                            }
                        }
                    }
                } else {

                    if(data.saveOther) {
                        resetShipmentModal();
                    } else {
                        if ($('#modal-remote-xlg').is(':visible')) {
                            $('#modal-remote-xlg').modal('hide');
                        } else {
                            $('#modal-remote-xl').modal('hide');
                        }
                    }
                }

            } else if (data.syncError) {

                if (data.trkid) {
                    $(document).find('[name="trkid"]').remove();
                    $('.form-shipment').append('<input type="hidden" name="trkid" value="' + data.trkid + '"/>');
                }

                $('#modal-confirm-sync-error').find('.error-msg').html(data.feedback)
                $('#modal-confirm-sync-error').find('.error-provider').html($('[name="provider_id"] option:selected').text())
                $('#modal-confirm-sync-error').addClass('in').show();

                $('#modal-confirm-sync-error .btn-confirm-no').on('click', function (e) {
                    $('#modal-confirm-sync-error').removeClass('in').hide();
                })

                $('#modal-confirm-sync-error .btn-confirm-yes').on('click', function () {
                    oTable.draw(false);
                    Growl.success('Envio gravado com sucesso.');
                    $('#modal-remote-xl').modal('hide');
                })

            } else {
                Growl.error(data.feedback)
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


    $('#modal-remote-xl, #modal-remote-xlg').on('hidden.bs.modal', function () {
        $('.search-sender').autocomplete('dispose')
        $('.search-recipient').autocomplete('dispose')
        $('[name="sender_city"]').autocomplete('dispose')
        $('[name="recipient_city"]').autocomplete('dispose')
    })
});


/*========================================*/
/*============ FUNÇÕES GLOBAIS ===========*/
/*========================================*/

function openShpTab(tabId) {
    $('.form-shipment .tabbable-line .nav-tabs li, .form-shipment .tab-pane').removeClass('active');
    $('.form-shipment [href="'+tabId+'"]').closest('li').addClass('active');
    $(tabId).addClass('active')
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

function updateCustomer($this, update) {
    var $sender = $('#box-sender');
    var customerId = $this.val();
    var selectedVal = $this.find('option:selected');

    $('input[name=customer_id]').val(customerId);
    $('input[name=customer_km]').val(selectedVal.data('kms'));
    $sender.find('.has-error').remove();

    if (update) {

        if (IS_PICKUP == "1") {
            $('[name=agency_id]').val(selectedVal.data('agency')).trigger("change.select2");
            $('[name=recipient_agency_id]').val(selectedVal.data('agency')).trigger("change.select2");

            if(AUTOFILL_SENDER_INFO) {
                $('[name=recipient_name]').val(selectedVal.data('name'));
                $('[name=recipient_address]').val(selectedVal.data('address'));
                $('[name=recipient_zip_code]').val(selectedVal.data('zip_code'));
                $('[name=recipient_city]').val(selectedVal.data('city')).removeClass('has-error');
                $('[name=recipient_phone]').val(selectedVal.data('phone'));
                $('[name=recipient_vat]').val(selectedVal.data('vat'));
                $('[name=recipient_country]').val(selectedVal.data('country')).trigger("change.select2");

                if (selectedVal.data('obs')) {
                    $('[name="obs"]').val(selectedVal.data('obs'));
                }

                validatePhone($('[name=recipient_phone]'));
            }
        } else {
            $('[name=agency_id]').val(selectedVal.data('agency')).trigger("change.select2");
            $('[name=sender_agency_id]').val(selectedVal.data('agency')).trigger("change.select2");

            if (selectedVal.data('obs') && $('#modal-remote-xl [name=obs]').val() == '') {
                $('[name=obs]').val(selectedVal.data('data.obs'));
            }

            if(AUTOFILL_SENDER_INFO) {
                $('[name=sender_name]').val(selectedVal.data('name'));
                $('[name=sender_address]').val(selectedVal.data('address'));
                $('[name=sender_zip_code]').val(selectedVal.data('zip_code'));
                $('[name=sender_city]').val(selectedVal.data('city')).removeClass('has-error');
                $('[name=sender_phone]').val(selectedVal.data('phone'));
                $('[name=sender_vat]').val(selectedVal.data('vat'));
                $('[name=sender_country]').val(selectedVal.data('country')).trigger("change.select2");

                if (selectedVal.data('obs')) {
                    $('[name="obs"]').val(selectedVal.data('obs'));
                }

                validatePhone($('[name=sender_phone]'));
            }

            updateShipperName();
        }
    }
}

function resetModalCreateCustomer() {
    $('#modal-create-customer input').val('');
    $('#modal-create-customer select').val('').trigger('change.select2')
    $('#modal-create-customer select[name="payment_method"]').val('30d').trigger('change.select2')
    $('#modal-create-customer select[name="country"]').val('pt').trigger('change.select2')
    $('#modal-create-customer select[name="billing_country"]').val('pt').trigger('change.select2')
    $('#modal-create-customer select[name="default_invoice_type"]').val('invoice').trigger('change.select2')
}


function updateShipperName() {
    if($('[name="shipper_name"]').val() == '') { //shipper empty
        $('.modal .shipper-name').val($('[name="sender_name"]').val());
    } else {
        $('.modal .shipper-name').val($('[name="shipper_name"]').val());
    }

    if(ADICIONAL_ADDR_MODE == 'pro_fixed' || ADICIONAL_ADDR_MODE == 'pro') {
        updateAddressRowValues($('.addrs-container tr[data-target="#modal-main"]'));
    }
}

function updateReceiverName() {
    if($('[name="receiver_name"]').val() == '') { //receiver empty
        $('.modal .receiver-name').val($('[name="recipient_name"]').val());
    } else {
        $('.modal .receiver-name').val($('[name="receiver_name"]').val());
    }
}

function resetSender() {
    $('#modal-remote-xl [name="sender_name"]').val('');
    $('#modal-remote-xl [name="sender_address"]').val('');
    $('#modal-remote-xl [name="sender_zip_code"]').val('');
    $('#modal-remote-xl [name="sender_city"]').val('');
    $('#modal-remote-xl [name="sender_country"]').val('');
    $('#modal-remote-xl [name="sender_phone"]').val('');
    $('#modal-remote-xl [name="sender_agency_id"]').val('');
    $('#modal-remote-xl [name="sender_vat"]').val('');
    $('#modal-remote-xl [name="sender_attn"]').val('');
}
    
function resetRecipient() {
    $('#modal-remote-xl [name="recipient_name"]').val('');
    $('#modal-remote-xl [name="recipient_address"]').val('');
    $('#modal-remote-xl [name="recipient_zip_code"]').val('');
    $('#modal-remote-xl [name="recipient_city"]').val('');
    $('#modal-remote-xl [name="recipient_country"]').val('');
    $('#modal-remote-xl [name="recipient_phone"]').val('');
    $('#modal-remote-xl [name="recipient_agency_id"]').val('');
    $('#modal-remote-xl [name="recipient_vat"]').val('');
    $('#modal-remote-xl [name="recipient_attn"]').val('');
}

function resetDimensions(){
    $('.shipment-dimensions tbody').find("tr:gt(0)").remove()

    $target = $('.shipment-dimensions tbody tr:eq(0)');
    $target.find('[name="qty[]"]').val('1')
    $target.find('[name="box_description[]"]').val('')
    $target.find('[name="length[]"]').val('')
    $target.find('[name="width[]"]').val('')
    $target.find('[name="height[]"]').val('')
    $target.find('[name="box_weight[]"]').val('')
    $target.find('[name="box_adr_class[]"]').val('')
    $target.find('[name="box_adr_letter[]"]').val('')
    $target.find('[name="box_adr_number[]"]').val('')
    $target.find('[name="box_type[]"]').val('')
    $target.find('[name="dim_src[]"]').val('')
    $target.find('[name="sku[]"]').val('')
    $target.find('[name="product[]"]').val('')
    $target.find('[name="lote[]"]').val('')
    $target.find('[name="serial_no[]"]').val('')
    $target.find('[name="stock[]"]').val('');
}

function resetExpenses(){
    $('.shipment-dimensions tbody').find("tr:gt(0)").remove()
    $('#modal-shipment-expenses').find("[data-auto='1']").remove()

    $('#modal-shipment-expenses tbody tr').each(function(){
        $(this).find('[name="expense_id[]"]').val('').trigger('change.select2')
        $(this).find('[name="expense_billing_item[]"]').val('')
        $(this).find('[name="expense_qty[]"]').val('1')
        $(this).find('[name="expense_price[]"]').val('')
        $(this).find('[name="expense_subtotal[]"]').val('')
        $(this).find('[name="expense_cost_price[]"]').val('')
        $(this).find('[name="expense_cost_subtotal[]"]').val('')
        $(this).find('[name="expense_vat_rate_id[]"]').val('')
    })
}

function resetShipmentModal() {

    if(FULL_RESET) {
        $('#modal-' + STR_HASH_ID + ' [name="customer_id"]').val('').trigger('change.select2');
        $('#modal-' + STR_HASH_ID + ' [name="department_id"]').val('').trigger('change.select2');
        $('#modal-' + STR_HASH_ID + ' [name="service_id"]').val('').trigger('change.select2');
        $('#modal-' + STR_HASH_ID + ' [name="provider_id"]').val('').trigger('change.select2');

        $('#modal-' + STR_HASH_ID + ' [name="reference"]').val('');
        $('#modal-' + STR_HASH_ID + ' [name="reference2"]').val('');
        $('#modal-' + STR_HASH_ID + ' [name="reference3"]').val('');

        $('#modal-' + STR_HASH_ID + ' [name="operator_id"]').val('');
        $('#modal-' + STR_HASH_ID + ' [name="vehicle"]').val('');
        $('#modal-' + STR_HASH_ID + ' [name="trailer"]').val('');
    }

    $('#modal-' + STR_HASH_ID + ' [name="obs"]').val('');
    $('#modal-' + STR_HASH_ID + ' [name="obs_delivery"]').val('');
    $('#modal-' + STR_HASH_ID + ' [name="obs_internal"]').val('');
    $('#modal-' + STR_HASH_ID + ' [name="volumes"]').val('');
    $('#modal-' + STR_HASH_ID + ' [name="weight"]').val('');
    $('#modal-' + STR_HASH_ID + ' [name="fator_m3"]').val('');
    $('#modal-' + STR_HASH_ID + ' [name="kms"]').val('');
    $('#modal-' + STR_HASH_ID + ' [name="charge_price"]').val('');

    $('#modal-' + STR_HASH_ID + ' [name="zone"]').val('');
    $('#modal-' + STR_HASH_ID + ' [name="shipping_price"]').val('0.00');
    $('#modal-' + STR_HASH_ID + ' [name="expenses_price"]').val('0.00');
    $('#modal-' + STR_HASH_ID + ' [name="cost_price"]').val('0.00');
    $('#modal-' + STR_HASH_ID + ' [name="fuel_price"]').val('0.00');
    $('#modal-' + STR_HASH_ID + ' [name="fuel_tax"]').val('');
    $('#modal-' + STR_HASH_ID + ' [name="expenses_sum"]').val('0.00');
    $('#modal-' + STR_HASH_ID + ' [name="trkid"]').val('');

    $('.billing-subtotal, .billing-vat, .billing-total, .cost-billing-subtotal, .cost-billing-total, .cost-billing-vat, .billing-balance').html('0.00€')
    
    resetDimensions();
    resetExpenses();
    resetSender();
    resetRecipient();
    updateReceiverName();
    updateShipperName();
    disableRecipientEmail();
}

//calculate volume m3 for each dimensions row
function calcVolume(width, height, length, mesure) {
    width  = width  == "" ? 0 : width.replace(',', '.');
    length = length == "" ? 0 : length.replace(',', '.');
    height = height == "" ? 0 : height.replace(',', '.');

    if (mesure == 'cm') {
        return (parseFloat(width) * parseFloat(height) * parseFloat(length)) / 1000000; //convert from cm3 to m3
    } else {
        return (parseFloat(width) * parseFloat(height) * parseFloat(length));
    }
}

//VALIDATE TOTAL VOLUMES
function validateTotalVolumes() {

    var maxValue = $('.modal-xl [name=service_id]').find(':selected').data('max');
    var maxWeight = parseFloat($('.modal-xl [name=service_id]').find(':selected').data('max-weight'));
    var volumes = $('.modal-xl [name=volumes]').val();
    var weight = parseFloat($('.modal-xl [name=weight]').val());

    if (volumes > maxValue) {
        $('.helper-max-volumes').show();
        $('.modal-xl [name=volumes]').css('border-color', 'red');
        $('button[type=submit]').prop('disabled', true);
        $('.lbl-total-vol').html(maxValue);
    } else {
        $('.helper-max-volumes').hide();
        $('.modal-xl [name=volumes]').css('border-color', '#dddddd');
        $('button[type=submit]').prop('disabled', false);
    }

    if (weight > maxWeight) {
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

function calcDeliveryDate() {
    var shippingDate = $('#modal-' + STR_HASH_ID + ' [name="date"]').val();
    var shippingHour = $('#modal-' + STR_HASH_ID + ' [name="start_hour"]').val();
    var isCourier = parseInt($('#modal-' + STR_HASH_ID + ' [name="service_id"] option:selected').data('courier'));
    var serviceTransitTime = parseFloat($('#modal-' + STR_HASH_ID + ' [name="service_id"] option:selected').data('transit'));
    var serviceDeliveryHour = $('#modal-' + STR_HASH_ID + ' [name="service_id"] option:selected').data('delivery-hour');

    serviceTransitTime = isNaN(serviceTransitTime) || serviceTransitTime == 'undefined' ? 24.00 : serviceTransitTime;
    serviceDeliveryHour = serviceDeliveryHour == 'undefined' ? '' : serviceDeliveryHour;

    if (shippingHour != '') {
        shippingDate = shippingDate + ' ' + shippingHour + ":00";
    }

    //controla fins de semana (nao é estafeta e tempo de entrega superior a 24h)
    if (!isCourier && serviceTransitTime > 12.00) {
        var weekday = new Date(shippingDate).getDay();

        if (weekday == 5) { //recolha na sexta
            serviceTransitTime += 2 * 24; //3 dias x 24h
        } else if (weekday == 6) { //recolha ao sabado
            serviceTransitTime += 2 * 24;
        } else if (weekday == 0) { //recolha no domingo
            serviceTransitTime += 1 * 24;
        }
    }

    deliveryDate = Datetime.addHoursToDate(shippingDate, serviceTransitTime)
    deliveryDate = Datetime.toDateString(deliveryDate, 'Y-m-d H:i');

    deliveryDate = deliveryDate.split(' ');

    if (serviceDeliveryHour != '') {
        deliveryDate[1] = serviceDeliveryHour;
    }

    if (deliveryDate[1] == '00:00' && serviceDeliveryHour == '') {
        deliveryDate[1] = '';
    }

    if (serviceTransitTime < 24.00 && shippingHour == '') {
        deliveryDate[1] = '';
    }

    $('.modal [name="delivery_date"]').val(deliveryDate[0]);
    $('.modal [name="end_hour"]').val(deliveryDate[1]).trigger('change.select2');
}

function calcDeliveryHours(){

    var startDate = $('.form-shipment [name="date"]').val()+' '+$('.form-shipment [name="start_hour"]').val()+':00';
    var endDate   = $('.form-shipment [name="delivery_date"]').val()+' '+$('.form-shipment [name="end_hour"]').val()+':00';

    startDate = new Date(startDate);
    endDate   = new Date(endDate);

    var hours = Math.abs(startDate.getTime() - endDate.getTime()) / 3600000;
    hours = Math.ceil(hours);

    $('.form-shipment [name="hours"]').val(hours);

}

function insertOrUpdateExpenses(data) {

    var expenses = data.expenses

    //remove todas as linhas automaticas
    $('.table-expenses tr[data-auto="1"]').remove();

    if(expenses) {

        //percorre cada taxa e adiciona-a
        $.each(expenses, function(rowId, expenseRow){

            if(expenseRow.auto) {
                var $tr = $('.table-expenses tr:last').clone();

                $tr.find('.select2-container').remove();

                $tr.attr('data-auto', '1');
                $tr.find('[name="expense_auto[]"]').val(1);
                $tr.find('[name="expense_id[]"]').html('<option value="' + expenseRow.expense_id + '">' + expenseRow.name + '</option>');
                $tr.find('[name="expense_billing_item[]"]').val(expenseRow.billing_item_id);
                $tr.find('[name="expense_unity[]"]').val(expenseRow.unity);
                $tr.find('[name="expense_qty[]"]').val(expenseRow.qty);
                $tr.find('[name="expense_price[]"]').val(expenseRow.price);
                $tr.find('[name="expense_subtotal[]"]').val(expenseRow.subtotal);
                $tr.find('[name="expense_vat[]"]').val(expenseRow.vat);
                $tr.find('[name="expense_total[]"]').val(expenseRow.total);
                $tr.find('[name="expense_vat_rate_id[]"]').val(expenseRow.vat_rate_id).trigger('change.select2');
                $tr.find('[name="expense_provider_id[]"]').val(expenseRow.provider_id).trigger('change.select2');
                $tr.find('[name="expense_cost_price[]"]').val(expenseRow.cost_price);
                $tr.find('[name="expense_cost_subtotal[]"]').val(expenseRow.cost_subtotal);
                $tr.find('[name="expense_cost_vat[]"]').val(expenseRow.cost_vat);
                $tr.find('[name="expense_cost_total[]"]').val(expenseRow.cost_total);
                $tr.find('[name="expense_cost_vat_rate_id[]"]').val(expenseRow.cost_vat_rate_id)

                $tr.find('.select2').select2(Init.select2());
                $tr.find('.remove-expenses').remove();

                $('.table-expenses tr:first').after($tr)
            }
        });
    }
}

function insertOrUpdateFastExpense($obj) {
    var expenseValue = $obj.val();
    var expenseId    = $obj.data('id');
    var expenseTag   = $obj.data('tag');
    var expenseInput = $obj.data('input');
    var isCheckbox   = $obj.is(':checkbox')
    var isChecked    = $obj.is(':checked')
    var applyCost    = false;

    if(isCheckbox && !isChecked) {
        expenseValue = 0; //para poder eliminar a taxa caso seja uma checkbox porque o value é sempre igual
    }

    if(expenseTag == 'tolls') {
        applyCost = true
    }


    //verifica se as linhas estão todas preenchidas.
    //caso estejam, adiciona uma nova linha livre
    if($('#modal-shipment-expenses select[name="expense_id[]"] option[value=""]:selected').length == 0) {
        $('.btn-add-expenses').trigger('click');
    }

    $(document).find('#modal-shipment-expenses .row-expenses').each(function () {

        var $tr    = $(this);
        var curId  = $tr.find('[name="expense_id[]"]').val();
        var isAuto = $tr.data('auto');

        if(!isAuto) {

            if (curId != '') { //linha com despesa atribuida
                if (curId == expenseId) { //encontrou a despesa
                    if (expenseValue == '' || expenseValue == 0) {
                        //remove a linha porque valor vazio
                        $tr.remove();
                        $('.modal [name="agency_id"]').trigger('change'); //força atualizar preço
                    } else {
                        //atualiza o valor da linha
                        if(expenseInput == 'qty' || expenseInput == 'percent') {
                            $tr.find('[name="expense_qty[]"]').val(expenseValue)
                            $tr.find('[name="expense_id[]"]').val(expenseId).trigger('change')
                        } else if(expenseInput == 'money') {
                            $tr.find('[name="expense_id[]"]').val(expenseId)
                            $tr.find('[name="expense_qty[]"]').val(1);

                            if(applyCost) {
                                $tr.find('[name="expense_cost_price[]"]').val(expenseValue).trigger('change').trigger('focusout')
                            }

                            $tr.find('[name="expense_price[]"]').val(expenseValue).trigger('change').trigger('focusout')
                        }

                        $tr.append('<input type="hidden" name="is_fast_expense" value="1"/>'); //vai permitir disparar o pedido ajax de atualização global
                    }
                    return false;
                }
            } else {
                //não encontrou linhas preenchidas.

                if (expenseInput == 'qty' || expenseInput == 'percent') {
                    $tr.find('[name="expense_qty[]"]').val(expenseValue);
                    $tr.find('[name="expense_id[]"]').val(expenseId).prop('readonly', true).trigger('change')
                } else if (expenseInput == 'money') {

                    $tr.find('[name="expense_id[]"]').val(expenseId).trigger('change.select2')
                    $tr.find('[name="expense_qty[]"]').val(1);

                    if (applyCost) {
                        $tr.find('[name="expense_cost_price[]"]').val(expenseValue).trigger('change').trigger('focusout')
                    }

                    $tr.find('[name="expense_price[]"]').val(expenseValue).trigger('change').trigger('focusout')
                }

                $tr.append('<input type="hidden" name="is_fast_expense" value="1"/>'); //vai permitir disparar o pedido ajax de atualização global

                return false;
            }
        }
    })

    //dispara o atualizador de preços total após terminar o pedido ajax para inserir a linha da taxa
    /*$(document).ajaxSuccess(function(event, jqXHR, settings) {
        url = settings.url;

        if (settings.type == 'POST' && url.includes('/admin/shipments/expenses/')) {
            console.log(settings);
            $('.modal-shipment [name="agency_id"]').trigger('change');
        }
    });*/
}

//Atualiza os valores totais de uma linha de taxas
/**
 * COMENTADO A 26/10/2023
 * --
 * O backend agora encarrega-se calcular as alterações feitas à taxa
 */
// function updateExpenseRow($tr) {
//     var qty     = parseInt($tr.find('[name="expense_qty[]"]').val());
//     var price   = parseFloat($tr.find('[name="expense_price[]"]').val());
//     var cost    = parseFloat($tr.find('[name="expense_cost_price[]"]').val());
//     var vatRate = $tr.find('[name="expense_vat_rate_id[]"]').val();
//     var unity   = $tr.find('[name="expense_unity[]"]').val();

//     if(unity == 'percent') {
//         //se a taxa é por percentagem, força a sincronização via ajax, para recalcular os valores corretos
//         $tr.find('.update-expenses').trigger('click');
//     } else {

//         if(!vatRate) {
//             vatRate = $('.modal [name="vat_rate"]').val(); //obtem a taxa de iva geral do envio
//         }

//         vatRate = VAT_RATES_VALUES[vatRate];

//         if(!vatRate) {
//             vatRate = 0.23;
//         } else {
//             vatRate = parseFloat(vatRate) / 100;
//         }

//         qty      = isNaN(qty) ? 1 : qty;
//         price    = isNaN(price) ? 0 : price;
//         cost     = isNaN(cost) ? 0 : cost;

//         if(unity == 'percent') {

//             oldPercent   = 5/100;
//             percentValue = price / 100;
//             oldSubtotal  = parseFloat($tr.find('[name="expense_subtotal[]"]').val());

//             //para saber o subtotal, faz uma regra de 3 simples
//             //entre o valor anterior e o valor novo
//             subtotal = (oldSubtotal * percentValue) / oldPercent;
//         } else {
//             subtotal = qty * price;
//         }

//         total    = subtotal * (1 + vatRate)
//         vat      = total - subtotal

//         costSubtotal = qty * cost;
//         costTotal    = costSubtotal * (1 + vatRate)
//         costVat      = costTotal - costSubtotal

//         $tr.find('[name="expense_subtotal[]"]').val(round(subtotal).toFixed(2));
//         $tr.find('[name="expense_vat[]"]').val(round(vat).toFixed(2));
//         $tr.find('[name="expense_total[]"]').val(round(total).toFixed(2));
//         $tr.find('[name="expense_cost_price[]"]').val(round(cost).toFixed(2));
//         $tr.find('[name="expense_cost_subtotal[]"]').val(round(costSubtotal).toFixed(2));
//         $tr.find('[name="expense_cost_vat[]"]').val(round(costVat).toFixed(2));
//         $tr.find('[name="expense_cost_total[]"]').val(round(costTotal).toFixed(2));
//     }
// }

//atualiza o valor total de taxas

function updateExpensesTotal() {
    var subtotal = 0;
    var vat = 0;
    var total = 0;
    var costSubtotal = 0;
    var costVat = 0;
    var costTotal = 0;

    $('.table-expenses [name="expense_id[]"]').each(function(){

        var $tr = $(this).closest('tr');

        if($(this).val() != '') {

            var rowSubtotal     = parseFloat($tr.find('[name="expense_subtotal[]"]').val());
            var rowVat          = parseFloat($tr.find('[name="expense_vat[]"]').val());
            var rowTotal        = parseFloat($tr.find('[name="expense_total[]"]').val());
            var rowCostSubtotal = parseFloat($tr.find('[name="expense_cost_subtotal[]"]').val());
            var rowCostVat      = parseFloat($tr.find('[name="expense_cost_vat[]"]').val());
            var rowCostTotal    = parseFloat($tr.find('[name="expense_cost_total[]"]').val());

            subtotal     += isNaN(rowSubtotal) ? 0 : rowSubtotal;
            vat          += isNaN(rowVat) ? 0 : rowVat;
            total        += isNaN(rowTotal) ? 0 : rowTotal;
            costSubtotal += isNaN(rowCostSubtotal) ? 0 : rowCostSubtotal;
            costVat      += isNaN(rowCostVat) ? 0 : rowCostVat;
            costTotal    += isNaN(rowCostTotal) ? 0 : rowCostTotal;
        }
    })

    var fuelPrice = parseFloat($('[name="fuel_price"]').val());
    fuelPrice      = isNaN(fuelPrice) ? 0 : fuelPrice;

    $('.modal [name="expenses_subtotal"]').val(subtotal);
    $('.modal [name="expenses_vat"]').val(vat);
    $('.modal [name="expenses_total"]').val(total);

    $('.modal [name="expenses_cost_subtotal"]').val(costSubtotal)
    $('.modal [name="expenses_cost_vat"]').val(costVat)
    $('.modal [name="expenses_cost_total"]').val(costTotal)

    $('.expenses-subtotal').html(round(subtotal).toFixed(2))
    $('.expenses-vat').html(round(vat).toFixed(2))
    $('.expenses-total').html(round(total).toFixed(2))
}

function enablePudoFields($obj) {

    var providerId   = $('#modal-remote-xl [name="provider_id"]').val();
    var providerName = $('#modal-remote-xl [name="provider_id"] option:selected').text();

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

    if (providerId != '') {
        $target.find('.pudo-loading').show();
        //obtem os pontos PUDO
        $.post(ROUTE_GET_PUDOS, { providerId: providerId }, function (data) {

            if (data.length === 0) {
                $target.find('.pudo-error').html('<span class="text-red">O fornecedor ' + providerName + ' não tem pontos de entrega disponíveis.</span>').show()
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
    } else {
        $target.find('.pudo-error').html('<span class="text-red">Não é possível obter os dados. Tem de escolher um fornecedor da lista.</span>').show()
    }
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
}

function filterServicesList(servicesArr) {

    if (servicesArr) {
        if ($.inArray(parseInt($('.modal [name=service_id]').val()), servicesArr) === -1) {
            $('[name=service_id]').val('').trigger('change.select2'); //remove serviço selecionado
        }

        var countEnabled = 0;
        $('.modal [name=service_id] option').each(function (item) {
            if ($.inArray(parseInt($(this).val()), servicesArr) !== -1) {
                $(this).prop('disabled', false);
                countEnabled++;
            } else {
                $(this).prop('disabled', 'disabled');
            }
        })

        if (countEnabled == 1) {
            $('.modal [name=service_id] option:not(:disabled)').prop('selected', true); //seleciona automatico se apenas 1 opção disponível
        }

    } else {
        $('.modal [name=service_id] option').each(function () {
            $(this).prop('disabled', false);
        });
    }

    $(document).find('.modal [name=service_id]').select2(Init.select2());
}

function setAgency(data, target) {

    multipleCities = true;
    autoKm      = true;
    zipCodeKms  = isNaN(data.kms) || data.kms == null ? 0 : parseFloat(data.kms);
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
    //$inputCity.val('').closest('.form-group').removeClass('has-error');
    $inputCity.closest('.form-group').removeClass('has-error');
    $inputZipCode.removeClass('has-error');
    $inputZipCode.closest('.form-group').removeClass('has-error')
    $inputCountry.val(data.country).trigger("change.select2");
    $inputAgency.html(data.agenciesHtml);

    if(!SHIPMENT_CALC_AUTO_KM) {
        $inputKms.val(totalKms);
    }

    validateZipCode(target);

    //verifica se a localidade escrita pertence às localidades possiveis
    if(data.cities.length > 1) {
        cityName = $inputCity.val().toLowerCase();
        cityNameValid = false;
        $.each(data.cities, function(key, value) {
            if(cityName == value.value.toLowerCase()) {
                cityNameValid = true;
                return;
            }
        });

        if(!cityNameValid) {
            $inputCity.val(''); //limpa campo da localidade se localidade não é igual a uma das strings da lista
        }
    }

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
        autoKm = true;
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

    getAutoKm();

    toggleStateField(data.states_select, data.state, target + '_state');
}

function getAgencyAdicionalAddress(target) {
    var $inputZipCode  = target.find('.zip-code');
    var $inputCity     = target.find('.shcity');
    var $inputCountry  = target.find('.select2-country');
    var $inputAgency   = target.find('.shagency');
    var zipCode        = $inputZipCode.val();
    var country        = $inputCountry.val();
    var multipleCities = true;

    var fields  = $('.form-shipment').find('[name!=_method]').serialize()
    fields+= '&country='+country+'&zip_code='+zipCode;

    target.find('[for="recipient_address"] i').removeClass('hide');

    $.post(ROUTE_GET_AGENCY, fields, function(data){

        if(data) {
            target.find('.select2-country').val(data.agency_id).trigger('change.select2');
            target.find('.select2-country').val(data.country).trigger('change.select2');

            //valida codigo postal
            $inputZipCode.removeClass('has-error');
            $inputZipCode.closest('.form-group').removeClass('has-error')
            $inputCity.closest('.form-group').removeClass('has-error');
            $inputAgency.html(data.agenciesHtml);

            validateZipCode(target);

            if($inputZipCode.val() != '' && $inputCountry.val() != '') {
                //valida o codigo postal
                if (!ZipCode.validate($inputCountry.val(), $inputZipCode.val())) {
                    $inputZipCode.addClass('has-error');
                    $inputZipCode.closest('.form-group').addClass('has-error');
                }
            }

            //preenche localidade
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

            //força agencia
            if (data.agency_id) {
                $inputAgency.val(data.agency_id).trigger('change.select2');
            } else {
                $inputAgency.val($('.modal [name="agency_id"]').val()).trigger("change.select2");
            }
        }

    }).always(function(){
        target.find('[for="recipient_address"] i').addClass('hide');
    })
}

function validateZipCode(target){

    if(typeof target == 'string') {
        $inputZipCode = $('.modal [name="'+target+'_zip_code"]');
        $inputCity    = $('.modal [name="'+target+'_city"]');
        $inputCountry = $('.modal [name="'+target+'_country"]');
    } else {
        $inputZipCode = $(target).find('.zip-code');
        $inputCity    = $(target).find('.shcity');
        $inputCountry = $(target).find('.select2-country');
    }
   
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

function getAutoKm() {

    if (SHIPMENT_CALC_AUTO_KM
        && $('.modal-xl .btn-auto-km').length
        && $('[name=service_id] option:selected').data('unity') == 'km') {


        var triangulation = $('.modal-xl [name="km_agency"]:checked').length

        var returnBack = 0; //$('.modal-xl [value="rpack"]:checked').length; //quando ha retorno nao precisa de duplicar a distancia porque o envio gerado já vai ter a distancia
        if(SHIPMENT_KM_RETURN_BACK) {
            returnBack = 1;
        }

        var agencyZp = $.trim($('.modal-xl [name="agency_zp"]').val());
        var agencyCity = $.trim($('.modal-xl [name="agency_city"]').val());

        var originZp = $.trim($('.modal-xl [name="sender_zip_code"]').val());
        var originCity = $.trim($('.modal-xl [name="sender_city"]').val());
        var originCountry = $.trim($('.modal-xl [name="sender_country"]:selected').text())

        var destZp = $.trim($('.modal-xl [name="recipient_zip_code"]').val());
        var destCity = $.trim($('.modal-xl [name="recipient_city"]').val());
        var destCountry = $.trim($('.modal-xl [name="recipient_country"]:selected').text());

        /*   if(!triangulation) {
               originZp = agencyZp == '' ? originZp : agencyZp;
               originCity = agencyCity == '' ? originCity : agencyCity;
           }*/

        originCountry = originCountry == '' ? 'Portugal' : originCountry;
        destCountry = destCountry == '' ? 'Portugal' : destCountry

        var origin = originZp + ' ' + originCity + ',' + originCountry;
        var destination = destZp + ' ' + destCity + ',' + destCountry;
        var agency = agencyZp + ' ' + agencyCity + ',pt';

        /*if(distance_from_agency) {
            origin = agency;
        }*/

        if (originZp != '' && originCity != '' && destZp != '' && destCity != '') {

            var $icon = $('.modal-xl .btn-auto-km').find('.fas');
            $icon.addClass('fa-spin');


            $.get(ROUTE_GET_DISTANCE_KM, {
                source: SOURCE,
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
                $icon.removeClass('fa-spin')
            })
        }
    }
}

function toggleStateField(objList, defaultState, target) {

    if (Object.keys(objList).length >= 1) {
        $('.row-state > div').removeClass('col-sm-12').addClass('col-sm-8');
        $('.row-state .col-sm-4').removeClass('hide');
        $('.modal [name="' + target + '"]').select2('destroy').empty().select2({ data: objList }).val(defaultState);
        $('.modal [name="' + target + '"]').prop('required', true).trigger('select2:select')
        $('.select2-selection__rendered').val();
    } else {
        $('.row-state > div').removeClass('col-sm-8').addClass('col-sm-12');
        $('.row-state .col-sm-4').addClass('hide');
        $('.modal [name="' + target + '"]').prop('required', false).empty();
    }
}

function calcPricePerTon() {

    var price_fixed = $('.modal-xl [name="price_fixed"]:checked').length;
    var price_blocked = $('.modal-xl [name="shipping_price"]').is('[readonly]');
    var kgUnity = $('.modal-xl [name="price_kg_unity"]').val();

    if (kgUnity == 'kg') {
        kgUnity = 1;
    } else {
        kgUnity = 1000;
    }

    if ($('#modal-' + STR_HASH_ID + ' [name="price_kg"]').length && !price_fixed && !price_blocked) {
        var price = parseFloat($('#modal-' + STR_HASH_ID + ' [name="price_kg"]').val());
        var weight = parseFloat($('#modal-' + STR_HASH_ID + ' [name="weight"]').val());
        totalPrice = price * (weight / kgUnity);
        totalPrice = isNaN(totalPrice) ? 0.00 : totalPrice;

        $('#modal-' + STR_HASH_ID + ' [name="shipping_price"]').val(round(totalPrice).toFixed(2));
    }
}

function countTotalQtyDimensions() {

    if(ADICIONAL_ADDR_MODE == 'pro' || ADICIONAL_ADDR_MODE == 'pro_fixed') {
        $tr = $('.shipment-dimensions .visible [name="qty[]"]');
    } else {
        $tr = $('.shipment-dimensions [name="qty[]"]');
    }

    var totalQty = 0;
    $tr.each(function () {
        var $tr    = $(this).closest('tr');
        var desc   = $tr.find('[name="box_description[]"]').val()
        var weight = $tr.find('[name="box_weight[]"]').val()
        var m3     = $tr.find('[name="fator_m3_row[]"]').val()

        if(m3 != '' || weight != '' || desc != '') { //so considera linha valida se tiver dimensoes ou peso ou descricao
            qty = parseInt($(this).val());
            totalQty += qty;
        }
    })

    return totalQty;
}

function prefillProviderWeight(originalWeight) {
    var weight = parseFloat(originalWeight);
    var kg = weight;

    if(weight < 2.00) {
        kg = 1.00;
    } else if(weight >= 2.00 && weight <= 5.00) {
        kg = 2.00;
    } else if(weight >= 6.00 && weight < 10.00) {
        kg = 5.00;
    } else if(weight >= 10.00 && weight < 15.00) {
        kg = 10.00;
    } else if(weight >= 15.00 && weight < 20.00) {
        kg = 15.00;
    } else if(weight >= 20.00 && weight < 25.00) {
        kg = 20.00;
    } else if(weight >= 25.00 && weight < 30.00) {
        kg = 25.00;
    } else if(weight >= 30.00 && weight < 35.00) {
        kg = 30.00;
    } else if(weight >= 25.00 && weight < 30.00) {
        kg = 25.00;
    } else {
        kg = weight - 10;
    }

    $('[name="provider_weight"]').val(kg.toFixed(2))
}

function makeShipmentRoute() {

    //limpa markers
    for (var i = 0; i < shpMarkers.length; i++) {
        shpMarkers[i].setMap(null);
    }

    var loadingHtml = ' <ul><li class="dlvr-loading text-center p-t-10">' +
        '<i class="fas fa-spin fa-circle-notch"></i> A calcular trajeto...' +
        '</li></ul>'
    $('.shp-dlvr-route').html(loadingHtml);

    var fields = $('.form-shipment').find('[name!=_method]').serialize();
    fields+= '&return_back='+$('.shp-dlvr-route-options [name="return_back"]').is(':checked')
    fields+= '&optimize='+$('.shp-dlvr-route-options [name="optimize"]').is(':checked')
    fields+= '&waypoint_agency='+$('.shp-dlvr-route-options [name="waypoint_agency"]').is(':checked')

    $.post(ROUTE_OPTIMIZE_DELIVERY, fields, function(data){
        if(data.result) {
            $('.shp-dlvr-route').html(data.html);
            $('.total-distance').html(data.distance);

            if(data.fuel_price > 0.00) {
                $('.shp-dlvr-fuel-no').hide();
                $('.shp-dlvr-fuel-yes').show();
                $('.dlvr-liters').html(data.fuel_liters);
                $('.dlvr-price-liter').html(data.fuel_price_liter);
                $('.dlvr-fuel-price').html(data.fuel_price);
                $('.dlvr-vehicle-consumption').html(data.fuel_consumption);
            } else {
                $('.shp-dlvr-fuel-no').show();
                $('.shp-dlvr-fuel-yes').hide();
            }
            setDeliveryMapMarkers()
        } else {
            $('.shp-dlvr-route').html('<ul><li style="border:none;" class="text-center text-red m-t-10">' +
                '<i class="fas fa-exclamation-triangle"></i> ' + data.feedback +
                '</li></ul>');
        }
    }).fail(function(){
        Growl.error500();
        $('.shp-dlvr-route').html('<ul><li class="text-center p-t-10 text-red">' +
            '<i class="fas fa-exclamation-triangle"></i> Não foi possível calcular trajeto.' +
            '</li></ul>');
    })

}

function setDeliveryMapMarkers() {

    var bounds     = new google.maps.LatLngBounds();
    var infowindow = new google.maps.InfoWindow();

    $(document).find('.shp-dlvr-route li').each(function(index){

        address   = $(this).find('input').val();
        markerId  = 'mrk'+$(this).data('id');
        latitude  = $(this).data('lat');
        longitude = $(this).data('lng');

        var marker = new google.maps.Marker({
            id: markerId,
            position: new google.maps.LatLng(latitude, longitude),
            map: shpmap
        });

        shpMarkers.push(marker);

        google.maps.event.addListener(marker, 'click', (function (marker) {
            return function () {
                infowindow.setContent(address);
                infowindow.open(shpmap, marker);
            }
        })(marker, markerId));

        //extend the bounds to include each marker's position
        bounds.extend(marker.position);

        //now fit the map to the newly inclusive bounds
        shpmap.fitBounds(bounds);
    });

    //(optional) restore the zoom level after the map is done scaling
    var listener = google.maps.event.addListener(shpmap, "idle", function () {
        shpmap.setZoom(12);
        google.maps.event.removeListener(listener);
    });

    initDeliveryDirections();
}

function initDeliveryDirections(){

    directionsDisplay.setMap(shpmap);

    var avoidHighways = !$('.shp-dlvr-route-options [name="avoid_highways"]').is(':checked');
    var avoidTolls    = !$('.shp-dlvr-route-options [name="avoid_tolls"]').is(':checked');
    var returnBack    = $('.shp-dlvr-route-options [name="return_back"]').is(':checked');

    var waypts    = [];
    var locations = []

    $(document).find('.shp-dlvr-route ul li').each(function (index) {
        lat = $(this).data('lat');
        lng = $(this).data('lng');

        if(lat!="" && lng != "") {
            locations.push(['title', lat,lng])
        }
    })

    var lastLocation = locations.length-1;
    for (i = 1; i < lastLocation; i++) {

        if (!waypts) {
            waypts = [];
        }

        waypts.push({
            location: new google.maps.LatLng(locations[i][1], locations[i][2]),
            stopover: true
        });
    }

    originAddress      = new google.maps.LatLng(locations[0][1],locations[0][2]);
    destinationAddress = new google.maps.LatLng(locations[lastLocation][1],locations[lastLocation][2]);

    /*if(returnBack) {

        waypts.push({
            location: destinationAddress,
            stopover: true
        });

        destinationAddress = originAddress;
    }*/

    directionsService.route({
        origin: originAddress,
        destination: destinationAddress,
        waypoints: waypts,
        optimizeWaypoints: false, //reorganiza para melhor rota
        avoidHighways: avoidHighways,
        avoidTolls: avoidTolls,
        travelMode: 'DRIVING'
    }, function (response, status) {

        if (status === 'OK') {
            directionsDisplay.setDirections(response);

            var fuelPriceLiter  = parseFloat($('.dlvr-price-liter').html());
            var fuelConsumption = parseFloat($('.dlvr-vehicle-consumption').html());
            var totalDistance = 0;
            var totalDuration = 0;
            var deliveryList = '';
            var legs = response.routes[0].legs;
            for (var i = 0; i < legs.length; ++i) {
                deliveryList+='<li>'+legs[i].end_address+'</li>';
                totalDistance += legs[i].distance.value;
                totalDuration += legs[i].duration.value;
            }

            totalDistance = totalDistance / 1000
            $('.ordered-route-list').html('<ul>' + deliveryList + '</ul>')
            $('.total-distance').html(totalDistance.toFixed(2) + ' km')
            $('.total-time').html(secondsTimeSpanToHMS(totalDuration.toFixed(2)))

            if($('.shp-dlvr-fuel-yes').is(':visible')) {

                fuelPriceLiter  = isNaN(fuelPriceLiter) ? 0 : fuelPriceLiter;
                fuelConsumption = isNaN(fuelConsumption) ? 0 : fuelConsumption;
                fuelLiters = (totalDistance*fuelConsumption) / 100;
                fuelPrice  = round(fuelLiters * fuelPriceLiter, 2);
                $('.dlvr-liters').html(fuelLiters.toFixed(2))
                $('.dlvr-fuel-price').html(fuelPrice.toFixed(2))
            }
        } else {
            window.alert('Directions request failed due to ' + status);
        }
    });
}

function hasEmptyFields($form) {

    emptyFields = $form.find("[required]").filter(function () {
        return !$(this).val();
    });

    countEmptyFields = emptyFields.length;

    emptyFields.each(function(){
        $(this).addClass('has-error')
        if($(this).is('select')) {
            $(this).next().addClass('has-error')
        }
    })

    return countEmptyFields;
}

function secondsTimeSpanToHMS(s) {
    var h = Math.floor(s/3600); //Get whole hours
    s -= h*3600;
    var m = Math.floor(s/60); //Get remaining minutes
    s -= m*60;
    return h+"h"+(m < 10 ? '0'+m : m); //zero padding on minutes and seconds
}

function addTag(tagName) {
    var tags = $('.modal [name="tags"]').val();

    if(!tags.includes(tagName)) {
        tags+=','+tagName;
        $('.modal [name="tags"]').val(tags);
    }
}

function removeTag(tagName) {
    var tags = $('.modal [name="tags"]').val();
    if(tags.includes(tagName)) {
        tags = tags.split(',');


        const index = tags.indexOf(tagName);
        if (index > -1) { // only splice array when item is found
            tags.splice(index, 1); // 2nd parameter means remove one item only
        }

        $('.modal [name="tags"]').val(tags.join(','));
    }
}


//add new address row
function addAddressRow() {
    var hash       = Str.random(5)
    var $targetObj = $('.table-addrs tr[data-target="#modal-main"]').clone(); //clone linha master

    $targetObj.addClass(hash);
    
    $targetObj.find('[data-action="del-addr"]').removeClass('hide');
    $targetObj.removeAttr('data-id');
    $targetObj.attr('data-target','#modal-'+hash);
    $targetObj.attr('data-hash', hash);
    $targetObj.find('.modal-new-addr').attr('id','modal-'+hash);
    
    $targetObj.find('input, select, textarea').each(function (item) {
        $(this).attr('name', 'addr[' + hash + '][' + $(this).attr('name') + ']');
        $(this).val('')
    });

    $targetObj.find('td').not('td:last').html('');
    $targetObj.find('td:first').html('<i>Clique para adicionar...</i>');
    $targetObj.find('.select2-country').val('pt').trigger('change.select2')
    $targetObj.find('.search-sender').autocomplete(SEARCH_SENDER_OPTIONS)
    $targetObj.find('.search-recipient').autocomplete(SEARCH_RECIPIENT_OPTIONS);

    $targetObj.find('.select2-container').remove();
    $targetObj.find('.select2').select2(Init.select2())
    $targetObj.find('.select2-country').select2(Init.select2Country())
    $targetObj.find('.datepicker').datepicker(Init.datepicker())

    $('.addrs-container table tbody').append($targetObj.append());

    updateCountDischarges()
    
    openAddressRowModal($('.addrs-container table tbody tr:last'))
}

//seleciona uma linha de morada
function selectAddressRow($tr) {
    $('.row-destinations-advanced tr').removeClass('active');
    $tr.addClass('active');

    var hash   = $tr.attr('data-hash');
    var $modal = $tr.find('.modal-new-addr');

    //preenche na janela principal os valores dinamicos de acordo com os input
    $modal.find('[main-modal]').each(function(){
        var className = $(this).attr('main-modal');
        $(className).val($(this).val());
    })

    $('.row-destinations-advanced tr').removeClass('active');
    $tr.addClass('active');

    showDimensionsRows(hash)
}

//update row values
function updateAddressRowValues($tr){

    var senderAddr = '<i class="flag-icon flag-icon-'+$tr.find('.box-sender-content .select2-country').val()+'"></i> ';
        senderAddr+= $tr.find('.box-sender-content .zip-code').val() + ' ';
        senderAddr+= $tr.find('.box-sender-content .shcity').val();

    var recipientAddr = '<i class="flag-icon flag-icon-'+$tr.find('.box-recipient-content .select2-country').val()+'"></i> ';
        recipientAddr+=  $tr.find('.box-recipient-content .zip-code').val() + ' ';
        recipientAddr+= $tr.find('.box-recipient-content .shcity').val();

    var fm3     = $tr.find('.input-fm3').val() ? parseFloat($tr.find('.input-fm3').val()).toFixed(3) : '0.000';
    var weight  = $tr.find('.input-kg').val() ? $tr.find('.input-kg').val() : '0.00';
    var ldm     = $tr.find('.input-ldm').val() ? $tr.find('.input-ldm').val() : '0.000';
    var date    = $tr.find('.input-date').val() ? $tr.find('.input-date').val() : $('#modal-remote-xl [name="date"]').val();
    var ref     = $tr.find('.input-ref').val() + ($tr.find('.input-ref2').val() ? '<br/>' +$tr.find('.input-ref2').val() : '')
    
    $tr.find('.addr-sname').html($tr.find('.box-sender-content .shname').val());
    $tr.find('.addr-saddr').html(senderAddr);
    $tr.find('.addr-rname').html($tr.find('.box-recipient-content .shname').val());
    $tr.find('.addr-raddr').html(recipientAddr);
    $tr.find('.addr-vol').html($tr.find('.input-vol').val());
    $tr.find('.addr-kg').html(weight);
    $tr.find('.addr-ldm').html(ldm);
    $tr.find('.addr-fm3').html(fm3);
    $tr.find('.addr-date').html(date);
    $tr.find('.addr-ref').html(ref);

    if(ADICIONAL_ADDR_MODE == 'pro' && $(document).find('tr[data-target]').length > 1 ){
        $('[data-action="rem-addr"]').addClass('hide');
    }
}

//copia uma linha de morada
function copyAddressRow($tr) {
    $clone = $tr.clone();
    refactorFieldsName($clone);
    $clone.removeClass('active')
    $clone.removeAttr('data-id');
    $clone.find('[data-action="del-addr"]').removeClass('hide');
    $clone.find('.input-id').val('')
    $clone.insertAfter($tr);

    updateCountDischarges();

    if($(document).find('tr[data-target]').length == 1){
        $('[data-action="rem-addr"]').addClass('hide');
    }
    
}

//remove uma linha de morada
function removeAddressRow($tr) {
    if($(document).find('tr[data-target]').length > 1) {
        var id   = $tr.data('id');
        var hash = $tr.attr('data-hash', hash);
        id = id == 'undefined' ? null : id;

        if(!$('.form-shipment [name="deleted_addrs"]').length) {
            $('.form-shipment').append('<input type="hidden" name="deleted_addrs" value="">');
        } 

        if(id) {
            deletedIds = $('.form-shipment [name="deleted_addrs"]').val() + "," + id;
            $('.form-shipment [name="deleted_addrs"]').val(deletedIds);    
        }
    
        $('[data-action="rem-addr"]').addClass('hide');
        
        $tr.remove();

        removeDimensionsRows(hash);
        updateCountDischarges();
    }
    
    if($(document).find('tr[data-target]').length == 1){
        $('[data-action="rem-addr"]').removeClass('hide');
    }
}

function updateCountDischarges() {

    var countDischarges = $(document).find('.form-shipment .table-addrs tr[data-target]').length
    $('.form-shipment [name="count_discharges"]').val(countDischarges);
}

//Abre a modal de edição de uma linha
function openAddressRowModal($tr) {
    selectAddressRow($tr);
    $tr.find('.modal').addClass('in').show();
}

//refactor address fields name
//a função corre as linhas de morada (excepto a primeira - master) e corrige o nome de todos os input para addr[xxxx][nomecampo]
function refactorFieldsName($tr) {
    var hash = Str.random(5);
    $tr.attr('data-hash', hash);

    $tr.find('input, select, textarea').each(function (item) {
        var name = getRealFieldName($(this).attr('name'));
        $(this).attr('name', 'addr[' + hash + '][' + name + ']');
    });
}

//obtem o nome real do campo input, caso o campo seja no formato addr[xxxx][nomecampo]
function getRealFieldName(str) {
    var regex = /\[([^\]]+)\]/g;
    var matches = str.match(regex);

    if (matches && matches.length > 0) {
        // O último elemento do array "matches" conterá o texto entre parênteses mais externo
        var ultimoNome = matches[matches.length - 1].slice(1, -1); // Remove os parênteses
        return ultimoNome;
    } else {
        return str; // Não foram encontrados parênteses na string
    }
}

function multipleAddrModeDisableOriginalFields() {
    //adiciona underscore antes do nome dos campos da modal principal para impedir gravar esses dados
    $('.vols-panel input, .vols-panel select, .refs-panel input, .nav-obs textarea:not([name="obs_internal"])').each(function(){ 
        var name = "__" + $(this).attr('name');
        $(this).attr('name', name);
    })

    //ativa os campos da janela de multiplas moradas
    $('.row-destinations-advanced tr[data-target="#modal-main"] input, .row-destinations-advanced tr[data-target="#modal-main"] select, .row-destinations-advanced tr[data-target="#modal-main"] textarea').each(function(){ 
        var name = $(this).attr('name').replace("__", "");
        $(this).attr('name', name).prop('disabled', false); //desativa para não ter obrigar o required
    })
}

function multipleAddrModeEnableOriginalFields() {
    //remove underscore antes do nome para permitir que os campos originais voltem a ficar com o nome original novamente possam ser gravados
    $('.vols-panel input, .vols-panel select, .refs-panel input, .nav-obs textarea:not([name="obs_internal"])').each(function(){ 
        var name = $(this).attr('name').replace("__", "");
        $(this).attr('name', name);
    })
    
    //desativa campos da janela de multiplas moradas
    $('.row-destinations-advanced tr[data-target="#modal-main"] input, .row-destinations-advanced tr[data-target="#modal-main"] select, .row-destinations-advanced tr[data-target="#modal-main"] textarea').each(function(){ 
        var name = "__" + $(this).attr('name');
        $(this).attr('name', name).prop('disabled', true);
    })
}

function initAddrDimensions() {

    //mostra dimensoes mediante cada 
    if(ADICIONAL_ADDR_MODE == 'pro_fixed' || ADICIONAL_ADDR_MODE == 'pro') {
        $('.table-addrs tr[data-target]:not([data-target="#modal-main"])').each(function(){ //percorre cada linha adicional
            $tr = $(this);

            var shipmentId = $tr.attr('data-id');
            var hash       = $tr.data('hash');
        
            hash = hash == 'undefinied' || typeof hash == 'undefined' ? '' : hash;

            $('.shipment-dimensions tbody tr[data-addr-id='+shipmentId+']').attr('data-hash', hash); //coloca a hash de acordo com o shipmentId da dimensão
            $('.shipment-dimensions tbody tr[data-addr-id='+shipmentId+']').find('[name="dim_src[]"]').val(hash)
            $tr.attr('data-hash', hash);
        });

        $('.shipment-dimensions tbody tr').removeClass('visible').hide();
        $('.shipment-dimensions tbody tr[data-hash="master"]').addClass('visible').show(); //mostra por defeito a master
    }
}

function initAddrModeProFixed() {

    var multipleAddrVisible = !$('.row-destinations-default').is(':visible');

    //quando a janela inicializa, o nome dos input fiedls de cada linha está todo igual.
    //força todos os input das linhas adicionais de morada a estarem normalizados no formato addr[xxxx][nomecamo]
    //só não altera o campo da linha considerada main.
    $('.table-addrs tr[data-target]:not([data-target="#modal-main"])').each(function(){
        $tr = $(this);
        refactorFieldsName($tr);
    });

    initAddrDimensions();

    if(multipleAddrVisible) { //se está o modo de multiplas moradas ativo e visivel
        multipleAddrModeDisableOriginalFields(); //desativa os campos originais
    } else {
        multipleAddrModeEnableOriginalFields();
    }
    

    //inicia movimentação de linhas para cima e para baixo pelo teclado
    $(document).on('keyup', function(e){
        var countRows = $(document).find('.table-addrs tr').length;
        $trActive = $(document).find('.table-addrs tr.active');
        $trPrev   = $trActive.prev();
        $trNext   = $trActive.next();

        if (e.keyCode === 38) { // Tecla para cima
            if($trPrev.index() >= 1) {
                $trPrev.find('td:first').trigger('click');
            }
        } else if (e.keyCode === 40) { // Tecla para baixo
            if($trNext.index() > 0 && $trNext.index() < countRows) {
                $trNext.find('td:first').trigger('click');
            }
        }
    })
}

function showDimensionsRows(hash) {
    hash = typeof hash == 'undefined' ? 'master' : hash;
    $('.shipment-dimensions tbody tr').removeClass('visible').hide();
    $('.shipment-dimensions tbody tr[data-hash="'+hash+'"]').addClass('visible').show();
}

function removeDimensionsRows(hash) {
    $('.shipment-dimensions tbody tr[data-hash="'+hash+'"]').remove();
}
}
