{{ Form::model($shipment, $formOptions) }}
<div class="modal-header">
    <button class="close" data-dismiss="modal" type="button">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title text-white">{{ $action }}</h4>
</div>
<div class="modal-body p-l-15 p-t-15 p-r-15 p-b-10 modal-shipment">
    <div class="row row-5 shipment-top-header">
        <div class="col-sm-3 col-md-2">
            @if (!empty($services))
                <div class="form-group m-b-5 p-0 services-shipments">
                    {{ Form::label('service', trans('account/global.word.service'), ['class' => 'col-sm-2 col-md-3 control-label p-0']) }}
                    <div class="col-sm-10 col-md-9" style="padding-right: 15px;">
                        {!! Form::selectWithData('services', $services, @$shipment->service_id, ['class' => 'form-control select2', 'required']) !!}
                    </div>
                </div>
            @endif
        </div>

        @if (!empty($providers) && count($providers) > 1)
            <div class="col-sm-2">
                @if (count($providers) == 1)
                    <div class="hide">
                        {{ Form::select('provider_id', $providers) }}
                    </div>
                @else
                    <div class="form-group m-b-5 p-0">
                        {{ Form::label('provider_id', trans('account/global.word.provider'), ['class' => 'col-sm-3 control-label p-r-0']) }}
                        <div class="col-sm-9">
                            {{ Form::select('provider_id', ['' => ''] + $providers, null, ['class' => 'form-control select2', 'data-toggle' => 'tooltip', 'title' => 'Utilize o campo referência livremente para referênciar a sua encomenda.']) }}
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="col-sm-3 col-md-2">
            <div class="form-group" data-toggle="tooltip" data-placement="bottom"
                title="Utilize este campo para inserir um código ou referência sua. Por exemplo, o número da sua fatura, guia de transporte ou encomenda. Máximo 15 caracteres.">
                {{ Form::label('reference', trans('account/global.word.reference'), ['class' => 'col-sm-4 control-label p-r-0']) }}
                <div class="col-sm-8">
                    {{ Form::text('reference', null, ['class' => 'form-control', 'maxlength' => 15]) }}
                </div>
            </div>
        </div>
        <div class="col-sm-1 col-md-2">
            @if (Setting::get('shipments_reference2_visible'))
                <div class="form-group" data-toggle="tooltip" data-placement="bottom" title="{{ Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : '' }}">
                    {{ Form::label('reference2', Setting::get('shipments_reference2_name') ? Setting::get('shipments_reference2_name') : 'Ref #2', ['class' => 'col-sm-4 control-label p-r-0']) }}
                    <div class="col-sm-8">
                        {{ Form::text('reference2', null, ['class' => 'form-control', 'maxlength' => 15]) }}
                    </div>
                </div>
            @else
                &nbsp;
            @endif
        </div>

        @if (empty($providers) || count($providers) <= 1)
            <div class="hidden-sm col-md-1 col-lg-2"></div>
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
        <div class="col-sm-3 col-md-3 col-lg-2">
            <div class="form-group m-b-5">
                {{ Form::label('hour', trans('account/global.word.hour'), ['class' => 'col-sm-3 col-lg-2 control-label p-0']) }}
                <div class="col-sm-9 col-lg-10 p-r-0 p-l-5">
                    <div class="input-group">
                        <div style="float: left; width: 64px">
                            {{ Form::select('start_hour', ['' => ''] + $hours, null, ['class' => 'form-control select2', Setting::get('customers_shipment_hours_required') ? 'required' : '']) }}
                        </div>
                        <div style="float: left;width: 23px;height: 32px; font-size: 12px; margin: 0 -1px;padding: 8px 2px;background: #ccc;color: #333;">
                            {{ trans('account/global.word.to') }}
                        </div>
                        <div style="float: left; width: 64px">
                            {{ Form::select('end_hour', ['' => ''] + $hours, null, ['class' => 'form-control select2', Setting::get('customers_shipment_hours_required') ? 'required' : '']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-default m-b-0">
                <div class="panel-heading">
                    <h4 class="panel-title">{{ trans('account/shipments.modal-shipment.sender-block') }}</h4>
                </div>
                <div class="panel-body p-10 p-b-3 bg-gray-light" id="box-sender">
                    @include('account.shipments.partials.edit.sender_block')
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="panel panel-default m-b-0">
                <div class="panel-heading">
                    <h4 class="panel-title">{{ trans('account/shipments.modal-shipment.recipient-block') }}</h4>
                </div>
                <div class="panel-body p-10 p-b-3 bg-gray-light" id="box-recipient">
                    @include('account.shipments.partials.edit.recipient_block')
                </div>
            </div>
        </div>
    </div>
    <div class="m-t-20"></div>
    <div class="row row-10">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-3" style="width: 22%;margin-right: 15px; padding: 0">
                    <div class="form-group m-b-5">
                        {{ Form::label('volumes', 'Volumes', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                {{ Form::text('volumes', null, ['class' => 'form-control number', 'maxlength' => 4, 'required', 'data-toggle' => 'tooltip', 'title' => 'Número de pacotes ou objetos a enviar.', 'autocomplete' => 'off']) }}
                                <div class="input-group-btn">
                                    <button class="btn btn-default" data-target="#modal-shipment-dimensions" type="button">
                                        <i class="fas fa-expand"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="helper-max-volumes italic text-red line-height-1p0" style="display: none">
                                <small><i class="fas fa-info-circle"></i> O serviço selecionado apenas permite <b class="lbl-total-vol">1</b> volumes</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('weight', trans('account/global.word.weight'), ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="weight-col {{ !empty($shipment->volumetric_weight) ? 'col-sm-5' : 'col-sm-8' }}">
                            <div class="input-group">
                                {{ Form::text('weight', null, ['class' => 'form-control decimal', 'maxlength' => 7, 'required', 'data-toggle' => 'tooltip', 'title' => 'Peso total dos pacotes ou objetos a enviar.', 'autocomplete' => 'off']) }}
                                <div class="input-group-addon">kg</div>
                            </div>
                            <div class="helper-max-weight italic text-red line-height-1p0" style="display: none">
                                <small><i class="fas fa-info-circle"></i> O serviço selecionado apenas permite um máximo de <b class="lbl-total-kg">1</b> kg por expedição.</small>
                            </div>
                        </div>
                        <div class="col-sm-3 helper-volumetric-weight" style="{{ !empty($shipment->volumetric_weight) ? '' : 'display:none;' }} padding: 0; margin-left: -3px; font-size: 12px; color: #0c82ff;">
                            <p class="m-0">
                                <small>Volumétrico</small>
                                <br />
                                <b>{{ money($shipment->volumetric_weight) }}</b> kg
                            </p>
                        </div>
                    </div>
                    <div class="form-group input-km m-b-0" style="display: none">
                        {{ Form::label('kms', 'Distância', ['class' => 'col-sm-4 control-label p-r-0']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                {{ Form::text('kms', null, ['class' => 'form-control decimal', 'maxlength' => 7, 'data-toggle' => 'tooltip', 'title' => 'Km totais entre a origem e o destino.', 'autocomplete' => 'off']) }}
                                <div class="input-group-addon" style="padding: 5px 9px 5px 10px;">km</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3" style="width: 22%;">
                    <div class="form-group">
                        {{ Form::label('total_price_when_collecting', trans('account/global.word.advance'), ['class' => 'col-sm-4 control-label p-r-0 bold']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                {{ Form::text('total_price_when_collecting', $shipment->total_price_when_collecting == 0.0 ? '' : null, ['class' => 'form-control decimal', 'maxlength' => 8]) }}
                                <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('charge_price', trans('account/global.word.charge'), ['class' => 'col-sm-4 control-label p-r-0 bold']) }}
                        <div class="col-sm-8">
                            <div class="input-group">
                                {{ Form::text('charge_price', $shipment->charge_price == 0.0 ? '' : null, ['class' => 'form-control decimal', 'maxlength' => 8]) }}
                                <div class="input-group-addon">{{ Setting::get('app_currency') }}</div>
                            </div>
                        </div>
                    </div>
                    {{-- @if (!Setting::get('customers_hide_payment_at_recipient'))
                        <div class="form-group m-b-0">
                            <div class="checkbox pull-left">
                                <label style="margin-top: -5px;display: block; padding-left: 105px">
                                    {{ Form::checkbox('payment_at_recipient', 1) }}
                                    {{ trans('account/global.word.cod') }} {!! tip(trans('account/shipments.modal-shipment.tips.cod')) !!}
                                </label>
                            </div>
                        </div>
                    @endif --}}
                </div>
                <div class="col-sm-3" style="padding: 0">
                    <div class="form-group m-b-0">
                        {{ Form::label('obs', trans('account/global.word.obs-pickup'), ['class' => 'col-sm-3 control-label p-r-0', 'style' => 'height:45px']) }}
                        <div class="col-sm-9">
                            {{ Form::textarea('obs', null, ['class' => 'form-control', 'rows' => Setting::get('app_rpack') ? 2 : 4, 'maxlength' => 150]) }}
                        </div>
                    </div>
                    @if (Setting::get('app_rpack'))
                        <div class="form-group" data-toggle="tooltip" title="Ative esta opção caso no ato da entrega pretenda que seja devolvida uma nova encomenda para o remetente." style="padding-left: 70px;">
                            <div class="checkbox">
                                <label>
                                    {{ Form::checkbox('has_return[]', 'rpack', null, ['class' => 'trigger-price']) }}
                                    {{ trans('account/shipments.modal-shipment.return-pack') }}
                                </label>
                            </div>
                        </div>
                    @endif

                </div>
                <div class="col-sm-3" style="width: 29%">
                    <div class="form-group">
                        {{ Form::label('obs', trans('account/global.word.obs-shipment'), ['class' => 'col-sm-2 control-label p-r-0', 'style' => 'height:45px']) }}
                        <div class="col-sm-10">
                            {{ Form::textarea('obs_delivery', null, ['class' => 'form-control', 'rows' => 2, 'maxlength' => 150]) }}
                        </div>
                    </div>
                    @if (Setting::get('tracking_email_active') && Setting::get('app_mode') != 'cargo')
                        <div class="form-group p-r-0 m-b-0">
                            {{ Form::label('recipient_email', 'E-mail', ['class' => 'col-sm-2 control-label p-r-0']) }}
                            <div class="col-sm-10">
                                <div class="input-group">
                                    {{ Form::email('recipient_email', null, ['class' => 'form-control email nospace lowercase']) }}
                                    <div class="input-group-addon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <div class="extra-options">
        {{-- @if (Setting::get('tracking_email_active'))
            <div class="row row-5" style="float: left; width: 320px; margin-right: 25px;">
                <div class="form-group m-b-0 m-t-0">
                    {{ Form::label('recipient_email', 'Notificar por E-mail', ['class' => 'col-sm-3 control-label p-r-0 lh-1-1', 'style' => 'margin-top:-4px']) }}
                    <div class="col-sm-9">
                        <div class="input-group input-group-email">
                            <div class="input-group-addon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            {{ Form::email('recipient_email', null, ['class' => 'form-control email nospace lowercase']) }}
                        </div>
                    </div>
                </div>
            </div>
        @endif --}}
        {{-- <div class="pull-left">
            <p style="margin: 5px 8px 0 0;"><b>{{ trans('account/global.word.print') }}</b></p>
        </div>
        <div class="checkbox" style="margin-top: 3px">
            <label>
                {{ Form::checkbox('print_guide', 1, $shipment->exists ? false : (($defaultPrint == 'guide' || $defaultPrint == 'all') ? true : false)) }}
                {{ trans('account/shipments.print.guide') }}
            </label>
        </div>
        <div class="checkbox" style="margin-left: -8px; margin-top: 3px;">
            <label>
                {{ Form::checkbox('print_label', 1, $shipment->exists ? false : (($defaultPrint == 'labels' || $defaultPrint == 'all') ? true : false)) }}
                {{ trans('account/shipments.print.labels') }}
            </label>
        </div>
        @if ($defaultPrint == 'cmr')
            <div class="checkbox" style="margin-left: -8px; margin-top: 3px;">
                <label>
                    {{ Form::checkbox('print_cmr', 1, $shipment->exists ? false : (($defaultPrint == 'cmr' || $defaultPrint == 'all') ? true : false)) }}
                    {{ trans('account/shipments.print.cmr') }}
                </label>
            </div>
        @endif --}}
        <div class="clearfix"></div>
    </div>

    @if ($shipment->type == \App\Models\Shipment::TYPE_RETURN)
        {{ Form::hidden('parent_tracking_code') }}
        {{ Form::hidden('type') }}
    @endif
    {{ Form::hidden('customer_id', null) }}
    {{ Form::hidden('service_id', null) }}
    {{ Form::hidden('volumetric_weight') }}
    {{ Form::hidden('fator_m3') }}
    <input name="is_collection" type="hidden" value="0" />
    <button class="btn btn-default" data-dismiss="modal" type="button">{{ trans('account/global.word.close') }}</button>
    <button class="btn btn-black" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> {{ trans('account/global.word.loading') }}..." type="submit">{{ trans('account/global.word.save') }}</button>
</div>
@include('account.shipments.partials.edit.dimensions', ['isPickup' => true])
@include('account.shipments.modals.confirm_sync_error')
{{ Form::close() }}

{{ Html::script(asset('vendor/devbridge-autocomplete/dist/jquery.autocomplete.js')) }}
<script>
    $(".modal .select2").select2(Init.select2());
    $('.modal .select2-country').select2(Init.select2Country());

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        language: 'pt',
        startDate: '{{ $shipmentDate }}'
    });


    var ROUTE_SEARCH_RECIPIENT = "{{ route('account.shipments.search.recipient') }}";
    var ROUTE_GET_AGENCY = "{{ route('account.shipments.get.agency') }}";
    var FILL_HOURS = {{ Setting::get('customers_shipment_hours_fill') ? 1 : 0 }};
    var VOLUMES_MESURE_UNITY    = "{{ Setting::get('shipments_volumes_mesure_unity') ? Setting::get('shipments_volumes_mesure_unity') : 'cm' }}";
    var APP_SOURCE              = "{{ config('app.source') }}";

    $(" [name=services]").on('change', function() {
        var $this = $(this).find('option:selected');
        var serviceId = $(this).val();
        $('[name=service_id]').val(serviceId);

        // DISABLE NON ALLOWED HOURS ON SERVICE
        $('.modal [name="start_hour"] option, .modal [name="end_hour"] option').each(function() {
            $(this).prop('disabled', false);
        });
        $('.modal [name="start_hour"], .modal [name="end_hour"]').select2(Init.select2());

        $('.modal [name="start_hour"] option, .modal [name="end_hour"] option').each(function(index, el) {
            var $el = $(el);
            if (!$el.val()) {
                return
            };

            if ($el.val() < $this.data('min-hour') || $el.val() > $this.data('max-hour')) {
                $el.prop('disabled', true);
            }
        });
        //--

        // SELECT DEFAULT MIN HOUR AND MAX HOUR
        if (FILL_HOURS) {
            $('.modal [name="start_hour"]').val($this.data('default-min-hour')).trigger('change');
            $('.modal [name="end_hour"]').val($this.data('default-max-hour')).trigger('change');
        }
        //--
    });

    /**
     * SEARCH SENDER
     * ajax method
     */
    $('.search-sender').autocomplete({
        serviceUrl: ROUTE_SEARCH_RECIPIENT,
        minChars: 2,
        onSearchStart: function() {
            $('[name="sender_id"]').val('');
        },
        beforeRender: function(container, suggestions) {
            container.find('.autocomplete-suggestion').each(function(key, suggestion, data) {
                if (suggestions[key].code != '') {
                    $(this).append('<div class="autocomplete-address">[' + suggestions[key].code + '] - ' + suggestions[key].address + ' - ' + suggestions[key].zip_code + ' ' + suggestions[key].city + '</div>')
                } else {
                    $(this).append('<div class="autocomplete-address">' + suggestions[key].address + ' - ' + suggestions[key].zip_code + ' ' + suggestions[key].city + '</div>')
                }
            });
        },
        onSelect: function(suggestion) {
            $('[name="sender_id"]').val(suggestion.data);
            $('[name="sender_attn"]').val(suggestion.responsable);
            $('[name="sender_name"]').val(suggestion.name).trigger('change');
            $('[name="sender_address"]').val(suggestion.address);
            $('[name="sender_zip_code"]').val(suggestion.zip_code);
            $('[name="sender_city"]').val(suggestion.city);
            $('[name="sender_country"]').val(suggestion.country).trigger('change.select2');
            $('[name="sender_phone"]').val(suggestion.phone).trigger('change');
            $('[name="sender_agency_id"]').val(suggestion.agency).trigger('change.select2');

            $('.search-sender').autocomplete('hide');
            $('#box-sender .save-checkbox').hide();
            $('#box-sender input[name="save_sender"]').prop('checked', false);
        },
    });

    /**
     * SEARCH RECIPIENT
     * ajax method
     */
    $('.search-recipient').autocomplete({
        serviceUrl: ROUTE_SEARCH_RECIPIENT,
        minChars: 2,
        onSearchStart: function() {
            $('[name="recipient_id"]').val('');
        },
        beforeRender: function(container, suggestions) {
            container.find('.autocomplete-suggestion').each(function(key, suggestion, data) {
                $(this).append('<div class="autocomplete-address">' + suggestions[key].address + ' - ' + suggestions[key].zip_code + ' ' + suggestions[key].city + '</div>')
            });
        },
        onSelect: function(suggestion) {
            $('[name="recipient_id"]').val(suggestion.data)
            $('[name="recipient_attn"]').val(suggestion.responsable);
            $('[name="recipient_name"]').val(suggestion.name).trigger('change');
            $('[name="recipient_address"]').val(suggestion.address);
            $('[name="recipient_zip_code"]').val(suggestion.zip_code);
            $('[name="recipient_city"]').val(suggestion.city);
            $('[name="recipient_country"]').val(suggestion.country).trigger('change.select2');
            $('[name="recipient_phone"]').val(suggestion.phone).trigger('change');
            $('[name="recipient_agency_id"]').val(suggestion.agency).trigger('change.select2');
            if (suggestion.obs && $('[name=obs]').val() == '') {
                $('[name=obs]').val(suggestion.obs);
            }
            $('.search-recipient').autocomplete('hide');
            $('#box-recipient .save-checkbox').hide();
            $('#box-recipient input[name="save_recipient"]').prop('checked', false);
        }
    })

    $('.search-sender').on('change', function() {
        if ($('[name="sender_id"]').val() == '') {
            $('.box-sender-content .save-checkbox').show();
        } else {
            $('.box-sender-content .save-checkbox').hide();
        }
    })

    $('.search-recipient').on('change', function() {
        if ($('[name="recipient_id"]').val() == '') {
            $('.box-recipient-content .save-checkbox').show();
        } else {
            $('.box-recipient-content .save-checkbox').hide();
        }
    })

    /**
     * Get department data
     */
    $('[name=department_id]').on('change', function() {
        var $box = $(this).closest('.box-body');

        $box.find('.has-error').remove();

        $('label[for=recipient_address] .fa-spin').removeClass('hide');

        $.post("{{ route('account.shipments.get.department') }}", {
            id: $(this).val()
        }, function(data) {

            $('[name=recipient_name]').val(data.name);
            $('[name=recipient_address]').val(data.address);
            $('[name=recipient_zip_code]').val(data.zip_code);
            $('[name=recipient_city]').val(data.city);
            $('[name=recipient_phone]').val(data.phone);
            $('[name=recipient_country]').val(data.country).trigger("change");

        }).fail(function() {
            $box.append('<p class="text-red m-b-0 m-t-5 has-error"><i class="fas fa-exclamation-circle"></i> Ocorreu um erro ao obter os dados do departamento.</p>');
        }).always(function() {
            $('label[for=recipient_address] i').addClass('hide');
        })
    });

    /**
     * Change service
     */
    $(document).on('change', '.modal-xl [name=services]', function() {

        var $this = $(this).find(':selected');
        if ($this.data('unity') == 'km') {
            $('.input-km').show();
        } else {
            $('.input-km').hide();
        }

        validateTotalVolumes();
    })

    /**
     * Get zone and agency of recipient from zip code
     */
    $('[name=recipient_zip_code]').on('change', function() {
        var zipCode = $(this).val();

        $('label[for=recipient_address] .fa-spin').removeClass('hide');
        $('[name=recipient_city]').val('');
        $('[name=recipient_city]').autocomplete('dispose');

        $.post("{{ route('account.shipments.get.agency') }}", {
            zipCode: zipCode
        }, function(data) {
            $('[name=recipient_country]').val(data.zone).trigger("change");
            if (data.agency_id) {
                $('[name=recipient_agency_id]').val(data.agency_id).trigger("change");
            } else {
                $('[name=recipient_agency_id]').val($('#modal-remote-xl select[name=agency_id]').val()).trigger("change");
            }

            if (data.cities.length > 1) {
                $('[name=recipient_city]').autocomplete({
                    lookup: data.cities,
                    minChars: 0
                });
            } else if (data.cities.length == 1) {
                $('[name=recipient_city]').val(data.cities[0]['value'])
            }

        }).fail(function() {
            $box.append('<p class="text-red m-b-0 m-t-5 has-error"><i class="fas fa-exclamation-circle"></i> Ocorreu um erro ao obter a agência correspondente.</p>');
        }).always(function() {
            $('label[for=recipient_address] i').addClass('hide');
            $('[name=recipient_city]').focus();
        })
    });

    /**
     * GET SENDER AGENCY FROM ZIP CODE
     * ajax method
     */
    $('[name=sender_zip_code]').on('change', function() {
        var $this = $(this);
        var zipCode = $this.val();
        var country = $('[name=sender_country]').val();

        $('label[for=sender_address] .fa-spin').removeClass('hide');
        $('[name=sender_city]').val('');
        $('[name=sender_city]').autocomplete('dispose')

        $.post(ROUTE_GET_AGENCY, {
            zipCode: zipCode
        }, function(data) {
            data.zone = data.zone == '' ? 'pt' : data.zone;
            $('[name=sender_country]').val(data.zone).trigger("change.select2");

            if (data.agency_id) {
                $('[name=sender_agency_id]').val(data.agency_id).trigger("change.select2");
            } else {
                $('[name=sender_agency_id]').val($('#modal-remote-xl select[name=agency_id]').val()).trigger("change.select2");
            }

            if (data.cities.length > 1) {
                $('[name=sender_city]').autocomplete({
                    lookup: data.cities,
                    minChars: 0,
                    onSelect: function(suggestion) {
                        $('[name="sender_city"]').val(suggestion.data).trigger('change');
                        $('[name="sender_country"]').val(suggestion.country).trigger('change.select2');

                        var zipCode = $('[name=sender_zip_code]').val();
                        $('[name="sender_city"]').closest('.form-group').removeClass('has-error');
                        if (!ZipCode.validate($('[name=sender_country]').val(), zipCode)) {
                            $('.modal [name=sender_zip_code]').closest('.form-group').addClass('has-error');
                        }
                    },
                });
            } else if (data.cities.length == 1) {
                $('[name=sender_city]').val(data.cities[0]['value']);
            }

            if ($('[name=sender_country]').val() != '') {
                $('.btn-refresh-prices').trigger('click');
            }

        }).fail(function() {
            Growl.error('Ocorreu um erro ao obter a agência correspondente.')
        }).always(function() {
            $('label[for=sender_address] i').addClass('hide');
            $('[name=sender_city]').focus();

            $this.closest('.form-group').removeClass('has-error');
            if (!ZipCode.validate($('[name=sender_country]').val(), zipCode)) {
                $('.modal [name=sender_zip_code]').closest('.form-group').addClass('has-error');
            }
        })
    });

    $('[name=sender_country]').on('change', function() {
        var zipCode = $('[name=sender_zip_code]').val();
        var country = $(this).val();

        $('[name=sender_zip_code]').closest('.form-group').removeClass('has-error');
        if (!ZipCode.validate(country, zipCode)) {
            $('[name=sender_zip_code]').closest('.form-group').addClass('has-error');
        }
    })

    $('[name=recipient_country]').on('change', function() {
        var zipCode = $('[name=recipient_zip_code]').val();
        var country = $(this).val();

        $('[name=recipient_zip_code]').closest('.form-group').removeClass('has-error');
        if (!ZipCode.validate(country, zipCode)) {
            $('[name=recipient_zip_code]').closest('.form-group').addClass('has-error');
        }
    })

    /**
     * DIMENSIONS
     */
    //show dimensions modal
    $('[data-target="#modal-shipment-dimensions"]').on('click', function() {
        $('#modal-shipment-dimensions').addClass('in').show();

        var hash = $('.vol:visible').data('hash'); //get current volume field
        hash = typeof hash == 'undefined' ? 'master' : hash;
        $('table.shipment-dimensions tbody tr').hide(); //hide all rows
        $('table.shipment-dimensions tbody tr[data-hash="' + hash + '"]').show() //show only current rows
    })

    //duplica dimensões
    $(document).on('click', '#modal-shipment-dimensions .copy-dimensions', function() { //show
        var $tr = $(this).closest('tr');
        var $nextTr = $tr.next('tr');

        $nextTr.find('td').each(function(item) {
            lastTrVal = $tr.find('td:eq(' + item + ')').find('input, select').val();
            $(this).find('input, select').val(lastTrVal).trigger('change')
        })
    })

    //pre-preenche dimensoes
    $(document).on('change', '#modal-shipment-dimensions [name="box_type[]"]', function(e) {

        var globalType = '';
        var lastType = '';
        $('#modal-shipment-dimensions').find('[name="box_type[]"]').each(function() {
            var type = $(this).val();

            if (lastType == '') {
                lastType = type;
            }

            if (type != lastType) {
                globalType = 'multiple'
            }
        })

        if (globalType != 'multiple') {
            globalType = lastType;
        }

        $('[name="packaging_type"]').val(globalType)
        var $tr = $(this).closest('tr');

        var width = $(this).find('option:selected').data('width');
        var lenght = $(this).find('option:selected').data('length');
        var height = $(this).find('option:selected').data('height');
        var weight = $(this).find('option:selected').data('weight');
        var description = $(this).find('option:selected').data('description');

        if (width != '' && typeof width != 'undefined') {
            $tr.find('[name="width[]"]').val(width).trigger('change');
        }

        if (lenght != '' && typeof lenght != 'undefined') {
            $tr.find('[name="length[]"]').val(lenght)
        }

        if (height != '' && typeof height != 'undefined') {
            $tr.find('[name="height[]"]').val(height)
        }

        if (weight != '' && typeof weight != 'undefined') {
            $tr.find('[name="box_weight[]"]').val(weight)
        }

        if (description != '' && typeof description != 'undefined') {
            $tr.find('[name="box_description[]"]').val(description)
        }

        $tr.find('[name="width[]"]').trigger('change');

        $tr.find('.bxtp').removeClass('has-error');
    })

    //editado peso linha
    $(document).on('change', '#modal-shipment-dimensions [name="box_weight[]"]', function(e) {
        calcDimsTotals();
    })

    //calcula M3 de cada linha
    $(document).on('change', '#modal-shipment-dimensions [name="qty[]"], #modal-shipment-dimensions [name="width[]"], #modal-shipment-dimensions [name="height[]"], #modal-shipment-dimensions [name="length[]"]', function() {

        var $tr = $(this).closest('tr');
        var width = $tr.find('[name="width[]"]').val();
        var height = $tr.find('[name="height[]"]').val();
        var length = $tr.find('[name="length[]"]').val();
        var volume = calcVolume(width, height, length, VOLUMES_MESURE_UNITY);

        $tr.find('[name="fator_m3_row[]"]').val(volume);

        calcDimsTotals();
    })

    //hide dimensions modal
    $('.confirm-dimensions').on('click', function(){

        var dimLines = validateDimensionLines();

        var vols = parseInt($('.modal [name="volumes"]').val());
        if (isNaN(vols)) {
            vols = 1;
            $('.modal [name="volumes"]').val(1);
        }
        $('.modal [name="volumes"]').trigger('change');

        if ($('#modal-shipment-dimensions .has-error').length) {
            Growl.error('<i class="fas fa-exclamation-triangle"></i> Corrija os campos a vermelho antes de confirmar.')
        } else if (dimLines > vols) {
            $('#modal-confirm-vols').addClass('in').show().find('.cvol').html(dimLines);
        } else {
            $('#modal-shipment-dimensions').removeClass('in').hide();

            var dims = calcDimsTotals()
            $('.modal-xl [name="fator_m3"]').val(dims.m3);
            $('.modal-xl [name="volumes"]').val(dims.volumes);

            if(dims.weight > 0.00) {
                $('.modal-xl [name="weight"]').val(dims.weight);
            }

            $('.modal-xl [name="weight"]').trigger('change');
        }

    })

    $(document).on('change', '[name="width[]"], [name="height[]"], [name="length[]"]', function() {
        var $tr = $(this).closest('tr');

        var width = $tr.find('[name="width[]"]').val();
        var height = $tr.find('[name="height[]"]').val();
        var length = $tr.find('[name="length[]"]').val();

        $tr.find('[name="fator_m3_row[]"]').val(calcVolume(width, height, length));
    })

    //adiciona linha
    $('.modal .btn-new-dim-row').on('click', function(e) {
        e.preventDefault()

        var hash = $(this).data('hash');
        hash = typeof hash == 'undefined' ? 'master' : hash;
        var $tr = $('table.shipment-dimensions tbody tr:first');

        clonedRow = $tr.clone();
        clonedRow.attr('data-hash', hash);
        clonedRow.find('input').val('');
        clonedRow.find('input[name="dim_src[]"]').val(hash == 'master' ? '' : hash);
        clonedRow.find('input[name="qty[]"]').val('1');
        clonedRow.find('span.select2').remove()
        clonedRow.find('.select2').select2(Init.select2());
        clonedRow.find('.sku-feedback, .m3lbl').hide();
        $('table.shipment-dimensions tbody').append(clonedRow);

        $('table.shipment-dimensions tbody tr').hide(); //hide all rows
        $('table.shipment-dimensions tbody tr[data-hash="' + hash + '"]').show() //show only current rows
    })

    //apaga linha
    $(document).on('click', '#modal-shipment-dimensions .btn-del-dim-row', function() {
        if ($('[name="qty[]"]').length > 1) {
            $(this).closest('tr').remove();
            calcDimsTotals();
        } else {
            Growl.error('Não pode remover esta linha.')
        }
    })

    $('#modal-confirm-vols [data-answer]').on('click', function() {
        if ($(this).data('answer') == '1') {
            var dims = calcDimsTotals();
            $('.modal-xl [name="fator_m3"]').val(dims.m3);
            $('.modal-xl [name="volumes"]').val(dims.volumes);

            if (dims.weight > 0.00) {
                $('.modal-xl [name="weight"]').val(dims.weight)
            }

            $('.modal-xl [name="weight"]').trigger('change');
        }
        $(this).closest('.modal').removeClass('in').hide();
        $('#modal-shipment-dimensions').removeClass('in').hide();

        if (APP_SOURCE == 'hunterex') {
            var val;
            var fatorM3 = 0;
            $('#modal-shipment-dimensions [name="fator_m3_row[]"]').each(function() {
                val = $(this).val() == "" ? 0 : $(this).val();
                val = parseFloat(val);
                qty = parseInt($(this).closest('tr').find('[name="qty[]"]').val());
                fatorM3 += (val * qty);
            })

            var weight = 0;
            $('#modal-shipment-dimensions [name="box_weight[]"]').each(function() {
                val = $(this).val() == "" ? 0 : $(this).val();
                val = parseFloat(val);
                qty = parseInt($(this).closest('tr').find('[name="qty[]"]').val());
                weight += (val * qty);
            })


            if (weight != 0) {
                weight = weight.toFixed(2);
                $('[name="weight"]').val(weight);
            }

            $('.modal [name="fator_m3"]').val(fatorM3);
            $('.fator_m3').html(fatorM3).closest('small').show();
        }
    });

    $('.modal [name="weight"]').on('change', function() {
        validateTotalVolumes()
    });

    $('.modal [name="volumes"]').on('change', function() {
        var volumes = $(this).val();

        $('[name="fator_m3"], [name="volumetric_weight"]').val('');
        $('.helper-volumetric-weight').hide().prev().removeClass('col-sm-5').addClass('col-sm-8');
        $('.fator_m3').closest('small').hide();
        $('.helper-empty-service').hide();

        $tr = $('table.shipment-dimensions tbody tr:first');
        rowCount = $('table.shipment-dimensions tbody tr').length;

        validateTotalVolumes()
    })

    function calcVolume(width, height, length) {
        var width = width == "" ? 0 : width;
        var length = length == "" ? 0 : length;
        var height = height == "" ? 0 : height;
        return (parseFloat(width) * parseFloat(height) * parseFloat(length)) / 1000000;
    }

    function validateTotalVolumes() {

        var maxValue = $('.modal-xl [name=services]').find(':selected').data('max');
        var maxWeight = parseFloat($('.modal-xl [name=services]').find(':selected').data('max-weight'));
        var volumes = $('.modal-xl [name=volumes]').val();
        var weight = parseFloat($('.modal-xl [name=weight]').val());

        if (volumes > maxValue) {
            $('.helper-max-volumes').show();
            $('.modal-xl [name=volumes]').css('border-color', 'red');
            $('button[type=submit]').prop('disabled', true);
            $('.lbl-total-vol').html(maxValue);
        } else {
            $('.helper-max-volumes').hide();
            $('.modal-xl [name=volumes]').css('border-color', '#dddddd');
            $('button[type=submit]').prop('disabled', false);
        }

        if (weight > maxWeight) {
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

    function validateDimensionLines() {

        var totalQty = 0;
        $('.shipment-dimensions tbody tr').each(function() {
            var $tr = $(this);
            var qty = parseInt($tr.find('[name="qty[]"]').val());
            var type = $tr.find('[name="box_type[]"]').val();
            var desc = $tr.find('[name="box_description[]"]').val();
            var m3 = $tr.find('[name="fator_m3_row[]"]').val();
            var weight = $tr.find('[name="box_weight[]"]').val();

            if (type == '' && (desc != '' || m3 != '' || weight != '')) {
                $tr.find('.bxtp').addClass('has-error');
                return false;
            } else if (type != '' && (desc != '' || m3 != '' || weight != '')) {
                totalQty += qty
            }
        })
        return totalQty
    }

    function calcDimsTotals() {

        var val;
        var totalM3 = 0;
        var totalQty = 0;
        var totalWeight = 0;

        $('#modal-shipment-dimensions [name="fator_m3_row[]"]').each(function() {
            val = $(this).val() == "" ? 0 : $(this).val();
            val = parseFloat(val);
            qty = parseInt($(this).closest('tr').find('[name="qty[]"]').val());
            totalM3 += (val * qty);
        })

        $('#modal-shipment-dimensions [name="box_weight[]"]').each(function() {
            var $tr = $(this).closest('tr');

            var qty = parseInt($tr.find('[name="qty[]"]').val());
            var type = $tr.find('[name="box_type[]"]').val();
            var desc = $tr.find('[name="box_type[]"]').val();
            var m3 = $tr.find('[name="fator_m3_row[]"]').val();

            var weight = $(this).val() == "" ? 0 : $(this).val();
            weight = parseFloat(weight);
            totalWeight += (weight * qty);

            if (type != '' && (weight != '' || desc != '' || m3 != '')) {
                totalQty += qty; //so soma os totais para linhas preenchidas
            }
        })

        var totalQty = 0;
        $('.shipment-dimensions tbody tr').each(function() {
            var $tr = $(this);
            var qty = parseInt($tr.find('[name="qty[]"]').val());
            var type = $tr.find('[name="box_type[]"]').val();
            var desc = $tr.find('[name="box_description[]"]').val();
            var m3 = $tr.find('[name="fator_m3_row[]"]').val();
            var weight = $tr.find('[name="box_weight[]"]').val();

            if (type != '' && (desc != '' || m3 != '' || weight != '')) {
                totalQty += qty
            }
        })

        if (totalWeight != 0) {
            totalWeight = totalWeight.toFixed(2);
        }

        totalQty = totalQty == 0 ? 1 : totalQty;

        $('.dims-ttl-vols').html(totalQty);
        $('.dims-ttl-weight').html(totalWeight);
        $('.dims-ttl-m3').html(totalM3.toFixed(3));

        return {
            'volumes': totalQty,
            'weight': totalWeight,
            'm3': totalM3
        }
    }

    $('.form-shipment').on('submit', function(e) {
        e.preventDefault();

        if ($(document).find('.has-error').length) {
            Growl.error("<i class='fas fa-exclamation-circle'></i> Corrija os campos a vermelho antes de gravar.");
        } else {
            var $form = $(this);
            var $button = $('button[type=submit],.btn-submit');

            $button.button('loading');
            $.post($form.attr('action'), $form.serialize(), function(data) {
                if (data.result && !data.syncError) {
                    if (typeof oTable !== "undefined") {
                        oTable.draw(false); //update datatable without change pagination
                    }
                    Growl.success(data.feedback);
                    if (data.printGuide || data.printLabel) {

                        if (data.printGuide) {
                            if (window.open(data.printGuide, '_blank')) {
                                $('#modal-remote-xl').modal('hide');
                            } else {
                                $('#modal-remote-xl').find('.modal-xl').removeClass('modal-xl').find('.modal-content').html(data.html);
                            }
                        }

                        if (data.printLabel) {
                            if (window.open(data.printLabel, '_blank')) {
                                $('#modal-remote-xl').modal('hide');
                            } else {
                                $('#modal-remote-xl').find('.modal-xl').removeClass('modal-xl').find('.modal-content').html(data.html);
                            }
                        }

                    } else {
                        $('#modal-remote-xl').modal('hide');
                    }

                } else if (data.syncError) {
                    $('#modal-confirm-sync-error').find('.error-msg').html(data.feedback)
                    $('#modal-confirm-sync-error').find('.error-provider').html($('[name="provider_id"] option:selected').text())
                    $('#modal-confirm-sync-error').addClass('in').show();

                    $('#modal-confirm-sync-error .btn-confirm-no').on('click', function(e) {
                        $('#modal-confirm-sync-error').removeClass('in').hide();
                    })

                    $('#modal-confirm-sync-error .btn-confirm-yes').on('click', function() {
                        if (typeof oTable !== "undefined") {
                            oTable.draw(false); //update datatable without change pagination
                        }
                        Growl.success('Envio gravado com sucesso.');
                        $('#modal-remote-xl').modal('hide');
                    })

                } else {
                    Growl.error(data.feedback)
                }

                if (data.debug) {
                    window.open(data.debug, '_blank')
                }

            }).fail(function() {
                Growl.error500();
            }).always(function() {
                $button.button('reset');
            })
        }


        $('#modal-remote-xl').on('hidden.bs.modal', function() {
            $('.search-sender').autocomplete('dispose')
            $('.search-recipient').autocomplete('dispose')
            $('[name="sender_city"]').autocomplete('dispose')
            $('[name="recipient_city"]').autocomplete('dispose')
        })
    });

    @if (count($services) == 2)
        $(document).ready(function() {
            $('[name="services"] option:last-child').attr('selected', true).trigger('change');
        })
    @elseif (count($services) == 1)
        $(document).ready(function() {
            $('[name="services"]').trigger('change');
        })
    @endif
</script>
