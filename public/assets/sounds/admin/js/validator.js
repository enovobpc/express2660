/**
 * Force input to dont accept spaces
 */
$(document).on('keypress', '.nospace',function(e) {
    if(e.which === 32) { return false; }
})

$(document).on('keyup', '.nospace',function(e) {
    $(this).val($(this).val().replace(/\s/g, ''))
});

$(document).on('click', '.nospace',function(e) {
    $(this).val($(this).val().replace(/\s/g, ''))
});

/**
 * Force input to be lowercase
 */
$(document).on('keypress keyup', '.lowercase',function(e) {
    $(this).val($(this).val().toLowerCase());
})

/**
 * Force input to be uppercase
 */
$(document).on('keypress keyup', '.uppercase',function(e) {
    $(this).val($(this).val().toUpperCase());
})


/**
 * Force input to have first char in uppercase
 */
$(document).on('keypress keyup', '.ucwords',function(e) {
    $(this).val($(this).val().toLowerCase().replace(/^(.)|(\s|\-)(.)/g, function(c) {
        return c.toUpperCase();
    }));
})

/**
 * validate phone
 */

function validatePhone($this) {

    $this.closest('.form-group').removeClass('has-error');

    var feedback = '';
    var phone = $this.val();
    var phone = phone.replace(/[^\d/+;]/g, '');

    $this.val(phone);

    var invalidPhones = [
        '123456789',
        '999999990',
        '+++++++++',
        '/////////',
        '999999000'
    ]

    if($this.val() != '') {

        if (phone.length < 9) {
            feedback = 'O telefone indicado tem dígios em falta.'
        }

        if (phone.length > 25) {
            feedback = 'O contacto indicado é demasiado extenso.'
        }

        if(/^(.)\1+$/.test(phone)) {
            feedback = 'O número de telefone indicado é inválido.'
        }

        if ($.inArray(phone, invalidPhones) >= 0) {
            feedback = 'O número de telefone indicado é inválido.'
        }

        if (feedback != '') {
            $this.closest('.form-group').addClass('has-error');
            Growl.error(feedback)
        }
    }



    /*var feedback = '';

    console.log('Telefone: ' + $this.val());
 
    $this.closest('.form-group').removeClass('has-error');

    if($this.val() != '') {
        if(!$this.intlTelInput("isValidNumber")) {
            console.log('ERRO = ' + $this.intlTelInput("isValidNumber"))
            feedback = 'O número de telefone indicado é inválido.'
        }

        var intlTelError = $this.intlTelInput("getValidationError");

        if (intlTelError == 1) { //1 = INVALID_COUNTRY_CODE
            feedback = 'O código de chamada do país está incorreto.'
        }

        if (intlTelError == 2) { //2 = TOO_SHORT
            feedback = 'O telefone indicado tem dígios em falta.'
        }

        if (intlTelError == 3) { //3 = TOO_LONG
            feedback = 'O telefone indicado tem dígitos a mais.'
        }

        if (intlTelError == 4) { //3 = NOT_A_NUMBER
            feedback = 'O telefone indicado não é um número de telefone.'
        }

        if(feedback != '') {
            $this.closest('.form-group').addClass('has-error');
            Growl.error(feedback)
        }
    } else {
        $this.intlTelInput("setCountry", APP_COUNTRY)
    }*/
}

$(document).on('keyup keypress', '.phone', function(e){

    var $this = $(this);
    var phone = $(this).val();
    phone = phone.replace(/[^\d/+;]/g, '');

    if(phone.startsWith("00") || phone.startsWith("119") || phone.startsWith("810")) {
        $.each(DIALCODES00, function(key, value){
            if(phone.startsWith(value)) {
                phone = phone.replace(value, DIALCODES[key])
                /*$this.intlTelInput("setCountry", key)*/

                return false;
            }
        })
    }

    $(this).val(phone);

    var validKeys = ['0','1','2','3','4','5','6','7','8','9','+','/']
    if (validKeys.indexOf(e.key) < 0) {
        e.preventDefault();
    }
})

$(document).on('change', '.phone', function(e) {
    validatePhone($(this));
})

/*Comentado em 7/novembro/2019*/
/*$(".phone").intlTelInput(Init.intlTelInput())*/


/**
 * Force input to have only numbers
 */
$(document).on('keyup keypress', '.int, .number',function(e) {
    if (e.which < 48 || e.which > 57) {
        e.preventDefault();
    }
});

/**
 * Force input to have first char in uppercase
 */
$(document).on('keyup keypress', '.decimal',function(e) {

    var val = $(this).val();
    val = val.replace(',','.');

    $(this).val(val)
    var charCode = (e.which) ? e.which : e.keyCode;

    if (charCode == 46 || charCode == 44) {

        //Check if the text already contains the . character
        if (val.indexOf('.') === -1) {
            return true;
        } else {
            return false;
        }
    } else {

        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
    }
    return true;
});

