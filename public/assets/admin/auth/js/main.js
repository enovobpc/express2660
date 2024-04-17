if($('[name="email"]').val() != '') {
    $('[name="email"]').focus();
}

$(document).on('change', '[name="email"],[name="password"]', function(){
    $('.help-block').slideDown('slow');
})

$(document).on('submit', 'form', function(){
    $('.main-block').hide();
    $('.submit-loading').show();

    setTimeout(function(){
        $('.submit-loading').find('p').html('A ligação parece estar a demorar um pouco mais que o normal.');
        $('.login100-form-btn').submit();
    }, 5000);
})

$(document).on('keyup', '[name="email"]', function(){
    $(this).val($(this).val().toLowerCase());
    $(this).val($(this).val().replace(/\s/g, ''))
})

$(document).on('keypress', '.nospace',function(e) {
    if(e.which === 32) { return false; }
})

$(document).on('keyup', '.nospace',function(e) {
    $(this).val($(this).val().replace(/\s/g, ''))
});

/**
 * Helper functions
 */
var Helper = {

    /**
     * Replace broken images by a given image url
     * @param image
     * @param replaceUrl
     * @returns {boolean}
     */
    imgError: function(image, replaceUrl) {
        image.onerror = "";
        image.src     = replaceUrl;
        return true;
    }
};

(function ($) {
    "use strict";

    /*==================================================================
    [ Focus input ]*/
    $('.input100').each(function(){
        $(this).on('blur', function(){
            if($(this).val().trim() != "") {
                $(this).addClass('has-val');
            }
            else {
                $(this).removeClass('has-val');
            }
        })    
    })
  
  
    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit',function(){
        var check = true;

        for(var i=0; i<input.length; i++) {
            if(validate(input[i]) == false){
                showValidate(input[i]);
                check=false;
            }
        }

        return check;
    });


    $('.validate-form .input100').each(function(){
        $(this).focus(function(){
           hideValidate(this);
        });
    });

    function validate (input) {
        if(($(input).attr('type') == 'email' || $(input).attr('name') == 'email') && $(input).val().includes("@")) {
            if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        }
        else {
            if($(input).val().trim() == ''){
                return false;
            }
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');
    }
    
    /*==================================================================
    [ Show pass ]*/
    var showPass = 0;
    $('.btn-show-pass').on('click', function(){
        if(showPass == 0) {
            $(this).next('input').attr('type','text');
            $(this).find('i').removeClass('zmdi-eye');
            $(this).find('i').addClass('zmdi-eye-off');
            showPass = 1;
        }
        else {
            $(this).next('input').attr('type','password');
            $(this).find('i').addClass('zmdi-eye');
            $(this).find('i').removeClass('zmdi-eye-off');
            showPass = 0;
        }
    });
})(jQuery);