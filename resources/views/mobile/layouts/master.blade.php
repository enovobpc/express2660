<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
        <title>{{ config('app.name') }}</title>

        <!-- Add to homescreen for Chrome on Android -->
        <meta name="mobile-web-app-capable" content="yes">

        <!-- Add to homescreen for Safari on iOS -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
        <meta name="format-detection" content="telephone=no">

        <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
        <link rel="manifest" href="{{ asset('/assets/mobile/manifest.json') }}">

        <link href="https://fonts.googleapis.com/css?family=Oswald:300,500,700" rel="stylesheet">
        {{ Html::style('assets/admin/css/helper.css') }}
        {{ Html::style('assets/mobile/css/style.css?v='.time()) }}
        @yield('styles')
        <style>
            header,
            .home-button .button-circle,
            .footer-buttons{
                background: {{ env('APP_MAIL_COLOR_PRIMARY') }};
            }

            .list {
                overflow-x: scroll !important;
                height: 600px;
            }

            .slippylist li {
                user-select: none;
                -moz-user-select: none;
                -webkit-user-select: none;
                cursor: default;
                display: block;
                position: relative;
            }
        </style>
    </head>
    <body>
       {{-- @if(hasModule('app_apk'))
        <div class="android-popup">
            <a href="" class="android-btn-cancel">✕</a>
            <a href="{{ coreUrl('mobile/enovo_tms.apk') }}" class="android-btn-download">Download</a>
            <p class="msg">
                Instalar versão Android
            </p>
        </div>
        @endif--}}
        @include('mobile.partials.loading')
        @include('mobile.partials.network_error')
        @include('mobile.partials.ajax_error')
        @include('mobile.partials.session_error')
        @include('mobile.pages.delivery')
        @include('mobile.partials.location_disabled')
        <div class="scanner-result"></div>
        <div id="main-window">
            @yield('content')
        </div>

        <input name="bg_lat" style="display: none">
        <input name="bg_lng" style="display: none">

    </body>
</html>

<style>
    .android-popup {
        position: absolute;
        background: #382d2d;
        left: 0;
        right: 0;
        color: #fff;
        bottom: 0;
        z-index: 30;
    }
    .android-popup .msg{
        float: left;
        padding: 24px 15px 20px;
        line-height: 0;
    }
    .android-popup .android-btn-download{
        float: right;
        padding: 7px 12px;
        line-height: 15px;
        font-size: 16px;
        margin: 10px 0px;
        background: #ffa315;
        color: #fff;
        text-decoration: none;
        border-radius: 30px;
        text-transform: uppercase;
    }
    .android-popup .android-btn-cancel{
        float: right;
        padding: 13px 8px 16px;
        line-height: 0;
        margin: 10px;
        /* background: #ffa315; */
        color: #fff;
        text-decoration: none;
        border-radius: 30px;
    }
</style>
{{ Html::script('vendor/admin-lte/plugins/jQuery/jquery-2.2.3.min.js') }}
{{ Html::script('assets/mobile/js/jquery.mobile-events.min.js') }}
{{ Html::script('vendor/admin-lte/bootstrap/js/bootstrap.min.js') }}
{{ Html::script('assets/mobile/js/pusher.min.js') }}
{{ Html::script('vendor/push.js/bin/push.js') }}
{{ Html::script('vendor/pusher/pusher.min.js') }}
{{ Html::script("vendor/jSignature/libs/jSignature.min.js") }}
{{ Html::script("vendor/jSignature/libs/modernizr.js") }}
{{ Html::script("vendor/jSignature/libs/flashcanvas.js") }}
{{ Html::script("assets/mobile/js/slip.js") }}
<script src="{{ asset('assets/admin/js/maps.js') }}"></script>
{{ Html::script("assets/admin/js/helper.js") }}
{{ Html::script('assets/mobile/js/main.js') }}
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ getGoogleMapsApiKey('public') }}"></script>

@yield('scripts')

