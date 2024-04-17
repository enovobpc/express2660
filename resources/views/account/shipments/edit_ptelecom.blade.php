{{ Form::model($shipment, $formOptions) }}
{{ Form::hidden('is_collection', 0) }}
{{ Form::hidden('volumes', 1) }}
{{ Form::hidden('weight', 1) }}
{{ Form::hidden('customer_id') }}
{{ Form::hidden('service_id') }}
{{ Form::hidden('charge_price') }}
@if($shipment->type == \App\Models\Shipment::TYPE_RETURN)
    {{ Form::hidden('parent_tracking_code') }}
    {{ Form::hidden('type', \App\Models\Shipment::TYPE_RETURN) }}
@endif

<div class="modal-header bg-primary">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title text-white">{{ $action }}</h4>
</div>
<div class="modal-body p-l-15 p-t-15 p-r-15 p-b-10 modal-shipment">
    <div class="row row-5 shipment-top-header">
        @if(!empty($services))
            <div class="col-sm-3 col-md-2">
                <div class="form-group m-b-5 p-0 services-shipments">
                    {{ Form::label('service', trans('account/global.word.service'), ['class' => 'col-sm-2 col-md-3 control-label p-0']) }}
                    <div class="col-sm-10 col-md-9" style="padding-right: 15px;">
                        {!! Form::selectWithData('services', $services, @$shipment->service_id, ['class' => 'form-control select2', 'required']) !!}
                    </div>
                </div>
            </div>
        @endif

        @if(!empty($providers))
            @if(count($providers) == 1)
                <div class="hide">
                    {{ Form::select('provider_id', $providers) }}
                </div>
            @else
                <div class="col-sm-2">
                    <div class="form-group m-b-5 p-0">
                        {{ Form::label('provider_id', trans('account/global.word.provider'), ['class' => 'col-sm-3 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2', 'data-toggle'=> 'tooltip', 'title' => 'Utilize o campo referência livremente para referênciar a sua encomenda.']) }}
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <div class="col-sm-3 col-md-2">
            <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="Utilize este campo para inserir um código ou referência sua. Por exemplo, o número da sua fatura, guia de transporte ou encomenda. Máximo 15 caracteres.">
                {{ Form::label('reference', trans('account/global.word.reference'), ['class' => 'col-sm-4 control-label p-r-0']) }}
                <div class="col-sm-8">
                    {{ Form::text('reference', null, ['class' => 'form-control', 'maxlength' => 15]) }}
                </div>
            </div>
        </div>
        <div class="col-sm-1 col-md-2">
            <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="{{ Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : '' }}">
                {{ Form::label('reference2', 'Operador', ['class' => 'col-sm-4 control-label p-r-0']) }}
                <div class="col-sm-8">
                    {{ Form::select('reference2', ['NOS' => 'NOS','Vodafone' => 'Vodafone','Nowo' => 'Nowo', 'Woo' => 'Woo'], null, ['class' => 'form-control', 'maxlength' => 15, 'required']) }}
                </div>
            </div>
        </div>

        @if(empty($services))
            <div class="hidden-sm col-sm-3 col-md-2"></div>
        @endif

        @if(empty($providers) || count($providers) <= 1)
            <div class="hidden-sm col-md-1 col-lg-2"></div>
        @endif

        @if(!Setting::get('customers_shipment_hours'))
            <div class="col-sm-2 col-md-1 col-lg-2"></div>
        @endif
        <div class="col-sm-2 col-md-2 col-lg-2">
            <div class="form-group m-b-5">
                {{ Form::label('date', trans('account/global.word.date'), ['class' => 'col-lg-3 col-sm-2 control-label p-0']) }}
                <div class="col-sm-10 col-lg-8 p-r-0 p-l-5">
                    <div class="input-group">
                        {{ Form::text('date', $shipment->exists ? null : $shipmentDate, ['class' => 'form-control datepicker', 'required']) }}
                        <span class="input-group-addon">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @if(Setting::get('customers_shipment_hours'))
            <div class="col-sm-3 col-md-3 col-lg-2">
                <div class="form-group m-b-5">
                    {{ Form::label('hour', trans('account/global.word.hour'), ['class' => 'col-sm-3 col-lg-2 control-label p-0']) }}
                    <div class="col-sm-9 col-lg-10 p-r-0 p-l-5">
                        <div class="input-group">
                            <div style="float: left; width: 64px">
                                {{ Form::select('start_hour', ['' => ''] + $hours, null, ['class' => 'form-control select2']) }}
                            </div>
                            <div style="float: left;width: 23px;height: 32px; font-size: 12px; margin: 0 -1px;padding: 8px 2px;background: #ccc;color: #333;">
                                {{ trans('account/global.word.to') }}
                            </div>
                            <div style="float: left; width: 64px">
                                {{ Form::select('end_hour', ['' => ''] + $hours, null, ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @if(!empty(Setting::get('shipments_daily_limit_hour')) && $exceeded)
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-info m-b-15">
                    <i class="fas fa-info-circle"></i> Já não é possível o motorista efectuar a recolha dos seus volumes hoje. Data prevista para recolha e expedição: {{ $shipmentDate }}.
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-default m-b-0">
                <div class="panel-heading">
                    <h4>Titular</h4>
                </div>
                <div class="panel-body p-10 bg-gray-light" id="box-sender">
                    @include('account.shipments.partials.ptelecom.senderBlock')
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-default m-b-0">
                <div class="panel-heading">
                    <h4>Beneficiário</h4>
                </div>
                <div class="panel-body p-10 bg-gray-light" id="box-recipient">
                    @include('account.shipments.partials.ptelecom.recipientBlock')
                </div>
            </div>
        </div>
    </div>
    <div class="m-t-20"></div>
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group form-group-sm m-b-5">
                {{ Form::label('custom_fields[field-1]', 'Linha Rede #1', ['class' => 'col-sm-4 control-label p-r-0']) }}
                <div class="col-sm-8">
                    {{ Form::text('custom_fields[field-1]', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="form-group form-group-sm m-b-5">
                {{ Form::label('custom_fields[field-2]', 'Linha Rede #2', ['class' => 'col-sm-4 control-label p-r-0']) }}
                <div class="col-sm-8">
                    {{ Form::text('custom_fields[field-2]', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="form-group form-group-sm m-b-0">
                {{ Form::label('custom_fields[field-3]', 'Linha Rede #3', ['class' => 'col-sm-4 control-label p-r-0']) }}
                <div class="col-sm-8">
                    {{ Form::text('custom_fields[field-3]', null, ['class' => 'form-control']) }}
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group form-group-sm m-b-5">
                {{ Form::label('custom_fields[field-4]', 'Linha Rede #4', ['class' => 'col-sm-4 control-label p-r-0']) }}
                <div class="col-sm-8">
                    {{ Form::text('custom_fields[field-4]', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="form-group form-group-sm m-b-5">
                {{ Form::label('custom_fields[field-5]', 'Linha Rede #5', ['class' => 'col-sm-4 control-label p-r-0']) }}
                <div class="col-sm-8">
                    {{ Form::text('custom_fields[field-5]', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="form-group form-group-sm m-b-0">
                {{ Form::label('custom_fields[field-6]', 'Linha Rede #6', ['class' => 'col-sm-4 control-label p-r-0']) }}
                <div class="col-sm-8">
                    {{ Form::text('custom_fields[field-6]', null, ['class' => 'form-control']) }}
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group form-group-sm m-b-0">
                {{ Form::label('obs', 'Observações (Max. 150 Caractéres)', ['class' => 'col-sm-2 control-label p-r-0']) }}
                <div class="col-sm-10">
                    {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => 5, 'maxlength' => 150]) }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A criar envio...">Gravar</button>
</div>
{{--@include('account.shipments.partials.dimensions')--}}
{{ Form::close() }}

<script>
    $(".select2").select2(Init.select2());

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        language: 'pt',
        startDate: '{{ $shipmentDate }}'
    });

    $("[name=services_collection], [name=services]").on('change', function(){
        var serviceId = $(this).val();
        $('[name=service_id]').val(serviceId);
    });

    /**
     * Search recipient
     */
    /*$("select[name=recipient_id]").select2({
        ajax: {
            url: "{{ route('account.shipments.search.recipient') }}",
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                $('select[name=recipient_id] option').remove();
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });*/

    $('.search-sender').autocomplete({
        serviceUrl: '{{ route('account.shipments.search.recipient') }}',
        onSearchStart: function () {
            $('[name="sender_id"]').val('');
        },
        onSelect: function (suggestion) {
            $('[name="sender_id"]').val(suggestion.data).trigger('change');
            $('.box-sender-content .save-checkbox').hide();
        },
    });

    $('.search-recipient').autocomplete({
        serviceUrl: '{{ route('account.shipments.search.recipient') }}',
        onSearchStart: function () {
            $('[name="recipient_id"]').val('');
        },
        onSelect: function (suggestion) {
            $('[name="recipient_id"]').val(suggestion.data).trigger('change');
            $('.box-recipient-content .save-checkbox').hide();
        }
    });

    $('.search-sender').on('change', function(){
        if($('[name="sender_id"]').val() == '') {
            $('.box-sender-content .save-checkbox').show();
        } else {
            $('.box-sender-content .save-checkbox').hide();
        }
    })

    $('.search-recipient').on('change', function(){
        if($('[name="recipient_id"]').val() == '') {
            $('.box-recipient-content .save-checkbox').show();
        } else {
            $('.box-recipient-content .save-checkbox').hide();
        }
    })


    /**
     * Search or add new customer
     */
    $('.btn-toggle-customer').on('click', function () {
        var $formGroup = $(this).closest('.form-group');
        var $panel = $(this).closest('.panel-body');

        if ($formGroup.find('.select2').is(':visible')) {
            $formGroup.find('input').not('[name=recipient_code]').prop('required', true);
            $formGroup.find('select').prop('required', false);
            $(this).find('i').removeClass('fa-user-plus').addClass('fa-search');

        } else {
            $formGroup.find('input').prop('required', false);
            $formGroup.find('select').prop('required', true);
            $(this).find('i').removeClass('fa-search').addClass('fa-user-plus');
        }

        $panel.find('.save-checkbox').toggle();
        $panel.find('input[type=text], .select').val('');
        $formGroup.find('input, .select2').toggle();
        $formGroup.find('select option').remove();

        $('input[name=customer_id]').val($('select[name=customer_search]').val());
    });

    /**
     * Get recipient data
     */
    $('[name=recipient_id]').on('change', function () {
        var $box = $(this).closest('.box-body');

        $box.find('.has-error').remove();

        $('label[for=recipient_address] .fa-spin').removeClass('hide');

        $.post("{{ route('account.shipments.get.recipient') }}", {id: $(this).val()}, function (data) {

            $('[name=recipient_name]').val(data.name);
            $('[name=recipient_address]').val(data.address);
            $('[name=recipient_zip_code]').val(data.zip_code);
            $('[name=recipient_city]').val(data.city);
            $('[name=recipient_phone]').val(data.phone);
            $('[name=recipient_city]').val(data.city);
            $('[name=recipient_country]').val(data.country).trigger("change");
            $('[name=recipient_agency_id]').val(data.agency_id).trigger("change");
            $('[name=recipient_email]').val(data.email);
            $('[name=recipient_attn]').val(data.responsable);
            $('[name=obs]').val(data.obs);

        }).fail(function () {
            $box.append('<p class="text-red m-b-0 m-t-5 has-error"><i class="fas fa-exclamation-circle"></i> Ocorreu um erro ao obter os dados do destinatário.</p>');
        }).always(function () {
            $('label[for=recipient_address] i').addClass('hide');
        })
    });

    $('[name=sender_id]').on('change', function () {
        var $box = $(this).closest('.box-body');

        $box.find('.has-error').remove();

        $('label[for=sender_address] .fa-spin').removeClass('hide');

        $.post("{{ route('account.shipments.get.recipient') }}", {id: $(this).val()}, function (data) {

            $('[name=sender_name]').val(data.name);
            $('[name=sender_address]').val(data.address);
            $('[name=sender_zip_code]').val(data.zip_code);
            $('[name=sender_city]').val(data.city);
            $('[name=sender_phone]').val(data.phone);
            $('[name=sender_city]').val(data.city);
            $('[name=sender_country]').val(data.country).trigger("change");
            $('[name=sender_agency_id]').val(data.agency_id).trigger("change");
            $('[name=sender_email]').val(data.email);
            $('[name=sender_attn]').val(data.responsable);
            $('[name=obs]').val(data.obs);

        }).fail(function () {
            $box.append('<p class="text-red m-b-0 m-t-5 has-error"><i class="fas fa-exclamation-circle"></i> Ocorreu um erro ao obter os dados do destinatário.</p>');
        }).always(function () {
            $('label[for=sender_address] i').addClass('hide');
        })
    });

    /*
     * change service
     */
    $('.modal-xl [name=is_collection]').on('change', function () {
        var tmp;
        var changePosition = false;
        var $selectSearchCustomer = $('.select-search-customer');
        var $selectSearchRecipient = $('.select-search-recipient');
        var $senderPlace = $selectSearchCustomer.closest('.input-group');
        var $recipientPlace = $selectSearchRecipient.closest('.input-group');

        if ($(this).val() == 1) { //serviço de recolhas
            if ($('#box-sender .box-sender-content').length > 0) {
//                //troca posição das caixas
                $('#box-recipient').append($('.box-sender-content'));
                $('#box-sender').append($('.box-recipient-content'));
                $('label[for=recipient_id]').html('Remetente');
                $('label[for=customer_id]').html('Destinatário');
            }

            $('.services-shipments').hide().find('select').prop('required', false);
            $('.services-collections').show().find('select').prop('required', true);
            $('.services-shipments, .services-collections').find('select').val('').trigger('change');
            $('.hide-on-collection').hide();
            $('[name=print_guide]').prop('checked', false);
        } else { //serviços normais
            if (!$('#box-sender .box-sender-content').length > 0) {
                $('#box-sender').append($('.box-sender-content'));
                $('#box-recipient').append($('.box-recipient-content'));
                $('label[for=recipient_id]').html('Destinatário');
                $('label[for=customer_id]').html('Remetente');
            }

            $('.services-shipments').show().find('select').prop('required', true);
            $('.services-collections').hide().find('select').prop('required', false);
            $('.services-shipments, .services-collections').find('select').val('').trigger('change');
            $('.hide-on-collection').show();
            $('[name=print_guide]').prop('checked', true);
        }
    })

    /**
     * Change service
     */
    $(document).on('change', '.modal-xl [name=services]', function(){

        var $this = $(this).find(':selected');
        if($this.data('unity') == 'km') {
            $('.input-km').show();
        } else {
            $('.input-km').hide();
        }
        calcVolumetricWeight();
        validateTotalVolumes();
    })

    /**
     * Get zone and agency of recipient from zip code
     */
    $('[name=recipient_zip_code]').on('change', function () {
        var zipCode = $(this).val();

        $('label[for=recipient_address] .fa-spin').removeClass('hide');
        $('[name=recipient_city]').val('');
        $('[name=recipient_city]').autocomplete('dispose');

        $.post("{{ route('account.shipments.get.agency') }}", {zipCode: zipCode}, function (data) {
            $('[name=recipient_country]').val(data.zone).trigger("change");
            if(data.agency_id) {
                $('[name=recipient_agency_id]').val(data.agency_id).trigger("change");
            } else {
                $('[name=recipient_agency_id]').val($('#modal-remote-xl select[name=agency_id]').val()).trigger("change");
            }

            if(data.cities.length > 1) {
                $('[name=recipient_city]').autocomplete({lookup: data.cities, minChars: 0});
            } else if(data.cities.length == 1) {
                $('[name=recipient_city]').val(data.cities[0]['value'])
            }

        }).fail(function () {
            $box.append('<p class="text-red m-b-0 m-t-5 has-error"><i class="fas fa-exclamation-circle"></i> Ocorreu um erro ao obter a agência correspondente.</p>');
        }).always(function() {
            $('label[for=recipient_address] i').addClass('hide');
            $('[name=recipient_city]').focus();
        })
    });

    /**
     * Get sender agency from zip code
     */
    $('[name=sender_zip_code]').on('change', function () {
        var zipCode = $(this).val();

        $('label[for=sender_address] .fa-spin').removeClass('hide');
        $('[name=sender_city]').val('');
        $('[name=sender_city]').autocomplete('dispose')

        $.post("{{ route('account.shipments.get.agency') }}", {zipCode: zipCode}, function (data) {
            $('[name=sender_country]').val(data.zone).trigger("change");
            if(data.agency_id) {
                $('[name=sender_agency_id]').val(data.agency_id).trigger("change");
            } else {
                $('[name=sender_agency_id]').val($('#modal-remote-xl select[name=agency_id]').val()).trigger("change");
            }

            if(data.cities.length > 1) {
                $('[name=sender_city]').autocomplete({lookup: data.cities, minChars: 0});
            } else if(data.cities.length == 1) {
                $('[name=sender_city]').val(data.cities[0]['value'])
            }

        }).fail(function () {
            $box.append('<p class="text-red m-b-0 m-t-5 has-error"><i class="fas fa-exclamation-circle"></i> Ocorreu um erro ao obter a agência correspondente.</p>');
        }).always(function() {
            $('label[for=sender_address] i').addClass('hide');
            $('[name=sender_city]').focus();
        })
    });

    /**
     * DIMENSIONS
     */
    //show dimensions modal
    $('[data-target="#modal-shipment-dimensions"]').on('click', function(){
        $('#modal-shipment-dimensions').addClass('in').show();
    })

    //hide dimensions modal
    $('.confirm-dimensions').on('click', function(){
        $('#modal-shipment-dimensions').removeClass('in').hide();
        var val;
        var fatorM3 = 0;
        $('#modal-shipment-dimensions [name="fator_m3_row[]"]').each(function(){
            val = $(this).val() == "" ? 0 : $(this).val();
            fatorM3+= parseFloat(val);
        })

        var weight  = 0;
        $('#modal-shipment-dimensions [name="weight_row[]"]').each(function(){
            val = $(this).val() == "" ? 0 : $(this).val();
            weight+= parseFloat(val);
        })

        $('[name="fator_m3"]').val(fatorM3);
        if(weight != 0) {
            $('[name="weight"]').val(weight);
        }
        $('.fator_m3').html(fatorM3).closest('small').show();
        calcVolumetricWeight();
    })

    //Change volumes
    $('.modal-xl [name=volumes]').on('change', function(){
        var volumes = $(this).val();

        $('[name="fator_m3"], [name="volumetric_weight"]').val('');
        $('.fator_m3').closest('small').hide();
        $('.helper-empty-service').hide();

        $tr = $('table.shipment-dimensions tbody tr:first');
        rowCount = $('table.shipment-dimensions tbody tr').length;


        if(rowCount < volumes) { // add rows
            for (i = rowCount; i < volumes; i++) {
                clonedRow = $tr.clone();
                clonedRow.find('input').val('');
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

        /*if (volumes != $('[name="width[]"]').length) {
            $('table.shipment-dimensions tr:gt(0)').html('');
            $('.modal-xl [name=length], .modal-xl [name=width],.modal-xl [name=height], .modal-xl [name=fator_m3]').val("")
        }

        var i;
        for (i = 1; i <= volumes; i++) {
            var html = '<tr>';
            html += '<td>' + i + '</td>';
            html += '<td><div class="input-group input-group-sm"><input class="form-control input-sm m-0" name="length[]" type="text"><div class="input-group-addon">cm</div></div></td>';
            html += '<td><div class="input-group input-group-sm"><input class="form-control input-sm m-0" name="width[]" type="text"><div class="input-group-addon">cm</div></div></td>';
            html += '<td><div class="input-group input-group-sm"><input class="form-control input-sm m-0" name="height[]" type="text"><div class="input-group-addon">cm</div></div></td>';
            html += '<td><div class="input-group input-group-sm"><input class="form-control input-sm m-0" name="weight_row[]" type="text"><div class="input-group-addon">kg</div></div></td>';
            html += '<td><input class="form-control input-sm m-0" name="fator_m3_row[]" type="text" readonly></td>';
            html += '</tr>';

            $('table.shipment-dimensions').append(html);
        }*/

        validateTotalVolumes()
    })

    $(document).on('change', '[name="width[]"], [name="height[]"], [name="length[]"]', function(){
        var $tr = $(this).closest('tr');

        var width   = $tr.find('[name="width[]"]').val();
        var height  = $tr.find('[name="height[]"]').val();
        var length  = $tr.find('[name="length[]"]').val();

        $tr.find('[name="fator_m3_row[]"]').val(calcVolume(width, height, length));
    })

    function calcVolume(width, height, length) {
        var width  = width == "" ? 0 : width;
        var length = length == "" ? 0 : length;
        var height = height == "" ? 0 : height;
        return (parseFloat(width) * parseFloat(height) * parseFloat(length)) / 1000000;
    }

    function calcVolumetricWeight(){
        var fatorM3 = $('[name="fator_m3"]').val();
        var serviceId = $('[name="service_id"]').val();

        $('.helper-empty-service').hide();
        if(fatorM3 != "" && (serviceId == "" || serviceId === "undifined")) {
            $('.helper-empty-service').show();
        } else {
            $.post('{{ route("account.shipments.get.volumetric-weight") }}', {fatorM3:fatorM3, serviceId:serviceId}, function(data){
                $('[name="volumetric_weight"]').val(data.weight);

                if(data.emptyService) {
                    $('.helper-empty-service').show();
                }
            })
        }
    }

    $('.modal-xl [name=weight]').on('change', function(){
        validateTotalVolumes()
    });

    /**
     * Validate total volumes
     */
    function validateTotalVolumes(){

        var maxValue  = $('.modal-xl [name=services]').find(':selected').data('max');
        var maxWeight = parseFloat($('.modal-xl [name=services]').find(':selected').data('max-weight'));
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

    /**
     * Submit form
     *
     * @param {type} param1
     * @param {type} param2S
     */
    $('.form-shipment').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $button = $('button[type=submit]');

        $button.button('loading');
        $.post($form.attr('action'), $form.serialize(), function (data) {
            if (data.result) {
                oTable.draw(); //update datatable
//                $.bootstrapGrowl(data.feedback, {type: 'success', align: 'center', width: 'auto', delay: 8000});

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
                $('.form-shipment .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> ' + data.feedback);
            }

        }).fail(function () {
            $('.form-shipment .modal-feedback').html('<i class="fas fa-exclamation-circle"></i> Erro de processamento interno. Não foi possível submeter e concluir o seu pedido.');
        }).always(function () {
            $button.button('reset');
        })
    });
</script>