<?php
$hash = str_random(5);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">{{ trans('account/global.word.close') }}</span>
    </button>
    <h4 class="modal-title"><i class="fas fa-calculator"></i> {{ trans('account/shipments.budget.title') }}</h4>
</div>
<div class="modal-body modal-budget modal-{{ $hash }}">
    <div class="row">
        <div class="col-sm-8">
            {{ Form::open(['route' => 'account.budgeter.preview-prices.calc', 'class' => 'form-budgeter']) }}
            <div class="row row-10">
                <div class="col-xs-12 col-sm-5">
                    <h5 class="budgeter-group-title m-t-10">
                        <i class="fas fa-truck"></i> {{ trans('account/global.word.service') }}
                    </h5>
                    <div class="row row-5">
                        <div class="col-sm-12 col-service">
                            <div class="form-group is-required m-b-5">
                                {{ Form::label('service_id', trans('account/global.word.service'), ['class' => 'control-label']) }}<br/>
                                {!! Form::selectWithData('service_id', $services, null, ['class' => 'form-control select2 trigger-price', 'required'])!!}
                            </div>
                        </div>
                        <div class="col-sm-5 col-adicional-field" style="display: none">
                            <div class="form-group m-0 is-required kms" style="display: none">
                                {{ Form::label('kms', 'Distância', ['class' => 'control-label']) }}
                                <div class="input-group input-group-money">
                                    {{ Form::text('kms', null, ['class' => 'form-control decimal trigger-price']) }}
                                    <span class="input-group-addon">Km</span>
                                </div>
                            </div>
                            <div class="form-group m-0 is-required ldm" style="display: none">
                                {{ Form::label('ldm', 'LDM', ['class' => 'control-label']) }}
                                <div class="input-group input-group-money">
                                    {{ Form::text('ldm', null, ['class' => 'form-control decimal trigger-price']) }}
                                    <span class="input-group-addon">Mt</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12" style="margin-left: -2px; padding-right: 0">
                            @if(config('app.source') == 'baltrans')
                            <div class="row row-5">
                                <div class="col-sm-6 p-r-5">
                                    {{ Form::label('volumes', 'Bultos') }}
                                    {{ Form::text('volumes', 1, ['class' => 'form-control datepicker trigger-price', 'style' => 'border-radius: 2px']) }}
                                </div>
                                <div class="col-sm-6 p-l-5">
                                    {{ Form::label('weight', 'Peso') }}
                                    {{ Form::text('weight', null, ['class' => 'form-control select2 trigger-price', 'required']) }}
                                </div>
                            </div>
                            @else
                            {{ Form::label('date', 'Data/Hora recolha') }}
                            <div class="row row-5">
                                <div class="col-sm-7 p-r-5">
                                    <div class="input-group input-group-money">
                                        {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker trigger-price', 'style' => 'border-radius: 2px']) }}
                                        <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-5 p-l-5">
                                    {{ Form::select('start_hour', [''=> '--:--'] + listHours(5), null, ['class' => 'form-control select2 trigger-price', 'required']) }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-7">
                    <h5 class="budgeter-group-title">
                        <span class="km-label" style="display:none">0 km</span>
                        <i class="fas fa-map-marker-alt"></i> Origem e Destino
                    </h5>
                    <div class="row row-5">
                        <div class="col-xs-8 col-sm-7 col-md-8">
                            <div class="form-group m-b-5">
                                {{ Form::label('sender_country', 'País de Recolha') }}
                                {{ Form::select('sender_country', ['' => ''] + trans('country'), Setting::get('app_country'), ['class' => 'form-control select2 trigger-price']) }}
                            </div>
                        </div>
                        <div class="col-xs-4 col-sm-5 col-md-4">
                            <div class="form-group m-b-5">
                                {{ Form::label('sender_zip_code', 'Cod. Postal') }}
                                {{ Form::text('sender_zip_code', null, ['class' => 'form-control trigger-price', 'style' => 'margin-left: -1px;']) }}
                            </div>
                        </div>
                    </div>
                    <div class="row row-5">
                        <div class="col-xs-8 col-sm-7 col-md-8">
                            <div class="form-group">
                                {{ Form::label('recipient_country', 'País de Destino') }}
                                {{ Form::select('recipient_country', ['' => ''] + trans('country'), Setting::get('app_country'), ['class' => 'form-control select2 trigger-price']) }}
                            </div>
                        </div>
                        <div class="col-xs-4 col-sm-5 col-md-4">
                            <div class="form-group">
                                {{ Form::label('recipient_zip_code', 'Cod Postal') }}
                                {{ Form::text('recipient_zip_code', null, ['class' => 'form-control trigger-price', 'style' => 'margin-left: -1px;']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="packages-box">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="pull-right">
                            @if(config('app.source') != 'baltrans')
                            <ul class="list-inline packages-counter">
                                <li>
                                    <i class="fas fa-box"></i> <span class="count-packages">1 vol/pal</span>
                                </li>
                                <li>
                                    <i class="fas fa-weight"></i> <span class="count-kg">0.00</span>Kg
                                </li>
                                <li>
                                    <i class="fas fa-cube"></i> <span class="count-m3">0.000</span>m<sup>3</sup>
                                </li>
                                {{--<li>
                                    <i class="fas fa-weight-hanging"></i> <span class="count-volumetric-kg">0.00</span>Kg<sup>3</sup>
                                </li>--}}
                            </ul>
                            @endif
                        </div>
                        <h5 class="budgeter-group-title pull-left">
                            <i class="fas fa-boxes"></i> Mercadoria
                        </h5>
                        @if(config('app.source') != 'baltrans')
                        <small>
                            <a href="#" class="m-l-20 btn-add-package">
                                <i class="fas fa-plus"></i> {{ trans('account/global.word.add') }}
                            </a>
                        </small>
                        @endif
                        {{--<a href="#" class="m-l-20 btn-copy-package">
                            <small><i class="fas fa-copy"></i></small> {{ trans('account/global.word.copy-volume') }}
                        </a>--}}
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="packages">
                    <div class="row row-5 row-bulto">
                        <div class="col-sm-4">
                            <div class="form-group m-b-2">
                                {{ Form::label('pack_type[]', trans('account/global.word.packtype')) }}
                                {{ Form::select('pack_type[]', $packTypes, null , ['class' => 'form-control select2 trigger-price']) }}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group m-b-2">
                                {{ Form::label('pack_weight[]', trans('account/global.word.weight')) }}
                                <div class="input-group input-group-money">
                                    {{ Form::text('pack_weight[]', null, ['class' => 'form-control box-weight decimal']) }}
                                    <div class="input-group-addon">Kg</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <label>
                                {{ trans('account/global.word.dimensions') }} (CxLxA)
                            </label>
                            <div class="row row-0">
                                <div class="col-sm-4">
                                    <div class="form-group m-b-2">
                                        <div class="input-group input-group-money">
                                            {{ Form::text('pack_width[]', null, ['class' => 'form-control box-width decimal']) }}
                                            <div class="input-group-addon">✕</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group m-b-2">
                                        <div class="input-group input-group-money">
                                            {{ Form::text('pack_length[]', null, ['class' => 'form-control box-length decimal', 'style' => 'margin-left: -1px']) }}
                                            <div class="input-group-addon">✕</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group m-b-2">
                                        <div class="input-group input-group-money">
                                            {{ Form::text('pack_height[]', null, ['class' => 'form-control box-height decimal', 'style' => 'margin-left: -2px; border-radius: 0 2px 2px;']) }}
                                            <div class="input-group-addon">cm</div>
                                        </div>
                                    </div>
                                    <a href="#" class="text-red btn-remove-package" style="display:none">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(!$complementarServices->isEmpty())
            <div class="row row-5 m-t-15">
                <div class="col-sm-12">
                    <h5 class="budgeter-group-title">
                        <i class="fas fa-plus-circle"></i> Serviços adicionais
                    </h5>
                </div>
                @if(Setting::get('customers_show_charge_price') && Setting::get('shipments_show_charge_price'))
                    <div class="col-sm-3">
                        <div class="form-group m-b-0">
                            <div class="checkbox">
                                <label style="padding-left: 0">
                                    {{ Form::checkbox('charge_price', '1', null, ['class' => 'trigger-price']) }}
                                    Cobrança
                                </label>
                            </div>
                        </div>
                    </div>
                @endif
                @if(Setting::get('shipments_show_assembly'))
                <div class="col-sm-3">
                    <div class="form-group m-b-0">
                        <div class="checkbox">
                            <label style="padding-left: 0">
                                {{ Form::checkbox('has_assembly', '1', null, ['class' => 'trigger-price']) }}
                                Serviço Montagem
                            </label>
                        </div>
                    </div>
                </div>
                @endif
                @foreach($complementarServices as $service)
                <div class="col-sm-3">
                    <div class="form-group m-b-0">
                        @if($service->form_type_account != 'checkbox')
                            <label style="margin-top: -11px; margin-bottom: 2px; display: block;">
                                {{ $service->short_name ? $service->short_name : $service->name }}
                            </label>
                        @endif
                        @if($service->form_type_account == 'select-io')
                            {{ Form::select('optional_fields['.$service->id.']', ['' => 'Não', 1 => 'Sim'], null, ['class' => 'form-control select2', 'data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'qty']) }}
                        @elseif($service->form_type_account == 'select-time')
                            <?php
                            $listHours = listNumeric(Setting::get('shipments_wainting_time_fractions') ?: 10, Setting::get('shipments_wainting_min_time') ?: 10, Setting::get('shipments_wainting_min_time') + 520, ' min');
                            ?>
                            {{ Form::select('optional_fields['.$service->id.']', ['0' => ''] + $listHours, null, ['class' => 'form-control select2', 'data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'qty']) }}
                        @elseif($service->form_type_account == 'input')
                            <div class="input-group input-group-money">
                                {{ Form::text('optional_fields['.$service->id.']',null, ['class' => 'form-control input-sm decimal', 'data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'qty']) }}
                                <div class="input-group-addon">{{ $service->addon_text ? $service->addon_text : 'Qtd' }}</div>
                            </div>
                        @elseif($service->form_type_account == 'money')
                            <div class="input-group input-group-money">
                                {{ Form::text('optional_fields['.$service->id.']',null, ['class' => 'form-control input-sm decimal', 'data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'money']) }}
                                <div class="input-group-addon">{{ $service->addon_text ? $service->addon_text : $appCurrency }}</div>
                            </div>
                        @elseif($service->form_type_account == 'percent')
                            <div class="input-group input-group-money">
                                {{ Form::text('optional_fields['.$service->id.']',null, ['class' => 'form-control input-sm decimal', 'data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'percent']) }}
                                <div class="input-group-addon">{{ $service->addon_text ? $service->addon_text : '%' }}</div>
                            </div>
                        @else
                            <div class="checkbox">
                                <label style="padding-left: 0">
                                {{ Form::checkbox('optional_fields['.$service->id.']', $service->value ? $service->value : 1,null, ['data-id' => $service->id, 'data-tag' => $service->type, 'data-input' => 'qty']) }}
                                {{ $service->short_name ? $service->short_name : $service->name }}
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            {{ Form::hidden('weight', 1) }}
            {{ Form::hidden('volumes', 1) }}
            {{ Form::hidden('fator_m3') }}
            {{ Form::hidden('vat_enabled', 0) }}
            {{ Form::hidden('sender_city') }}
            {{ Form::hidden('recipient_city') }}
            {{ Form::close() }}
        </div>
        <div class="col-sm-4 results-list">
            @include('account.budgeter.modal.partials.price_preview')
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('account/global.word.close') }}</button>
</div>

<script>
    $('.select2').select2(Init.select2());
    $('.datepicker').datepicker(Init.datepicker());

    //change field
    $(document).on('change', '.modal-{{ $hash }} .trigger-price, .modal-{{ $hash }} [name*=optional_fields]', function(e){
        e.preventDefault();

        var targetName   = $(this).attr('name');
        var serviceUnity = $('.modal-budget [name="service_id"] option:selected').data('unity');

        if(serviceUnity == 'km' && (
            targetName == 'service_id' ||
            targetName == 'sender_country' ||
            targetName == 'sender_zip_code' ||
            targetName == 'recipient_country' ||
            targetName == 'recipient_zip_code')) {
            callKms()
        }

        updatePackagesTotals();
    })


    //add new package
    $(document).on('click', '.modal-{{ $hash }} .btn-add-package', function(e){
        e.preventDefault();
        addPackage();
        resetPackage();
        updatePackagesTotals();
    })

    //copy package
    $(document).on('click', '.modal-{{ $hash }} .btn-copy-package', function(e){
        e.preventDefault();
        addPackage();
        updatePackagesTotals();
    })

    //remove package
    $(document).on('click', '.modal-{{ $hash }} .btn-remove-package', function(e){
        e.preventDefault();

        if($('.row-bulto').length == 1){
            Growl.error('El envío debe tener al menos 1 bulto.')
        } else {
            $(this).closest('.row-bulto').remove();
            updatePackagesTotals();
        }
    })

    //change package weight or dimensions
    $(document).on('change', '.modal-budget .row-bulto input', function(e){
        e.preventDefault();
        updatePackagesTotals();
    })

    function addPackage() {
        var packageRow = $('.row-bulto:first-child').clone();
        packageRow.find('.btn-remove-package').show();
        packageRow.find('label, .spacer-20, .select2-container').remove();
        packageRow.find('.select2').select2(Init.select2())

        $('.packages').append(packageRow);
    }

    function resetPackage() {
        $('.modal-budget .row-bulto:last-child').find('input, select').val('').trigger('change.select2');
    }

    function updatePackagesTotals() {

        var totalKg  = 0;
        var totalM3  = 0;
        var totalKg3 = 0;

        $(document).find('.modal-budget .row-bulto').each(function(){

            rowKg = parseFloat($(this).find('.box-weight').val())
            rowKg = isNaN(rowKg) ? 0 : rowKg;

            width  = parseFloat($(this).find('.box-width').val());
            width  = isNaN(width) ? 0 : width;
            length = parseFloat($(this).find('.box-length').val());
            length = isNaN(length) ? 0 : length;
            height = parseFloat($(this).find('.box-height').val());
            height = isNaN(height) ? 0 : height;
            rowM3  = (width * length * height) / 1000000;

            rowKg3 = rowM3 * 167;

            totalKg+= rowKg;
            totalM3+= rowM3;
            totalKg3+= rowKg3

        })

        $('.modal-budget .count-packages').html($('.modal-budget .row-bulto').length);
        $('.modal-budget .count-kg').html(round(totalKg).toFixed(2));
        $('.modal-budget .count-m3').html(totalM3.toFixed(3));
        $('.modal-budget .count-volumetric-kg').html(round(totalKg3));
        $('.modal-budget .form-budgeter [name="fator_m3"]').val(totalM3);

        callBudgeter();
    }

    function showLoadingOverlay() {
        var html = '<div class="text-center text-muted m-t-80"><i class="fas fa-spin fa-circle-notch"></i> Aguarde...</div>'
        $('.modal-budget .budget-details').html(html);
    }

    function showLoadingError() {
        html = '<div class="text-red text-center m-t-80"><i class="fas fa-exclamation-triangle"></i> Erro ao obter preços.</div>';
        $('.modal-budget .budget-details').html(html);
    }

    function callKms() {

        var triangulation = 0
        var returnBack = 1;

        var agencyZp        = $.trim($('.modal-budget [name="agency_zp"]').val());
        var agencyCity      = $.trim($('.modal-budget [name="agency_city"]').val());

        var originZp        = $.trim($('.modal-budget [name="sender_zip_code"]').val());
        var originCity      = $.trim($('.modal-budget [name="sender_city"]').val());
        var originCountry   = $.trim($('.modal-budget [name="sender_country"]:selected').text())

        var destZp          = $.trim($('.modal-budget [name="recipient_zip_code"]').val());
        var destCity        = $.trim($('.modal-budget [name="recipient_city"]').val());
        var destCountry     = $.trim($('.modal-budget [name="recipient_country"]:selected').text());

        originCountry       = originCountry == '' ? 'Portugal' : originCountry;
        destCountry         = destCountry == '' ? 'Portugal' : destCountry

        var origin          = originZp + ' ' + originCity + ',' + originCountry;
        var destination     = destZp + ' ' + destCity + ',' + destCountry;
        var agency          = agencyZp + ' ' + agencyCity + ',pt';


        if(originCountry == '' || originZp == '' || destCountry == '' || destZp == '') {

            html = '<div class="text-blue text-center m-t-80">' +
                '<i class="fas fa-info-circle"></i><br/>' +
                '<b>Calcular Distância</b><br/>' +
                '<small>Necessário preencher Código Postal e o País.</small>' +
                '</div>';
            $('.modal-budget .budget-details').html(html);

        } else {

            $('.modal-budget .km-label').html('<i class="fas fa-spin fa-circle-notch"></i> km').show();
            showLoadingOverlay();

            var $form  = $('.modal-budget .form-budgeter').closest('form');
            var params = $form.serialize();
            var $icon  = $('.modal-xl .btn-auto-km').find('.fas');
            $icon.addClass('fa-spin');

            $('.modal [name="waint_ajax"]').val(1);
            $('.modal-budget [name="kms"]').val('');

            $.post("{{ route('account.budgeter.get.distance') }}", params, function (data) {
                if (data.result) {
                    distance = parseFloat(data.distance_value);
                    distance = distance.toFixed(2);

                    $('.modal-budget [name="kms"]').val(distance).trigger('change')
                    $('.modal-budget .km-label').html(distance+'km').show();

                } else {
                    $('.modal-budget .km-label').html('<i class="fas fa-exclamation-triangle"></i> km');
                    showLoadingError();
                }
            }).always(function(){
                $('.modal [name="waint_ajax"]').val(0);
            })
        }
    }

    function showServiceAdicionalField() {
        $('.modal-budget [name="service_id"]').closest('.col-service').removeClass('col-sm-12').addClass('col-sm-7');
        $('.col-adicional-field').show();
        $('.col-adicional-field .form-group').hide()
    }

    function hideServiceAdicionalField() {
        $('.modal-budget [name="service_id"]').closest('.col-service').removeClass('col-sm-7').addClass('col-sm-12');
        $('.col-adicional-field').hide();
        $('.col-adicional-field .form-group').hide();
    }

    function callBudgeter() {
        var canCalculate = true;
        var waintingAjax = $('.modal [name="waint_ajax"]').val();
        var unity = $('.modal-budget [name="service_id"] option:selected').data('unity');

        if(unity == 'km') {
            showServiceAdicionalField();
            $('.kms').show();
        } else if(unity == 'ldm') {
            showServiceAdicionalField();
            $('.ldm').show();
        } else {
            hideServiceAdicionalField();
        }

        if(unity == 'km' && $('.modal-budget [name="kms"]').val() == '') {
            canCalculate = false;
        } else if($('.modal-budget [name="service_id"]').val() == '' ||
            $('.modal-budget [name="sender_country"]').val() == '' ||
            $('.modal-budget [name="recipient_country"]').val() == ''){
            canCalculate= false;
        }


        if(canCalculate && !waintingAjax) {
            var $form = $('.modal-budget .form-budgeter').closest('form');
            var url = $form.attr('action');
            var params = $form.serialize();

            showLoadingOverlay();

            $.post(url, params, function (data) {
                $('.modal-budget .results-list').html(data.html);
            }).fail(function () {
                showLoadingError();
            })
        }
    }
</script>