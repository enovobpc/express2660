@section('title')
    {{ trans('account/budgeter.title') }} -
@stop

@section('account-content')
    {{ Form::open(['route' => 'account.budgeter.calc', 'class' => 'form-budgeter']) }}
    <div class="row">
        <div class="col-xs-12 col-sm-10">
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <h5 class="budgeter-group-title">
                        <i class="fas fa-map-marker-alt"></i> {{ trans('account/budgeter.form.groups.sender') }}
                    </h5>
                    <div class="row row-5">
                        <div class="col-xs-5 col-sm-5 col-md-5">
                            <div class="form-group">
                                {{ Form::label('sender_country', trans('account/global.word.country')) }}
                                {{ Form::select('sender_country', ['' => ''] + trans('country'), Setting::get('app_country'), ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3">
                            <div class="form-group">
                                {{ Form::label('sender_zip_code', trans('account/global.word.zip_code')) }}
                                {{ Form::text('sender_zip_code', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                            </div>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <div class="form-group">
                                {{ Form::label('sender_city', trans('account/global.word.city')) }}
                                {{ Form::text('sender_city', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <h5 class="budgeter-group-title">
                        <i class="fas fa-crosshairs"></i> {{ trans('account/budgeter.form.groups.recipient') }}
                    </h5>
                    <div class="row row-5">
                        <div class="col-xs-5 col-sm-5 col-md-5">
                            <div class="form-group">
                                {{ Form::label('recipient_country', trans('account/global.word.country')) }}
                                {{ Form::select('recipient_country', ['' => ''] + trans('country'), Setting::get('app_country'), ['class' => 'form-control select2']) }}
                            </div>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3">
                            <div class="form-group">
                                {{ Form::label('recipient_zip_code', trans('account/global.word.zip_code')) }}
                                {{ Form::text('recipient_zip_code', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                            </div>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <div class="form-group">
                                {{ Form::label('recipient_city', trans('account/global.word.city')) }}
                                {{ Form::text('recipient_city', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-2">
            <h5 class="budgeter-group-title">
                <i class="fas fa-calendar-alt"></i> {{ trans('account/budgeter.form.groups.date') }}
            </h5>
            <div class="form-group">
                <small class="pull-right text-blue lbl-pickup-type" style="display:none"><i class="fas fa-info-circle"></i> Recogida Normal</small>
                {{ Form::label('date', trans('account/global.word.date')) }}
                <div class="input-group">
                    {{ Form::text('date', date('Y-m-d'), ['class' => 'form-control datepicker']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-12">
            <h5 class="budgeter-group-title">
                <i class="fas fa-cube"></i> {{ trans('account/budgeter.form.groups.type') }}
            </h5>
        </div>
        <div class="col-sm-4">
            <button type="button" class="btn btn-block btn-default" data-tipology="docs">
                <i class="fas fa-envelope"></i> {{ trans('account/global.word.packtype-doc') }}
            </button>
        </div>
        <div class="col-sm-4">
            <button type="button" class="btn btn-block btn-info" data-tipology="boxes">
                <i class="fas fa-box-open"></i> {{ trans('account/global.word.packtype-box') }}
            </button>
        </div>
        <div class="col-sm-4">
            <button type="button" class="btn btn-block btn-default" data-tipology="pallets">
                <i class="fas fa-pallet"></i> {{ trans('account/global.word.packtype-pallet') }}
            </button>
        </div>
        {{--<div class="col-sm-3">
            <button type="button" class="btn btn-block btn-default" data-tipology="others">
                <i class="fas fa-asterisk"></i> {{ trans('account/global.word.packtype-other') }}
            </button>
        </div>--}}
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="spacer-15"></div>
            <div class="pull-right">
                <ul class="list-inline">
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
            </div>
            <h5 class="budgeter-group-title pull-left">
                <i class="fas fa-boxes"></i> {{ trans('account/budgeter.form.groups.packages') }}
                (<span class="count-packages">1</span>)
            </h5>

            <a href="#" class="m-l-20 btn-add-package">
                <small><i class="fas fa-plus"></i></small> {{ trans('account/global.word.add') }}
            </a>
            <a href="#" class="m-l-20 btn-copy-package">
                <small><i class="fas fa-copy"></i></small> {{ trans('account/global.word.copy-volume') }}
            </a>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="packages">
        <div class="row row-10 row-bulto">
            <div class="col-sm-4">
                <div class="form-group m-b-2">
                    {{ Form::label('pack_type[]', trans('account/global.word.packtype')) }}
                    {{-- {{ Form::select('pack_type[]', $packTypes, null , ['class' => 'form-control select2']) }} --}}
                    <select class="form-control select2" name="pack_type[]">
                        @foreach ($packTypes as $key => $pack)
                            <option data-type="{{ $pack['type'] }}" value="{{ $key }}">{{ $pack['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group m-b-2">
                    {{ Form::label('pack_weight[]', trans('account/global.word.weight')) }}
                    <div class="input-group">
                        {{ Form::text('pack_weight[]', null, ['class' => 'form-control box-weight decimal']) }}
                        <div class="input-group-addon">Kg</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-5" style="width: 44%">
                <div class="row row-0">
                    <div class="col-sm-4">
                        <div class="form-group m-b-2">
                            {{ Form::label('pack_length[]', trans('account/global.word.length')) }}
                            <div class="input-group">
                                {{ Form::text('pack_length[]', null, ['class' => 'form-control box-length decimal']) }}
                                <div class="input-group-addon">cm</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group m-b-2">
                            {{ Form::label('pack_width[]', trans('account/global.word.width')) }}
                            <div class="input-group">
                                {{ Form::text('pack_width[]', null, ['class' => 'form-control box-width decimal']) }}
                                <div class="input-group-addon">cm</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group m-b-2">
                            {{ Form::label('pack_height[]', trans('account/global.word.height')) }}
                            <div class="input-group">
                                {{ Form::text('pack_height[]', null, ['class' => 'form-control box-height decimal']) }}
                                <div class="input-group-addon">cm</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-1" style="width: 6%">
                <div class="spacer-20"></div>
                <a href="#" class="btn btn-default btn-block btn-remove-package" style="display:none">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        </div>
    </div>
    <small class="text-muted">
        <i class="fas fa-info-circle"></i> {{ trans('account/budgeter.form.tips.bultos') }}
    </small>
    {{ Form::hidden('fator_m3') }}
    {{ Form::hidden('shipment_tipology', 'boxes') }}
    {{ Form::hidden('vat_enabled', 0) }}
    {{ Form::hidden('empty_prices', 0) }}
    {{ Form::close() }}
    </div>
    <div class="row row-service-title" style="display: none">
        <div class="col-sm-12">
            <div class="pull-right m-t-15">
                <label style="padding: 7px 10px;">
                    {{ trans('account/budgeter.form.tips.vat') }}
                </label>
                {{ Form::checkbox('btn_vat_enabled', 1, @$vatEnabled, ['class' => 'ios hide']) }}
            </div>
            @if(Auth::check())
            <div class="pull-right m-t-15">
                <label style="padding: 7px 10px;">
                    {{ trans('account/budgeter.form.tips.empty-prices') }}
                </label>
                {{ Form::checkbox('btn_show_empty_prices', 1, null, ['class' => 'ios hid']) }}
            </div>
            @endif
            <h4 class="title-services text-uppercase m-t-20 pull-left">
                <i class="fas fa-truck"></i> {{ trans('account/budgeter.form.groups.services') }}
                <span class="lbl-count-services" style="display: none"></span>
            </h4>
        </div>
    </div>
    <div class="results-list"></div>

    {{ HTML::style('vendor/ios-checkbox/dist/css/iosCheckbox.min.css')}}
    <style>
        .divider {
            border-top: 1px dotted #999;
            width: 100%;
            padding-top: 10px;
        }

        .budgeter-group-title {
            margin-top: 0;
            margin-bottom: 5px
        }

        .input-group-addon {
            background: #fff;
        }

        .form-budgeter .select2-container {
            margin-top: 2px;
            width: 100% !important;
            max-width: 100%;
        }

        .form-budgeter .input-group .form-control{
            border-right: none;
        }

        .form-budgeter .datepicker {
            padding: 6px 10px;
        }

        .btn-info,
        .btn-info:hover,
        .btn-info:active,
        .btn-info:focus,
        .btn-info:focus:active,
        .btn-info:hover:active {
            background-color: #014572;
            border-color: #014572;
        }

        .results-list {
            position: relative;
        }

        .results-list .result-row {
            border: 1px solid #ccc;
            box-shadow: 0 1px 6px #ccc;
            border-radius: 3px;
            margin-bottom: 15px;
            background: #fff;
        }

        .results-list .result-row-left {
            width: 11%;
        }

        .results-list .result-row-center {
            width: 72.3%;
        }

        .results-list p.text-muted {
            color: #bbb;
        }

        .results-list .service-img {
            height: 85px;
            text-align: center;
            vertical-align: middle;
            display: table-cell;
            padding: 10px !important;
            width: 11%;
        }

        .results-list .service-img img {
            max-width: 100%;
            max-height: 100%;
        }

        .service-vehicle {
            text-align: center;
            padding: 8px 10px;
            font-size: 12px;
            text-transform: uppercase;
            color: #24ad04;
        }

        .results-list .service-info {
            height: 85px;
            padding: 15px;
            border-left: 1px solid #eee;
        }

        .results-list .service-info h4 {
            margin: 0 0 5px
        }

        .results-list .service-info p {
            margin: 0;
            font-size: 13px;
            color: #777
        }

        .results-list .service-details {
            padding: 8px 15px;
            border-left: 1px solid #eee;
            border-top: 1px solid #eee;
        }

        .results-list .service-details p {
            margin: 0;
            font-size: 13px
        }

        .results-list .service-price {
            border-left: 1px solid #eee;
            height: 120px;
            padding: 10px;
            position: relative;
        }

        .results-list .service-price h1 {
            margin: 0;
            text-align: right;
            line-height: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        .results-list .service-price .btn-create-shipment {
            position: absolute;
            left: 0;
            bottom: 0;
            right: -1px;
            border-radius: 0 0 3px;
            border: 0;
            height: 35px;
            background: #FF5722 !important;
            border-color: #FF5722 !important;
            text-transform: uppercase;
            font-weight: bold;
            color: #fff;
        }

        .results-list .result-row-details {
            background: #E3EDF9;
            border-top: 2px solid #014572;
            padding: 14px;
            color: #555;
        }

        .results-list .results-loading {
            text-align: center;
            color: #777;
        }

        .row-service-title .icheckbox_minimal-blue {
            display: none !important;
        }

        .row-service-title .ios-checkbox-wrap {
            float: right;
        }

        .row-service-title .ios-ui-select {
            border: 0;
            height: 30px;
            width: 54px;
        }

        .row-service-title .ios-ui-select.checked {
            -webkit-box-shadow: inset 0 0 0 36px #014572;
            box-shadow: inset 0 0 0 36px #014572;
        }

        .row-service-title .ios-ui-select .inner {
            width: 24px;
            height: 24px;
        }

        .result-row.result-row-loading {
            height: 122px;
            animation-duration: 1.25s;
            animation-fill-mode: forwards;
            animation-iteration-count: infinite;
            animation-name: placeHolderShimmer;
            animation-timing-function: linear;
            background: darkgray;
            background: linear-gradient(to left, #ffffff 10%, #e9e9e9 18%, #ffffff 33%);
            background-size: 1000px 240px;
            position: relative;
            border: 1px solid #ddd;
            box-shadow: none;
        }


        @keyframes placeHolderShimmer{
            0%{
                opacity: 1;
                background-position: -500px 0
            }
            100%{
                opacity: 1;
                background-position: 500px 0
            }
        }

        .result-loading-overlay {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            top: 0px;
            background: -moz-linear-gradient(top,  rgba(255,255,255,0) 0%, rgba(245,245,246,1) 100%);
            background: -webkit-linear-gradient(top,  rgba(255,255,255,0) 0%,rgba(245,245,246,1) 100%);
            background: linear-gradient(to bottom,  rgba(255,255,255,0) 0%,rgba(245,245,246,1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00ffffff', endColorstr='#f5f5f6',GradientType=0 );

        }
    </style>
@stop

@section('scripts')
{{ HTML::script('vendor/ios-checkbox/dist/js/iosCheckbox.min.js')}}
<script type="text/javascript">

    $(".ios").iosCheckbox();


    $('.ios-checkbox-wrap').on('click', function() {
        $(this).find('input').trigger('change');
    })

    $(document).on('change', '[name="btn_vat_enabled"]', function(){
        var enabled  = $(this).is(':checked') ? 1 : 0;
        $('[name="vat_enabled"]').val(enabled)

        if(enabled) {
            $(document).find('.price-with-vat').removeClass('hide')
            $(document).find('.price-without-vat').addClass('hide')
        } else {
            $(document).find('.price-with-vat').addClass('hide')
            $(document).find('.price-without-vat').removeClass('hide')
        }
    })

    $(document).on('change', '[name="btn_show_empty_prices"]', function(){
        var enabled  = $(this).is(':checked') ? 1 : 0;
        $('[name="empty_prices"]').val(enabled)
        $('[name="sender_country"]').trigger('change');
    })

    //change sender or recipient
    $(document).on('change', '[name="sender_country"], [name="sender_zip_code"], [name="recipient_country"], [name="recipient_zip_code"], [name="date"]', function(e){
        e.preventDefault();
        updatePackagesTotals();
    })

    //change package selection
    $(document).on('click', '[data-tipology]', function(e){
        e.preventDefault();
        var $obj = $(this);

        $('[data-tipology]').removeClass('btn-info').addClass('btn-default')
        $obj.removeClass('btn-default').addClass('btn-info');
        $('[name="shipment_tipology"]').val($obj.data('tipology'));

        // Filter volumes types
        $('option[data-type]').prop('disabled', true);
        $('option[data-type="'+ $obj.data('tipology') +'"]').prop('disabled', false);
        $('select[name="pack_type[]"]').select2(Init.select2());

        // Select first option
        var firstOptionPackType = $('option[data-type="'+ $obj.data('tipology') +'"]').val();
        $('[name="pack_type[]"]').val(firstOptionPackType).trigger('change');

        callBudgeter();
    })

    //add new package
    $(document).on('click', '.btn-add-package', function(e){
        e.preventDefault();
        addPackage();
        resetPackage();
        updatePackagesTotals();
    })

    //copy package
    $(document).on('click', '.btn-copy-package', function(e){
        e.preventDefault();
        addPackage();
        updatePackagesTotals();
    })

    //remove package
    $(document).on('click', '.btn-remove-package', function(e){
        e.preventDefault();

        if($('.row-bulto').length == 1){
            Growl.error('El envío debe tener al menos 1 bulto.')
        } else {
            $(this).closest('.row-bulto').remove();
            updatePackagesTotals();
        }
    })

    //change package weight or dimensions
    $(document).on('change', '.row-bulto input, .row-bulto select', function(e){
        e.preventDefault();
        updatePackagesTotals();
    })

    //show service details
    $(document).on('click', '.btn-service-details', function(e){
        e.preventDefault();
        var $thisBtn = $(this);
        var $target  = $thisBtn.closest('.result-row').find('.result-row-details');
        $target.slideToggle(function(){

            if($target.is(':visible')) {
                $thisBtn.find('i').removeClass('fa-angle-down').addClass('fa-angle-up')
            } else {
                $thisBtn.find('i').removeClass('fa-angle-up').addClass('fa-angle-down')
            }
        });
    })

    function addPackage() {
        var packageRow = $('.row-bulto:first-child').clone();
        packageRow.find('.btn-remove-package').show();
        packageRow.find('label, .spacer-20, .select2-container').remove();
        packageRow.find('.select2').select2(Init.select2())

        $('.packages').append(packageRow);
    }

    function resetPackage() {
        $('.row-bulto:last-child').find('input, select').val('').trigger('change.select2');
    }

    function updatePackagesTotals() {

        var totalKg  = 0;
        var totalM3  = 0;
        var totalKg3 = 0;

        $(document).find('.row-bulto').each(function(){

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

        $('.count-packages').html($('.row-bulto').length);
        $('.count-kg').html(round(totalKg).toFixed(2));
        $('.count-m3').html(totalM3.toFixed(3));
        $('.count-volumetric-kg').html(round(totalKg3));
        $('.form-budgeter [name="fator_m3"]').val(totalM3);

        callBudgeter();
    }

    function showLoadingOverlay() {
        $('.row-service-title').show();
        $('.lbl-count-services').hide();

        html = '<div class="result-row result-row-loading"></div>';
        html+= '<div class="result-row result-row-loading"></div>';
        html+= '<div class="result-row result-row-loading"></div>';
        html+= '<div class="result-loading-overlay"></div>';
        $('.results-list').html(html);

    }

    function callBudgeter() {

        var $form  = $('.form-budgeter').closest('form');
        var url    = $form.attr('action');
        var params = $form.serialize();

        showLoadingOverlay();

        $.post(url, params, function(data){
            $('.results-list').html(data.html);

            $('.lbl-pickup-type').show().html(data.pickupTypeLabel)
            $('.lbl-count-services').show().html("("+data.countServices+")")

            if (data.date) {
                $('[name="date"]').val(data.date);
            }

            callTransitTimes();

        }).fail(function(){
            html = '<div class="results-loading text-red"><h4><i class="fas fa-exclamation-triangle"></i> Cant loading results.</h4></div>';
            $('.row-service-title').show();
            $('.results-list').html(html);
        })
    }

    function callTransitTimes() {

        $(document).find('.provider-delivery-date a').each(function(){

            var $this    = $(this);
            var url      = $this.attr('href')
            var service  = $this.data('service')
            var provider = $this.data('provider')

            var formData = $('.form-budgeter').serialize();
            formData = formData+'&service='+service+'&provider='+provider

            $.post(url, formData, function(data){
                if(data.html) {
                    $this.closest('.delivery-date').find('.provider-delivery-date').html(data.html);
                    $this.closest('.delivery-date').find('.default-delivery-date').hide();
                } else {
                    $this.closest('.delivery-date').find('.provider-delivery-date').hide();
                    $this.closest('.delivery-date').find('.default-delivery-date').hide();
                    $this.closest('.delivery-date').find('.default-delivery-date-error').show();
                }


            }).fail(function(){
                /*html = '<div class="text-red"><i class="fas fa-exclamation-triangle"></i> N/A</div>';
                $this.html(html);*/
                $this.closest('.delivery-date').find('.provider-delivery-date').hide();
                $this.closest('.delivery-date').find('.default-delivery-date').hide();
                $this.closest('.delivery-date').find('.default-delivery-date-error').show();
            })
        })
    }

    if (SEARCH_ZIP_CODE) {
        function initSenderZipCodeSearch() {
            ZipCode.searchInputAutocomplete(
                '[name="sender_zip_code"]',
                '[name="sender_city"]',
                $('[name="sender_country"]').val(),
                function () {
                    callBudgeter();
                });
        }

        function initRecipientZipCodeSearch() {
            ZipCode.searchInputAutocomplete(
                '[name="recipient_zip_code"]',
                '[name="recipient_city"]',
                $('[name="recipient_country"]').val(),
                function () {
                    callBudgeter();
                });
        }

        initSenderZipCodeSearch();
        $('[name="sender_country"]').on('change', function () {
            initSenderZipCodeSearch();
        });

        initRecipientZipCodeSearch();
        $('[name="recipient_country"]').on('change', function () {
            initRecipientZipCodeSearch();
        });
    }


</script>
@stop