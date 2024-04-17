var returnTypes = ['return','rcheck','rpack','rguide','out_hour','weekend']

$('.modal .select2').select2(Init.select2());
$('.modal input').iCheck(Init.iCheck());
$('.modal .datepicker').datepicker(Init.datepicker());
/*$('.modal .phone').intlTelInput(Init.intlTelInput());*/

$('.modal .select2').on('select2:open', function (event) {
    $('.select2-dropdown').css('min-width', $(this).next().css('width'))
})

/*function formatState (state) {
    if (!state.id) {
        return state.text;
    }
    var $state = $(
        '<span><div class="iti-flag '+state.element.value.toLowerCase()+'"></div> ' + state.text + '</span>'
    );
    return $state;
};

$('.modal .select2-country').select2({
    templateSelection: formatState
});
*/

$('.modal .select2-country').select2(Init.select2Country());

$('[name="weight"]').on('focus', function(){
    $('.provider-weight').show();
})

$('[name="cost"]').on('change', function(){
    $('[name="cost_price"]').val($(this).val());
});

/**
 * SCHEDULE FUNCTIONS
 */
$('[name="schedule_frequency"]').on('change', function(){
    var frequency = $(this).val();

    if(frequency == 'day') {
        $('.schedule-repeat').hide();
        $('.schedule-weekdays').hide();
        $('.schedule-month-days').hide();
    } else if(frequency == 'week') {
        $('.schedule-repeat').hide();
        $('.schedule-weekdays').show();
        $('.schedule-month-days').hide();
    } else if(frequency == 'month') {
        $('.schedule-repeat').show();
        $('.schedule-weekdays').hide();
        $('.schedule-month-days').show();
    } else if(frequency == 'year') {

    }
})

$('[name="schedule_repeat"]').on('change', function(){
    var repeat = $(this).val();

    if(repeat == 'day') {
        $('.schedule-weekdays').hide();
        $('.schedule-month-days').show();
    } else {
        $('.schedule-weekdays').show();
        $('.schedule-month-days').hide().find('input').val('');
    }
})

$('[name="schedule_end_time"]').on('change', function(){
    var type = $(this).val();

    if(type == 'date') {
        $('[name="schedule_end_date"]').val('').prop('required', true).closest('.input-group').show();
        $('[name="schedule_end_repetitions"]').val('').prop('required', false).closest('.input-group').hide();
    } else {
        $('[name="schedule_end_date"]').val('').prop('required', false).closest('.input-group').hide();
        $('[name="schedule_end_repetitions"]').val('').prop('required', true).closest('.input-group').show();
    }
})

/**
 * Adiciona os encargos a partir da janela de envios quando selecionados
 */
$('#modal-'+ STR_HASH_ID).on('ifChanged', '[name*="optional_fields"]', function (e) {
    addReturnService($(this));
    addExpenseOnModal($(this));
})

$('[name*=optional_fields]').on('change', function (e) {
    addReturnService($(this));
    addExpenseOnModal($(this));
})

$('#modal-'+ STR_HASH_ID).on('change', '[name="charge_price"]', function (e) {

    $('.loading-expenses').removeClass('hide');

    var remove = false;
    if($(this).val() == '') {
        remove = true;
    }

    var total = 0;
    $('#modal-shipment-expenses .row-expenses').each(function(){
        total++;

        var $select = $(this).find('[name="expense_id[]"]');

        if(remove) {
            if($(this).find('[name="expense_id[]"] option:selected').data('type') == 'charge') {
                $(this).find('.remove-expenses').trigger('click');
                return false;
            }
        } else {

            if($(this).find('[name="expense_id[]"] option:selected').data('type') == 'charge') {
                return false;
            }

            if ($select.val() == "") {
                $select.find('option[data-type="charge"]').prop('selected', true).trigger('change');
                return false;
            }
        }
    });

    $('.loading-expenses').addClass('hide');
})


$('#modal-'+ STR_HASH_ID).on('ifChanged', '[name="sms"]', function (e) {

    $('.loading-expenses').removeClass('hide');

    var remove = true;
    if ($(this).prop('checked')) {
        remove = false;
    }

    var total = 0;
    $('#modal-shipment-expenses .row-expenses').each(function () {
        total++;

        var $select = $(this).find('[name="expense_id[]"]');

        if (remove) {
            if ($(this).find('[name="expense_id[]"] option:selected').data('type') == 'sms') {
                $(this).find('.remove-expenses').trigger('click');
                $(document).find('button.confirm-expenses').trigger('click')
                return false;
            }
        } else {

            if ($select.val() == "") {
                $select.find('option[data-type="sms"]').prop('selected', true).trigger('change');
                return false;
            }
        }
    });

    $('.loading-expenses').addClass('hide');
});

/*$('#modal-'+ STR_HASH_ID).on('ifChecked', '[name="sms"]', function (e) {
    if(!isMobile($('.modal [name="recipient_phone"]').val(), $('.modal [name="recipient_country"]').val())) {
        $(this).iCheck('uncheck');
        Growl.error('Não é possível enviar SMS. O número de telemóvel inserido é inválido ou não incluíu o indicativo do país.');
    }
})*/

/**
 * Adiciona opções de retorno caso sejam selecionadas
 * Remove caso sejam desselecionadas
 * @param $this
 */
function addReturnService($this) {

    var isSelect   = $this.is('select');
    var isChecked  = $this.is(':checked');
    var inputType  = $this.attr('type');
    var returnType = $this.data('type');
    var value      = $this.val();

    if(returnTypes.includes(returnType)) {

        //STR_HASH_ID previne a acumulação e repetição de instancias
        $element = $(document).find('#modal-'+ STR_HASH_ID + ' input[value="' + returnType + '"]');

        if($element.length > 0) {
            if(value == '' || value == '0' || value == 0 || (inputType == 'checkbox' && !isChecked)) {
                $element.remove();
            }
        } else {
            var html = '<input type="hidden" name="has_return[]" value="'+ returnType+'"/>';
            $('#modal-remote-xl .has-return').append(html);
        }
    }
}

/**
 * Adiciona as despesas na modal
 * @param $this
 */
function addExpenseOnModal($this) {

    var zone = $('[name="zone"]').val();

    if(zone == '') {
        zone = 'pt';
    }

    var id    = $this.data('id');
    var type  = $this.attr('type');
    var value = $this.val();
    value = value.replace(',', '.');
    value = parseFloat(value);
    value = isNaN(value) ? 0 : value


    if(type == 'checkbox') { //se é checkbox
        if(!$this.is(':checked')) {
            value = 0
        }
    }

    $('.loading-expenses').removeClass('hide');

    $('#modal-shipment-expenses .row-expenses').each(function(){

        var curId = $(this).find('[name="expense_id[]"]').val();

        if(curId != '') {
            if(curId == id) { //a despesa já existia
                if(value == '' || value == 0 || value == '0') { //remove se o valor é 0 ou vazio
                    removeExpenseRow($(this), true);
                    $(document).find('button.confirm-expenses').trigger('click')
                } else { //atualiza o valor caso seja diferente
                    $(this).find('[name="expense_price[]"]').val(value)
                    $(this).find('[name="expense_id[]"]').val(id).trigger('change')
                }
                return false;
            }
        } else {
            $(this).find('[name="expense_qty[]"]').val(value);
            //$(this).find('[name="expense_price[]"]').val(value).trigger('change');
            $(this).find('[name="expense_id[]"]').val(id).prop('readonly', true).trigger('change')
            return false;
        }
    })

    $('.loading-expenses').addClass('hide');

}

/**
 * Faz trigger no botão de confirmação dos encargos quando todos os pedidos ajax tiverem terminado.
 * Atualiza apenas quando a modal não está visivel
 */
$(document).ajaxStop(function() {
    if(!$('#modal-shipment-expenses').is(':visible')) {
        $('.loading-expenses').addClass('hide');
        $(document).find('button.confirm-expenses').trigger('click')
        sumTotalPrice()
    }
});

/**
 * Atualiza todos os encargos
 * AJAX METHOD
 */
function updateAllExpenses() {
    $('#modal-shipment-expenses .row-expenses').each(function() {
        if($(this).find('[name="expense_id[]"]').val() != '') {
            $(this).find('[name="expense_id[]"]').trigger('change')
        }
    });
}


/**
 * Change operator
 */
$('#modal-remote-xl [name="operator_id"]').on('change', function(){
    var vehicle  = $(this).find(':selected').data('vehicle');
    var provider = $(this).find(':selected').data('provider');
    $('#modal-remote-xl [name="vehicle"]').val(vehicle).trigger('change');

    if(typeof provider !== "undefined") {
        $('#modal-remote-xl [name="provider_id"]').val(provider).trigger('change');
    }
})

