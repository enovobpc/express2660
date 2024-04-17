{{ Form::model($waybill, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="tabbable-line m-b-15">
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
    </ul>
</div>
<div class="modal-body p-t-0 p-b-0 modal-shipment">
    <div class="tab-content m-b-0" style="padding-bottom: 10px;">
        <div class="tab-pane active" id="tab-info">
            @include('admin.awb.air_waybills.partials.hawb_geral')
        </div>
        <div class="tab-pane" id="tab-goods">
            @include('admin.awb.air_waybills.partials.goods')
        </div>
        <div class="tab-pane" id="tab-expenses">
            @include('admin.awb.air_waybills.partials.expenses')
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="extra-options">
        <div class="text-red m-t-5 m-b-0 modal-feedback text-left"></div>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Gravar</button>
</div>
{{ Form::hidden('hawb_hash', @$hash) }}
{{ Form::hidden('main_waybill_id', @$parentId) }}
{{ Form::close() }}

<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker());

    $(".form-hawb select[name=source_airport], .form-hawb select[name=recipient_airport], .form-hawb .search-airport").select2({
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
                $('.form-hawb select[name=customer_id] option').remove()
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });


    $('.form-hawb .search-customer').autocomplete({
        serviceUrl: '{{ route('admin.air-waybills.search.customer') }}',
        onSearchStart: function () {
            $('.form-hawb [name="customer_id"]').val('');
        },
        onSelect: function (suggestion) {
            $('.form-hawb [name="customer_id"]').val(suggestion.data).trigger('change');
        },
    });

    $('.form-hawb .search-consignee').autocomplete({
        serviceUrl: '{{ route('admin.air-waybills.search.customer') }}',
        onSearchStart: function () {
            $('.form-hawb [name="consignee_id"]').val('');
        },
        onSelect: function (suggestion) {
            $('.form-hawb [name="consignee_id"]').val(suggestion.data).trigger('change');
        },
    });

    /**
     * Change customer
     */
    $('.form-hawb [name=customer_id]').on('change', function () {
        var customerId = $(this).val();

        $('.form-hawb input[name=customer_id]').val(customerId);

        $.post("{{ route('admin.air-waybills.get.customer') }}", {id: customerId}, function (data) {

            $('.form-hawb [name=sender_name]').val(data.name);
            $('.form-hawb [name=sender_address]').val(data.address);
            $('.form-hawb [name=sender_vat]').val(data.vat);

        }).error(function () {
            $('.form-hawb .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function() {
            $('label[for=sender_address] .fa-spin').addClass('hide');
        })
    });

    /**
     * Change consignee
     */
    $('.form-hawb [name=consignee_id]').on('change', function () {
        var consigneeId = $(this).val();

        $('.form-hawb input[name=consignee_id]').val(consigneeId);

        $.post("{{ route('admin.air-waybills.get.customer') }}", {id: consigneeId}, function (data) {

            $('.form-hawb [name=consignee_name]').val(data.name);
            $('.form-hawb [name=consignee_address]').val(data.address);
            $('.form-hawb [name=consignee_vat]').val(data.vat);

        }).error(function () {
            $('.form-hawb .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function() {
            $('.form-hawb label[for=sender_address] .fa-spin').addClass('hide');
        })
    });

    /**
     * Get provider data
     */
    $('.form-hawb [name=provider_id]').on('change', function () {

        $.post("{{ route('admin.air-waybills.get.provider') }}", {id: $(this).val()}, function (data) {
            $('[name=issuer_id]').val(data.id);
            $('[name=issuer_name]').val(data.name);
            $('[name=issuer_address]').val(data.address);
            $('[name="awb[1]"]').val(data.iata_no).trigger('change');

        }).error(function () {
            $('.form-hawb .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function() {
            $('label[for=recipient_address] i').addClass('hide');
        })
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

    $('.form-hawb .remove-goods').on('click', function(){

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
     * Add goods
     */
    $('.form-hawb .btn-add-expenses').on('click', function(){
        $('.table-expenses').find('tr:hidden:first').show();

        if($('.table-expenses').find("tr:hidden").length == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

    $('.form-hawb .remove-expenses').on('click', function(){

        if($('.form-hawb .table-expenses').find("tr:visible").length > 2) {
            var $tr = $(this).closest('tr');
            $tr.find('select').val('').trigger('change');
            $tr.find('input').val('').trigger('change');
            $tr.hide();

            $tr.detach();
            $('.form-hawb .table-expenses').append($tr);

            if ($('.form-hawb .table-expenses').find("tr:hidden").length == 0) {
                $('.form-hawb .btn-add-expenses').hide();
            } else {
                $('.form-hawb .btn-add-expenses').show();
            }
        }
    });

    $('.form-hawb .rate-charge, .chargeable-weight').on('change', function(){
        $tr = $(this).closest('tr');

        var chargeableWeight = parseFloat($tr.find('.chargeable-weight').val());
        var rateCharge       = parseFloat($tr.find('.rate-charge').val());

        total = chargeableWeight * rateCharge
        $tr.find('.total').val(total.toFixed(2));
    });

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

                    if($target.val() == "" || $target.val() == '0.00') {
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

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-hawb').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function (data) {
            if (data.result) {
                $('.table-hawb').html(data.html);
                $('.layer-disabled, .table-goods-hawb-alert').show();
                $('#modal-remote-lg').modal('hide');
                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});
            } else {
                $('.form-hawb .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> ' + data.feedback);
            }

        }).error(function () {
            $('.form-hawb .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function () {
            $button.button('reset');
        });
    });

</script>

<style>
    .table-sender td {
        padding: 2px !important;
    }

    .select2-container .select2-selection--single {
        padding: 4px 10px;
        height: 30px;
    }
</style>

