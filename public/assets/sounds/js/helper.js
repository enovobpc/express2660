/**
 * Init configurations
 */
var Init = {
   
   select2: function() {
        return {
            language: 'pt',
            minimumResultsForSearch: 7
        };
    },

    datepicker: function() {
        return {
            format: 'yyyy-mm-dd',
            language: 'pt'
        };
    },
    
    iCheck: function() {
        return {
            checkboxClass: 'icheckbox_minimal-orange',
            radioClass: 'iradio_minimal-orange',
            increaseArea: '20%'
        }
    },

    intlTelInput: function() {
        return {
            allowDropdown: false,
            autoHideDialCode: true,
            autoPlaceholder: false,
            customContainer: "",
            customPlaceholder: null,
            dropdownContainer: null,
            excludeCountries: [],
            formatOnDisplay: false,
            geoIpLookup: null,
            hiddenInput: "",
            initialCountry: APP_COUNTRY,
            localizedCountries: null,
            nationalMode: true,
            onlyCountries: [],
            placeholderphoneType: "MOBILE",
            preferredCountries: ["pt", "es", "fr", "de", "ch"],
            separateDialCode: false,
            utilsScript: "/vendor/intl-tel-input/build/js/utils.js",
        }
    },

    /**
     * Translations for dropzone plugin
     */
    dropzoneTranslations: function() {
      Dropzone.options.imageDropzone = {
        //paramName: "file", // The name that will be used to transfer the file
        maxFilesize: 6, // MB
        addRemoveLinks: true,
        acceptedFiles: 'image/*',
        dictDefaultMessage: "Arraste imagens ou clique aqui para fazer upload",
        dictFallbackMessage: "O seu browser não suporta a funcionalidade drag'n'drop (arrastar).",
        dictFallbackText: "Utilize o formulário abaixo para fazer o upload de seus ficheiros.",
        dictFileTooBig: "O ficheiro é demasiado grande({{filesize}}MB). Tamanho máximo: {{maxFilesize}}MB.",
        dictInvalidFileType: "Não pode fazer upload de ficheiros deste tipo.",
        dictResponseError: "O servidor respondeu com o código {{statusCode}}.",
        dictCancelUpload: "Cancelar upload",
        dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
        dictCancelUploadConfirmation: "Tem acerteza que pretende cancelar o upload?",
        dictRemoveFile: "Remover ficheiro",
        dictRemoveFileConfirmation: null,
        dictMaxFilesExceeded: "Não pode enviar mais ficheiros",
        success: function(file, response) {
            $('.uploaded-images').prepend(response.html);
            $('.uploaded-images > .no-images').remove();

            var num = parseInt($('#foto-count').text());
            $('#foto-count').text(num+1);
        },
      };
    },
    
    /**
     * Init popover for preview images
     */
    imagePreview: function() {
      $(document).popover({
          placement: 'auto bottom',
          container: 'body',
          trigger: 'hover',
          selector: '.image-preview',
          html: true,
          delay: 100,
          content: function() {
               return '<img class="img-responsive" src="'+$(this).data('img') + '" />';
          },
      });
    },

    /**
     * Init tabs hash history
     */
    tabsHash: function() {

        $('.nav-tabs > li > a').not('.nav-tab-url > li > a').bind('click', function (e) {
            var url = $(this).attr('href');
            url = url.replace("#tab-", "");
            window.history.pushState("", "", "?tab=" + url);
        });
    },

    /**
     * Init bootstrapGrowl
     */
    bootstrapGrowl: function() {
        //change offset tip to go under the mainbar
        $.bootstrapGrowl.default_options.offset = {
            from: "top",
            amount: 55
        };
    },

    /**
     * Init tooltip
     */
    tooltip: function() {
        return {
            trigger: 'hover',
            selector: '[data-toggle="tooltip"]',
        }
    },
}

/**
 * Helper functions
 */
