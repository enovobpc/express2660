$(document).ready(function(){
    $('.select2').select2(Init.select2());
    $('input').iCheck(Init.iCheck());
    $('.datepicker').datepicker(Init.datepicker());
    $(document).tooltip(Init.tooltip());
    
    Init.tabsHash();

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
    $(document).on('keypress', '.lowercase',function(e) {
        $(this).val($(this).val().toLowerCase());
    })

    $(document).on('keyup', '.lowercase',function(e) {
        $(this).val($(this).val().toLowerCase());
    });

    /**
     * Force input to have first char in uppercase
     */
    $(document).on('keypress', '.ucwords',function(e) {
        $(this).val($(this).val().toLowerCase().replace(/^(.)|(\s|\-)(.)/g, function(c) {
            return c.toUpperCase();
        }));
    })

    $(document).on('keyup', '.ucwords',function(e) {
        $(this).val($(this).val().toLowerCase().replace(/^(.)|(\s|\-)(.)/g, function(c) {
            return c.toUpperCase();
        }));
    });
    
    $('select[data-toggle="search-location"]').select2({        
        ajax: {
            url: "/pt/pedido-de-cotacao/search",
            dataType: 'json',
            method: 'POST',
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
        },
        minimumInputLength: 2
    });
    
    /*
     * Datatables defaults
     */
    /*$.extend(true, $.fn.dataTable.defaults, {
        dom: "<'row'<'col-md-12'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
        pageLength: 10,
        responsive: true,
        processing: true,
        serverSide: true,
        stateSave: true,
        order: [[0, "desc"]],
        fnInitComplete: function () {
            $('.datatable-filters-extended').appendTo($('.datatable-filters-area-extended'))
            $('.datatable-filters-extended.active').removeClass('hide');
            $('.datatable-filters').appendTo($('.datatable-filters-area')).removeClass('hide');

        },
        oLanguage: {
            "sProcessing": "A processar...",
            "sLoadingRecords": "A carregar...",
            "sLengthMenu": "Ver _MENU_",
            "sZeroRecords": "Não foram encontrados resultados",
            "sEmptyTable": "Não existem registos para apresentar.",
            "sInfo": "A mostrar de _START_ até _END_ de _TOTAL_ registos",
            "sInfoEmpty": "",
            "sInfoFiltered": "(filtrado de _MAX_ registos no total)",
            "sInfoPostFix": "",
            "sSearch": "<i class='fas fa-search text-muted'></i>",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "<i class='fas fa-angle-double-left'></i>",
                "sPrevious": "<i class='fas fa-angle-left'></i>",
                "sNext": "<i class='fas fa-angle-right'></i>",
                "sLast": "<i class='fas fa-angle-double-right'></i>",
            }
        }
    });*/

    /*
     * DATATABLE DEFAULT
     */
    $('.dataTables_filter input')
        .unbind('keypress keyup')
        .bind('keypress keyup', function(e){
            if ($(this).val().length < 3 && e.keyCode != 13) return;
            $(this).fnFilter($(this).val());
        });

    $.extend(true, $.fn.dataTable.defaults, {
        dom: "<'row row-0'<'col-md-9 col-sm-8 datatable-filters-area'><'col-sm-4 col-md-3'f><'col-sm-12 datatable-filters-area-extended'>>" +
        "<'row row-0'<'col-sm-12'tr>>" +
        "<'row row-0'<'col-sm-5'li><'col-sm-7'p>>",
        pageLength: 25,
        responsive: true,
        processing: true,
        serverSide: true,
        stateSave: true,
        deferRender: true,
        "search": {
            "caseInsensitive": false
        },
        lengthMenu: [[10, 25, 50, 100, 250, 500, 750, 1000], [10, 25, 50, 100, 250, 500, 750, 1000]],
        order: [[1, "desc"]],
        drawCallback: function() {
            $('.dataTables_filter i, .datatable-search-loading i')
                .removeClass('fa-spin')
                .removeClass('fa-circle-notch')
                .removeClass('fa-search')
                .addClass('fa-search')

            $(document).find('.fa-exclamation-triangle').closest('tr').css('background', '#ff05051a')
        },
        fnInitComplete: function () {
            var id = $(this).attr('id')
            $('[data-target="#' + id + '"].datatable-filters-extended').appendTo('#' + id + '_wrapper .datatable-filters-area-extended');
            $('#' + id + '_wrapper .datatable-filters-extended.active').removeClass('hide');
            $('[data-target="#' + id + '"].datatable-filters').appendTo('#' + id + '_wrapper .datatable-filters-area').removeClass('hide');
        },
        oLanguage: {
            "sProcessing": "A processar...",
            "sLoadingRecords": "A carregar...",
            "sLengthMenu": "Ver _MENU_",
            "sZeroRecords": "Não foram encontrados resultados",
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
        }
    });
    
});