/**
 * Checkbox payment at the recipient
 */
$('#modal-remote-xl [name="payment_at_recipient"]').on('ifChecked' ,function(){
    $('[name="total_price_for_recipient"]').prop('disabled', false).val($('[name="total_price"]').val());
    $('[name="total_price"],[name="service_price"],[name="cost_price"]').val('0.00');
    $('[name="charge_price"]').trigger('change'); //force update price
})

$('#modal-remote-xl [name="payment_at_recipient"]').on('ifUnchecked',function(){
    $('[name="total_price"]').val($('[name="total_price_for_recipient"]').val());
    $('[name="total_price_for_recipient"]').prop('disabled', true).val('');
    $('[name="charge_price"]').trigger('change'); //force update price
})

/**
 * SHOW CHECKBOX TO SAVE NEW SENDER/RECIPIENT
 */
$('.search-sender').on('change', function(){
    if($('[name="sender_id"]').val() == '') {
        $('#box-sender .save-checkbox').show();

        if($('#box-sender input[name="default_save_sender"]').val() == '1') {
            $('#box-sender input[name="save_sender"]').iCheck('check');
        } else {
            $('#box-sender input[name="save_sender"]').iCheck('uncheck');
        }

    } else {
        $('#box-sender .save-checkbox').hide();
        $('#box-sender input[name="save_sender"]').iCheck('uncheck');
    }
})

$('.search-recipient').on('change', function(){
    if($('[name="recipient_id"]').val() == '') {
        $('#box-recipient .save-checkbox').show();

        if($('#box-recipient input[name="default_save_recipient"]').val() == '1') {
            $('#box-recipient input[name="save_recipient"]').iCheck('check');
        } else {
            $('#box-recipient input[name="save_recipient"]').iCheck('uncheck');
        }
    } else {
        $('#box-recipient .save-checkbox').hide();
        $('#box-recipient input[name="save_recipient"]').iCheck('uncheck');
    }
})

/**
 * BUTTON TO UPDATE PRICE
 */
$('.btn-refresh-prices').on('click', function(){
    $('[name=agency_id]').trigger('change');
})

/**
 * EVENT WHEN CHANGE SERVICE
 */
$('.modal-xl [name=service_id]').on('change', function(){

    var tmp;
    var changePosition = false;
    var $selectSearchCustomer  = $('.select-search-customer');
    var $selectSearchRecipient = $('.select-search-recipient');
    var $senderPlace = $selectSearchCustomer.closest('.input-group');
    var $recipientPlace = $selectSearchRecipient.closest('.input-group');
    var $selected = $(this).find(':selected');

    if(IS_PICKUP || $selected.data('import')) { //serviço de recolhas
        if($('#box-sender .box-sender-content').length > 0){
            //troca posição das caixas
            /*$('#box-recipient').append($('.box-sender-content'));
            $('#box-sender').append($('.box-recipient-content'));
            $('label[for=recipient_id]').html('Remetente');
            $('label[for=sender_name]').html('Destinatário');

            var tmp = $('.recipient-agency-content select').html();
            $('.recipient-agency-content select').html($('.sender-agency-content select').html())
            $('.sender-agency-content select').html(tmp)*/

            $('input[name="volumes"]').prop('required', false);
            $('input[name="weight"]').prop('required', false);

            if($selected.data('import')) {
                $('[name="is_import"]').val(1);
            }
        }

    } else { //serviços normais

        if(!$('#box-sender .box-sender-content').length > 0){
            /*$('#box-sender').append($('.box-sender-content'));
            $('#box-recipient').append($('.box-recipient-content'));
            $('label[for=recipient_id]').html('Destinatário');
            $('label[for=sender_name]').html('Remetente');

            var tmp = $('.sender-agency-content select').html();
            $('.sender-agency-content select').html($('.recipient-agency-content select').html())
            $('.recipient-agency-content select').html(tmp)*/
            $('input[name="volumes"], input[name="weight"]').prop('required', true);
            $('[name="is_import"]').val(0);
        }
    }


    if($selected.data('unity') == 'm3') { //services M3
        $('[for="volumes"]').html('Volumes');
        $('[name="weight"]').val('').prop('required', false).prop('readonly', false);
        $('[name="volume_m3"]').prop('required', true);
        $('.form-group-volume-m3, .btn-set-dimensions').show();
        $('.form-group-weight, .btn-set-pallets').hide();
        $('.form-group-kms, .form-group-hours').hide();
        $('[name=kms],[name=hours]').val('').prop('required', false);
    } else if($selected.data('unity') == 'pallet') { //services pallet
        $('[for="volumes"]').html('Paletes');
        $('[name="weight"]').val('').prop('required', true).prop('readonly', true);
        $('[name="volume_m3"]').val('').prop('required', false);
        $('.form-group-volume-m3, .btn-set-dimensions').hide();
        $('.form-group-weight, .btn-set-pallets').show();
        $('.btn-set-dimensions').data('target', '#modal-shipment-pallets');
        $('.form-group-kms, .form-group-hours').hide();
        $('[name=kms],[name=hours]').val('').prop('required', false);
    } else if($selected.data('unity') == 'km') { //services km
        $('.form-group-kms').show();
        $('.form-group-hours').hide();
        $('[name=kms]').val('').prop('required', true);
        $('[name=hours]').val('').prop('required', false);
    } else if($selected.data('unity') == 'hours') { //services hours
        $('.form-group-kms').hide();
        $('.form-group-hours').show();
        $('[name=kms]').val('').prop('required', false);
        $('[name=hours]').val('').prop('required', true);
    } else { //all other services
        $('[for="volumes"]').html('Volumes');
        $('[name="volume_m3"]').val('').prop('required', false).prop('readonly', false);
        $('[name="weight"]').prop('required', true).prop('readonly', false);
        $('.form-group-volume-m3, .btn-set-pallets').hide();
        $('.form-group-weight, .btn-set-dimensions').show();
        $('.btn-set-dimensions').data('target', '#modal-shipment-dimensions');
        $('.form-group-kms, .form-group-hours').hide();
        $('[name=kms],[name=hours]').val('').prop('required', false);
    }

    if(IS_PICKUP) {
        $('[name="volumes"], [name="weight"]').prop('required', false);
    }

    validateTotalVolumes();
})

$(document).on('ifChanged', '.modal-xl [name="average_weight"]',function(){
    $('.modal-xl [name=weight]').trigger('change');
})

/**
 * Toggle sender and recipient data
 */
$('.toggle-sender').on('click',function(){
    var tmpName     = $('#modal-remote-xl [name="sender_name"]').val();
    var tmpAddress  = $('#modal-remote-xl [name="sender_address"]').val();
    var tmpZipCode  = $('#modal-remote-xl [name="sender_zip_code"]').val();
    var tmpCity     = $('#modal-remote-xl [name="sender_city"]').val();
    var tmpCountry  = $('#modal-remote-xl [name="sender_country"]').val();
    var tmpPhone    = $('#modal-remote-xl [name="sender_phone"]').val();

    $('#modal-remote-xl [name="sender_name"]').val($('#modal-remote-xl [name="recipient_name"]').val());
    $('#modal-remote-xl [name="sender_address"]').val($('#modal-remote-xl [name="recipient_address"]').val());
    $('#modal-remote-xl [name="sender_zip_code"]').val($('#modal-remote-xl [name="recipient_zip_code"]').val());
    $('#modal-remote-xl [name="sender_city"]').val($('#modal-remote-xl [name="recipient_city"]').val());
    $('#modal-remote-xl [name="sender_country"]').val($('#modal-remote-xl [name="recipient_country"]').val());
    $('#modal-remote-xl [name="sender_phone"]').val($('#modal-remote-xl [name="recipient_phone"]').val())

    $('#modal-remote-xl [name="recipient_name"]').val(tmpName);
    $('#modal-remote-xl [name="recipient_address"]').val(tmpAddress);
    $('#modal-remote-xl [name="recipient_zip_code"]').val(tmpZipCode);
    $('#modal-remote-xl [name="recipient_city"]').val(tmpCity);
    $('#modal-remote-xl [name="recipient_country"]').val(tmpCountry);
    $('#modal-remote-xl [name="recipient_phone"]').val(tmpPhone);
    $('#modal-remote-xl [name="recipient_attn"]').val('');
    $('#modal-remote-xl [name="recipient_email"]').val('');

    $('#modal-remote-xl [name="sender_country"], #modal-remote-xl [name="recipient_country"]').trigger('change.select2');

    $('.btn-refresh-prices').trigger('click');
});


/**
 * SHOW OR HIDE DIMENSIONS MODAL
 */