var Helper = {

    /**
     * Show header alert
     * 
     * @param {type} type
     * @param {type} message
     * @param {type} timeOut
     * @returns {undefined}
     */
    showHeaderAlert: function(type, message, timeOut) {

        clearTimeout(this.timeoutHandler);
        $(".ajax-alert").remove();
 
        var alertType = "alert-" + type;
        var div = "<div class=\"ajax-alert alert " + alertType + "\">";
        div += "<a class=\"close\" onclick=\"Helper.hideHeaderAlert()\" data-dismiss=\"alert\" href=\"#\">⨉</a>"
        div += message + "</div>";
        var $header = $(".alert-top");
        var $novo = $(div);
        $novo.hide();
        $header.append($novo);
        $novo.fadeIn("fast");
 
        //default value
        if (typeof timeOut == 'undefined') {
            timeOut = 10000;
        }
 
        if (timeOut != 0)
            this.timeoutHandler = setTimeout(this.hideHeaderAlert, timeOut);
    },
    
    /**
     * Hide header alert
     * 
     * @returns {undefined}
     */
    hideHeaderAlert: function() {
        clearTimeout(this.timeoutHandler);
        $(".ajax-alert").fadeOut("fast", function() {
            this.remove();
        });
    },
    
    /**
     * Open a popup on center of screen
     * 
     * @param {type} url
     * @param {type} title
     * @param {type} w
     * @param {type} h
     * @returns {undefined}
     */
    popupCenter: function (url, title,w, h) {
        // Fixes dual-screen position                         Most browsers      Firefox
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;
        var newWindow = window.open(url, title, 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

        // Puts focus on the newWindow
        if (window.focus) {
            newWindow.focus();
        }
    }
};


var URL = {
    /**
     * Change url in adress bar
     * @param string url
     * @param boolean fullPath
     * @returns void
     */
    change: function(url, fullPath) {
        if (typeof fullPath === 'undefined' || fullPath === false) {
            window.history.replaceState("object or string", "Title", url);
        } else {
            var currentUrl = document.URL
            window.history.replaceState("object or string", "Title", currentUrl + url);
        }
    },
    /*
     *  Return the anchor portion of a URL. e.g. #part2  
     */
    getUrlHash: function() {
        return document.location.hash;
    },
    
    /**
     * Return the given url without query string
     */
    removeQueryString: function(url){
        return url.split("?")[0];
    },
    
    /**
     * Return the query string of given url
     */
    getQueryString: function(url){
        return url.split("?")[1];
    },
    
    /**
     * Return current url path
     * @returns {window.location.pathname|DOMString}
     */
    currentPath: function(){
        return window.location.pathname
    },
    
    /**
     * Return current full url
     * @returns {DOMString}
     */
    current: function() {
        return window.location.href
    },
    
    /**
     * Return current full url
     * @returns {DOMString}
     */
    removeParameter: function(url, parameter) {
        //prefer to use l.search if you have a location/link object
        var urlparts= url.split('?');   
        if (urlparts.length>=2) {

            var prefix= encodeURIComponent(parameter)+'=';
            var pars= urlparts[1].split(/[&;]/g);

            //reverse iteration as may be destructive
            for (var i= pars.length; i-- > 0;) {    
                //idiom for string.startsWith
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                    pars.splice(i, 1);
                }
            }

            url= urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
            return url;
        } else {
            return url;
        }
    },
    
    /**
     * Update url parameter
     * 
     * @param {type} url
     * @param {type} param
     * @param {type} paramVal
     * @returns {String}
     */
    updateParameter: function(url, param, paramVal) {
        var TheAnchor = null;
        var newAdditionalURL = "";
        var tempArray = url.split("?");
        var baseURL = tempArray[0];
        var additionalURL = tempArray[1];
        var temp = "";

        if (additionalURL) 
        {
            var tmpAnchor = additionalURL.split("#");
            var TheParams = tmpAnchor[0];
                TheAnchor = tmpAnchor[1];
            if(TheAnchor)
                additionalURL = TheParams;

            tempArray = additionalURL.split("&");

            for (i=0; i<tempArray.length; i++)
            {
                if(tempArray[i].split('=')[0] != param)
                {
                    newAdditionalURL += temp + tempArray[i];
                    temp = "&";
                }
            }        
        }
        else
        {
            var tmpAnchor = baseURL.split("#");
            var TheParams = tmpAnchor[0];
                TheAnchor  = tmpAnchor[1];

            if(TheParams)
                baseURL = TheParams;
        }

        if(TheAnchor)
            paramVal += "#" + TheAnchor;

        var rows_txt = temp + "" + param + "=" + paramVal;
        return baseURL + "?" + newAdditionalURL + rows_txt;
    }
}



var Str = {
    
    /**
     * Returns a random string
     * 
     * @param {type} length
     * @returns {String}
     */
    random: function(length) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }
        return text;
    }
}