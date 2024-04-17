/**
 * Init configurations
 */
var Init = {

    niceScroll: function() {
        /*return {
            cursorcolor:"aquamarine",
            cursorwidth:"24px",
            background:"rgba(20,20,20,0.3)",
            cursorborder:"1px solid aquamarine",
            cursorborderradius:0
        }*/
    },

   select2: function() {
        return {
            language: LOCALE,
            minimumResultsForSearch: 7,
        };
    },

    select2Ajax: function(url) {
        return {
            url: url,
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        };
    },

    select2Country: function () {
        return {
            templateSelection: function(state) {
                if (!state.id) {
                    return state.text;
                }
                var $state = $(
                    '<span><div class="iti-flag '+state.element.value.toLowerCase()+'"></div> ' + state.text + '</span>'
                );
                return $state;
            }
        }
    },

    select2Multiple: function() {
        return {
            language: LOCALE,
            minimumResultsForSearch: 7,
            templateSelection: function(selected, total) {
                if(!selected.length) { return 'Todos'; }
                return selected.length + " selecionados";
            }
        }
    },

    tooltip: function() {
        return {
            trigger: 'hover',
            selector: '[data-toggle="tooltip"]',
        }
    },

    datepicker: function() {
        return {
            format: 'yyyy-mm-dd',
            language: LOCALE,
            todayHighlight: true
        };
    },

    timepicker: function() {
        return {
            timeFormat: 'hh:mm',
            interval: 60,
            dynamic: false,
            dropdown: true,
            scrollbar: true
        };
    },

    iCheck: function() {
        return {
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue',
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
     *
     */
    datatableTranslations: function() {

        if(LOCALE == 'pt' || LOCALE == '') {
            return {
                "sProcessing": "A processar...",
                "sLoadingRecords": "A carregar...",
                "sLengthMenu": "Ver _MENU_",
                "sZeroRecords": "Não foram encontrados resultados.",
                "sEmptyTable": "Não existem registos para apresentar.",
                //"sInfo": "Registo _START_ a _END_ | _TOTAL_ no total",
                "sInfo": "<span><b></b>_TOTAL_ registos</span> <small>A ver registo _START_ ao _END_ </small>",
                "sInfoEmpty": "",
                "sInfoFiltered": "<small>(filtrado de _MAX_ no total)</small>",
                "sInfoPostFix": "",
                "sSearch": "<i class='fas fa-search text-muted'></i>",
                "sUrl": "",
                "oPaginate": {
                    "sFirst": "<i class='fas fa-angle-double-left'></i>",
                    "sPrevious": "<i class='fas fa-angle-left'></i>",
                    "sNext": "<i class='fas fa-angle-right'></i>",
                    "sLast": "<i class='fas fa-angle-double-right'></i>",
                }
            };

        } else if(LOCALE == 'fr') {
            return {
                "sProcessing": "En traitement...",
                "sLoadingRecords": "En chargement...",
                "sLengthMenu": "Voir _MENU_",
                "sZeroRecords": "Aucun resultat n'a été trouvé",
                "sEmptyTable": "Il n'y a aucun enregistrement à afficher.",
                //"sInfo": "Registo _START_ a _END_ | _TOTAL_ no total",
                "sInfo": "<span><b></b>_TOTAL_ records</span> <small>Affichage de _START_ sur _END_ </small>",
                "sInfoEmpty": "",
                "sInfoFiltered": "<small>(filtrat de _MAX_ au total)</small>",
                "sInfoPostFix": "",
                "sSearch": "<i class='fas fa-search text-muted'></i>",
                "sUrl": "",
                "oPaginate": {
                    "sFirst": "<i class='fas fa-angle-double-left'></i>",
                    "sPrevious": "<i class='fas fa-angle-left'></i>",
                    "sNext": "<i class='fas fa-angle-right'></i>",
                    "sLast": "<i class='fas fa-angle-double-right'></i>",
                }
            };

        } else {
            return {
                "sProcessing": "Processing...",
                "sLoadingRecords": "Loading...",
                "sLengthMenu": "See _MENU_",
                "sZeroRecords": "No results were found.",
                "sEmptyTable": "There are no records to display.",
                //"sInfo": "Registo _START_ a _END_ | _TOTAL_ no total",
                "sInfo": "<span><b></b>_TOTAL_ records</span> <small>Viewing record _START_ to _END_ </small>",
                "sInfoEmpty": "",
                "sInfoFiltered": "<small>(filtered from _MAX_ in total)</small>",
                "sInfoPostFix": "",
                "sSearch": "<i class='fas fa-search text-muted'></i>",
                "sUrl": "",
                "oPaginate": {
                    "sFirst": "<i class='fas fa-angle-double-left'></i>",
                    "sPrevious": "<i class='fas fa-angle-left'></i>",
                    "sNext": "<i class='fas fa-angle-right'></i>",
                    "sLast": "<i class='fas fa-angle-double-right'></i>",
                }
            };
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

        $(document).find('.nav-tabs > li > a, .nav-pills > li > a').not('.nav-tab-url > li > a, [data-toggle="subtab"]').bind('click', function (e) {
            var tabUrl = $(this).attr('href');
            var tabId = tabUrl.replace("#tab-", "");
            var currentUrl = Url.current();
            var url = Url.updateParameter(currentUrl, 'tab', tabId);
            Url.change(url);
        });

        //subtabs
        $(document).on('click','.nav-tabs [data-toggle="subtab"]', function (e) {
            e.preventDefault();
            var tabUrl = $(this).attr('href');
            var tabId = tabUrl.replace("#tab-", "");
            var currentUrl = Url.current();
            var url = Url.updateParameter(currentUrl, 'subtab', tabId);
            Url.change(url);

            $(this).closest('ul').find('li').removeClass('active');
            $(this).closest('li').addClass('active');
            $(tabUrl).closest('.tab-content').find('.tab-pane').removeClass('active');
            $(tabUrl).addClass('active');
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
}


/**
 * Init configurations
 */
var Datatables = {

    trkDom: function() {
        return "<'row row-0'<'col-lg-10 col-md-9 col-sm-8 datatable-filters-area'><'col-sm-4 col-md-3 col-lg-2' '<'dt-srch' f'<'sbtn'>>><'col-sm-12 datatable-filters-area-extended'>>" +
            "<'row row-0'<'col-sm-12'tr>>" +
            "<'row row-0'<'col-xs-12 col-sm-5'li><'col-xs-12 col-sm-7'p>>"
    },

    trkSearch: function() {
        $('.dt-srch').before($('.fltr-trk').show())

    },

    /**
     * Search datatable only when press ENTER
     * @param oTable
     */
    searchOnEnter: function (oTable) {
        if(DATATABLE_SEARCH_ON_ENTER == 1) {
            $('.dataTables_filter').parent().addClass('dt-srch-enter');
            $('.dataTables_filter input').unbind();
            $('.dataTables_filter input').bind('keyup', function(e) {
                if((e.keyCode == 13 && typeof  oTable !== 'undefined') || $(this).val() == '') {
                    oTable.search($(this).val()).draw();
                }
            });
        }
    },

    /**
     * Cancel datatable previous request
     *
     * @param oTable
     */
    cancelDatatableRequest: function (oTable) {
        if(typeof oTable !== 'undefined') {
            if (oTable.hasOwnProperty('settings')) {
                oTable.settings()[0].jqXHR.abort();
            }
        }
    },

    /**
     * Event to execute when ajax call is complete
     */
    complete: function() {
        $('.dataTables_filter').find('i').removeClass('fa-circle-notch fa-spin').addClass('fa-search');
        $('.dataTable').width('auto');
    },

    /**
     * Default error handler
     *
     * @param oTable
     */
    error: function () {
        $.bootstrapGrowl("<i class='fas fa-exclamation-circle'></i> Erro de processamento ao carregar listagem.", {
            type: 'error',
            align: 'center',
            width: 'auto',
            delay: 8000
        });
    }
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
    },

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


var Url = {
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


var Growl = {

    success: function(message) {
        $.bootstrapGrowl(message, {
            type: 'success',
            align: 'center',
            width: 'auto',
            delay: 8000
        });
    },

    error: function(message) {
        $.bootstrapGrowl(message, {
            type: 'error',
            align: 'center',
            width: 'auto',
            delay: 8000
        });
    },

    warning: function(message) {
        $.bootstrapGrowl(message, {
            type: 'warning',
            align: 'center',
            width: 'auto',
            delay: 8000
        });
    },

    info: function(message) {
        $.bootstrapGrowl(message, {
            type: 'info',
            align: 'center',
            width: 'auto',
            delay: 8000
        });
    },

    error500: function(message) {
        message = typeof message !== 'undefined' ? message : 'Erro interno. Não foi possível executar o pedido.';
        $.bootstrapGrowl(message, {
            type: 'error',
            align: 'center',
            width: 'auto',
            delay: 8000
        });
    }
}


var Str = {

    /**
     * Returns a random string
     *
     * @param {type} length
     * @returns {String}
     */
    random: function (length) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }
        return text;
    },

    isEmail: function(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    },

    chunk: function (str, n) {
        var ret = [];
        var i;
        var len;

        for(i = 0, len = str.length; i < len; i += n) {
            ret.push(str.substr(i, n))
        }

        return ret
    },

    nospace: function(str) {
        str = str.replace(/\s/g, '');
        return str;
    },

    slug: function(str) {
        str = str.toLowerCase();

        // remove accents, swap ñ for n, etc
        var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
        var to   = "aaaaaeeeeeiiiiooooouuuunc------";
        for (var i=0, l=from.length ; i<l ; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-'); // collapse dashes

        str = str.replace(/^\s+|\s+$/g, ''); // trim

        return str;
    }
}

var Notifier = {

    /**
     * Get total of notiications
     */
    getTotal: function() {
        return $('[data-toggle="notifications-counter"]').html();
    },

    /**
     * Set notifications
     *
     * @param {type} length
     * @returns {String}
     */
    set: function(total, sendAlert) {
        var sendAlert = typeof sendAlert !== 'undefined' ? sendAlert : false;
        var $target = $('[data-toggle="notifications-counter"]');
        $target.html(total);

        if(total == '0' || total == 0) {
            Notifier.clean();
        } else {
            $target.show();
            if(sendAlert) {
                Notifier.soundAlert();
            }
        }
    },

    /**
     * Clean notifications
     */
    clean: function() {
        $('[data-toggle="notifications-counter"]').hide();
    },

    /**
     * Increment total notifications
     *
     * @param total
     */
    increment: function(sendAlert) {
        var sendAlert = typeof sendAlert !== 'undefined' ? sendAlert : false;
        var $target = $('[data-toggle="notifications-counter"]');
        var total = parseInt($target.html());
        total = total + 1;
        Notifier.set(total, sendAlert);
    },

    /**
     * Decrement total notifications
     *
     * @param total
     */
    decrement: function(sendAlert) {
        var sendAlert = typeof sendAlert !== 'undefined' ? sendAlert : false;
        var $target = $('[data-toggle="notifications-counter"]');
        var total = parseInt($target.html());
        total = total - 1;
        Notifier.set(total, sendAlert);
    },

    /**
     * Send push alert
     */
    pushAlert: function(title, message) {
        var title   = typeof title !== 'undefined' ? title : 'Notificação';
        var message = typeof message !== 'undefined' ? message : '';

        Push.create(title, {
            body: message,
            timeout: 4000,
            vibrate: [200, 100, 200, 100, 200, 100, 200]
        });
    },

    /**
     * Send sound alert
     */
    soundAlert: function() {

        if(NOTIFICATION_SOUND) {
            var audio = new Audio('/assets/sounds/' + NOTIFICATION_SOUND + '.mp3');
        } else {
            var audio = new Audio('/assets/sounds/notification09.mp3');
        }

        audio.play();
    },

    /**
     * Send sound alert
     */
    soundWarning: function() {
        var audio = new Audio('/assets/sounds/error01.mp3');
        audio.play();
    },

    /**
     * Send sound alert
     */
    soundError: function() {
        var audio = new Audio('/assets/sounds/error03.mp3');
        audio.play();
    },

    /**
     * Send sound alert
     */
    soundOk: function() {
        var audio = new Audio('/assets/sounds/ok02.mp3');
        audio.play();
    },
}

function removeAccents(strAccents) {
    var strAccents = strAccents.split('');
    var strAccentsOut = new Array();
    var strAccentsLen = strAccents.length;
    var accents = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÕÖØòóôõöøÈÉÊËèéêëðÇçÐÌÍÎÏìíîïÙÚÛÜùúûüÑñŠšŸÿýŽž';
    var accentsOut = "AAAAAAaaaaaaOOOOOOOooooooEEEEeeeeeCcDIIIIiiiiUUUUuuuuNnSsYyyZz";
    for (var y = 0; y < strAccentsLen; y++) {
        if (accents.indexOf(strAccents[y]) != -1) {
            strAccentsOut[y] = accentsOut.substr(accents.indexOf(strAccents[y]), 1);
        } else
            strAccentsOut[y] = strAccents[y];
    }
    strAccentsOut = strAccentsOut.join('');
    return strAccentsOut;
}

(function ($) {
    $.fn.extend({

        // With every keystroke capitalize first letter of ALL words in the text
        upperFirstAll: function() {
            $(this).keyup(function(event) {
                var box = event.target;
                var txt = $(this).val();
                var start = box.selectionStart;
                var end = box.selectionEnd;

                $(this).val(txt.toLowerCase().replace(/^(.)|(\s|\-)(.)/g, function(c) {
                    return c.toUpperCase();
                }));
            });
            return this;
        },

        // With every keystroke capitalize first letter of the FIRST word in the text
        upperFirst: function() {
            $(this).keyup(function(event) {
                var box = event.target;
                var txt = $(this).val();
                var start = box.selectionStart;
                var end = box.selectionEnd;

                $(this).val(txt.toLowerCase().replace(/^(.)/g, function(c) {
                    return c.toUpperCase();
                }));
            });
            return this;
        },

        // Converts with every keystroke the hole text to lowercase
        lowerCase: function() {
            $(this).keyup(function(event) {
                var box = event.target;
                var txt = $(this).val();
                var start = box.selectionStart;
                var end = box.selectionEnd;

                $(this).val(txt.toLowerCase());
            });
            return this;
        },

        // Converts with every keystroke the hole text to uppercase
        upperCase: function() {
            $(this).keyup(function(event) {
                var box = event.target;
                var txt = $(this).val();
                var start = box.selectionStart;
                var end = box.selectionEnd;

                $(this).val(txt.toUpperCase());
            });
            return this;
        }
    });
}(jQuery));

/**
 * Validate
 * @type {{getForCountry: ZipCode.getForCountry, validate: ZipCode.validate}}
 */
var ZipCode = {

    validate: function(country, zipCode) {

        if(country == 'gw' && zipCode.inArray(['BISSAU','CANCHUNGO'])) {
            return true
        }

        var regex = zipcodes_regex[country.toUpperCase()];

        if(typeof regex !== 'undefined') {
            regex = new RegExp(regex);
            return regex.test(zipCode.trim());
        }

        return false
    },

    validateInput: function(country, zipCode, $form) {

        var valid   = ZipCode.validate(country, zipCode);

        if(typeof $form !== 'undefined') {
            $form.find('[name="zip_code"]').closest('.form-group').removeClass('has-error');

            if (!valid) {
                $form.find('[name="zip_code"]').attr('title', 'Código Inválido');
                $form.find('[name="zip_code"]').closest('.form-group').addClass('has-error');
            }
        } else {
            $('[name="zip_code"]').closest('.form-group').removeClass('has-error');

            if (!valid) {
                $('[name="zip_code"]').attr('title', 'Código Inválido');
                $('[name="zip_code"]').closest('.form-group').addClass('has-error');
            }
        }
    },

    searchInputAutocomplete: function(input, cityInput = null, country = null, callback = null) {
        var $input = $(input);

        $input.autocomplete('destroy');
        $input.autocomplete({
            serviceUrl: ROUTE_SEARCH_ZIP_CODES,
            minChars: 2,
            params: {
                'index': $(this).attr('name'),
                'country': country
            },
            onSelect: function (suggestion) {
                $input.val(suggestion.data);
                if (cityInput) { $(cityInput).val(suggestion.city); }
                $input.autocomplete('hide');
                
                if (callback) {
                    callback();
                }
            },
            width: 250,
        });
    }
}

/**
 * Datetime
 */
var Datetime = {

    addHoursToDate: function (date, hours) {
        date = new Date(date);
        return new Date(new Date(date).setHours(date.getHours() + hours));
    },

    toDateString: function (date, dateFormat) {

        hours   = "";
        year    = date.getFullYear();
        month   = date.getMonth() + 1;
        day     = date.getDate();
        hour    = date.getHours();
        minutes = date.getMinutes();
        seconds = date.getSeconds();

        month   = month < 10 ? "0"+month : month;
        day     = day < 10 ? "0"+day : day;
        hour    = hour < 10 ? "0"+hour : hour;
        minutes = minutes < 10 ? "0"+minutes : minutes;
        seconds = seconds < 10 ? "0"+seconds : seconds;

        dateFormat = typeof dateFormat == 'undefined' ? 'Y-m-d H:i:s' : dateFormat;

        var strDate = '';
        for (var i = 0; i < dateFormat.length; i++) {
            char = dateFormat.charAt(i);

            if (char == 'Y') {
                strDate+= year;
            } else if (char == 'm') {
                strDate+= month;
            } else if (char == 'd') {
                strDate+= day;
            } else if (char == 'H') {
                strDate+= hour;
            } else if (char == 'i') {
                strDate+= minutes;
            } else if (char == 's') {
                strDate+= seconds;
            } else {
                strDate+= char;
            }
        }
        return strDate;
    },

}
/**
 * Copy to clipboard
 */
$.fn.CopyToClipboard = function() {
    var textToCopy = false;
    if(this.is('select') || this.is('textarea') || this.is('input')){
        textToCopy = this.val();
    }else {
        textToCopy = this.html();
    }
    CopyToClipboard(textToCopy);
};

function CopyToClipboard( val ){
    var hiddenClipboard = $('#_hiddenClipboard_');
    if(!hiddenClipboard.length){
        $('body').append('<textarea style="position:absolute;top: -9999px;" id="_hiddenClipboard_"></textarea>');
        hiddenClipboard = $('#_hiddenClipboard_');
    }
    hiddenClipboard.html(val);
    hiddenClipboard.select();
    document.execCommand('copy');
    document.getSelection().removeAllRanges();
    hiddenClipboard.remove();
}

$(function(){
    $('[data-clipboard-target]').each(function(){
        $(this).click(function() {
            $($(this).data('clipboard-target')).CopyToClipboard();
        });
    });
    $('[data-clipboard-text]').each(function(){
        $(this).click(function(){
            CopyToClipboard($(this).data('clipboard-text'));
        });
    });
});

/**
 * Round number up
 * @param value
 * @param decimals
 * @returns {number}
 */
function round(value, decimals) {

    if(typeof decimals == 'undefined' || decimals == 2) {
        decimals = 100; //2 casas
    } else if(decimals == 3) {
        decimals = 1000; //3 casas
    } else if(decimals == 0) {
        decimals = 0; //3 casas
    }

    return Math.round((value + Number.EPSILON) * decimals) / decimals;
}

var Select2 = {
    renderDefaultHtml: {
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            return data.html;
        },
        templateSelection: function(data) {
            return data.text;
        }
    }
}