/**
 * Idle timer
 * @type {number}
 */
var idleTime = 0;
$(document).ready(function () {
    //Increment the idle time counter every minute.
    var idleInterval = setInterval(timerIncrement, 60000); // 1 minute

    //Zero the idle timer on mouse movement.
    $(this).mousemove(function (e) {idleTime = 0; });
    $(this).keypress(function (e) {idleTime = 0; });
});

function timerIncrement() {
    idleTime = idleTime + 1;
    if (idleTime > 25) { // 25 minutes
        window.location.reload();
    }
}

/**
 * Action after toogle field ajax
 */
$(document).on('ajax:success','.toggle-field a', function(event, data){
    $(event.target).parent().replaceWith(data.html);
});

/**
 * Generic event when modal fails
 */
$(document).ajaxError(function( event, jqxhr, settings, exception ) {

    if($('.modal.in .modal-content form').length == 0 && ($('#modal-remote').hasClass('in') || $('#modal-remote-lg').hasClass('in') || $('#modal-remote-xl').hasClass('in'))) {
        html = '<h4 class="modal-title text-center m-t-40 m-b-40 text-red">' +
            '<i class="fas fa-exclamation-circle bigger-250"></i><br> Não foi possível abrir a janela porque ocorreu um erro de processamento interno.' +
            '<br>Se o problema persistir, contacte o suporte informático.' +
            '</h4>';
        $('.modal.in .modal-body').html(html);
    }
});

/**
 * Datatable filters
 */
$(document).on('click', '.btn-filter-datatable', function(){

    var url = Url.current();

    if($('.datatable-filters-extended').is(':visible')) {
        url = Url.updateParameter(url, 'filter', 1);
    } else {
        url = Url.removeParameter(url, 'filter');
    }

    Url.change(url);
})

$('.datatable-filters select, .datatable-filters-extended select, .datatable-filters-extended input').on('change', function(){
    var name  = $(this).attr('name');
    var value = $(this).val();
    var url = Url.current();
    var newUrl = '';

    if(value == '') {
        newUrl = Url.removeParameter(url, name);
    } else {
        newUrl = Url.updateParameter(url, name, value);
    }

    Url.change(newUrl);
})


$('.btn-filter-datatable').on('click', function(){
    $('.datatable-filters-extended').toggleClass('hide');
})

/**
 * Datatable select multiple rows
 */
$(document).on('ifChanged', '[name=select-all]', function(){

    if($(this).is(':checked')) {
        $('.row-select').iCheck('check')
    } else {
        $('.row-select').iCheck('uncheck')
    }
})

$(document).on('ifChanged', '.row-select',function(){
    if($('.row-select:checked').length) {
        $('.selected-rows-action').removeClass('hide');
        $('.selected-rows-action button').prop('disabled', false);
    } else {
        $('.selected-rows-action').addClass('hide');
        $('.selected-rows-action button').prop('disabled', true);
    }

    var ids = [];
    var queryString = '';

    $('input[name=row-select]:checked').each(function(i, selected){
        ids[i] = $(selected).val();
        queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()
    });

    $('[name=ids]').remove();
    $('.selected-rows-action form').each(function(){
        $(this).append('<input type="hidden" name="ids" value="'+ ids +'">');
    })

    var url;
    $('a[data-toggle="datatable-action-url"]').each(function(){
        url = Url.removeQueryString($(this).attr('href'));
        $(this).attr('href', url + '?' + queryString);
    });
});