$('[data-target="#modal-shipment-dimensions"]').on('click', function(){ //show
    $('#modal-shipment-dimensions').addClass('in').show();
})

$(document).on('click', '#modal-shipment-dimensions .copy-dimensions', function(){ //show
    var $tr = $(this).closest('tr');
    var $nextTr = $tr.next('tr');

    $nextTr.find('td').each(function(item){
        lastTrVal = $tr.find('td:eq(' + item +')').find('input, select').val();
        $(this).find('input, select').val(lastTrVal).trigger('change')
    })
})

$(document).on('change', '[name="box_type[]"]',function(){

    var globalType = '';
    var lastType = '';
    $('#modal-shipment-dimensions').find('[name="box_type[]"]').each(function(){
        var type = $(this).val();

        if(lastType == '') {
            lastType = type;
        }

        if(type != lastType) {
            globalType = 'multiple'
        }
    })

    if(globalType != 'multiple') {
        globalType = lastType;
    }

    $('[name="packaging_type"]').val(globalType)
})

$('.confirm-dimensions').on('click', function(){ //hide
    $('#modal-shipment-dimensions').removeClass('in').hide();

    var m3;
    var somaDimensoes = 0;
    var weightVal;
    var fatorM3 = 0;
    var totalWeight = 0;
    $('.fator-m3').hide();

    $('#modal-shipment-dimensions [name="fator_m3_row[]"]').each(function(){
        m3 = $(this).val() == "" ? 0 : $(this).val();
        fatorM3+= parseFloat(m3);

    })

    $('#modal-shipment-dimensions [name="box_weight[]"]').each(function(){
        var weightVal = $(this).val() == "" ? 0 : parseFloat($(this).val());
        totalWeight+= parseFloat(weightVal);
    })


    /*$('#modal-shipment-dimensions [name="fator_m3_row[]"]').each(function(){
        m3 = $(this).val() == "" ? 0 : $(this).val();
        fatorM3+= parseFloat(m3);
    })*/

    //só cubica acima das dimensões indicadas (gls)
    if(somaDimensoes >= 0) {
        $('[name="fator_m3"]').val(fatorM3);
    } else {
        fatorM3 = 0;
        $('[name="fator_m3"]').val(0);
    }


    if(totalWeight > 0) {
        $('[name="weight"]').val(totalWeight);
    }

    if(fatorM3 != "" || fatorM3 != "0") {
        $('.fator-m3').show().find('span').html(fatorM3);
    }

    /*if(totalWeight > 0) {
        $('[name="weight"]').val(totalWeight);
    }*/

    if($('[name="service_id"] option:selected').data('unity') == 'm3') {
        $('[name="volume_m3"]').val(fatorM3.toFixed(2))
    }




    $('[name="fator_m3"]').trigger('change');

})

//calculate fator m3
$(document).on('change', '[name="width[]"], [name="height[]"], [name="length[]"]', function(){
    var $tr = $(this).closest('tr');

    var width   = $tr.find('[name="width[]"]').val();
    var height  = $tr.find('[name="height[]"]').val();
    var length  = $tr.find('[name="length[]"]').val();

    $tr.find('[name="fator_m3_row[]"]').val(calcVolume(width, height, length, VOLUMES_MESURE_UNITY));
})

function calcVolume(width, height, length, mesure) {
    width  = width == "" ? 0 : width.replace(',', '.');
    length = length == "" ? 0 : length.replace(',', '.');
    height = height == "" ? 0 : height.replace(',', '.');

    if(mesure == 'cm') {
        return (parseFloat(width) * parseFloat(height) * parseFloat(length)) / 1000000; //convert from cm3 to m3
    } else {
        return (parseFloat(width) * parseFloat(height) * parseFloat(length));
    }

}

/**
 * SHOW OR HIDE PALLETS MODAL
 */
$('[data-target="#modal-shipment-pallets"]').on('click', function(){ //show
    $('#modal-shipment-pallets').addClass('in').show();
})

$('.confirm-pallets').on('click', function(){ //hide
    $('#modal-shipment-pallets').removeClass('in').hide();

    var val, totalPrice = 0, totalCost = 0, totalWeight = 0;
    $('#modal-shipment-pallets [name="pallet_price[]"]').each(function(){
        val = $(this).val() == "" ? 0 : $(this).val();
        totalPrice+= parseFloat(val);
    })

    $('#modal-shipment-pallets [name="pallet_cost[]"]').each(function(){
        val = $(this).val() == "" ? 0 : $(this).val();
        totalCost+= parseFloat(val);
    })

    $('#modal-shipment-pallets [name="pallet_weight[]"]').each(function(){
        val = $(this).val() == "" ? 0 : $(this).val();
        totalWeight+= parseFloat(val);
    })

    $('[name="weight"]').val(totalWeight)
    $('[name="cost_price"]').val(totalCost)
    $('[name="total_price"]').val(totalPrice)
    $('[name="service_price"]').val(totalPrice)
})


/**
 * SHOW OR HIDE EXPENSES MODAL
 */
$('[data-target="#modal-shipment-expenses"]').on('click', function(){ //show
    $('#modal-shipment-expenses').addClass('in').show();
})

$('.confirm-expenses').on('click', function() { //hide
    $('.loading-expenses').addClass('hide');
    $('#modal-shipment-expenses').removeClass('in').hide();

    var val = totalExpenses = 0;
    var valCost = totalExpensesCost = 0;
    $('#modal-'+ STR_HASH_ID +' [name="expense_subtotal[]"]').each(function(){
        val = $(this).val() == "" ? 0 : $(this).val();
        totalExpenses+= parseFloat(val);

        $row = $(this).closest('.row-expenses').find('[name="expense_cost_price[]"]')
        valCost = $row.val() == "" ? 0 : $row.val();
        totalExpensesCost+= parseFloat(valCost);
    })

    $('[name="total_expenses"]').val(totalExpenses.toFixed(2));
    $('[name="total_expenses_cost"]').val(totalExpensesCost.toFixed(2));
    $('[name="cost_price"]').trigger('change');

    sumTotalPrice();
})

/**
 * CALCULATE EXPENSE SUBTOTAL
 * ajax method
 */
$('#modal-shipment-expenses [name="expense_id[]"]').on('change', function(){
    var tr = $(this).closest('.row-expenses');
    var $selected     = tr.find('[name="expense_id[]"] option:selected');
    var expenseId     = tr.find('[name="expense_id[]"]').val();
    var qty           = tr.find('[name="expense_qty[]"]').val();
    var expensePrice  = tr.find('[name="expense_price[]"]').val();
    var chargePrice   = $('.modal-xl [name="charge_price"]').val();
    var customerId    = $('.modal-xl [name="customer_id"]').val();
    var providerId    = $('.modal-xl [name="provider_id"]').val();
    var shipmentPrice = $('.modal-xl [name="total_price"]').val();
    var basePrice     = $('.modal-xl [name="base_price"]').val();
    var baseCost      = $('.modal-xl [name="cost_price"]').val();
    var agencyId      = $('.modal-xl [name="agency_id"]').val();
    var serviceId     = $('.modal-xl [name="service_id"]').val();
    var weight        = $('.modal-xl [name="weight"]').val();
    var volumes       = $('.modal-xl [name="volumes"]').val();
    var volumeM3      = $('.modal-xl [name="volume_m3"]').val();
    var zone          = $('.modal-xl [name="zone"]').val();
    var returnType    = $selected.data('type');

    price = $selected.data(zone);

    if(expenseId != '' && zone != '') {

        tr.find('[name="expense_price[]"]').val(price)

        tr.find('.fa-spin').removeClass('hide');
        tr.find('.fa-times').addClass('hide');

        $.post(ROUTE_GET_EXPENSE_PRICE, {
            expense: expenseId,
            qty: qty,
            expensePrice: expensePrice,
            provider: providerId,
            customer: customerId,
            totalPrice: shipmentPrice,
            basePrice: basePrice,
            baseCost: baseCost,
            chargePrice: chargePrice,
            agency: agencyId,
            service: serviceId,
            zone: zone,
            weight: weight,
            volumes: volumes,
            volumeM3: volumeM3
        },function (data) {

            if(data.price != 0.00) {
                tr.find('[name="expense_price[]"]').val(data.price).trigger('change');
            }
            if(data.qty != '') {
                tr.find('[name="expense_qty[]"]').val(data.qty).trigger('change');
            }
            tr.find('[name="expense_cost_price[]"]').val(data.cost);

            //adiciona serviço de retorno se existir apenas se a janela de encargos está visivel
            if(returnTypes.includes(returnType)) {
                if($('#modal-shipment-expenses').is(':visible') === true) {
                    optionalField = $(document).find('#modal-'+ STR_HASH_ID + ' [name="optional_fields['+expenseId+']"]');

                    if(optionalField.is('select')) {
                        optionalField.val(data.price).trigger('change.select2'); //change value without trigger
                    } else if(optionalField.is(':checkbox')) {
                        optionalField.prop('checked', true).iCheck('update');
                    } else {
                        optionalField.val(data.price);
                    }

                    $('#modal-remote-xl .has-return').append('<input type="hidden" name="has_return[]" value="'+ returnType+'"/>');
                }
            }

        }).always(function(){
            tr.find('.fa-spin').addClass('hide');
            tr.find('.fa-times').removeClass('hide');
        })
    }
})

