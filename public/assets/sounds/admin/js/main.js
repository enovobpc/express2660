var modalInstance;


$(document).ready(function(){

    //$("body").niceScroll(Init.niceScroll());
    $(".nicescroll").niceScroll(Init.niceScroll());

    /**
     * DATEPICKER
     */
    $('.datepicker').prop('autocomplete', 'off');
    $('.datepicker').datepicker(Init.datepicker());

    /**
     * SELECT2
     */
    $('.fast-search.select2').select2(Init.select2());

    /**
     * ICHECK
     */
    $('input').iCheck(Init.iCheck());


    /**
     * INIT POPOVER
     */
    $(document).popover({
        container: 'body',
        trigger: 'hover',
        selector: '[data-toggle="popover"]',
        html: true,
        delay: 100,
        content: function () {
            return $(this).data('content');
        },
    });

    /**
     * HIDE POPOVER WHEN CLICK
     */
    $('body').on('click', function (e) {
        $('[data-toggle=popover]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });

    /*
     * DATATABLE DEFAULT
     */
    $('.dataTables_filter input')
        .unbind('keypress keyup')
        .bind('keypress keyup', function(e){
            if ($(this).val().length < 3 && e.keyCode != 13) return;
            $(this).fnFilter($(this).val());
    });

  /*  if(DATATABLE_SEARCH_ON_ENTER == 1) {
        $('.dataTables_filter input').unbind();
        $('.dataTables_filter input').bind('keyup', function(e) {
            if(e.keyCode == 13 && typeof oTable !== 'undefined') {
                oTable.search($(this).val()).draw();
            }
        });
    }*/

    $.extend(true, $.fn.dataTable.defaults, {
        dom: "<'row row-0'<'col-md-9 col-sm-8 datatable-filters-area'><'col-sm-4 col-md-3' '<'dt-srch' f'<'sbtn'>>><'col-sm-12 datatable-filters-area-extended'>>" +
                    "<'row row-0'<'col-sm-12'tr>>" +
                    "<'row row-0'<'col-xs-12 col-sm-5'li><'col-xs-12 col-sm-7'p>>",
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
                .removeClass('fa-spinner')
                .removeClass('text-black')
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

    //Datatable filter spin
    $(document).on('keydown', '.dataTables_filter', function () {
        if(DATATABLE_SEARCH_ON_ENTER == 0) {
            $('.dataTables_filter').find('i').removeClass('fa-search').addClass('fa-spinner fa-spin text-black');
        }
    });

    //trigger when click on datatable search button
    $(document).on('click', '.sbtn', function(){
        var e = $.Event("keyup");
        e.keyCode = 13; // # Some key code value
        $('.dataTables_filter input').trigger(e);
    })

    //highlight active filters
    $('.filter-datatable').each(function(){
        if($(this).val() != '') {
            $(this).closest('li').addClass('fltr-enabled')
        }
    })

    //change filter
    $('.datatable-filters select, .datatable-filters input, .datatable-filters-extended select, .datatable-filters-extended input').on('change', function(){
        var name  = $(this).attr('name');
        var text  = $(this).data('query-text');
        var value = $(this).val();
        var url = Url.current();
        var newUrl  = '';
        var textVal = '';

        if(text == true) {
            textVal = $(this).find('option:selected').text();
        }

        if(value == '') {
            $(this).closest('li').removeClass('fltr-enabled')
            newUrl = Url.removeParameter(url, name);

            if(text == true) {
                newUrl = Url.removeParameter(newUrl, name + '-text');
            }
        } else {
            $(this).closest('li').addClass('fltr-enabled')
            newUrl = Url.updateParameter(url, name, value);

            if(text == true) {
                newUrl = Url.updateParameter(newUrl, name + '-text', textVal);
            }
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
        var $targetTable = $(this).closest('table');
        if($(this).is(':checked')) {
            $($targetTable).find('.row-select').iCheck('check')
        } else {
            $($targetTable).find('.row-select').iCheck('uncheck')
        }
     })
     
    $(document).on('ifChanged', '.row-select',function(){

        var $targetTable = $(this).closest('table');
        $targetTable.find('.row-select').closest('tr').removeClass('row-selected');
        if($targetTable.find('.row-select:checked').length) {
            $('.selected-rows-action').removeClass('hide');
            $('.selected-rows-action button').prop('disabled', false);
        } else {
            $('.selected-rows-action').addClass('hide');
            $('.selected-rows-action button').prop('disabled', true);
        }
        
        var ids = []; 
        var queryString = '';
        var totalSelected = 0;
        var tableTotal = 0;
        var rowTotal = 0;

        $targetTable.find('input[name=row-select]:checked').each(function(i, selected){
            var $tr = $(this).closest('tr');

            $tr.addClass('row-selected');
            totalSelected++;
            ids[i] = $(selected).val(); 
            queryString+=  (i == 0) ? 'id[]=' + $(selected).val() : '&id[]=' + $(selected).val()

            //sum datatable totals
            rowTotal = parseFloat($tr.find('[data-total]').data('total'));
            tableTotal+= rowTotal;
            $('.dt-sum-total').html(tableTotal.toFixed(2));
        });

        $('[name=ids]').remove();
        $('.selected-rows-action form').each(function(){
            $(this).append('<input type="hidden" name="ids" value="'+ ids +'">');
        })

        var url;
        $('a[data-toggle="datatable-action-url"], a[data-action-url="datatable-action-url"]').each(function(){
            url = Url.removeQueryString($(this).attr('href'));
            $(this).attr('href', url + '?' + queryString)
        });

        if(totalSelected) {
            word = totalSelected == 1 ? 'selecionado' : 'selecionados';
            $('.dataTables_wrapper').find('.dataTables_info span b').html(totalSelected + ' ' + word + '&nbsp;&bull;&nbsp;')
        } else {
            $('.dataTables_wrapper').find('.dataTables_info span b').html('');
        }

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
            animate: false,
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
    });

    /**
     * Edit datatable field inline
     */
    $(document).on('click','.edit-datatable-field',function(e){
        e.preventDefault();
        $(this).addClass('hide');
        $(this).closest('td').find('form').removeClass('hide');
    });

    $(document).on('click','.edit-datatable-field-cancel',function(e){
        e.preventDefault();
        $form = $(this).closest('form');
        $form.addClass('hide');
        $form.closest('td').find('.edit-datatable-field').removeClass('hide');
    });

    $(document).on('ajax:success','form.form-edit-datatable-field', function(event, data){
        if(data.type == 'success' || data.result == true) {
            $form = $(event.target);
            if($form.find('.target-datatable-field').val() == '') {
                $form.closest('td').find('.edit-datatable-field').html('<i>Editar...</i>');
            } else {
                if(typeof data.html !== 'undefined') {
                    $form.closest('td').html(data.html);
                } else {
                    $form.closest('td').find('.edit-datatable-field').html($form.find('.target-datatable-field').val());
                }
            }
            $form.find('.edit-datatable-field-cancel').trigger('click');
        }
    });

    /**
     * Pace
     */
    var paceOptions = {
      ajax: {
        trackMethods: ['GET', 'POST', 'PUT'],
        trackWebSockets: true,
        ignoreURLs: []
      }
    };

    /**
     * Alterar janela de confirmação
     */
    //Override the default confirm dialog by rails
    $.rails.allowAction = function(link){
      if (link.data("confirm") == undefined){
        return true;
      }

      $.rails.showConfirmationDialog(link);
      return false;
    }

    //User click confirm button
    $.rails.confirmed = function(link){
      link.data("confirm", null);
      link.trigger("click.rails");
    }

    //Display the confirmation dialog
    $.rails.showConfirmationDialog = function(link) {
      var message = link.data("confirm");
      var confirmClass = link.data("confirm-class");
      var confirmLabel = link.data("confirm-label");
      var confirmTitle = link.data("confirm-title");

      if(typeof confirmTitle === 'undefined'){
        confirmTitle = "Confirmar remoção";
      } 
      
      if(typeof confirmClass === 'undefined'){
        confirmClass = "btn-danger";
      } 

      if(typeof confirmLabel === 'undefined'){
        confirmLabel = "Remover";
      }

      bootbox.confirm({
          animate: false,
          title: confirmTitle,
          message: "<h4>" + message +  "</h4>",
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
          callback: function(result) {

              if (result) {
                  $.rails.confirmed(link);
              }
          }
      });

    }

    Init.imagePreview();
    Init.tabsHash();
    Init.bootstrapGrowl();
});

/**
 * Copy to clipboard
 */
$(document).on('click', '[data-toggle="copy-clipboard"]', function(){
    var $target = $($(this).data('target'))
    var feedback = $(this).data('feedback')

    msg = 'Copiado para a área de transferência.';
    if(typeof feedback !== 'undefined'){
        msg = feedback
    }

    $target.CopyToClipboard();
    Growl.success(msg)
})

/**
 * Select all checkboxes
 */
$(document).on('click', '.icheck-select-all', function(e){
    e.preventDefault();
    var $target = $(this).data('target')
    var $checkboxes = $($target).find('[type="checkbox"]');
    if($($target).find('[type="checkbox"]:checked').length > 0) {
        $checkboxes.iCheck('uncheck').trigger('change')
    } else {
        $checkboxes.iCheck('check').trigger('change')
    }
})


$('.select2').on('select2:open', function (event) {
    $('.select2-dropdown').css('min-width', $(this).next().css('width'))
})

function formatState (state) {
    if (!state.id) {
        return state.text;
    }
    var $state = $(
        '<span><div class="iti-flag '+state.element.value.toLowerCase()+'"></div> ' + state.text + '</span>'
    );
    return $state;
};

$('.select2-country').select2({
    /*templateResult: formatState,*/
    templateSelection: formatState
});

/**
 * Select multiple 2 with checkboxes
 */
jQuery(function($) {
    $('.select2-multiple').select2MultiCheckboxes(Init.select2Multiple())
});

/**
 * RANDOM PASSWORD
 */
$(document).on('click', '#random-password', function () {
    $('input[name=password]').val(Str.random(8));
});

/**
 * TOGGLE SHOW HORE
 */
$(document).on('click', '[data-toggle="show-more"]',function(){
    $('.more-about').slideToggle();
})

/**
 * BUTTON TO CLEAN SELECTBOX
 */
$(document).on('click', '.clean-select', function () {
    $(this).closest('.input-group').find('select').val('').trigger('change');
});

/**
 * BUTTON/LINK TO FILL VAT FIELD WITH FINAL CONSUMER VAT
 */
$(document).on('click', '.set-cfinal-vat', function(e){
    e.preventDefault();
    $(this).closest('.form-group').find('input').val('999999990')
})

/**
 * SHOW REMAINING CHARACTERS
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

/*======================================================================================================================
 *   POPOVER
 ======================================================================================================================*/
//Show/hidden popover graph
$(document).on('mouseover', '[data-popover-graph]', function(){
    $('.popover-graph').css('display', 'none').hide();
    var $target = $(this).data('popover-graph');
    $($target).css('display', 'block').show();
})

//hide class popover-graph when user moves out
$(document).mouseup(function(e) {
    var container = $('.popover-graph');
    if (!container.is(e.target) && container.has(e.target).length === 0) {
        container.hide();
    }
});

$(document).on('click', '.popover-graph .close', function(){
    $(this).closest('.popover-graph').hide();
})

/*======================================================================================================================
 *   BOOTBOX CONFIRM
 ======================================================================================================================*/
$(document).on('click', '[data-ajax-confirm], [data-toggle="ajax-confirm"]', function(e){
    e.preventDefault();

    var $this = $(this);
    var message      = $this.data("ajax-confirm");
    var method       = $this.data("ajax-method");
    var confirmClass = $this.data("confirm-class");
    var confirmLabel = $this.data("confirm-label");
    var confirmTitle = $this.data("confirm-title");

    if(typeof method === 'undefined'){
        method = "delete";
    }

    if(typeof confirmTitle === 'undefined'){
        confirmTitle = "Confirmar remoção";
    }

    if(typeof confirmClass === 'undefined'){
        confirmClass = "btn-danger";
    }

    if(typeof confirmLabel === 'undefined'){
        confirmLabel = "Remover";
    }

    bootbox.confirm({
        title: confirmTitle,
        message: "<h4>" + message +  "</h4>",
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
        callback: function(result) {

            if (result) {
                $.ajax({
                    url: $this.attr('href'),
                    type: method,
                    success: function(data) {

                        if (data.html) {
                            $(data.target).html(data.html);
                        }

                        if (data.result) {
                            var info = oTableModal.page.info();
                            var page = info.page;
                            oTableModal.draw(false);
                            $.bootstrapGrowl(data.feedback, {
                                type: 'success',
                                align: 'center',
                                width: 'auto',
                                delay: 8000
                            });
                        } else {
                            $.bootstrapGrowl('Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.', {
                                type: 'error',
                                align: 'center',
                                width: 'auto',
                                delay: 8000
                            });
                        }
                    }
                }).fail(function () {
                    $.bootstrapGrowl('Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.', {type: 'error', align: 'center', width: 'auto', delay: 8000});
                }).always(function () {
                });
            }
        }
    });
});

/*======================================================================================================================
 *   FORM SUBMIT BY AJAX
 ======================================================================================================================*/
/**
 * Submit form by ajax
 *
 * @param {type} param1
 * @param {type} param2S
 */
$(document).on('submit', '[data-toggle="ajax-form"]',function(e){
    e.preventDefault();

    var $form = $(this);
    var $submitBtn = $form.find('button[type=submit]');
    $submitBtn.button('loading');

    var form = $(this)[0];
    var formData = new FormData(form);
    var method = $form.attr("method");
    if(typeof method === 'undefined'){
        method = "POST";
    }

    $.ajax({
        url: $form.attr('action'),
        data: formData,
        type: method,
        contentType: false,
        processData: false,
        success: function (data) {
            if (data.result) {
                if($form.data('refresh-datatables')) {
                    try {
                        oTable.draw();
                    } catch(e) {}
                }

                if($form.data('replace-with') && data.html) {
                    var target = $form.data('replace-with');
                    $(document).find(target).html(data.html)
                }

                Growl.success(data.feedback);
                $form.closest('.modal').modal('hide');
            } else {
                Growl.error('Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.')
                $form.find('.modal-feedback').html('<i class="fas fa-exclamation-circle"></i> ' + data.feedback);
            }

            if (data.html) {
                $(data.target).html(data.html);
            }

            $submitBtn.button('reset');
            $form.find('[data-dismiss=fileinput]').trigger('click');
        }
    }).fail(function () {
        Growl.error500()
        $form.find('.modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
    }).always(function () {
        $submitBtn.button('reset');
    });

});

/**
 * Prevent submit form if fields not
 */
$(document).on('submit', 'form', function(e){
    e.stopImmediatePropagation();

    var $form = $(this);
    var $submit = $(this).find('[type="submit"]');

    if($submit.data('loading-text') === undefined) {
        var value = $submit.val(); //only works on <input>
        $submit.data('loading-text', value + "...");
    }

    $submit.button('loading');

    if($form.find('input[name="agencies[]"]').length > 0 || $form.find('input[name="role_id[]"]').length > 0) {

        if($form.find('[name="agencies[]"]').length > 0 && $form.find('[name="agencies[]"]:checked').length == 0) {
            Growl.error('Deve indicar a que agência(s) este utilizador pertence.');
            $submit.button('reset');
            e.preventDefault();
            return false;
        } else if($form.find('[name="role_id[]"]').length > 0 && $form.find('[name="role_id[]"]:checked').length == 0) {
            Growl.error('Deve escolher pelo menos um ou mais perfís de permissão.')
            $submit.button('reset');
            e.preventDefault();
            return false;
        }
    }
})

//generic message feedback on AJAX form submit
$(document).on('ajax:complete','form', function() {
    $(this).find('[type="submit"]').button('reset');
}).on('ajax:error','form', function() {
    Growl.error("Ocorreu um erro tente mais tarde")
}).on('ajax:success','form', function(event, data){
    $.bootstrapGrowl(data.message, { type: data.type});
});

/*======================================================================================================================
 *   EVENTS
 ======================================================================================================================*/

//Mark event as concluded
$(document).on('click', '.event-mark-concluded', function(e){
    e.preventDefault();
    var $row = $(this).closest('tr');

    $.post($(this).attr('href'), function(data){
        if(data.concluded) {
            $row.find('.fa-check').removeClass('text-muted').addClass('text-green');
            $row.find('.event-title').html('<strike>' + $row.find('.event-title').html()+ '</strike>')
        } else {
            $row.find('.fa-check').removeClass('text-green').addClass('text-muted');
            $row.find('.event-title').html($row.find('.event-title strike').html())
        }
    });
});

//Read / Unread notification
$(document).on('click', '.notifications-menu', function (e) {
    e.stopPropagation();
});

$(document).on('click', '.notifications-menu > a', function (e) {
    $('.notifications-menu .menu').html('<li class="text-muted text-center" style="margin-top: 85px"><i class="fas fa-spin fa-circle-notch"></i> A carregar...</li>')
    $.post($(this).parent().data('href'), function(data){
        $('.notifications-menu .menu').html(data);
    }).fail(function(){
        $('.notifications-menu .menu').html('<li class="text-red text-center" style="margin-top: 85px"><i class="fas fa-exclamation-circle"></i> Erro ao obter notificações.</li>')
    });
});

$(document).on('click', '.notifications-menu .menu > li > a', function (e) {
    $(this).closest('.unread').find('.btn-notification-read').trigger('click');
});


$(document).on('click', '.btn-notification-read', function(e){
    e.preventDefault();

    var totalNotifications = parseInt($('[data-toggle="notifications-counter"]').html());
    var $target = $(this);
    $('.notifications-menu').addClass('open');

    $.post($target.data('href'), function(data){
        if(data.read) {
            totalNotifications--;
            Notifier.set(totalNotifications);
            $target.parent().removeClass('unread');
            $target.find('.fa').removeClass('fa-circle').addClass('fa-circle-o')
        } else {
            totalNotifications++;
            Notifier.set(totalNotifications);
            $target.parent().addClass('unread');
            $target.find('.fa').removeClass('fa-circle-o').addClass('fa-circle')
        }
    })
})

$(document).on('click', '.btn-read-all-notifications', function(e){
    e.preventDefault();

    $.post($(this).attr('href'), function(data){
        $('.notifications-menu .menu').html(data);
        Notifier.set(0);
    })
})


/*======================================================================================================================
 *   MODALS
 ======================================================================================================================*/
//Generic event when modal fails
$(document).ajaxError(function( event, jqxhr, settings, exception ) {
    if($('.modal.in .modal-content form').length == 0 && ($('#modal-remote').hasClass('in') || $('#modal-remote-lg').hasClass('in') || $('#modal-remote-xl').hasClass('in'))) {
        html = '<h4 class="modal-title text-center m-t-40 m-b-15 text-red">' +
            '<i class="fas fa-exclamation-circle fs-40"></i><br> Erro interno de servidor.' +
            '<br><span class="fw-400">Não foi possível abrir a janela porque ocorreu um erro de processamento interno.</span>' +
            '</h4>' +
            '<div class="text-center m-b-40"><button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Fechar</button></div>';

        $('.modal.in').data('backdrop', 'true');
        $('.modal.in .modal-body').html(html);
    }
});

$('body').on('shown.bs.modal', '#modal-remote, #modal-remote-lg, #modal-remote-xl, #modal-remote-xs', function () {
    $('input').iCheck(Init.iCheck());
    $('.datepicker').datepicker(Init.datepicker());
    modalInstance = $(this);
});

//reset modal remote
$('body').on('hidden.bs.modal', '#modal-remote, #modal-remote-lg, #modal-remote-xl, #modal-remote-xs', function (e) {

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

    modalInstance = null;

    var html = '<div class="modal-body">'
                +'<h4 class="modal-title text-center m-t-40 m-b-40 text-muted">'
                +'<i class="fas fa-circle-notch fa-spin"></i> A carregar...'
                +'</h4>'
                +'</div>';

    $(this).find('.modal-content').html(html);
});

$(document).on('click', '.close-popup', function(e){
    e.preventDefault();
    var $target = $(this).closest('.popup-message');
    var cookieName = $target.data('cookie');

    Cookies.set(cookieName, 'hidden', {expires: 365});

    if($target.hasClass('header-message')) {
        $($target).slideUp();
    } else {
        $($target).remove();
    }
});

/*======================================================================================================================
 *   PRODUCT TABLES
 ======================================================================================================================*/
$(document).on('click', '[data-action]',function(e){
    e.preventDefault();
    var $this = $(this);
    var $target = $($this.data('target'));
    var rowId, columnId, locale, index;
    
    if($this.data('action') == 'table-line-add') {
        addLine($target)
    } else if($this.data('action') == 'table-line-after') {
        addLineAfter($target, $(this).closest('tr'));
        
    } else if($this.data('action') == 'table-line-remove') {
        $this.closest('tr').remove();
        if($target.find('tr').lenght == 2){
            $target.find('tr:last').addClass('hidden')
        }
    }
})

function addLine($target){
    $trLast = $target.find("tr:last");
    addLineAfter($target, $trLast);
}

function addLineAfter($target, $this){
    $trNew = $this.clone();
    $trNew.find('input').val('');
    $trNew.find('td').prop('style', null)
    $this.after($trNew);
    Init.maskPrice();
}


/*======================================================================================================================
 *   VALIDATE FILE SIZE & EXTENSION
 ======================================================================================================================*/
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

/*======================================================================================================================
 *   PLAY SOUNDS
 ======================================================================================================================*/
/**
 * Play sound notifications
 * @param {type} $
 * @returns {undefined}
 */
$.extend({
    playSound: function(){
        return $(
            '<audio autoplay="autoplay" style="display:none;">'
            + '<source src="' + arguments[0] + '.mp3" />'
            + '<source src="' + arguments[0] + '.ogg" />'
            + '<embed src="' + arguments[0] + '.mp3" hidden="true" autostart="true" loop="false" class="playSound" />'
            + '</audio>'
        ).appendTo('body');
    }
});



/*======================================================================================================================
 *   NIF VALIDATOR
 ======================================================================================================================*/

//Button validate NIF
$(document).on('click', '.btn-validate-nif', function(){
    var hash    = Str.random(5);
    var $modal  = $('#modal-vat-validation');
    var $input  = $(this).closest('.input-group').find('input');
    var vat     = $input.val()
    var country = $($(this).data('vv-country')).val()
    var countryName = $($(this).data('vv-country')).find('option:selected').text()
    var url     = $(this).data('href') + '?vat=' + vat + '&country=' + country;
    var title   = country.toUpperCase() + ' ' + vat; //Str.chunk(vat,3).join(' ');
    var valid   = checkVATNumber(country + vat);

    vat = vat == '' ? '999999990' : vat
    $(this).prop('id', hash)
    $modal.find('[name="vv-target"]').val(hash)

    $('.vv-accept').prop('disabled', true);
    $modal.find('.vv-vat').html(title)
    $modal.find('.vv-country-name').html(countryName)

    if(valid.valid_vat) {
        $('.vv-valid').show();
        $('.vv-invalid').hide();
        $input.closest('.form-group').removeClass('has-error')

        if(vat == '999999990') {
            $modal.find('table, .vv-feedback').hide()
        } else {
            $modal.find('table').hide();
            $modal.find('.vv-feedback').show()

            $modal.find('.vv-name,.vv-address,.vv-phone,.vv-mobile').html('<i class="fas fa-spin fa-circle-notch"></i>')

            if (valid.valid_vat) {
                $('.vv-feedback').html('<h4 class="m-b-0 fw-400"><i class="fas fa-spin fa-circle-notch"></i> A procurar dados associados à entidade.</h4>')
                $.get(url, function (response) {

                    if (response.status != false) {
                        $modal.find('table').show();
                        $modal.find('.vv-feedback').hide();

                        $modal.find('[name="vv-name"]').val(response.data.name)
                        $modal.find('[name="vv-address"]').val(response.data.address)
                        $modal.find('[name="vv-zip-code"]').val(response.data.zip_code)
                        $modal.find('[name="vv-city"]').val(response.data.city)
                        $modal.find('[name="vv-phone"]').val(response.data.phone)
                        $modal.find('[name="vv-mobile"]').val(response.data.mobile)
                        $modal.find('[name="vv-logo"]').val(response.data.logo)

                        $modal.find('.vv-name').html(response.data.name)
                        $modal.find('.vv-address').html(response.data.address + '<br/>' + response.data.zip_code + ' ' + response.data.city)
                        $modal.find('.vv-phone').html(response.data.phone)
                        $modal.find('.vv-mobile').html(response.data.mobile)
                        if (response.data.logo) {
                            $modal.find('.vv-logo').attr('src', response.data.logo)
                        }

                        $('.vv-accept').prop('disabled', false);

                    } else {
                        $modal.find('table').hide();
                        $modal.find('.vv-feedback').show().html('<p class="m-t-15 m-b-0">' + response.feedback + '</p>')
                    }
                }).fail(function () {
                    $modal.find('table').hide();
                    $modal.find('.vv-feedback').show().html('<p class="text-red m-t-15 m-b-0">' + response.feedback + '</p>')
                })
            }
        }
    } else {
        $('.vv-valid, .vv-feedback').hide();
        $('.vv-invalid').show();
        $modal.find('table, .vv-feedback').hide()
    }
});

//Fill nif fields with search result
$(document).on('click', '.vv-accept', function(){
    var $modal   = $(this).closest('.modal');
    var targetId = '#' + $modal.find('[name="vv-target"]').val();
    var $target  = $(targetId);

    $($target.data('vv-name')).val($modal.find('[name="vv-name"]').val())
    $($target.data('vv-address')).val($modal.find('[name="vv-address"]').val())
    $($target.data('vv-zip-code')).val($modal.find('[name="vv-zip-code"]').val())
    $($target.data('vv-city')).val($modal.find('[name="vv-city"]').val())
    $($target.data('vv-phone')).val($modal.find('[name="vv-phone"]').val())
    $($target.data('vv-mobile')).val($modal.find('[name="vv-mobile"]').val())
    $($target.data('vv-logo')).val($modal.find('[name="vv-logo"]').val())

    $modal.modal('hide');
});

$(document).on('click', '.show-all-agencies', function(){
    $(this).closest('div').find('.checkbox').show()
})

/**
 * Change input locales
 *
 * @type type
 */
$(document).on('click', '[data-toggle="change-locale"] a',function(e) {
    e.preventDefault();
    var $localeBtn = $('[data-toggle="change-locale"]');
    var locale = $(this).data('locale-target');

    $localeBtn.find('.locale-key').html(locale);
    $('.locale-key').html(locale);
    $('.locale-flag').html('<i class="flag-icon flag-icon-'+locale+'"></i>');
    $('[name=locale_key]').val(locale);
    $('[data-input-locale]').hide();
    $('[data-input-locale='+locale+']').show();

    URL.change(URL.updateParameter(URL.current(), 'locale', locale));
})


$(document).on('click','.btn-fast-search',function(e){
    e.preventDefault();
    $(this).closest('form').submit();
});