/**
 * Multiple rows selection actions
 */
$(document).on('click', '.selected-rows-action button[data-action=confirm]', function(e){
    e.preventDefault();
    var $form = $(this).closest('form');
    var title = $(this).data('title') === undefined ? "" :  $(this).data('title');
    var message = $(this).data('message') === undefined ? '<h4>Confirma a remoção dos items selecionados?</h4>' : '<h4>' + $(this).data('message') + '</h4>';
    var confirmBtn =  $(this).data('confirm-text') === undefined ? 'Eliminar' : $(this).data('confirm-text');
    var confirmBtnClass = $(this).data('confirm-class') === undefined ? 'btn-danger' : $(this).data('confirm-class');
    var cancelBtn = $(this).data('cancel-text') === undefined ? 'Cancelar' : $(this).data('cancel-text');

    bootbox.dialog({
        title: title,
        message: message,
        buttons: {
            cancel: {
                label: cancelBtn
            },
            main: {
                label: confirmBtn,
                className: confirmBtnClass,
                callback: function(result) { $form.submit(); }
            }
        }
    });
})

/**
 * Bootbox confirmation
 * 
 * @param {type} link
 * @returns {Boolean}
 */
$.rails.allowAction = function (link) {
    if (link.data("confirm") == undefined) {
        return true;
    }

    $.rails.showConfirmationDialog(link);
    return false;
}

//User click confirm button
$.rails.confirmed = function (link) {
    link.data("confirm", null);
    link.trigger("click.rails");
}

//Display the confirmation dialog
$.rails.showConfirmationDialog = function (link) {
    var message = link.data("confirm");
    var confirmClass = link.data("confirm-class");
    var confirmLabel = link.data("confirm-label");

    if (typeof confirmClass === 'undefined') {
        confirmClass = "btn-danger";
    }

    if (typeof confirmLabel === 'undefined') {
        confirmLabel = "Remover";
    }

    bootbox.confirm({
        title: "Confirmar remoção",
        message: "<h4 style='font-weight: normal'>" + message + "</h4>",
        buttons: {
            confirm: {
                label: confirmLabel,
                className: confirmClass
            },
            cancel: {
                label: "Cancelar",
                className: "btn-default"
            }
        },
        callback: function (result) {
            if (result) {
                $.rails.confirmed(link);
            }
        }
    });
}
    
/**
 * Toggle view more panel
 * @param {type} param1
 * @param {type} param2
 */
$('[data-toggle="view-more"]').on('click', function(e){
        e.preventDefault();
        var target = $(this).data('target');
        var newLabel = $(this).data('toggle-text');
        $(this).data('toggle-text', $(this).html());
        $(this).html(newLabel);
        
        $(target).toggleClass('active');
    })
    
/**
 * Validate file size in fileinputs
 * 
 * @param {type} param1
 * @param {type} param2
 */
$('input[data-max-size]').bind('change', function(){
    
    if($(this).val() != '') {
        var max   = $(this).data('max-size');
        var unity = (unity === undefined || unity == null || unity.length <= 0) ? 'mb' : $(this).data('unity');
        var fileSize = $(this)[0].files[0].size;

        if(unity == 'gb') {
            var maxBytes = max * 1000000000;
        } else if(unity == 'mb') {
            var maxBytes = max * 1000000;
        } else if(unity == 'kb') {
            var maxBytes = max * 1000;
        } else {
            var maxBytes = max;
        }
        
        if(fileSize > maxBytes) {
  
            $(this).closest('.fileinput').find('[data-dismiss=fileinput]').trigger('click');
            
            swal({
                title: "",
                text: 'O tamanho do ficheiro excede o máximo permitido de '+ max + unity,
                type: 'warning',
                showCancelButton: false,
                confirmButtonClass: "btn-default",
                closeOnConfirm: false,
                closeOnCancel: false
            });
        }
    }
})