/**
 * UPDATE EXPENSE ROW QUANTITY
 */
$('#modal-shipment-expenses [name="expense_qty[]"], #modal-shipment-expenses [name="expense_price[]"]').on('change', function(){
    var tr = $(this).closest('.row-expenses');
    var $selected = tr.find('[name="expense_id[]"]')
    var expenseId = tr.find('[name="expense_id[]"]').val();
    var qty   = parseFloat(tr.find('[name="expense_qty[]"]').val() == "" ? 0 : tr.find('[name="expense_qty[]"]').val());
    var price = parseFloat(tr.find('[name="expense_price[]"]').val() == "" ? 0 : tr.find('[name="expense_price[]"]').val());
    var formType = $selected.find('option:selected').data('form-shipment');

    var shipmentPrice = $('[name="total_price"]').val();

    subtotal = price * qty;
    tr.find('[name="expense_subtotal[]"]').val(subtotal.toFixed(2))


    if($('#modal-shipment-expenses').is(':visible') === true) {
        optionalField = $(document).find('#modal-'+ STR_HASH_ID + ' [name="optional_fields['+expenseId+']"]');

        if(optionalField.is('select')) {
            if(formType == 'select-io') {
                qty = "1"; //para que na caixa de selecao fique ativa a opção "sim"
            }
            optionalField.val(qty).trigger('change.select2'); //change value without trigger
        } else {
            optionalField.val(price);
        }
    }
})

/**
 * ADD EXPENSE ROW
 */
/*$('#modal-shipment-expenses .btn-add-expenses').on('click', function(){
    $('#modal-shipment-expenses .table-expenses').find('tr:hidden:first').show();

    if($('#modal-shipment-expenses .table-expenses').find("tr:hidden").length == 0) {
        $(this).hide();
    } else {
        $(this).show();
    }
});*/

/**
 * UPDATE EXPENSE ROW
 */
$('#modal-shipment-expenses .remove-expenses').on('click', function(){
    removeExpenseRow($(this).closest('tr'));
});

/**
 * REMOVE EXPENSE ROW
 */
$('#modal-shipment-expenses .update-expenses').on('click', function(){
    $(this).find('.update-expenses i').addClass('fa-spin');
    $(this).closest('tr').find('[name="expense_id[]"]').trigger('change');
});


function removeExpenseRow($row, automatic){

    automatic = typeof automatic !== 'undefined' ? automatic : false;

    //if($('#modal-shipment-expenses .table-expenses').find("tr:visible").length > 2) {
    if($('#modal-shipment-expenses .table-expenses').find("tr").length > 2) {

        var $tr           = $row; //$this.closest('tr');shipping_expense_id
        var $selected     = $tr.find('[name="expense_id[]"] option:selected');
        var id            = $tr.find('[name="shipping_expense_id[]"]').val();
        var expenseId     = $selected.val();
        var type          = $selected.data('type');
        var optionalField = $('[name="optional_fields['+expenseId+']"]')

        //remove valores das caixas personalizadas fora da modal
        if(!automatic) {
            if(optionalField.is('select')) {
                optionalField.val('').trigger('change.select2'); //change value without trigger
            } else if(optionalField.is(':checkbox')) {
                optionalField.prop('checked', false).iCheck('update');
            } else {
                optionalField.val('');
            }

            $('[value="' + type + '"]').remove();
        }

        var deletedIds = $('[name="deleted_expenses"]').val();
        if(deletedIds == '') {
            deletedIds = [];
        } else {
            deletedIds = deletedIds.split(",");
        }

        deletedIds.push(id);
        deletedIds.join(',');
        $('[name="deleted_expenses"]').val(deletedIds);

        $tr.find('select').val('').trigger('change');
        $tr.find('input').val('');
        //$tr.hide();
        //$tr.detach();

        $('#modal-shipment-expenses .table-expenses').append($tr);

        if ($('#modal-shipment-expenses .table-expenses').find("tr:hidden").length == 0) {
            $('.btn-add-expenses').hide();
        } else {
            $('.btn-add-expenses').show();
        }

        $('[name="deleted_expenses"]').val(deletedIds)

    }
}

/**
 * EVENT WHEN CHANGE COST PRICE
 */
$('.modal-xl [name=cost_price]').on('change', function(){
    totalCost = 0;
    value = $(this).val();

    if(value != '') {
        costPrice = parseFloat(value);

        $('.modal-xl [name=total_expenses_cost]').val('');
        totalCost = costPrice;

        $('.modal-xl [name=cost]').val(totalCost.toFixed(2))
    }
});

/**
 * EVENT WHEN CHANGE VOLUMES
 */
$('.modal-xl [name=volumes]').on('change', function(){
    var volumes = $(this).val();

    if($('.modal-xl [name="service_id"]').find(':selected').data('unity') == 'pallet') {
        $('table.shipment-pallets tbody tr:gt(0)').html('');
    } else {
        $tr = $('table.shipment-dimensions tbody tr:first');
        rowCount = $('table.shipment-dimensions tbody tr').length;
        $('.modal-xl [name="fator_m3"]').val('').change();
    }

    var i, html;

    if($('.modal-xl [name=service_id]').find(':selected').data('unity') == 'pallet') {
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
    } else {
        if(rowCount < volumes) { // add rows
            for (i = rowCount; i < volumes; i++) {
                clonedRow = $tr.clone();
                clonedRow.find('input').val('');
                clonedRow.find('input[name="qty[]"]').val('1');
                clonedRow.find('span.select2').remove()
                clonedRow.find('.select2').select2(Init.select2());
                $('table.shipment-dimensions tbody').append(clonedRow);
            }
        } else { //remove rows
            rowsToRemove = rowCount - volumes;
            for(i=0 ; i < rowsToRemove ; i++ ){
                $('table.shipment-dimensions tbody tr:last').remove();
            }
        }

        var i = 1;
        $('table.shipment-dimensions tbody tr').each(function(){
            $(this).find('.nbr').html(i);
            i++;
        })

    }
    validateTotalVolumes()
})

$('.modal-xl [name=weight]').on('change', function(){
    validateTotalVolumes()
});

/**
 * VALIDATE TOTAL VOLUMES
 */
