<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title"><i class="fas fa-calculator"></i> Calcular custos de envio</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-8">
            <div class="row row-5">
                <div class="col-sm-9">
                    <div class="form-group is-required">
                        {{ Form::label('budget_customer', 'Cliente', ['class' => 'control-label']) }}
                        {{ Form::select('budget_customer', [], null, ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('budget_provider', 'Fornecedor', ['class' => 'control-label']) }}
                        {{ Form::select('budget_provider', ['' => ''] + $providers, null, ['class' => 'form-control select2']) }}
                    </div>
                </div>
            </div>

            <div class="row row-5">
                <div class="col-sm-2">
                    <div class="form-group" data-toggle="tooltip" title="Necessário apenas para serviços abrangidos por rotas">
                        {{ Form::label('budget_sender_zip_code', 'CP Origem', ['class' => 'control-label']) }}
                        {{ Form::text('budget_sender_zip_code', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('budget_sender_country', 'País Origem', ['class' => 'control-label']) }}
                        {{ Form::select('budget_sender_country', trans('country'), Setting::get('app_country'), ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group" data-toggle="tooltip" title="Necessário apenas para serviços abrangidos por rotas">
                        {{ Form::label('budget_recipient_zip_code', 'CP Destino', ['class' => 'control-label']) }}
                        {{ Form::text('budget_recipient_zip_code', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('budget_recipient_country', 'País Destino', ['class' => 'control-label']) }}
                        {{ Form::select('budget_recipient_country', trans('country'), Setting::get('app_country'), ['class' => 'form-control select2', 'required']) }}
                    </div>
                </div>
            </div>
            <div class="row row-5">
                <div class="col-sm-4">
                    <div class="form-group is-required">
                        {{ Form::label('budget_service', trans('account/global.word.service'), ['class' => 'control-label']) }}<br/>
                        {!! Form::selectWithData('budget_service', $services, null, ['class' => 'form-control select2', 'required'])!!}
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group is-required"
                         data-toggle="tooltip"
                         title="{{ trans('account/shipments.budget.tips.volumes') }}">
                        {{ Form::label('budget_volumes', trans('account/global.word.volumes'), ['class' => 'control-label']) }}
                        {{ Form::text('budget_volumes', null, ['class' => 'form-control number', 'required']) }}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group is-required"
                         data-toggle="tooltip"
                         title="{{ trans('account/shipments.budget.tips.weight') }}">
                        {{ Form::label('budget_weight', trans('account/global.word.weight'), ['class' => 'control-label']) }}
                        <div class="input-group">
                            {{ Form::text('budget_weight', null, ['class' => 'form-control decimal']) }}
                            <span class="input-group-addon">kg</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3" style="display: none">
                    <div class="form-group is-required"
                         data-toggle="tooltip"
                         title="{{ trans('account/shipments.budget.tips.kms') }}">
                        {{ Form::label('budget_kms', trans('account/global.word.distance').' (km)', ['class' => 'control-label']) }}
                        <div class="input-group">
                            {{ Form::text('budget_kms', null, ['class' => 'form-control decimal']) }}
                            <span class="input-group-addon">km</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group"
                         data-toggle="tooltip"
                         title="{{ trans('account/shipments.budget.tips.weight-vol') }}">
                        {{ Form::label('budget_volumetric_weight', trans('account/global.word.volumetric-weight'), ['class' => 'control-label']) }}
                        <div class="input-group">
                            {{ Form::text('budget_volumetric_weight', null, ['class' => 'form-control', 'required', 'readonly']) }}
                            <div class="input-group-addon"  data-toggle="modal" data-target="#modal-budget-dimensions">
                                kg <i class="fas fa-external-link-square-alt"></i>
                            </div>
                        </div>
                        {{ Form::hidden('budget_fator_m3') }}
                    </div>
                </div>
            </div>
            @if(Setting::get('app_mode') == 'express')
                <h4 class="m-0">{{ trans('account/shipments.budget.addicional-services.title') }}</h4>
                <div class="row row-5">
                    <div class="col-sm-3">
                        <div class="form-group m-b-0">
                            <div class="checkbox m-b-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('budget_pickup', '1') }} {{ trans('account/shipments.budget.addicional-services.pickup') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group m-b-0">
                            <div class="checkbox m-b-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('budget_charge', '1') }} {{ trans('account/shipments.budget.addicional-services.charge') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group m-b-0">
                            <div class="checkbox m-b-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('budget_rguide', '1') }} {{ trans('account/shipments.budget.addicional-services.rguide') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 hide">
                        <div class="form-group m-b-0">
                            <div class="checkbox m-b-0">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('budget_outstandard', '1') }} VFN
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-sm-4">
            <div class="text-center budget-result" style="padding: 0 20px; margin: 0px 0;border-left: 1px dotted #999; {{ (Setting::get('app_mode') == 'cargo' || Setting::get('app_mode') == 'courier') ? ' min-height: 200px; padding-top: 60px;' : ' min-height: 255px; padding-top: 40px;'  }}">
                <h1 class="m-0">
                    <small class="fs-15" style="display: block">Preço Previsto*</small>
                    <span class="budget-loading" style="display: none"><small><i class="fas fa-spin fa-circle-notch"></i></small></span>
                    <span class="budget-total">0,00{{ Setting::get('app_currency') }}</span>
                </h1>
                <p class="helper-empty-vat" style="display: none;">Isento de IVA</p>
                <p class="helper-vat">Com IVA: <b class="budget-total-vat">0,00{{ Setting::get('app_currency') }}</b></p>
                <p class="helper-particular" style="display: none">Cliente Particular.<br/>IVA já incluído.</p>
                <hr class="m-t-15 m-b-5 hide table-budget-details"/>
                <table class="table-condensed w-100 fs-13 hide table-budget-details">
                    <tr class="hide base">
                        <td class="text-right">Preço Base&nbsp;&nbsp;</td>
                        <td class="text-left bold w-90px"></td>
                    </tr>
                    <tr class="hide pickup">
                        <td class="text-right">Taxa de Recolha&nbsp;&nbsp;</td>
                        <td class="text-left bold w-90px"></td>
                    </tr>
                    <tr class="hide charge">
                        <td class="text-right">Taxa de Reembolso&nbsp;&nbsp;</td>
                        <td class="text-left bold w-90px"></td>
                    </tr>
                    <tr class="hide rguide">
                        <td class="text-right">Taxa Retorno Guia&nbsp;&nbsp;</td>
                        <td class="text-left bold w-90px"></td>
                    </tr>
                    <tr class="hide fueltax">
                        <td class="text-right">Taxa Combustível&nbsp;&nbsp;</td>
                        <td class="text-left bold w-90px"></td>
                    </tr>
                    <tr class="hide outstandard">
                        <td class="text-right">Volume Fora Norma&nbsp;&nbsp;</td>
                        <td class="text-left bold w-90px"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
</div>

<script>
    $('.select2').select2(Init.select2());

    $('[name="budget_service"], [name="budget_sender_country"],[name="budget_recipient_country"],[name="budget_volumes"],[name="budget_weight"],[name="budget_kms"],[name="budget_fator_m3"],[name="budget_sender_zip_code"],[name="budget_recipient_zip_code"],[name="budget_customer"],[name="budget_provider"]').on('change', function(e) {
        calcBudget();
    });

    $('[name="budget_pickup"],[name="budget_charge"],[name="budget_rguide"],[name="budget_outstandard"]').on('change', function(e) {
        calcBudget();
    });


    function calcBudget() {
        $('.budget-loading').show()
        $.post("{{ route('admin.shipments.budget.calculate') }}",
            {
                customer         : $('[name="budget_customer"]').val(),
                service          : $('[name="budget_service"]').val(),
                provider         : $('[name="budget_provider"]').val(),
                sender_country   : $('[name="budget_sender_country"]').val(),
                recipient_country: $('[name="budget_recipient_country"]').val(),
                sender_zip_code  : $('[name="budget_sender_zip_code"]').val(),
                recipient_zip_code: $('[name="budget_recipient_zip_code"]').val(),
                volumes          : $('[name="budget_volumes"]').val(),
                weight           : $('[name="budget_weight"]').val(),
                kms              : $('[name="budget_kms"]').val(),
                fatorM3          : $('[name="budget_fator_m3"]').val(),
                charge           : $('[name="budget_charge"]').is(':checked'),
                pickup           : $('[name="budget_pickup"]').is(':checked'),
                rguide           : $('[name="budget_rguide"]').is(':checked'),
                outstandard      : $('[name="budget_outstandard"]').is(':checked'),
            }, function (data) {
                $('.budget-total').html(data.price)
                $('.budget-total-vat').html(data.priceVat)
                $('[name="budget_volumetric_weight"]').val(data.volumetricWeight)

                if(data.isParticular) {
                    $('.helper-particular').show();
                    $('.helper-vat').hide();
                    $('.helper-empty-vat').hide();
                } else {
                    $('.helper-particular').hide();
                    $('.helper-vat').show();
                    $('.helper-empty-vat').show();
                }

                if(data.hasAdicionalService) {
                    $('.table-budget-details tr').addClass('hide');
                    $('.table-budget-details').removeClass('hide');
                    $('.budget-result').css('padding-top', '0');
                    $('.table-budget-details .base').removeClass('hide');
                    $('.table-budget-details .base').find('td:last-child').html(data.basePrice)

                    if(data.hasCharge) {
                        $('.table-budget-details .charge')
                            .removeClass('hide')
                            .find('td:last-child')
                            .html(data.charge)
                    }

                    if(data.hasOutStandard) {
                        $('.table-budget-details .outstandard')
                            .removeClass('hide')
                            .find('td:last-child')
                            .html(data.outStandard)
                    }

                    if(data.hasPickup) {
                        $('.table-budget-details .pickup')
                            .removeClass('hide')
                            .find('td:last-child')
                            .html(data.pickup)
                    }

                    if(data.hasRguide) {
                        $('.table-budget-details .rguide')
                            .removeClass('hide')
                            .find('td:last-child')
                            .html(data.rguide)
                    }

                    if(data.hasFuelTax) {
                        $('.table-budget-details .fueltax')
                            .removeClass('hide')
                            .find('td:last-child')
                            .html(data.fuelTax)
                    }

                } else {
                    $('.table-budget-details').addClass('hide');
                    $('.budget-result').css('padding-top', '40px')
                    $('.table-budget-details .base').addClass('hide');
                }


            }).always(function(){
            $('.budget-loading').hide();
        })
    }

    $("select[name=budget_customer]").select2({
        ajax: {
            url: "{{ route('admin.shipments.search.customer') }}",
            dataType: 'json',
            method: 'POST',
            delay: 450,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=budget_customer] option').remove()
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });


    $(document).on('change', '[name="budget_recipient_country"], [name="budget_sender_country"]', function(){
        var sender = $('[name="budget_sender_country"]').val();
        var recipient = $('[name="budget_recipient_country"]').val();

        if(sender == recipient) {
            $('.helper-vat').show();
            $('.helper-empty-vat').hide();
            $('.helper-particular').hide();
        } else {
            $('.helper-vat').hide();
            $('.helper-empty-vat').show();
            $('.helper-particular').hide();
        }
    })

    $('[name="budget_service"]').on('change', function(e) {
        var unity = $(this).find(':selected').data('unity');

        if(unity == 'km') {
            $('[name="budget_kms"]').val('').closest('.form-group').parent().show()
            $('[name="budget_weight"]').val('').closest('.form-group').parent().hide()
        } else if(unity == 'm3') {
            $('[name="budget_kms"]').val('').closest('.form-group').parent().hide()
            $('[name="budget_weight"]').val('').closest('.form-group').parent().show()
            $('[name="budget_weight"]').closest('.form-group').find('label').html('{{ trans('account/global.word.volume') }} M3')
        } else {
            $('[name="budget_kms"]').val('').closest('.form-group').parent().hide()
            $('[name="budget_weight"]').val('').closest('.form-group').parent().show()
            $('[name="budget_weight"]').closest('.form-group').find('label').html('{{ trans('account/global.word.weight') }}')
        }
    });

    /**
     * DIMENSIONS
     */
    /*//hide dimensions modal
    $('.confirm-budget-dimensions').on('click', function(){
        $('#modal-budget-dimensions').modal('hide');
        var val;
        var fatorM3 = 0;
        var weight  = $('[name="budget_weight"]').val();
        var maxDimension = "";
        var maxWeight    = "";

        $('#modal-budget-dimensions [name="budget_fator_m3_row[]"]').each(function(){
            val = $(this).val() == "" ? 0 : $(this).val();
            fatorM3+= parseFloat(val);
        })

        $('[name="budget_outstandard"]').prop('checked', false);
        $('#modal-budget-dimensions [name="budget_ml_row[]"]').each(function(){
            val = $(this).val() == "" ? 0 : $(this).val();

            if(parseFloat(val) > parseFloat(maxDimension)) {
                $('[name="budget_outstandard"]').prop('checked', true);
            }
        })

        if(parseFloat(weight) > parseFloat(maxWeight)) {
            $('[name="budget_outstandard"]').prop('checked', true);
        }

        $('[name="budget_fator_m3"]').val(fatorM3).trigger('change');
        $('[name="budget_size"]').val(fatorM3).trigger('change');
    })

    //change weight
    $('[name="budget_weight"]').on('change', function(){
        var weight       = $(this).val();
        var maxDimension = "{{ Setting::get('shipments_dimension_out_off_standard') }}";
        var maxWeight    = "{{ Setting::get('shipments_weight_out_off_standard') }}";

        $('[name="budget_outstandard"]').prop('checked', false);
        if(parseFloat(weight) > parseFloat(maxWeight)){
            $('[name="budget_outstandard"]').prop('checked', true);
        }

        $('#modal-budget-dimensions [name="budget_ml_row[]"]').each(function(){
            val = $(this).val() == "" ? 0 : $(this).val();
            if(parseFloat(val) > parseFloat(maxDimension)) {
                $('[name="budget_outstandard"]').prop('checked', true);
            }
        })
    })

    //Change volumes
    $('[name=budget_volumes]').on('change', function(){
        var volumes = $(this).val();

        $('[name="budget_fator_m3"], [name="budget_volumetric_weight"]').val('');

        if (volumes != $('[name="budget_width[]"]').length) {
            $('table.budget-dimensions tr:gt(0)').html('');
            $('[name=budget_length], [name=budget_width],[name=height], [name=budget_fator_m3_row]').val("")
        }

        var i;
        for (i = 1; i <= volumes; i++) {
            var html = '<tr>';
            html += '<td>' + i + '</td>';
            html += '<td><div class="input-group input-group-sm"><input class="form-control input-sm m-0" name="budget_length[]" type="text"><div class="input-group-addon">cm</div></div></td>';
            html += '<td><div class="input-group input-group-sm"><input class="form-control input-sm m-0" name="budget_width[]" type="text"><div class="input-group-addon">cm</div></div></td>';
            html += '<td><div class="input-group input-group-sm"><input class="form-control input-sm m-0" name="budget_height[]" type="text"><div class="input-group-addon">cm</div></div></td>';
            html += '<td><input class="form-control input-sm m-0" name="budget_fator_m3_row[]" type="text" readonly></td>';
            html += '<td><input class="form-control input-sm m-0" name="budget_ml_row[]" type="text" readonly></td>';
            html += '</tr>';

            $('table.budget-dimensions').append(html);
        }
    })

    $(document).on('change', '[name="budget_width[]"], [name="budget_height[]"], [name="budget_length[]"]', function(){
        var $tr = $(this).closest('tr');

        var width   = $tr.find('[name="budget_width[]"]').val();
        var height  = $tr.find('[name="budget_height[]"]').val();
        var length  = $tr.find('[name="budget_length[]"]').val();

        width  = width == "" ? 0 : width;
        length = length == "" ? 0 : length;
        height = height == "" ? 0 : height;

        var ml      = parseFloat(width) + parseFloat(height) + parseFloat(length);

        $tr.find('[name="budget_fator_m3_row[]"]').val(calcVolume(width, height, length));
        $tr.find('[name="budget_ml_row[]"]').val(ml);
    })

    function calcVolume(width, height, length) {
        var width  = width == "" ? 0 : width;
        var length = length == "" ? 0 : length;
        var height = height == "" ? 0 : height;
        return (parseFloat(width) * parseFloat(height) * parseFloat(length)) / 1000000;
    }*/
</script>