<script>
    @if(Setting::get('mobile_app_enable_read_service'))
    $(document).on('click', '.shipment-item', function(){
        var target = $(this).data('target');
        id = target.replace('#window-detail-', '')

        $.get('{{ route('mobile.shipments.set.read', '') }}/' + id, {source:'list'},function(){})
    })
    @endif

    $(document).on('click', '.shipment-item', function(){
        navigator.geolocation.getCurrentPosition(function(position) {
            $('[name="latitude"]').val(position.coords.latitude)
            $('[name="longitude"]').val(position.coords.longitude)
            console.log(position.coords.latitude + ', ' + position.coords.longitude)
        });
    })

    $(document).on('click', '[data-toggle="confirm"]', function () {
       var target = $(this).data('target');
       $(target).show();
    })

    $(document).on('click', '[data-toggle="confirm"]', function () {
        var target = $(this).data('target');
        $(target).show();
    })

    $('.check-notification').on('click', function(){

        Push.create('Notificação de teste', {
            body: 'Se recebeu esta mensagem, tem as notificações ativas.',
            timeout: 4000,
            vibrate: [200, 100, 200, 100, 200, 100, 200]
        });

        if (Notification.permission == "granted") { //permitidas
            alert('As notificações estão ativas');
        } else if (Notification.permission == "prompt") {
            alert('Pedido de permissão.');
        } else if (Notification.permission == 'denied') {
            alert('As notificações estão bloqueadas. Desbloqueie nas definições do Google Chrome.');
        }
    })


    var height = (screen.height - 97);
    $('.list').height(height);

    $(document).on('click', '.location-check', function(){
        geolocationStatus();
    })

    var $sigdiv;

    // Enable pusher logging - don't include this in production
    //Pusher.logToConsole = true;
    var pusher = new Pusher('{{ env('PUSHER_KEY') }}', {
        cluster: '{{ env('PUSHER_CLUSTER') }}',
        encrypted: '{{ env('PUSHER_ENCRYPTION') }}'
    });

    var channelUser = pusher.subscribe('channel-{{ Auth::user()->id }}');
    channelUser.bind('notifications-event', function(data) {

        if(data.message) {
            Notifier.pushAlert(data.title, data.message);
        }
    });

    var channelGlobal = pusher.subscribe('channel-operators-{{ config('app.source') }}');
    channelGlobal.bind('notifications-event', function(data) {
        if(data.message) {
            Notifier.pushAlert(data.title, data.message);
        }
    });

    $(document).ready(function(){
        checkNetworkConnection();
    })

    $('.network-check').on('click', function(){
        $(this).button('loading')
        checkNetworkConnection();
        $(this).button('reset')
    })

    /**
     * Try network connection
     * @type {boolean}
     */
    function checkNetworkConnection() {
        var online = navigator.onLine;

        if(!online) {
            $('.network-error').show();
        } else {
            $('.network-error').hide();
        }
    }

    ////////////////// DELIVERY //////////////////
    var $sigdiv = $("#signature")

    $sigdiv.jSignature({
        'UndoButton' : false,
        'color' : "#000000",
        'lineWidth' : 1,
        'width' : 360,
        'height': 460
    }) // inits the jSignature widget.

    $(document).on('click', '.reset-signature', function () {
        $sigdiv.jSignature("reset")
    });

    $("#signature").bind('change', function(e){
        $('[name="signature"]').val('');
        var datapair = $sigdiv.jSignature("getData")
        $('.saved-signature').attr('src', datapair);
        console.log(datapair);
        //$('[name="signature"]').text(datapair);
        $(document).find('[name="signature"]').val(datapair);
    })

    $(document).on('click', '.upload-photo', function(){
        $('[name="attachment"]').trigger('click');
    })

    $('[name="attachment"]').change(function() {
        previewPhoto(this);
    });

    function previewPhoto(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('.current-photo').hide();
                $('.preview-photo').show();
                $('.preview-photo').css('background-image', 'url(' + e.target.result + ')');
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).on('click', '.upload-user-photo', function(){
        $('.window-settings [name="attachment"]').trigger('click');
    })

    $(document).on('click', '.btn-remove-photo', function(){
        $('[name="remove_photo"]').val(1);
        $('.default-photo').show();
        $('.current-photo').hide();
    })

    $(document).on('click', '[data-target="#window-delivery"]', function(e){
        e.preventDefault();
        $('#window-delivery').show();
        $('#window-incidence').hide();
        $('#window-delivery [name=id]').val($(this).data('shipment-id'));
        $('#window-delivery [name=has_return]').val($(this).data('shipment-return'));

        if($(this).data('shipment-return') == '1') {
            $('[name="return_volumes"],[name="return_weight"]').prop('required', true)
        } else {
            $('[name="return_volumes"],[name="return_weight"]').prop('required', false)
        }
    })

    $(document).on('click', '[data-target="#window-incidence"]', function(e){
        e.preventDefault();
        $('#window-incidence').show();
        $('#window-delivery').hide();
        $('#window-incidence [name=id]').val($(this).data('shipment-id'));
        $('#window-delivery [name=has_return]').val(0);
        $('[name="return_volumes"],[name="return_weight"]').prop('required', false)
    })


    $(document).on('click', '[data-toggle="modal"]', function(e){
        e.preventDefault();
        var target = $(this).data('target');
        $(target).show();
    })

    $(document).on('click', '.btn-modal-close', function(){
        $(this).closest('.box-form').hide();
    })


    /////////////// SHOW SHIPMENT DETAIL ///////////////
    $(document).on('click', '.shipments-list .list-item', function () {
        var target = $(this).data('target');
        $(target).show();
        $('.shipments-list .list-item').removeClass('active');
        $(this).addClass('active')
    })

    $(document).on('click', '[data-toggle="back"]',function(e){
        e.preventDefault();

        var target = $(this).data('target');

        if(typeof target == 'undefined'){
            $(this).closest('.window').hide();
        } else {
            $('.window').hide();
            $(target).show();
        }

        $sigdiv.jSignature("reset")
        $('.checkbox-icon').attr('src', 'data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDYwIDYwIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA2MCA2MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIyNHB4IiBoZWlnaHQ9IjI0cHgiPgo8cGF0aCBkPSJNMzAsMEMxMy40NTgsMCwwLDEzLjQ1OCwwLDMwczEzLjQ1OCwzMCwzMCwzMHMzMC0xMy40NTgsMzAtMzBTNDYuNTQyLDAsMzAsMHogTTMwLDU4QzE0LjU2MSw1OCwyLDQ1LjQzOSwyLDMwICBTMTQuNTYxLDIsMzAsMnMyOCwxMi41NjEsMjgsMjhTNDUuNDM5LDU4LDMwLDU4eiIgZmlsbD0iIzMzMzMzMyIvPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K');
        $('[name=incidence_id]').prop('checked', false)
        $('[name=receiver],[name=obs]').val('');
    })

    /////////////// UPDATE STATUS ///////////////
    $(document).on('click', '.update-status-btn', function(){
        var status = $(this).data('status-id');
        $('[name="status_id"]').val(status);
        $(this).closest('form').submit()
    })

    $(document).on('click', '[name="receiver"], [name="obs"]', function(e){
        e.stopPropagation();
        $('#window-delivery [name="obs"]').show()
    })

    $(document).on('click', '#signature', function(){
        $('#window-delivery [name="obs"]').hide()
        $(this).blur();
    })

    $('body').on('click', function(e){
        $('#window-delivery [name=obs]').hide();
    });


    $(document).on('click', '.shipments-incidences .list-item, .shipments-operators .list-item', function (e) {
        $(this).find('[name=incidence_id], [name=operator_id]').prop('checked', true)
        $('.checkbox-icon').attr('src', 'data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDYwIDYwIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA2MCA2MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIyNHB4IiBoZWlnaHQ9IjI0cHgiPgo8cGF0aCBkPSJNMzAsMEMxMy40NTgsMCwwLDEzLjQ1OCwwLDMwczEzLjQ1OCwzMCwzMCwzMHMzMC0xMy40NTgsMzAtMzBTNDYuNTQyLDAsMzAsMHogTTMwLDU4QzE0LjU2MSw1OCwyLDQ1LjQzOSwyLDMwICBTMTQuNTYxLDIsMzAsMnMyOCwxMi41NjEsMjgsMjhTNDUuNDM5LDU4LDMwLDU4eiIgZmlsbD0iIzMzMzMzMyIvPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K');
        $(this).find('.checkbox-icon').attr('src', 'data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMjRweCIgaGVpZ2h0PSIyNHB4Ij4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNNDM3LjAxOSw3NC45OEMzODguNjY3LDI2LjYyOSwzMjQuMzgsMCwyNTYsMEMxODcuNjE5LDAsMTIzLjMzMiwyNi42MjksNzQuOTgsNzQuOThDMjYuNjI5LDEyMy4zMzIsMCwxODcuNjIsMCwyNTYgICAgczI2LjYyOSwxMzIuNjY3LDc0Ljk4LDE4MS4wMTlDMTIzLjMzMiw0ODUuMzcxLDE4Ny42Miw1MTIsMjU2LDUxMnMxMzIuNjY3LTI2LjYyOSwxODEuMDE5LTc0Ljk4ICAgIEM0ODUuMzcxLDM4OC42NjcsNTEyLDMyNC4zOCw1MTIsMjU2UzQ4NS4zNzEsMTIzLjMzMyw0MzcuMDE5LDc0Ljk4eiBNMzc4LjMwNiwxOTUuMDczTDIzNS4yNDEsMzM4LjEzOSAgICBjLTIuOTI5LDIuOTI5LTYuNzY4LDQuMzkzLTEwLjYwNiw0LjM5M2MtMy44MzksMC03LjY3OC0xLjQ2NC0xMC42MDctNC4zOTNsLTgwLjMzNC04MC4zMzNjLTUuODU4LTUuODU3LTUuODU4LTE1LjM1NCwwLTIxLjIxMyAgICBjNS44NTctNS44NTgsMTUuMzU1LTUuODU4LDIxLjIxMywwbDY5LjcyOCw2OS43MjdsMTMyLjQ1OC0xMzIuNDZjNS44NTctNS44NTgsMTUuMzU1LTUuODU4LDIxLjIxMywwICAgIEMzODQuMTY0LDE3OS43MTgsMzg0LjE2NCwxODkuMjE1LDM3OC4zMDYsMTk1LjA3M3oiIGZpbGw9IiMzMzMzMzMiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K')
    })

    /////////////// CREATE RETURN ///////////////
    $(document).on('click', '.create-return-yes', function(){
        if($('[name="return_volumes"]').val() == '') {
            $('.form-required-error').show()
            $('.form-required-error').find('.fieldname').html('Nº Volumes')
            $('.return-modal').hide();
            $('[name="create_return"]').val('-1');
        } else if($('[name="return_weight"]').val() == '') {
            $('.form-required-error').show()
            $('.form-required-error').find('.fieldname').html('Peso Total')
            $('.return-modal').hide();
            $('[name="create_return"]').val('-1');
        } else {
            $('[name="create_return"]').val(1);
            $('.return-modal').hide();
            //$(this).closest('form').submit()
        }
    })

    $(document).on('click', '.create-return-no', function(){
        $('[name="create_return"]').val(0);
        $('[name="return_volumes"],[name="return_weight"]').val('').prop('required', false)
        $('.return-modal').hide();
        $(this).closest('form').submit()
    })



    /////////////// GLOBAL ACTIONS ///////////////
    $(document).on('click', '[data-toggle="alert"]', function(e){
        var text = $(this).data('text');
        $('.alert-modal').show();
        $('.alert-modal p').html(text);
    })

    $(document).on('click', '.alert-modal .btn-close', function(){
        $('.alert-modal').hide();
    })

    $(document).on('click', '.error-close', function(){
        $(this).closest('div').hide();
    })

    $(document).on('click', '.close-feedback', function(){
        $(this).closest('.feedback').remove();
    })

    $(document).on('click', '.btn-change-password', function(){
        $(this).hide();
        $('.password-form').slideDown()
    })

    $(document).on('click', '[data-toggle="accordion"]', function(){
        var target = $(this).data('target');

        $('.accordion-content').not(target).slideUp('fast');

        $(target).slideToggle();
    })

    $(document).on('submit', '.ajax-form',function (e) {
        e.preventDefault();

        if(navigator.onLine) {

            if($(this).hasClass('form-delivery')) { //submeter envio

                if($('[name="has_return"]').val() == '1' && $('[name="create_return"]').val() == '-1') {
                    e.preventDefault();
                    $('.return-modal').show();
                    return false;
                }

                var formError = false;
                $(this).find(':input[required]').each(function(e){
                    if($(this).val() == '') {
                        var placeholder = $(this).attr('placeholder');
                        if(placeholder == '') {
                            placeholder = $(this).data('data-placeholder')
                        }
                        $('.form-required-error').show()
                        $('.form-required-error').find('.fieldname').html(placeholder)
                        $('.return-modal').hide();
                        $('[name="create_return"]').val('-1');
                        formError = true;
                    }
                })

                if(formError) {
                    e.preventDefault();
                    return false;
                }
            }

           var $form = $(this).closest('form');
            var form  = $(this)[0];
            var formData = new FormData(form);

            if($("[name='attachment']").val() == ''){
                formData.delete('attachment');
            }

            formData.append('bg_lat', $('[name="bg_lat"]').val());
            formData.append('bg_lng', $('[name="bg_lng"]').val());

            console.log(formData);
            $('.loading-window').show();

            $.ajax({
                url: $form.attr('action'),
                data: formData,
                type: $form.attr('method'),
                contentType: false,
                processData: false,
            }).success(function(data){

                $('.window').hide();
                $(data.target).show();

                if(data.html) {
                    if(data.target) {
                        $(data.target).html(data.html)
                    } else {
                        $('#main-window').html(data.html)
                    }
                }

                $sigdiv.jSignature("reset")

            }).error(function(httpObj, textStatus) {
                if(httpObj.status == 401){
                    $('.session-error').show();
                    location.reload();
                } else {
                    $('.ajax-error').show();
                }
            }).always(function () {
                setupSlip(document.getElementById('pendingList'));
                setupSlip(document.getElementById('acceptedList'));
                setupSlip(document.getElementById('shipmentsList'));
                $('.loading-window').hide();
            });
        } else {
            $('.loading-window, .ajax-error').hide();
            $('.network-error').show();
        }
    });

    $(document).on('click', '[data-toggle="ajax"]', function (e) {
        e.preventDefault();

        if(navigator.onLine) {

            lat = $('[name="bg_lat"]').val();
            lng = $('[name="bg_lng"]').val();

            var action = $(this).attr('href')
            var method = $(this).data('method');
            var target = $(this).data('target');

            $('.loading-window').show();

            Url.change(action);

            action = Url.updateParameter(action, 'bg_lat', lat);
            action = Url.updateParameter(action, 'bg_lng', lng);

            $.ajax({
                url: action,
                type: method,
                contentType: false,
                processData: false,
            }).success(function(data){
                $('.window').hide();
                $(target).show();

                if(data.html) {
                    if(data.target) {
                        $(data.target).html(data.html)
                    } else {
                        $('#main-window').html(data.html)
                    }
                }

            }).error(function(httpObj, textStatus) {
                if(httpObj.status==401){
                    $('.session-error').show();
                    location.reload();
                } else {
                    $('.ajax-error').show();
                }
            }).always(function () {
                setupSlip(document.getElementById('pendingList'));
                setupSlip(document.getElementById('acceptedList'));
                setupSlip(document.getElementById('shipmentsList'));
                $('.loading-window').hide();
            });
        } else {
            $('.loading-window, .ajax-error').hide();
            $('.network-error').show();
        }

    });

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

    $(document).on('keyup', '.filter-list input', function(){

        var txt = removeAccents($(this).val());
        var target = $(this).parent().data('target');

        if(txt == '') {
            $(target).show();
        } else {
            $(target).hide();
            $('.filter-noresults').hide()
            $(target).each(function(){
                divTxt = removeAccents($(this).text());
                if(divTxt.toUpperCase().indexOf(txt.toUpperCase()) != -1){
                    $(this).show();
                }
            });

            if($(target+':visible').length == 0) {
                $('.filter-noresults').show()
            }
        }
    })

    $(document).on('click', '.filter-list input', function(){
        $(this).focus();
    })


    $(document).on('click', '[data-toggle=tab]', function(){
        var target = $(this).data('target');
        $(this).closest('.tab').find('.tab-item').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').hide();
        $(target).show();
    })


    /**
     * FUEL PAGE
     */
    $(document).on('change', '[name="price_per_liter"]', function(){
        var pricePerLiter = $('[name="price_per_liter"]').val()
        var liters = $('[name="liters"]').val();

        total = liters * pricePerLiter;
        $('[name="total"]').val(total.toFixed(2));
    })

    $(document).on('change', '[name="liters"]', function(){
        var pricePerLiter = $('[name="price_per_liter"]').val()
        var liters = $('[name="liters"]').val();

        total = liters * pricePerLiter;
        $('[name="total"]').val(total.toFixed(2));
    })

    $(document).on('change', '[name="total"]', function(){
        var pricePerLiter = $('[name="price_per_liter"]').val()
        var liters = $('[name="liters"]').val();
        var total  = $('[name="total"]').val();

        if(liters == '' && pricePerLiter!= '') {
            liters = total * pricePerLiter;
            $('[name="liters"]').val(liters.toFixed(2));
        } else {
            pricePerLiter = total / liters;
            $('[name="price_per_liter"]').val(pricePerLiter.toFixed(2));
        }
    })


    /**
     * ORDER LISTS
     */
    function setupSlip(list) {

        if (list !== null) {
            list.addEventListener('slip:beforereorder', function(e){
                if (e.target.classList.contains('demo-no-reorder')) {
                    e.preventDefault();
                }
            }, false);
            list.addEventListener('slip:beforeswipe', function(e){
                if (e.target.nodeName == 'INPUT' || e.target.classList.contains('demo-no-swipe')) {
                    e.preventDefault();
                }
            }, false);
            list.addEventListener('slip:beforewait', function(e){
                if (e.target.classList.contains('instant')) e.preventDefault();
            }, false);
            list.addEventListener('slip:afterswipe', function(e){
                e.target.parentNode.appendChild(e.target);
            }, false);
            list.addEventListener('slip:reorder', function(e){
                e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);

                var dataList = $('#' + list.id + ' > li').map(function () {
                    return $(this).data("id");
                }).get();

                if(list.id == 'shipmentsList') {
                    $.get('{{ route('mobile.shipments.sort') }}', {ids:dataList}, function(data){})
                } else {
                    $.get('{{ route('mobile.pendings.sort') }}', {ids:dataList}, function(data){})
                }


                return false;
            }, false);
            return new Slip(list);
        }
    }
    setupSlip(document.getElementById('pendingList'));
    setupSlip(document.getElementById('acceptedList'));
    setupSlip(document.getElementById('shipmentsList'));


    /**
     * MAP PAGE
     */
    var map, infowindow, directionsDisplay, whatcherId;
    var geolocationEnabled = "{{ Auth::user()->location_enabled }}";
    var bounds = new google.maps.LatLngBounds();
    var markers  = [];
    var markerCluster = null;

    @if(Route::currentRouteName() != 'mobile.operators.map')
    $(document).ready(function(){
        getMyPosition_bgTask();
    });
    @endif

    if ($("#map").length) {
        initMap();
        getMyPosition();
    }


    //set map marker
    function setMarker(lat, lng, options) {
        var options   = typeof options !== 'undefined' ? options : {};
        var draggable = typeof options.draggable !== 'undefined' ? options.draggable : false;
        var html      = typeof options.html !== 'undefined' ? options.html : null;
        var zoom      = typeof options.zoom !== 'undefined' ? options.zoom : null;
        var centerMap = typeof options.centerMap !== 'undefined' ? options.centerMap : false;
        var icon      = typeof options.icon !== 'undefined' ? options.icon : '';
        var id        = typeof options.id !== 'undefined' ? options.id : '';

        var positionObj = new google.maps.LatLng(lat,lng);
        var marker, infowindow;

        //set map zoom
        if(zoom) {
            map.setZoom(zoom);
        }

        //center map on marker
        if(centerMap) {
            map.setCenter(positionObj);
        }

        infowindow = new google.maps.InfoWindow();
        infowindow.setContent(html);

        //add marker
        marker = new google.maps.Marker({
            position: positionObj,
            draggable: draggable,
            icon: icon,
            map: map,
            infowindow: infowindow
        });

        if(id != "") {
            marker.set("id", id)
        }

        //add info window
        if(html) {

            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                if (infowindow) {
                    infowindow.close();
                }

                return function () {
                    infowindow.open(map, marker);
                }
            })(marker));
        }

        return marker;
    };

    /**
     * Open infowindow by marker id
     */
    function openInfoWindowById(id) {

        for (var i = 0; i < markers.length; i++) {
            if (markers[i].id == id) {
                marker = markers[i];
                infowindow = marker.infowindow
                infowindow.open(map, marker);
                map.setZoom(16);

                map.setCenter({lat: marker.position.lat(), lng: marker.position.lng()});
                return;
            }
        }
    }


    /**
     * Clear all map markers
     */
    function clearMarkers() {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }

        if(markerCluster) {
            markerCluster.setMap(null);
        }
    }

    /**
     * Clear Marker by Id
     * @param id
     */
    function clearMarkerById(id) {
        //Find and remove the marker from the Array
        for (var i = 0; i < markers.length; i++) {
            if (markers[i].id == id) {
                //Remove the marker from Map
                markers[i].setMap(null);

                //Remove the marker from array.
                markers.splice(i, 1);
                return;
            }
        }
    };

    function initMap() {
        var props
        props = {
            center: {lat: 40.404874, lng: -7.874651},
            zoom: 10,
            zoomControl: true,
            mapTypeControl: true,
            navigationControl: false,
            streetViewControl: true,
            scrollwheel: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };


        map = new google.maps.Map(document.getElementById('map'), props);

        // Add a marker clusterer to manage the markers.
        markerCluster = new MarkerClusterer(map, markers, {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});

    }

    $(document).ready(function(){
        clearMarkers();

        $('.customers-list ul li').each(function () {
            var lat  = $(this).data('lat');
            var lng  = $(this).data('lng');
            var id   = $(this).data('id');
            var html = $(this).data('html');

            if(lat != "" && lng != "") {
                options = { 'html' : html, 'id' : id }
                marker = setMarker(lat, lng, options)
                markers.push(marker);
                bounds.extend(marker.position); //auto center map
            }
        })

        $('.operators-list ul li').each(function () {
            var lat  = $(this).data('lat');
            var lng  = $(this).data('lng');
            var id   = $(this).data('id');
            var html = $(this).data('html');
            var icon = $(this).data('marker');

            if(lat != "" && lng != "") {
                options = { 'html' : html, 'id' : id, icon: icon }
                marker = setMarker(lat, lng, options)
                markers.push(marker);
                bounds.extend(marker.position); //auto center map
            }
        })

        if($('.customers-list ul li').length) {
            map.fitBounds(bounds); //auto center map
        }

        if($('.operators-list ul li').length) {
            map.fitBounds(bounds); //auto center map
        }
    })


    function getMyPosition() {

        console.log('my pos');
        if(geolocationEnabled == '1') {
            if (navigator && navigator.geolocation) {
                $('.loading-window').show();
                whatcherId = navigator.geolocation.getCurrentPosition(function(position) {
                    $('.location-enabled').show()
                    $('.location-disabled').hide();
                    $('.location-disabled-warning').hide();
                    $('#location-denied').hide();
                    setMyPosition(position.coords.latitude, position.coords.longitude)
                },
                function (error) {
                    if (error.code == error.PERMISSION_DENIED) {
                        $('.location-enabled').hide()
                        $('.location-disabled').show();
                        $('.location-denied-warning').show();
                        $('.location-disabled-warning').hide();
                        $('#location-denied').show()
                        disableLocationSetting(1);
                    }
                });

            } else {
                $('.location-enabled').hide()
                $('.location-disabled').hide();
                $('.location-denied-warning').show();
                $('.location-disabled-warning').hide();
            }
        }
    }

    function storeCurPosition() {
        navigator.geolocation.getCurrentPosition(function(position) {
            $('[name="bg_lat"]').val(position.coords.latitude)
            $('[name="bg_lng"]').val(position.coords.longitude)
            $('[name="latitude"]').val(position.coords.latitude)
            $('[name="longitude"]').val(position.coords.longitude)

            $.get('{{ route('mobile.customers.map.location.enable') }}', {lat:position.coords.latitude, lng:position.coords.longitude}, function(data) {})
            $('#location-denied').hide()
        });
    }

    function getMyPosition_bgTask() {

        console.log('position bg task enabled');
        if(geolocationEnabled == '1') {
            if (navigator && navigator.geolocation) {

                storeCurPosition();

                window.setInterval(function () {
                    storeCurPosition();
                }, 60000);

                /*whatcherId = navigator.geolocation.watchPosition(function(position) {
                        $('[name="latitude"]').val(position.coords.latitude)
                        $('[name="longitude"]').val(position.coords.longitude)
                        console.log(position.coords.latitude+','+position.coords.longitude + ' date: ' + new Date());

                        $.get('{{ route('mobile.customers.map.location.enable') }}', {lat:position.coords.latitude, lng:position.coords.longitude}, function(data) {})
                        $('#location-denied').hide()
                    },
                    function (error) {
                        if (error.code == error.PERMISSION_DENIED) {
                            $('.location-enabled').hide()
                            $('.location-disabled').show();
                            $('#location-denied').show();
                            disableLocationSetting(1);
                        }
                    });*/

            } else {
                disableLocationSetting();
            }
        }
    }

    function geolocationStatus() {
        if (navigator && navigator.geolocation) {

            navigator.geolocation.getCurrentPosition(function(position) {
                    $('#location-denied').hide()
                },
                function (error) {
                    if (error.code == error.PERMISSION_DENIED) {
                        $('.location-denied-warning').show();
                        $('.location-disabled-warning').hide();
                        $('.operators-map .open-customers-list').prop('disabled', true);
                        $('#location-denied').show();
                        return false;
                    }
                });

        } else {
            $('.location-denied-warning').show();
            $('.location-disabled-warning').hide();
            $('.operators-map .open-customers-list').prop('disabled', true);
            $('#location-denied').hide();
            return false;
        }

        return true
    }

    function disableLocationSetting(denied) {

        denied = (typeof denied !== 'undefined') ?  denied : false;

        $('.loading-window').show();
        $.get('{{ route('mobile.customers.map.location.disable') }}', {denied: denied}, function(data) {
            $('.loading-window').hide();
        });
    }

    function setMyPosition(latitude, longitude){

        var options = {
            id: 'myLocation',
            html: '<b>Eu</b><br/>Agora',
            icon: '{{ Auth::user()->location_marker ? asset(Auth::user()->location_marker) : '/assets/img/default/avatar.png' }}'
        }

        clearMarkerById('myLocation');
        marker = setMarker(latitude, longitude, options);
        markers.push(marker);
        map.setZoom(10);
        map.setCenter({lat: latitude, lng: longitude});

        $('.loading-window').show();
        $.get('{{ route('mobile.customers.map.location.enable') }}', {lat:latitude, lng:longitude}, function(data) {
            $('.loading-window').hide();
        })
    }

    $(document).on('click', '.open-customers-list', function(){
        $('.customers-list, .operators-list').addClass('active');
        $('.open-customers-list').hide();
        $('.close-customers-list').show();
    })

    $(document).on('click', '.close-customers-list', function(){
        $('.customers-list, .operators-list').removeClass('active');
        $('.open-customers-list').show();
        $('.close-customers-list').hide();
    })

    $(document).on('change', '[name=route_id]', function(){
        $('.loading-window').show();

        var routeId = $(this).val();
        var url = Url.current();
        var newUrl = Url.updateParameter(url, 'route', routeId);
        window.location = newUrl;
    })

    $('.customers-list ul li, .operators-list ul li').click(function(){
        var id = $(this).data('id');
        openInfoWindowById(id);
        $('.close-customers-list').trigger('click');
    })

    $(document).on('click', '.location-enabled', function(){
        geolocationEnabled = '0';
        $('.location-enabled').hide();
        $('.location-disabled').show();
        $('.operators-map .open-customers-list').prop('disabled', true);
        $('.operators-map .location-disabled-warning').show();
        clearMarkerById('myLocation');
        disableLocationSetting();
    })

    $(document).on('click', '.location-disabled', function(){
        if(geolocationStatus()) {
            geolocationEnabled = '1';
            $('.open-customers-list').prop('disabled', false);
            $('.location-enabled').show();
            $('.location-disabled').hide();
            $('.location-disabled-warning').hide();
            getMyPosition();
        } else {
            $('.operators-map .open-customers-list').prop('disabled', true);
            $('.location-denied-warning').show();
            $('.location-disabled-warning').hide();
        }
    })

</script>