function validateTotalVolumes(){

    var maxValue  = $('.modal-xl [name=service_id]').find(':selected').data('max');
    var maxWeight = parseFloat($('.modal-xl [name=service_id]').find(':selected').data('max-weight'));
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

/****************************************************************************
 *
 *                               AJAX METHODS
 *
 ****************************************************************************/

/**
 * GET SHIPMENT PRICE
 * ajax method
 */
$('.modal-xl [name=agency_id], .modal-xl [name=sender_agency_id], .modal-xl [name=recipient_agency_id], .modal-xl [name=provider_id], .modal-xl [name=service_id], .modal-xl [name=weight], .modal-xl [name=volumes], .modal-xl [name=customer_id], .modal-xl [name=charge_price], .modal-xl [name=volume_m3], .modal-xl [name=fator_m3],.modal-xl [name=kms],.modal-xl [name=hours], .modal-xl [name="sender_country"], .modal-xl [name="recipient_country"]').on('change', function () {

    var agency = $('.modal-xl [name=agency_id]').val();
    var sender_agency = $('.modal-xl [name=sender_agency_id]').val();
    var recipient_agency = $('.modal-xl [name=recipient_agency_id]').val();
    var service = $('.modal-xl [name=service_id]').val();
    var assignedService = $('.modal-xl [name=service_id]').find(':selected').data('assigned-service');
    var serviceUnity = $('.modal-xl [name=service_id]').find(':selected').data('unity');
    var provider = $('.modal-xl [name=provider_id]').val();
    var customer = $('.modal-xl [name=customer_id]').val();
    var weight = $('.modal-xl [name=weight]').val();
    var volumes = $('.modal-xl [name=volumes]').val();
    var charge = $('.modal-xl [name=charge_price]').val();
    var fatorM3 = $('.modal-xl [name=fator_m3]').val();
    var volumeM3 = $('.modal-xl [name=volume_m3]').val();
    var kms = $('.modal-xl [name=kms]').val();
    var hours = $('.modal-xl [name=hours]').val();
    var sender_zone = $('.modal-xl [name="sender_country"]').val()
    var recipient_zone = $('.modal-xl [name="recipient_country"]').val()
    var sender_zip_code = $('.modal-xl [name="sender_zip_code"]').val()
    var recipient_zip_code = $('.modal-xl [name="recipient_zip_code"]').val()
    var price_fixed = $('.modal-xl [name="price_fixed"]:checked').length;
    var cod = $('.modal-xl [name="payment_at_recipient"]:checked').length;
    var price_blocked = $('.modal-xl [name="total_price"]').is('[readonly]');
    var is_import = $('.modal-xl [name="is_import"]').val();

    if(IS_PICKUP && (volumes == '' || weight == '')) {
        volumes = 1;
        weight  = 1;
    }

    if(IS_PICKUP == '1') {
        service = assignedService;
    }

    if(!price_fixed
        && !price_blocked
        && sender_zip_code != ''
        && sender_zone != ''
        && sender_agency != ''
        && recipient_zip_code != ''
        && recipient_zone != ''
        && recipient_agency != ''
        && service != ''
        && provider != ''
        && volumes !=''
        && (weight != '' || volumes!='') && $('.modal-xl [name=service_id]:selected').data('unity') != 'pallet' ) {

        if(serviceUnity == 'pallet') {
            $('.confirm-pallets').trigger('click');
        } else {
            $('.btn-refresh-prices i').addClass('fa-spin');

            $.post(ROUTE_GET_PRICE, {
                    agency: agency,
                    sender_agency: sender_agency,
                    recipient_agency: recipient_agency,
                    service: service,
                    customer: customer,
                    provider: provider,
                    weight: weight,
                    volumes: volumes,
                    charge: charge,
                    fatorM3: fatorM3,
                    volumeM3: volumeM3,
                    kms: kms,
                    hours: hours,
                    sender_zone: sender_zone,
                    recipient_zone: recipient_zone,
                    sender_zip_code: sender_zip_code,
                    recipient_zip_code:recipient_zip_code,
                    price_fixed: price_fixed,
                    is_import:is_import,
                    cod:cod
                },
                function (data) {
                    var oldBasePrice = $('[name=base_price]').val();
                    var oldZone      = $('[name=zone]').val();
                    $('[name=total_price]').val(data.total);
                    $('[name=base_price]').val(data.base_price);
                    $('[name=service_price]').val(data.total);
                    $('[name=total_charge_price]').val(data.totalCharge);
                    $('[name=cost_price]').val(data.cost);
                    $('[name=delivery_price]').val(data.delivery);
                    $('[name=zone]').val(data.zone);

                    $('.modal-xl [name=volumetric_weight]').val(data.volumetricWeight);
                    $('.base-price').html(data.base_price + '€');

                    if(data.defaultPrice) {
                        $('.helper-default-price').show();
                    } else {
                        $('.helper-default-price').hide();
                    }

                    if(data.averageWeight) {
                        $('.helper-average-weight').show();
                    } else {
                        $('.helper-average-weight').hide();
                    }

                    if(data.zoneNotAlowed) {
                        $('.helper-zone-not-alowed').show();
                    } else {
                        $('.helper-zone-not-alowed').hide();
                    }


                    if($('.modal-xl [name="payment_at_recipient"]:checked').length > 0) {
                        var paymentRecipient = $('[name="total_price_for_recipient"]').val();
                        //if((paymentRecipient == '' ||  paymentRecipient == '0.00')) {
                            $('[name="total_price_for_recipient"]').val(data.total);
                        //}

                        $('[name="total_price"],[name="service_price"]').val('0.00')
                    }

                    //enable all optional fields
                    $('[name*="optional_fields"]').prop('disable', false).trigger('update');

                    //se mudar de zona de faturação, atualiza os encargos
                    if((oldBasePrice != data.base_price) || (oldZone != data.zone && data.zone != '' && ($('[name="total_expenses"]').val() != '' || $('[name="total_expenses"]').val() != '0.00'))) {
                        updateAllExpenses()
                    }

                }).always(function () {
                    sumTotalPrice();
                    $('.btn-refresh-prices i').removeClass('fa-spin');
            })
        }
    }
});

/**
 * GET SHIPMENT PRICE WHEN HAS PALLETS
 * ajax method
 */
$(document).on('change', '[name="pallet_weight[]"], [name="pallet_qty[]"]', function(){
    var $tr = $(this).closest('tr');

    var agency = $('.modal-xl [name=agency_id]').val();
    var service = $('.modal-xl [name=service_id]').val();
    var customer = $('.modal-xl [name=customer_id]').val();
    var provider = $('.modal-xl [name=provider_id]').val();
    var sender_zone = $('.modal-xl [name="sender_country"]').val();
    var recipient_zone = $('.modal-xl [name="recipient_country"]').val();
    var kms     = $('.modal-xl [name=kms]').val();
    var hours   = $('.modal-xl [name=hours]').val();
    var weight  = $tr.find('[name="pallet_weight[]"]').val();
    var qty     = $tr.find('[name="pallet_qty[]"]').val();

    if(service != '' && provider != '' && qty !='' && weight != '') {

        $.post(ROUTE_GET_PRICE, {
            agency: agency,
            service: service,
            customer: customer,
            provider: provider,
            weight: weight,
            volumes: qty,
            sender_zone: sender_zone,
            recipient_zone: recipient_zone,
            kms:kms,
            hours:hours
        },function (data) {
            total = qty * parseFloat(data.total);
            cost = qty * parseFloat(data.cost);
            $tr.find('[name="pallet_price[]"]').val(total);
            $tr.find('[name="pallet_cost[]"]').val(cost);
        })
    }
})

/**
 * GET DISTANCE IN KM
 * ajax method
 */
$('.modal-xl [name="sender_city"], .modal-xl [name="recipient_city"]').on('change', function(){
    if($('.modal-xl .btn-auto-km').length && $('[name=service_id] option:selected').data('unity') == 'km') {
        getAutoKm();
    }
})

$('.modal-xl .btn-auto-km').on('click', function () {
    if($('.modal-xl [name="sender_zip_code"]').val() == '' || $('.modal-xl [name="sender_city"]').val() == '' || $('.modal-xl [name="sender_country"]').val() == ''
    || $('.modal-xl [name="recipient_zip_code"]').val() == '' || $('.modal-xl [name="recipient_city"]').val() == '' || $('.modal-xl [name="recipient_country"]').val() == '') {
        Growl.warning('Preencha primeiro as informações do remetente e destinatário.');
    } else {
        getAutoKm();
    }
});

function getAutoKm() {

    var agencyZp      = $.trim($('.modal-xl [name="agency_zp"]').val());
    var agencyCity    = $.trim($('.modal-xl [name="agency_city"]').val());
    var originZp      = $.trim($('.modal-xl [name="sender_zip_code"]').val());
    var originCity    = $.trim($('.modal-xl [name="sender_city"]').val());
    var originCountry = $.trim($('.modal-xl [name="sender_country"]:selected').text())
    var destZp        = $.trim($('.modal-xl [name="recipient_zip_code"]').val());
    var destCity      = $.trim($('.modal-xl [name="recipient_city"]').val());
    var destCountry   = $.trim($('.modal-xl [name="recipient_country"]:selected').text());


    originZp   = agencyZp == '' ? originZp : agencyZp;
    originCity = agencyCity == '' ? originCity : agencyCity;

    originCountry = originCountry == '' ? 'Portugal' : originCountry;
    destCountry   = destCountry == '' ? 'Portugal' : destCountry

    var origin      = originZp + ' ' + originCity + ',' + originCountry;
    var destination = destZp + ' ' + destCity + ',' + destCountry;

    if(originZp != '' && originCity != '' && destZp != '' && destCity != '') {

        var $icon       = $('.modal-xl .btn-auto-km').find('.fas');
        $icon.addClass('fa-spin');


        $.get(ROUTE_GET_DISTANCE_KM, {
            origin: origin,
            destination: destination,
            origin_zp: originZp,
            destination_zp: destZp,
            origin_city: originCity,
            destination_city: destCity,
            origin_country: originCountry,
            destination_country: destCountry,
        }, function (data) {
            if(data.result) {
                distance = parseFloat(data.distance);
                distance = distance * 2;
                $('.modal-xl [name=kms]').val(distance).trigger('change');
            } else {
                Growl.error('Não foi possível calcular a distância. Verifique os códigos postais e localidades inseridas.')
            }
        }).always(function () {
            $icon.removeClass('fa-spin')
        })
    }
}

/**
 * EVENT WHEN CHANGE CUSTOMER
 * ajax method
 */
$('[name=customer_id], [name=department_id]').on('select2:select', function (e) {
    var data  = e.params.data;
    var $this = $(this);

    if($('[name=department_id]').val() == '') {
        $this = $('[name=customer_id]');
    }

    if(data.blocked > 0) {
        $('[name=customer_id], [name=department_id]').val('').trigger('change.select2');
        $('#modal-customer-blocked').addClass('in').show();
        if(data.blocked_reason == 'credit') {
            $('#modal-customer-blocked').find('.blocked-credit').show();
            $('#modal-customer-blocked').find('.blocked-days').hide();
        } else {
            $('#modal-customer-blocked').find('.blocked-credit').hide();
            $('#modal-customer-blocked').find('.blocked-days').show();
            $('#modal-customer-blocked').find('.limitdays').html(data.blocked);
        }
    } else {

        $this.find('option:selected')
            .attr('data-name', data.name)
            .attr('data-address', data.address)
            .attr('data-zip_code', data.zip_code)
            .attr('data-city', data.city)
            .attr('data-country', data.country)
            .attr('data-phone', data.phone)
            .attr('data-agency', data.agency)
            .attr('data-obs', data.obs)
            .attr('data-departments', data.departments)
            .attr('data-kms', data.kms);

        $('[name=agency_zp]').val(data.origin_zp);
        $('[name=agency_city]').val(data.origin_city);

        if ($this.attr('name') != 'department_id') {
            if (data.departments !== null) {
                $('.select-department').removeClass('hide');
                $('.select-customer').removeClass('col-sm-12').addClass('col-sm-7');
                $('.select-customer').find('label').removeClass('col-sm-2').addClass('col-sm-4 p-r-15')
                $('.select-customer').find('.col-sm-10').removeClass('col-sm-10').addClass('col-sm-8 p-l-0')
                $('[name=department_id]').select2({data: data.departments});
            } else {
                $('.select-department').addClass('hide');
                $('.select-customer').removeClass('col-sm-7').addClass('col-sm-12');
                $('.select-customer').find('label').removeClass('col-sm-4').removeClass('p-r-15').addClass('col-sm-2')
                $('.select-customer').find('.col-sm-8').removeClass('col-sm-8').removeClass('p-l-0').addClass('col-sm-10')
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

$('#modal-customer-blocked .btn-confirm-no').on('click', function(){
    $('#modal-customer-blocked').removeClass('in').hide();
})

/**
 * SEARCH SENDER
 * ajax method
 */
$('.search-sender').autocomplete({
    //serviceUrl: ROUTE_SEARCH_SENDER,
    serviceUrl: ROUTE_SEARCH_RECIPIENT,
    minChars: 2,
    onSearchStart: function () {
        $('[name="sender_id"]').val('');
    },
    beforeRender: function (container, suggestions) {
        container.find('.autocomplete-suggestion').each(function(key, suggestion, data){
            $(this).append('<div class="autocomplete-address">' + suggestions[key].address + ' - ' + suggestions[key].zip_code + ' ' +  suggestions[key].city + '</div>')
        });
    },
    onSelect: function (suggestion) {
        $('[name="sender_id"]').val(suggestion.data);
        $('[name="sender_attn"]').val(suggestion.responsable);
        $('[name="sender_name"]').val(suggestion.name).trigger('change');
        $('[name="sender_address"]').val(suggestion.address);
        $('[name="sender_zip_code"]').val(suggestion.zip_code);
        $('[name="sender_city"]').val(suggestion.city);
        $('[name="sender_country"]').val(suggestion.country).trigger('change.select2');
        $('[name="sender_phone"]').val(suggestion.phone);

        if($('[name="sender_agency_id"]').is(':visible')) {
            $('[name="sender_agency_id"]').val(suggestion.agency).trigger('change.select2');
        }

        $('.search-sender').autocomplete('hide');
        $('#box-sender .save-checkbox').hide();
        $('#box-sender input[name="save_sender"]').iCheck('uncheck');

        validatePhone($('[name="sender_phone"]'));
    },
});

/**
 * SEARCH RECIPIENT
 * ajax method
 */
$('.search-recipient').autocomplete({
    serviceUrl: ROUTE_SEARCH_RECIPIENT,
    minChars: 2,
    onSearchStart: function () {
        $('[name="recipient_id"]').val('');
    },
    beforeRender: function (container, suggestions) {
        container.find('.autocomplete-suggestion').each(function(key, suggestion, data){
            $(this).append('<div class="autocomplete-address">' + suggestions[key].address + ' - ' + suggestions[key].zip_code + ' ' +  suggestions[key].city + '</div>')
        });
    },
    onSelect: function (suggestion) {
        $('[name="recipient_id"]').val(suggestion.data)
        $('[name="recipient_attn"]').val(suggestion.responsable);
        $('[name="recipient_name"]').val(suggestion.name).trigger('change');
        $('[name="recipient_address"]').val(suggestion.address);
        $('[name="recipient_zip_code"]').val(suggestion.zip_code);
        $('[name="recipient_city"]').val(suggestion.city);
        $('[name="recipient_country"]').val(suggestion.country).trigger('change.select2');
        $('[name="recipient_phone"]').val(suggestion.phone);

        if(suggestion.agency != '' && typeof suggestion.agency !== 'undefined') {
            $('[name="recipient_agency_id"]').val(suggestion.agency).trigger('change.select2');
        }

        if(suggestion.obs && $('.modal [name=obs]').val() == '') {
            $('[name=obs]').val(suggestion.obs);
        }
        $('.search-recipient').autocomplete('hide');
        $('#box-recipient .save-checkbox').hide();
        $('#box-recipient input[name="save_recipient"]').iCheck('uncheck');

        validatePhone($('[name="recipient_phone"]'));
    }
})


/**
 * edit sender fields
 */
$(document).on('change', '#modal-remote-xl [name="sender_address"], #modal-remote-xl [name="sender_zip_code"],#modal-remote-xl [name="sender_city"],#modal-remote-xl [name="sender_country"],#modal-remote-xl [name="sender_phone"]', function() {
    $('#box-sender input[name="save_sender"]').iCheck('check');
    $('#box-sender .save-checkbox').show();
});

/**
 * edit recipient fields
 */
$(document).on('change', '#modal-remote-xl [name="recipient_address"], #modal-remote-xl [name="recipient_zip_code"],#modal-remote-xl [name="recipient_city"],#modal-remote-xl [name="recipient_country"],#modal-remote-xl [name="recipient_phone"]', function() {
    $('#box-recipient input[name="save_recipient"]').iCheck('check');
    $('#box-recipient .save-checkbox').show();
});

/**
 * SEARCH CUSTOMER
 * ajax method
 */
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

function updateCustomer($this, update) {
    var $sender     = $('#box-sender');
    var customerId  = $this.val();
    var selectedVal = $this.find('option:selected');

    $('input[name=customer_id]').val(customerId);
    $('input[name=customer_km]').val(selectedVal.data('kms'));
    $sender.find('.has-error').remove();

    if(update) {

        if(IS_PICKUP == "1") {
            $('[name=recipient_name]').val(selectedVal.data('name'));
            $('[name=recipient_address]').val(selectedVal.data('address'));
            $('[name=recipient_zip_code]').val(selectedVal.data('zip_code'));
            $('[name=recipient_city]').val(selectedVal.data('city'));
            $('[name=recipient_phone]').val(selectedVal.data('phone'));
            $('[name=recipient_country]').val(selectedVal.data('country')).trigger("change.select2");
            $('[name=agency_id]').val(selectedVal.data('agency')).trigger("change.select2");
            $('[name=recipient_agency_id]').val(selectedVal.data('agency')).trigger("change.select2");

            validatePhone($('[name=recipient_phone]'));
        } else {
            $('[name=sender_name]').val(selectedVal.data('name'));
            $('[name=sender_address]').val(selectedVal.data('address'));
            $('[name=sender_zip_code]').val(selectedVal.data('zip_code'));
            $('[name=sender_city]').val(selectedVal.data('city'));
            $('[name=sender_phone]').val(selectedVal.data('phone'));
            $('[name=sender_country]').val(selectedVal.data('country')).trigger("change.select2");
            $('[name=agency_id]').val(selectedVal.data('agency')).trigger("change.select2");
            $('[name=sender_agency_id]').val(selectedVal.data('agency')).trigger("change.select2");
            if(selectedVal.data('obs') && $('#modal-remote-xl [name=obs]').val() == '') {
                $('[name=obs]').val(selectedVal.data('data.obs'));
            }

            validatePhone($('[name=sender_phone]'));
        }
    }
}

if(SHIPMENT_EXISTS == 1) {
    $('[name=customer_id]').on('change', function(e) {
        e.preventDefault();
        $('#modal-confirm-change-customer').addClass('in').show();
    });

    $('#modal-confirm-change-customer .btn-confirm-no').on('click', function(e){
        $('#modal-confirm-change-customer').removeClass('in').hide();
    })

    $('#modal-confirm-change-customer .btn-confirm-yes').on('click',function(){
        $('#modal-confirm-change-customer').removeClass('in').hide();
        var $this = $('[name=customer_id]');
        updateCustomer($this, true);
        getAutoKm()
    })
}

/**
 * GET SENDER AGENCY FROM ZIP CODE
 * ajax method
 */
$('[name=sender_zip_code]').on('change', function () {
    var $this   = $(this);
    var zipCode = $this.val();
    var country = $('[name=sender_country]').val();
    var autoKm  = false;

    $('label[for=sender_address] .fa-spin').removeClass('hide');
    $('[name=sender_city]').val('');
    $('[name=sender_city]').autocomplete('dispose')

    $.post(ROUTE_GET_AGENCY, {zipCode: zipCode}, function (data) {
        data.zone = data.zone == '' ? 'pt' : data.zone;
        $('[name=sender_country]').val(data.zone).trigger("change.select2");

        if($('[name="sender_agency_id"]').is(':visible')) { //só atualiza se o sender agency id nao esta bloqueado
            if (data.agency_id) {
                $('[name=sender_agency_id]').val(data.agency_id).trigger("change.select2");
            } else {
                $('[name=sender_agency_id]').val($('#modal-remote-xl select[name=agency_id]').val()).trigger("change.select2");
            }
        }

        if (data.cities.length > 1) {
            $('[name=sender_city]').autocomplete({
                lookup: data.cities,
                minChars: 0,
                onSelect: function (suggestion) {
                    $('[name="sender_city"]').val(suggestion.data).trigger('change');
                    if(suggestion.country != $('[name="sender_country"]').val()) {
                        $('[name="sender_country"]').val(suggestion.country).trigger('change');
                    }

                    var zipCode  = $('[name=sender_zip_code]').val();
                    $('[name="sender_city"]').closest('.form-group').removeClass('has-error');
                    if(!ZipCode.validate($('[name=sender_country]').val(), zipCode)) {
                        $('.modal [name=sender_zip_code]').closest('.form-group').addClass('has-error');
                    }
                },
            });
        } else if (data.cities.length == 1) {
            $('[name=sender_city]').val(data.cities[0]['value']);
            autoKm = true
        }

        if ($('[name=sender_country]').val() != '') {
            $('.btn-refresh-prices').trigger('click');
        }

        filterServicesList(data.services);

        if(autoKm && $('.modal-xl .btn-auto-km').length && $('[name=service_id] option:selected').data('unity') == 'km') {
            getAutoKm();
        }

    }).fail(function () {
        Growl.error('Ocorreu um erro ao obter a agência correspondente.')
    }).always(function () {
        $('label[for=sender_address] i').addClass('hide');
        $('[name=sender_city]').focus();

        $this.closest('.form-group').removeClass('has-error');
        if(!ZipCode.validate($('[name=sender_country]').val(), zipCode)) {
            $('.modal [name=sender_zip_code]').closest('.form-group').addClass('has-error');
        }
    })
});

/**
 * GET RECIPIENT AGENCY FROM ZIP CODE
 * ajax method
 */
$('[name=recipient_zip_code]').on('change', function () {
    var $this    = $(this);
    var zipCode  = $this.val();
    var country  = $('[name=recipient_country]').val();
    var provider = $('.modal-xl [name=provider_id]').val();
    var autoKm  = false;

    $('label[for=recipient_address] .fa-spin').removeClass('hide');
    $('[name=recipient_city]').val('');
    $('[name=recipient_city]').autocomplete('dispose');

    $.post(ROUTE_GET_AGENCY, {zipCode: zipCode, provider:provider}, function (data) {
        $('[name=recipient_country]').val(data.zone).trigger("change.select2");
        $('[name=recipient_agency_id]').html(data.agenciesHtml);

        zipCodeKms  = parseFloat(data.kms);
        customerKms = parseFloat($('[name=customer_km]').val());
        totalKms    = (customerKms + data.kms);
        $('[name=kms]').val(totalKms)

        if(data.provider_id) {
            $('[name=sync_agencies]').val('1');
            $('[name=provider_id]').val(data.provider_id).trigger("change.select2");
        }

        if(data.agency_id) {
            $('[name=recipient_agency_id]').val(data.agency_id).trigger('change.select2');
        } else {
            $('[name=recipient_agency_id]').val($('#modal-remote-xl select[name=agency_id]').val()).trigger("change.select2");
        }

        if(data.cities.length > 1) {
            $('[name=recipient_city]').autocomplete({
                lookup: data.cities,
                minChars: 0,
                onSelect: function (suggestion) {
                    $('[name="recipient_city"]').val(suggestion.data).trigger('change');
                    if(suggestion.country != $('[name="recipient_country"]').val()) {
                        $('[name="recipient_country"]').val(suggestion.country).trigger('change');
                    }

                    var zipCode  = $('[name=recipient_zip_code]').val();
                    $('[name="recipient_city"]').closest('.form-group').removeClass('has-error');
                    if(!ZipCode.validate($('[name=recipient_country]').val(), zipCode)) {
                        $('.modal [name=recipient_zip_code]').closest('.form-group').addClass('has-error');
                    }
                },
            });
        } else if(data.cities.length == 1) {
            $('[name=recipient_city]').val(data.cities[0]['value'])
            autoKm = true;
        }

        if($('[name=recipient_country]').val() != '') {
            $('.btn-refresh-prices').trigger('click');
        }

        filterServicesList(data.services);

        if(autoKm && $('.modal-xl .btn-auto-km').length && $('[name=service_id] option:selected').data('unity') == 'km') {
            getAutoKm();
        }

    }).fail(function () {
        Growl.error('Ocorreu um erro ao obter a agência correspondente.')
    }).always(function() {
        $('label[for=recipient_address] i').addClass('hide');
        $('[name=recipient_city]').focus();

        $this.closest('.form-group').removeClass('has-error');
        if(!ZipCode.validate($('[name=recipient_country]').val(), zipCode)) {
            $('.modal [name=recipient_zip_code]').closest('.form-group').addClass('has-error');
        }
    })
});

$('[name=sender_country]').on('change', function () {

    var provider = $('[name=provider_id]').val();
    var zipCode  = $('[name=sender_zip_code]').val();
    var country  = $(this).val();

    $('[name=sender_zip_code]').closest('.form-group').removeClass('has-error');
    if(!ZipCode.validate(country, zipCode)) {
        $('[name=sender_zip_code]').closest('.form-group').addClass('has-error');
    }

    $.post(ROUTE_GET_AGENCY, {zipCode: zipCode, provider:provider, country:country}, function (data) {
        $('[name=sender_agency_id]').html(data.agenciesHtml);

        zipCodeKms  = parseFloat(data.kms);
        customerKms = parseFloat($('[name=customer_km]').val());
        totalKms    = (customerKms + data.kms);
        $('[name=kms]').val(totalKms)

        if(data.provider_id) {
            $('[name=sync_agencies]').val('1');
            $('[name=provider_id]').val(data.provider_id).trigger("change.select2");
        }

        if($('[name="sender_agency_id"]').is(':visible')) {
            if (data.agency_id) {
                $('[name=sender_agency_id]').val(data.agency_id).trigger('change.select2');
            } else {
                $('[name=sender_agency_id]').val($('#modal-remote-xl select[name=agency_id]').val()).trigger("change.select2");
            }
        }

        filterServicesList(data.services);
    })
})

$('[name=recipient_country]').on('change', function () {

    var provider = $('[name=provider_id]').val();
    var zipCode  = $('[name=recipient_zip_code]').val();
    var country  = $(this).val();

    $('[name=recipient_zip_code]').closest('.form-group').removeClass('has-error');
    if(!ZipCode.validate(country, zipCode)) {
        $('[name=recipient_zip_code]').closest('.form-group').addClass('has-error');
    }

    $.post(ROUTE_GET_AGENCY, {zipCode: zipCode, provider:provider, country:country}, function (data) {
        $('[name=recipient_agency_id]').html(data.agenciesHtml);

        zipCodeKms  = parseFloat(data.kms);
        customerKms = parseFloat($('[name=customer_km]').val());
        totalKms    = (customerKms + data.kms);
        $('[name=kms]').val(totalKms)

        if(data.provider_id) {
            $('[name=sync_agencies]').val('1');
            $('[name=provider_id]').val(data.provider_id).trigger("change.select2");
        }

        if(data.agency_id) {
            $('[name=recipient_agency_id]').val(data.agency_id).trigger('change.select2');
        } else {
            $('[name=recipient_agency_id]').val($('#modal-remote-xl select[name=agency_id]').val()).trigger("change.select2");
        }

        filterServicesList(data.services);
    })
})

/**
 * CHANGE PROVIDER
 * ajax method
 */
$('.modal-xl [name=provider_id]').on('change', function () {

    var sync = $('[name=sync_agencies]').val();
    var provider = $(this).val();
    var zipCode  = $('.modal-xl [name=recipient_zip_code]').val();

    if(sync == '1') {
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
});

$('.modal-xl [name=provider_id]').on('select2:opening', function (e) {
    if($('#modal-confirm-change-provider').length) {
        e.preventDefault();
        $('#modal-confirm-change-provider').addClass('in').show();
        return false;
    }
});

$('#modal-confirm-change-provider .btn-confirm-yes').on('click', function () {
    $this = $(this);
    $this.button('loading')
    $.post(ROUTE_SYNC_RESET, {delete_provider: 1}, function(data){
        if(data.result) {
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

$('[name="total_price"]').on('keyup change', function(){
    sumTotalPrice()
})

function sumTotalPrice() {

    totalPrice    = parseFloat($('[name="total_price"]').val());
    totalPrice    = isNaN(totalPrice) ? 0 : totalPrice;
    totalExpenses = parseFloat($('[name="total_expenses"]').val());
    totalExpenses = isNaN(totalExpenses) ? 0 : totalExpenses;
    totalWithVat  = (totalPrice + totalExpenses)  * (1 + parseFloat(VAT_PERCENT));

    total = (parseFloat(totalPrice)+parseFloat(totalExpenses)).toFixed(2)
    $('.sum-total').html(total + '€');
    $('.helper-detail-price b').html(totalWithVat.toFixed(2) + '€');
}

/**
 * Add adicional address
 */
$('#modal-remote-xl [data-action="add-addr"]').on('click', function(e){
    var hash = Str.random(5)
    var $mainObj = $(this).closest('.box');
    var $element = $(this).closest('.address-tabs');
    var totalTab = parseInt($element.find('[data-action="pnladdr"]').length);
    var tabHtml  = '';

    $('.address-left').on('click', function(){
        $('.address-right').hide();
    })

    $('.address-right').on('click', function(){
        $('.address-left').hide();
    })

    if(!totalTab) {
        tabHtml+= '<li data-action="pnladdr" data-id="main-addr">Local 1</li>';
        tabHtml+= '<li class="active" data-action="pnladdr" data-id="' + hash +'">Local 2 <span><i class="fas fa-times fs-10"></i></span></li>';
    } else {
        tabHtml+= '<li class="active" data-action="pnladdr" data-id="' + hash +'">Local ' + (totalTab + 1) + '<span><i class="fas fa-times fs-10"></i></span></li>';
    }

    $element.find('[data-action="pnladdr"]').removeClass('active');
    $mainObj.find('.new-addr, .main-addr').hide();

    $element.find('li:last-child').before(tabHtml);

    $targetObj = $mainObj.find('.box-body>div:first-child').clone();
    $targetObj.removeClass('main-addr')
        .addClass('new-addr')
        .addClass(hash)
        .show();

    $targetObj.find('input, select').each(function(item) {
        $(this).attr('name', 'addr[' + hash + '][' + $(this).attr('name') +']');
        $(this).val('')
    });

    $targetObj.find('.select2-container').remove();
    $targetObj.find('.select2').select2(Init.select2())
    $targetObj.find('.select2-country').select2(Init.select2Country())
    $mainObj.find('.box-body').append($targetObj.append());
})

//Enable address tab
$(document).on('click', '#modal-remote-xl [data-action="pnladdr"]', function(){
    var hash = $(this).data('id');
    $(this).closest('.address-tabs').find('[data-action="pnladdr"]').removeClass('active');
    $(this).addClass('active');
    $(this).closest('.box').find('.box-body>div').hide();
    $('.' + hash).show();
})

//Remove adicional address
$(document).on('click', '#modal-remote-xl [data-action="pnladdr"] span', function(e){
    e.preventDefault();
    var $mainObj = $(this).closest('.box');
    var $element = $(this).closest('.address-tabs');
    var $li      =  $(this).closest('li');
    var hash     = $li.data('id');

    if(hash != 'main-addr') {
        $(this).closest('.box').find('.' + hash).remove();
        $(this).closest('li').remove();

        if($element.find('[data-action="pnladdr"]').length == 1) {
            $element.find('[data-action="pnladdr"]').remove();
            $('.address-right, .address-left').show();
        } else {
            var count = 1;
            $element.find('[data-action="pnladdr"]').each(function(){
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

/**
 * Submit form
 *
 * @param {type} param1
 * @param {type} param2S
 */
$('.form-shipment').on('submit', function(e){
    e.preventDefault();

    if($(document).find('.has-error').length) {
        Growl.error("<i class='fas fa-exclamation-circle'></i> Corrija os campos a vermelho antes de gravar.");
    } else {
        var $form = $(this);
        var $button = $('button[type=submit],.btn-submit');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function (data) {
            if (data.result && !data.syncError) {
                if(typeof oTable !== "undefined"){
                    oTable.draw(false); //update datatable without change pagination
                }

                Growl.success(data.feedback);
                if (data.printGuide || data.printLabel) {

                    if (data.printGuide) {
                        if (window.open(data.printGuide, '_blank')) {
                            $('#modal-remote-xl').modal('hide');
                        } else {
                            $('#modal-remote-xl').find('.modal-xl').removeClass('modal-xl').find('.modal-content').html(data.html);
                        }
                    }

                    if (data.printLabel) {
                        if (window.open(data.printLabel, '_blank')) {
                            $('#modal-remote-xl').modal('hide');
                        } else {
                            $('#modal-remote-xl').find('.modal-xl').removeClass('modal-xl').find('.modal-content').html(data.html);
                        }
                    }

                } else {
                    $('#modal-remote-xl').modal('hide');
                }

            } else if (data.syncError) {

                if(data.trkid) {
                    $(document).find('[name="trkid"]').remove();
                    $('.form-shipment').append('<input type="hidden" name="trkid" value="'+data.trkid+'"/>');
                }

                $('#modal-confirm-sync-error').find('.error-msg').html(data.feedback)
                $('#modal-confirm-sync-error').find('.error-provider').html($('[name="provider_id"] option:selected').text())
                $('#modal-confirm-sync-error').addClass('in').show();

                $('#modal-confirm-sync-error .btn-confirm-no').on('click', function(e){
                    $('#modal-confirm-sync-error').removeClass('in').hide();
                })

                $('#modal-confirm-sync-error .btn-confirm-yes').on('click',function(){
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


    $('#modal-remote-xl').on('hidden.bs.modal', function(){
        $('.search-sender').autocomplete('dispose')
        $('.search-recipient').autocomplete('dispose')
        $('[name="sender_city"]').autocomplete('dispose')
        $('[name="recipient_city"]').autocomplete('dispose')
    })
});

function filterServicesList(servicesArr) {

    if(servicesArr) {
        if($.inArray(parseInt($('.modal [name=service_id]').val()), servicesArr) === -1) {
            $('[name=service_id]').val('').trigger('change.select2'); //remove serviço selecionado
        }

        var countEnabled = 0;
        $('.modal [name=service_id] option').each(function(item){
            if($.inArray(parseInt($(this).val()), servicesArr) !== -1) {
                $(this).prop('disabled', false);
                countEnabled++;
            } else {
                $(this).prop('disabled', 'disabled');
            }
        })

        if(countEnabled == 1) {
            $('.modal [name=service_id] option:not(:disabled)').prop('selected', true); //seleciona automatico se apenas 1 opção disponível
        }

    } else {
        $('.modal [name=service_id] option').each(function(){
            $(this).prop('disabled', false);
        });
    }

    $(document).find('.modal [name=service_id]').select2(Init.select2());
}


/**
 *
 */
function isMobile(phone, country) {

    var validCountries = ['pt','es','ad'];
    var country = country.toLowerCase();

    if(validCountries.includes(country) || phone) {
        return false;
    }

    if(country == 'pt') {
        regex = new RegExp('^(\\+351|00351|351)?[9][0-9]{8}$');
    } else if(country == 'es') {
        regex = new RegExp('^(\\+34|0034|34)?[6|7][0-9]{8}$');
    } else if(country == 'ad') {
        regex = new RegExp('^(\\+376|00376|376)?[0-9]{6}$');
    } else {
        return true;
    }

    return regex.test(phone)
}