$(document).on('keyup keypress', '.url',function(e) {
    var url = $(this).val();

    if (!/^https?:\/\//i.test(url) && url != '') {
        url = 'http://' + url;
    }

    $(this).val(url);
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator


    if(pattern.test(url) || url == '') {
        $(this).closest('.form-group').removeClass('has-error');
    } else {
        $(this).closest('.form-group').addClass('has-error');
    }
});

$(document).on('change', '[type="email"], input.email',function(e) {
    var emails = [];
    var error  = false;
    var email = $(this).val();

    email = email.replace(',', ';')
    email = email.replace(' ', '')
    email = email.trim();
    emailArr = email.split(";");
    
    var pattern = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    $(this).closest('.form-group').removeClass('has-error');
    $.each(emailArr, function(key, value){
        if(pattern.test(value) || email == '') {
            emails.push(value);
        } else {
            error = true;
        }
    });

    if(error) {
        $(this).closest('.form-group').addClass('has-error');
        emails = emailArr;
    }

    $(this).val(emails.join(';'));
});

/**
 * Auto trim inputs when click or focus out
 */
$(document).on('click focusout', 'input[type="text"],input[type="email"],input[type="textarea"]',function(e) {
    $(this).val($(this).val().trim());
})

/**
 * Validate IBAN format
 */
$(document).on('change click', 'input.iban', function(e){

    $(this).mask('SS00 AAAA 0000 0000 0000 0000 000A AAAA');

    var pattern = new RegExp('[a-zA-Z]{2}[0-9]{2}[a-zA-Z0-9]{4}[0-9]{7}([a-zA-Z0-9]?){0,16}');
    var iban    = $(this).val().replace(/\s/g,'');

    if(pattern.test(iban) || iban == '') {
        $(this).closest('.form-group').removeClass('has-error');
    } else {
        $(this).closest('.form-group').addClass('has-error');
    }
})

/**
 * VALIDATE VAT
 */
$(document).on('keypress', '.vat',function(e) {
    if(e.which === 32) { return false; }
})

$(document).on('keyup', '.vat',function(e) {
    $(this).val($(this).val().replace(/\s/g, '').toUpperCase())
});

$(document).on('change', '.vat',function(e) {

    $(this).closest('.form-group').removeClass('has-error');

    if($(this).val() != '') {

        var vat = $(this).val();
        var country = $('[name="'+$(this).data('country') + '"]').val();

        $result = checkVATNumber(country + vat, country);

        if ($result.valid_vat) {
            Growl.success('O NIF é válido.');
        } else {
            $(this).closest('.form-group').addClass('has-error');
            Growl.error('O NIF é inválido para o país indicado.')
        }
    }
})

/**
 * Format slug
 */
$(document).on('keyup', '.slug', function () {
    var str = Str.slug($(this).val().toLowerCase());
    $(this).val(str)
})

/**
 * Validate zip codes
 */
/*$(document).on('change', '[data-zp-zip-code], [data-zp-country]', function() {
    var zipCode, country;
    if($(this).data('zp-country') != '') {
        zipCode = $(this).val();
        country = $($(this).data('zp-country')).val()
    } else if($(this).data('zp-zip-code').val()) {
        zipCode = $($(this).data('zp-zip-code')).val();
        country = $(this).val();
    }

    valid = ZipCode.validate(country, zipCode);

    if($(this).data('zp-country') != '') {
        $(this).closest('.form-group').addClass('has-error');
    } else if($(this).data('zp-zip-code').val()) {
        $($(this).data('zp-zip-code')).closest('.form-group').addClass('has-error');
    }
})*/

/**
 * Block auto submit if has has-error class
 */
$(document).on('click', 'button[type="submit"]', function(e) {
    if($(document).find('.has-error').length) {
        e.preventDefault();
        Growl.error("<i class='fas fa-exclamation-circle'></i> Corrija os campos a vermelho antes de gravar.")
    }

    /*else {
        $(this).closest('form').submit();
    }*/
})

/**
 * Count remaining characters
 */
$(document).on('keydown keyup', '[maxlength]', function () {
    var str      = $(this).val();
    var strLen   = parseInt(str.length)
    var maxLimit = parseInt($(this).attr('maxlength'))
    var $label   = $(this).closest('.form-group').find('label[data-content]');

    $label.removeClass('overlimit')
    if (strLen > maxLimit) {
        return false;
    } else if(strLen == maxLimit) {
        $label.addClass('overlimit')
    }

    remaining = maxLimit - strLen;
    $label.attr('data-content', remaining + ' caractéres restantes');
})