/**
 * Validate file extension in fileinputs
 * 
 * @param {type} param1
 * @param {type} param2
 */
$('input[data-file-format]').bind('change', function(){
    
    if($(this).val() != '') {
        var acceptedExtensions = $(this).data('file-format');
        acceptedExtensions = acceptedExtensions.split(",");

        var filename = $(this).val();
        var extension = filename.replace(/^.*\./, '');
        
        if (extension == filename) {
            extension = '';
        } else {
            extension = extension.toLowerCase();
        }
        
        if($.inArray(extension,acceptedExtensions) < 0) {
  
            $(this).closest('.fileinput').find('[data-dismiss=fileinput]').trigger('click');
            
            swal({
                title: "",
                text: 'A extenção do ficheiro selecionado não é permitida.',
                type: 'warning',
                showCancelButton: false,
                confirmButtonClass: "btn-default",
                closeOnConfirm: false,
                closeOnCancel: false
            });
        }
    }
})

/**
 * reset modal remote
 */
$('body').on('hidden.bs.modal', '#modal-remote, #modal-remote-lg, #modal-remote-xl', function () {
    
    if($(this).attr('id') == 'modal-remote-xl') {
        $(this).find('.modal-dialog').removeClass('modal-sm')
                                    .removeClass('modal-lg')
                                    .addClass('modal-xl');
    }else if($(this).attr('id') == 'modal-remote-lg') {
        $(this).find('.modal-dialog').removeClass('modal-sm')
                                    .removeClass('modal-xl')
                                    .addClass('modal-lg');
    }else if($(this).attr('id') == 'modal-remote') {
        $(this).find('.modal-dialog').removeClass('modal-sm')
                                    .removeClass('modal-xl');
    }
    
    
    $(this).removeData('bs.modal');
    var html = '<div class="modal-body">'
                +'<h4 class="modal-title text-center m-t-40 m-b-40 text-muted">'
                +'<i class="fas fa-circle-notch fa-spin"></i> A carregar...'
                +'</h4>'
                +'</div>';
    $(this).find('.modal-content').html(html);
});

/**
 * Submit form by ajax
 * 
 * @param {type} param1
 * @param {type} param2
 */
$('form.ajax-form').on('submit', function (e) {
    e.preventDefault();

    var $form = $(this);
    var $submitBtn = $form.find('button[type=submit]');
    $submitBtn.button('loading');

    var form = $(this)[0];
    var formData = new FormData(form);
    
    $.ajax({
        url: $form.attr('action'),
        data: formData,
        type: 'POST',
        contentType: false,
        processData: false,
    }).success(function(data){
        var feedbackText;
        var alertType;


        if (data.result) {
            $form.find('input[type=text], input[type=email], textarea, select').val('');
            $form.find('input[type=checkbox], input[type=radio]').prop('');
            $('select.select2').trigger("change");
            $('input').iCheck('uncheck');
            feedbackText = data.feedback;
            alertType = "success";
        } else {
            feedbackText = data.feedback;
            alertType = "error";
        }


        $submitBtn.button('reset');
        $form.find('[data-dismiss=fileinput]').trigger('click');
        $form.find('[data-dismiss=modal]').trigger('click');

        if(data.feedback) {
            swal({
                title: "",
                text: feedbackText,
                type: alertType,
                showCancelButton: false,
                confirmButtonClass: "btn-default",
                closeOnConfirm: false,
                closeOnCancel: false
            });
        }
        
    }).fail(function () {

        swal({
            title: "",
            text: 'Não foi possível processar o seu pedido porque ocorreu um erro interno de servidor.',
            type: "error",
            showCancelButton: false,
            confirmButtonClass: "btn-default",
            closeOnConfirm: false,
            closeOnCancel: false
        });

    }).always(function () {
        $submitBtn.button('reset');
    });
});