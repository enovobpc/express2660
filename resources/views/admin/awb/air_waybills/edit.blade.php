{{ Form::model($waybill, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="tabbable-line m-b-15">
    @if($preFillModels)
    <ul class="list-inline pull-right pre-fill">
        <li>
            <div style="margin: 7px 10px -9px; width: 200px">
            {{ Form::select('prefill', ['' => 'Modelo em branco'] + $preFillModels, null, ['class' => 'form-control select2']) }}
            </div>
        </li>
    </ul>
    @endif
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-info" data-toggle="tab">
                Informação Base
            </a>
        </li>
        <li>
            <a href="#tab-goods" data-toggle="tab">
                Mercadoria
            </a>
        </li>
        <li>
            <a href="#tab-expenses" data-toggle="tab">
                Encargos
            </a>
        </li>
        <li>
            <a href="#tab-observations" data-toggle="tab">
                Observações e Anotações
            </a>
        </li>
        <li>
            <a href="#tab-hawb" data-toggle="tab">
                HAWB
            </a>
        </li>
    </ul>
</div>
<div class="modal-body p-t-0 p-b-0 modal-shipment">
    <div class="tab-content m-b-0" style="padding-bottom: 10px;">
        <div class="tab-pane active" id="tab-info">
            @include('admin.awb.air_waybills.partials.geral')
        </div>
        <div class="tab-pane" id="tab-goods">
            @include('admin.awb.air_waybills.partials.goods')
        </div>
        <div class="tab-pane" id="tab-expenses">
            @include('admin.awb.air_waybills.partials.expenses')
        </div>
        <div class="tab-pane" id="tab-observations">
            @include('admin.awb.air_waybills.partials.observations')
        </div>
        <div class="tab-pane" id="tab-hawb">
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    $hash = str_random(5);
                    if($waybill->exists) {
                        $query = ['parent' => $waybill->id];
                    } else {
                        $query = ['hash' => $hash];
                    }

                    ?>
                    <a href="{{ route('admin.air-waybills.hawb.create', $query) }}" class="btn btn-sm btn-success m-b-15" data-toggle="modal" data-target="#modal-remote-lg">Adicionar HAWB</a>
                    @include('admin.awb.air_waybills.partials.hawb')
                    {{ Form::hidden('hawb_hash', $hash) }}
                </div>
            </div>
            <div class="spacer-30"></div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options">
        <div class="checkbox">
            <label>
                {{ Form::checkbox('print_awb', 1, $waybill->exists ? false : true) }}
                Ao gravar, imprimir carta de porte
            </label>
        </div>
        <div class="clearfix"></div>
        <div class="text-red m-t-5 m-b-0 modal-feedback text-left"></div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary btn-submit-waybill">Gravar</button>
</div>
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker());


    $('[name="sender_address"], [name="consignee_address"], [name="issuer_address"]').keydown(function(e) {
        newLines = $(this).val().split("\n").length;
        if(e.keyCode == 13 && newLines >= parseInt($(this).attr('rows'))) {
            return false;
        }
    });

    $(document).ready(function(){
        $('.table-goods').find('.volumes').trigger('change');
    })

    $(".form-waybill select[name=source_airport], .form-waybill select[name=recipient_airport], .form-waybill .search-airport").select2({
        ajax: {
            url: "{{ route('admin.air-waybills.search.airport') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('.form-waybill select[name=customer_id] option').remove()
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    /**
     * Prefill
     */
    $('[name=prefill]').on('change', function () {
        var prefillId = $(this).val();

        bootbox.dialog({
            title: 'Usar modelo pré-preenchido',
            message: '<h4>Ao usar um modelo pré-preenchido, todas as alterações que tenha efetuado serão subscritas ou apagadas. Deseja continuar?</h4>',
            buttons: {
                cancel: {
                    label: 'Cancelar'
                },
                main: {
                    label: 'Usar Modelo',
                    className: 'btn-success',
                    callback: function(result) {
                        var html = '<div class="modal-loading"><h4><i class="fas fa-spin fa-circle-notch"></i> A carregar modelo...</h4></div>'
                        $('#modal-remote-xl .modal-content').append(html)
                        $.post("{{ route('admin.air-waybills.prefill') }}", {prefill: prefillId}, function (data) {
                            $('#modal-remote-xl .modal-content').html(data)
                            $('#modal-remote-xl').css('overflow', 'auto');
                        }).fail(function () {
                            $('.form-waybill .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
                        }).always(function() {
                            if($('#modal-remote-xl .modal-content [name=provider_id]').val() != '') {
                                $('#modal-remote-xl .modal-content [name=provider_id]').trigger('change');
                            }
                        })
                    }
                }
            }
        });
    });

    $('.form-waybill .search-customer').autocomplete({
        serviceUrl: '{{ route('admin.air-waybills.search.customer') }}',
        onSearchStart: function () {
            $('.form-waybill [name="customer_id"]').val('');
        },
        onSelect: function (suggestion) {
            $('.form-waybill [name="customer_id"]').val(suggestion.data).trigger('change');
        },
    });

    $('.form-waybill .search-consignee').autocomplete({
        serviceUrl: '{{ route('admin.air-waybills.search.customer') }}',
        onSearchStart: function () {
            $('.form-waybill [name="consignee_id"]').val('');
        },
        onSelect: function (suggestion) {
            $('.form-waybill [name="consignee_id"]').val(suggestion.data).trigger('change');
        },
    });

    /**
     * Change customer
     */
    $('.form-waybill [name=customer_id]').on('change', function () {
        var customerId = $(this).val();

        $('.form-waybill input[name=customer_id]').val(customerId);

        $.post("{{ route('admin.air-waybills.get.customer') }}", {id: customerId}, function (data) {

            $('.form-waybill [name=sender_name]').val(data.name);
            $('.form-waybill [name=sender_address]').val(data.address);
            $('.form-waybill [name=sender_vat]').val(data.vat);

        }).fail(function () {
            $('.form-waybill .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function() {
            $('label[for=sender_address] .fa-spin').addClass('hide');
        })
    });

    /**
     * Change consignee
     */
    $('[name=consignee_id]').on('change', function () {
        var consigneeId = $(this).val();

        $('input[name=consignee_id]').val(consigneeId);

        $.post("{{ route('admin.air-waybills.get.customer') }}", {id: consigneeId}, function (data) {

            $('[name=consignee_name]').val(data.name);
            $('[name=consignee_address]').val(data.address);
            $('[name=consignee_vat]').val(data.vat);

        }).fail(function () {
            $('.form-waybill .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function() {
            $('label[for=sender_address] .fa-spin').addClass('hide');
        })
    });

    /**
     * Get provider data
     */
    $('[name=provider_id]').on('change', function () {

        $.post("{{ route('admin.air-waybills.get.provider') }}", {id: $(this).val()}, function (data) {
            $('[name=issuer_id]').val(data.id);
            $('[name=issuer_name]').val(data.name);
            $('[name=issuer_address]').val(data.address);
            $('[name="awb[1]"]').val(data.iata_no).trigger('change');

        }).fail(function () {
            $('.form-waybill .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function() {
            $('label[for=recipient_address] i').addClass('hide');
        })
    });


    $('[name="awb[1]"],[name="awb[2]"],[name="awb[3]"]').on('change', function () {
        var awb1 = $('[name="awb[1]"]').val();
        var awb2 = $('[name="awb[2]"]').val();
        var awb3 = $('[name="awb[3]"]').val();
        var title = awb1 + '-' + awb2 + ' ' + awb3;
        $('[name="title"]').val(title);
    });

    /**
     * Add flight scale
     */
    $('.btn-add-flight-scale').on('click', function(){
        $('.table-flight-scales').find('tr:hidden:first').show();

        if($('.table-flight-scales').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.remove-flight-scale').on('click', function(){

        if($('.table-flight-scales').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.table-flight-scales').append($tr);

            if ($('.table-flight-scales').find("tr:hidden").length == 0) {
                $('.btn-add-flight-scale').hide();
            } else {
                $('.btn-add-flight-scale').show();
            }
        }
    });

    /**
     * Add goods
     */
    $('.btn-add-goods').on('click', function(){
        $('.table-goods').find('tr:hidden:first').show();

        if($('.table-goods').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.remove-goods').on('click', function(){

        if($('.table-goods').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.find('input').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.table-goods').append($tr);

            if ($('.table-goods').find("tr:hidden").length == 0) {
                $('.btn-add-goods').hide();
            } else {
                $('.btn-add-goods').show();
            }
        }
    });

    /**
     * Add Expenses
     */
    $('.btn-add-expenses').on('click', function(){
        $('.table-expenses').find('tr:hidden:first').show();

        if($('.table-expenses').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.remove-expenses').on('click', function(){

        if($('.table-expenses').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.find('input').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.table-expenses').append($tr);

            if ($('.table-expenses').find("tr:hidden").length == 0) {
                $('.btn-add-expenses').hide();
            } else {
                $('.btn-add-expenses').show();
            }
        }
    });

    /**
     * Add Other Expenses
     */
    $('.btn-add-other-expenses').on('click', function(){
        $('.table-other-expenses').find('tr:hidden:first').show();

        if($('.table-other-expenses').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.remove-other-expenses').on('click', function(){

        if($('.table-other-expenses').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.find('input').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.table-other-expenses').append($tr);

            if ($('.table-other-expenses').find("tr:hidden").length == 0) {
                $('.btn-add-other-expenses').hide();
            } else {
                $('.btn-add-other-expenses').show();
            }
        }
    });

    $('.volumes, .rate-charge, .weight, .chargeable-weight, .rate_class').on('change', function(){
        $tr = $(this).closest('tr');
        var rateClass = $tr.find('.rate_class').val();
        var chargeableWeight = parseFloat($tr.find('.chargeable-weight').val());
        var rateCharge       = parseFloat($tr.find('.rate-charge').val());

        if(isNaN(chargeableWeight)) { chargeableWeight = 0; }
        if(isNaN(rateCharge)) { rateCharge = 0; }

        if(rateClass == 'M') {
            total = rateCharge
        } else {
            total = chargeableWeight * rateCharge
        }

        $tr.find('.total').val(total.toFixed(2));

        var totalVolumes = 0;
        var totalWeight = 0;
        var totalChargableWeight = 0;

        $('.table-goods tr').each(function() {

            volumes = parseInt($(this).find('.volumes').val())
            if(isNaN(volumes)) {
                var volumes = 0;
            }

            weight = parseFloat($(this).find('.weight').val())
            if(isNaN(weight)) {
                var weight = 0.00;
            }

            chargableWeight = parseFloat($(this).find('.chargeable-weight').val())
            if(isNaN(chargableWeight)) {
                var chargableWeight = 0.00;
            }

            totalVolumes = totalVolumes + volumes;
            totalWeight = totalWeight + weight;
            totalChargableWeight = totalChargableWeight + chargableWeight;
        })

        $('[name="volumes"]').val(totalVolumes)
        $('[name="weight"]').val(totalWeight)
        $('[name="chargable_weight"]').val(totalChargableWeight)
    });

    /**
     * Delete HAWB
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $(document).on('click', '.table-hawb .delete-hawb', function(e) {
        e.preventDefault();

        var url = $(this).attr('href');
        var $tr = $(this).closest('tr');

        bootbox.dialog({
            title: 'Eliminar HAWB',
            message: '<h4>Pretende eliminar esta HAWB?</h4>',
            buttons: {
                cancel: {
                    label: 'Cancelar'
                },
                main: {
                    label: 'Eliminar',
                    className: 'btn-danger',
                    callback: function(result) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            success: function(result) {
                                $tr.remove();
                                if($('.table-hawb .delete-hawb').length == 0) {
                                    $('.layer-disabled, .table-goods-hawb-alert').hide();
                                }
                            }
                        }).fail(function () {
                            $('.form-hawb .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
                        });
                    }
                }
            }
        });
    });

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-waybill input,.form-waybill textarea,.form-waybill select,.form-waybill .select2-selection').on('change', function(){
        $(this).css('border-color', '');
        $(this).next().find('.select2-selection').css('border-color', '');
    })

    $('.btn-submit-waybill').on('click', function (e) {
        e.preventDefault();

        var notFilled = 0

        $('.form-waybill input,.form-waybill textarea,.form-waybill select,.form-waybill .select2-selection').css('border-color', '');

        $('.form-waybill input,.form-waybill textarea,.form-waybill select').filter('[required]').each(function(){
            if($(this).val() == '') {
                notFilled = notFilled + 1;
                $(this).css('border-color', 'red');
                $(this).next().find('.select2-selection').css('border-color', 'red');
            }
        });

        if(notFilled > 0) {
            $('.modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Há ' + notFilled + ' campos obrigatórios não preenchidos. Corrija os campos assinalados antes de gravar.')
        } else {
            $('.form-waybill').submit();
        }
    })

    /**
     * Calculate expense price
     */
    $('.expense-id').on('change', function(e) {
        if($(this).val() == "") {
            $(this).removeClass('expense-id*');
        } else {
            $(this).addClass('expense-id-' + $(this).val());
        }
    });

    $('.btn-update-prices').on('click', function(){
        calcPrices();
    })

    $('.volumes, .rate-charge, .weight, .chargeable-weight, .rate_class, .expense-id').on('change', function(){
        calcPrices();
    });

    function calcPrices() {
        var expenses      = [];
        var provider      = $('.form-waybill [name="provider_id"]').val();
        var sourceAirport = $('.form-waybill select[name="source_airport"]').val();
        var recipientAirport = $('.form-waybill select[name="recipient_airport"]').val();
        var volumes       = $('.form-waybill [name="volumes"]').val();
        var weight        = $('.form-waybill [name="weight"]').val();
        var weightTaxable = $('.form-waybill [name="chargable_weight"]').val();

        $('.expense-id').each(function(){
            var $this = $(this);
            var value = $this.val();
            if(value != "") {
                $this.closest('tr').find('.input-group-addon').html('<i class="fas fa-spin fa-circle-notch"></i>');
                expenses.push(value);
            }
        })

        //$('.expense-price').val('');

        if(provider != '' || sourceAirport != '' || volumes != '') {
            $.post("{{ route('admin.air-waybills.get.price') }}", {
                expenses: expenses,
                provider: provider,
                sourceAirport: sourceAirport,
                recipientAirport: recipientAirport,
                weight: weight,
                weightTaxable: weightTaxable,
                volumes: volumes
            }, function (data) {

                data.forEach(function (item) {
                    var price = item.totalPrice;
                    var expenseId = item.expense;

                    $target = $('.expense-id option[value="' + expenseId + '"]:selected').closest('tr').find('.expense-price');

                    if($target.val() == "" || $target.val() == '0.00' || parseFloat(price) > parseFloat($target.val())) {
                        $target.val(price);
                    }
                })

            }).fail(function () {
                $.bootstrapGrowl('Não foi possível calcular os preços devido a um erro interno.', {
                    type: 'error',
                    align: 'center',
                    width: 'auto',
                    delay: 8000
                });
            }).always(function(){
                $('.price-currency-symbol').html("{{ Setting::get('app_currency') }}");
            })
        } else {
            $('.price-currency-symbol').html("{{ Setting::get('app_currency') }}");
        }
    };


    $('.form-waybill').on('submit', function(e) {
        e.preventDefault();

        $('.modal-feedback').html('');

        var $form   = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function (data) {
            if (data.result) {
                oTable.draw(false); //update datatable without change pagination
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});

                if (data.print) {
                    if (window.open(data.print, '_blank')) {
                        $('#modal-remote-xl').modal('hide');
                    } else {
                        $('#modal-remote-xl').find('.modal-xl').removeClass('modal-xl').find('.modal-content').html(data.html);
                    }
                } else {
                    $('#modal-remote-xl').modal('hide');
                }

            } else {
                $('.form-waybill .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> ' + data.feedback);
            }

        }).fail(function () {
            $('.form-waybill .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function () {
            $button.button('reset');
        });
    });


    var keynum, lines = 1;

    function limitLines(obj, e) {
        // IE
        if(window.event) {
            keynum = e.keyCode;
            // Netscape/Firefox/Opera
        } else if(e.which) {
            keynum = e.which;
        }

        if(keynum == 13) {
            if(lines == obj.rows) {
                return false;
            }else{
                lines++;
            }
        }
    }

</script>

<style>
    .table-sender td {
        padding: 2px !important;
    }

    #modal-remote-lg {
        z-index: 1600;
    }

    .select2-container .select2-selection--single {
        padding: 4px 10px;
        height: 30px;
    }
</style>

