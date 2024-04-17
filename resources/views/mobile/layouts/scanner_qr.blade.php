<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <meta name="format-detection" content="telephone=no">

    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" href="/favicon.png">

    <link href="https://fonts.googleapis.com/css?family=Oswald:300,700" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.orange-deep_orange.min.css" />
    {{ Html::style('assets/admin/css/helper.css') }}
    {{ Html::style('assets/mobile/css/style.css?v=3') }}
    <style>
        header,
        .home-button .button-circle,
        .footer-buttons{
            background: {{ env('APP_MAIL_COLOR_PRIMARY') }};
        }

        canvas {
            display: none;
        }
        hr {
            margin-top: 32px;
        }
        input[type="file"] {
            display: block;
            margin-bottom: 16px;
        }
        div {
            margin-bottom: 16px;
        }

        @keyframes zoominoutsinglefeatured {
            0% {
                transform: scale(0.8,0.8);
            }
            50% {
                transform: scale(1,1);
            }
            100% {
                transform: scale(0.95,0.95);
            }
        }

        .focus-square.focused {
            animation: zoominoutsinglefeatured 1s 1;
        }
    </style>
</head>
<body style="overflow: hidden">
    <div style="position: relative; z-index: 999999999997">
        @include('mobile.partials.loading')
        @include('mobile.partials.network_error')
        @include('mobile.partials.ajax_error')
        @include('mobile.pages.delivery')
    </div>
    <div class="scanner-result" style="position: relative; z-index: 999999999999"></div>
    <div id="main-window"></div>
    <div class="window" id="scanner-content">
        <div style="position: absolute;z-index: 1000;top: 23%;text-align: center;">
            <img class="focus-square" style="width: 55%;
    margin-top: 20%;
    position: fixed;
    left: 50%;
    margin-left: -27.5%;" src="data:image/svg+xml;base64,
    PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDMyNC4zMDMgMzI0LjMwMyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzI0LjMwMyAzMjQuMzAzOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgY2xhc3M9IiI+PGc+PGc+Cgk8cG9seWdvbiBzdHlsZT0iZmlsbDojRkY1NDAwIiBwb2ludHM9IjExLjcxLDExLjcxIDc3LjI1MywxMS43MSAxMjcuOTk1LDExLjcxIDEyNy45OTUsMCA3Ny4yNTMsMCAwLDAgMCwxMjcuOTk4IDExLjcxLDEyNy45OTggICAgICIgZGF0YS1vcmlnaW5hbD0iIzAzMDMwMyIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBkYXRhLW9sZF9jb2xvcj0iIzAzMDMwMyI+PC9wb2x5Z29uPgoJPHBvbHlnb24gc3R5bGU9ImZpbGw6I0ZGNTQwMCIgcG9pbnRzPSIzMTIuNTkyLDIyOC44MjMgMzEyLjU5MiwzMTIuNTkyIDE5Ni4zMTEsMzEyLjU5MiAxOTYuMzExLDMyNC4zMDMgMzI0LjMwMywzMjQuMzAzICAgIDMyNC4zMDMsMjI4LjgyMyAzMjQuMzAzLDE5Ni4zMTEgMzEyLjU5MiwxOTYuMzExICAiIGRhdGEtb3JpZ2luYWw9IiMwMzAzMDMiIGNsYXNzPSJhY3RpdmUtcGF0aCIgZGF0YS1vbGRfY29sb3I9IiMwMzAzMDMiPjwvcG9seWdvbj4KCTxwb2x5Z29uIHN0eWxlPSJmaWxsOiNGRjU0MDAiIHBvaW50cz0iMTEuNzEsMzEyLjU5MiAxMS43MSwxOTYuMzExIDAsMTk2LjMxMSAwLDMyNC4zMDMgMTI3Ljk5NSwzMjQuMzAzIDEyNy45OTUsMzEyLjU5MiAgIiBkYXRhLW9yaWdpbmFsPSIjMDMwMzAzIiBjbGFzcz0iYWN0aXZlLXBhdGgiIGRhdGEtb2xkX2NvbG9yPSIjMDMwMzAzIj48L3BvbHlnb24+Cgk8cG9seWdvbiBzdHlsZT0iZmlsbDojRkY1NDAwIiBwb2ludHM9IjMxMi41OTIsMTEuNzEgMzEyLjU5MiwxMjcuOTk4IDMyNC4zMDMsMTI3Ljk5OCAzMjQuMzAzLDAgMTk2LjMxMSwwIDE5Ni4zMTEsMTEuNzEgICIgZGF0YS1vcmlnaW5hbD0iIzAzMDMwMyIgY2xhc3M9ImFjdGl2ZS1wYXRoIiBkYXRhLW9sZF9jb2xvcj0iIzAzMDMwMyI+PC9wb2x5Z29uPgo8L2c+PC9nPiA8L3N2Zz4=" />
        </div>
        <header>
            <div class="action-buttons-right">
                <ul>
                    <li>
                        <a href="{{ route('mobile.logout') }}">
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCIgdmlld0JveD0iMCAwIDQ3NS4wODUgNDc1LjA4NSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDc1LjA4NSA0NzUuMDg1OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTIzNy41NDUsMjU1LjgxNmM5Ljg5OSwwLDE4LjQ2OC0zLjYwOSwyNS42OTYtMTAuODQ4YzcuMjMtNy4yMjksMTAuODU0LTE1Ljc5OSwxMC44NTQtMjUuNjk0VjM2LjU0NyAgICBjMC05LjktMy42Mi0xOC40NjQtMTAuODU0LTI1LjY5M0MyNTYuMDE0LDMuNjE3LDI0Ny40NDQsMCwyMzcuNTQ1LDBjLTkuOSwwLTE4LjQ2NCwzLjYyMS0yNS42OTcsMTAuODU0ICAgIGMtNy4yMzMsNy4yMjktMTAuODUsMTUuNzk3LTEwLjg1LDI1LjY5M3YxODIuNzI4YzAsOS44OTUsMy42MTcsMTguNDY0LDEwLjg1LDI1LjY5NCAgICBDMjE5LjA4MSwyNTIuMjA3LDIyNy42NDgsMjU1LjgxNiwyMzcuNTQ1LDI1NS44MTZ6IiBmaWxsPSIjRkZGRkZGIi8+CgkJPHBhdGggZD0iTTQzMy44MzYsMTU3Ljg4N2MtMTUuMzI1LTMwLjY0Mi0zNi44NzgtNTYuMzM5LTY0LjY2Ni03Ny4wODRjLTcuOTk0LTYuMDktMTcuMDM1LTguNDctMjcuMTIzLTcuMTM5ICAgIGMtMTAuMDg5LDEuMzMzLTE4LjA4Myw2LjA5MS0yMy45ODMsMTQuMjczYy02LjA5MSw3Ljk5My04LjQxOCwxNi45ODYtNi45OTQsMjYuOTc5YzEuNDIzLDkuOTk4LDYuMTM5LDE4LjAzNywxNC4xMzMsMjQuMTI4ICAgIGMxOC42NDUsMTQuMDg0LDMzLjA3MiwzMS4zMTIsNDMuMjUsNTEuNjc4YzEwLjE4NCwyMC4zNjQsMTUuMjcsNDIuMDY1LDE1LjI3LDY1LjA5MWMwLDE5LjgwMS0zLjg1NCwzOC42ODgtMTEuNTYxLDU2LjY3OCAgICBjLTcuNzA2LDE3Ljk4Ny0xOC4xMywzMy41NDQtMzEuMjY1LDQ2LjY3OWMtMTMuMTM1LDEzLjEzMS0yOC42ODgsMjMuNTUxLTQ2LjY3OCwzMS4yNjFjLTE3Ljk4Nyw3LjcxLTM2Ljg3OCwxMS41Ny01Ni42NzMsMTEuNTcgICAgYy0xOS43OTIsMC0zOC42ODQtMy44Ni01Ni42NzEtMTEuNTdjLTE3Ljk4OS03LjcxLTMzLjU0Ny0xOC4xMy00Ni42ODItMzEuMjYxYy0xMy4xMjktMTMuMTM1LTIzLjU1MS0yOC42OTEtMzEuMjYxLTQ2LjY3OSAgICBjLTcuNzA4LTE3Ljk5LTExLjU2My0zNi44NzctMTEuNTYzLTU2LjY3OGMwLTIzLjAyNiw1LjA5Mi00NC43MjQsMTUuMjc0LTY1LjA5MWMxMC4xODMtMjAuMzY0LDI0LjYwMS0zNy41OTEsNDMuMjUzLTUxLjY3OCAgICBjNy45OTQtNi4wOTUsMTIuNzAzLTE0LjEzMywxNC4xMzMtMjQuMTI4YzEuNDI3LTkuOTg5LTAuOTAzLTE4Ljk4Ni02Ljk5NS0yNi45NzljLTUuOTAxLTguMTgyLTEzLjg0NC0xMi45NDEtMjMuODM5LTE0LjI3MyAgICBjLTkuOTk0LTEuMzMyLTE5LjA4NSwxLjA0OS0yNy4yNjgsNy4xMzljLTI3Ljc5MiwyMC43NDUtNDkuMzQ0LDQ2LjQ0Mi02NC42NjksNzcuMDg0Yy0xNS4zMjQsMzAuNjQ2LTIyLjk4Myw2My4yODgtMjIuOTgzLDk3LjkyNyAgICBjMCwyOS42OTcsNS44MDYsNTguMDU0LDE3LjQxNSw4NS4wODJjMTEuNjEzLDI3LjAyOCwyNy4yMTgsNTAuMzQsNDYuODI2LDY5Ljk0OGMxOS42MDIsMTkuNjAzLDQyLjkxOSwzNS4yMTUsNjkuOTQ5LDQ2LjgxNSAgICBjMjcuMDI4LDExLjYxNSw1NS4zODgsMTcuNDI2LDg1LjA4LDE3LjQyNmMyOS42OTMsMCw1OC4wNTItNS44MTEsODUuMDgxLTE3LjQyNmMyNy4wMzEtMTEuNjA0LDUwLjM0Ny0yNy4yMTMsNjkuOTUyLTQ2LjgxNSAgICBjMTkuNjAyLTE5LjYwMiwzNS4yMDctNDIuOTIsNDYuODE4LTY5Ljk0OHMxNy40MTItNTUuMzkyLDE3LjQxMi04NS4wODJDNDU2LjgwOSwyMjEuMTc0LDQ0OS4xNiwxODguNTMyLDQzMy44MzYsMTU3Ljg4N3oiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                        </a>
                    </li>
                </ul>
            </div>
            <div class="action-buttons-left">
                <ul>
                    <li>
                        <a href="{{ route('mobile.index') }}" data-toggle="ajax" data-method="get" data-target="#main-window">
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCIgdmlld0JveD0iMCAwIDQ2MC4yOTggNDYwLjI5NyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDYwLjI5OCA0NjAuMjk3OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTIzMC4xNDksMTIwLjkzOUw2NS45ODYsMjU2LjI3NGMwLDAuMTkxLTAuMDQ4LDAuNDcyLTAuMTQ0LDAuODU1Yy0wLjA5NCwwLjM4LTAuMTQ0LDAuNjU2LTAuMTQ0LDAuODUydjEzNy4wNDEgICAgYzAsNC45NDgsMS44MDksOS4yMzYsNS40MjYsMTIuODQ3YzMuNjE2LDMuNjEzLDcuODk4LDUuNDMxLDEyLjg0Nyw1LjQzMWgxMDkuNjNWMzAzLjY2NGg3My4wOTd2MTA5LjY0aDEwOS42MjkgICAgYzQuOTQ4LDAsOS4yMzYtMS44MTQsMTIuODQ3LTUuNDM1YzMuNjE3LTMuNjA3LDUuNDMyLTcuODk4LDUuNDMyLTEyLjg0N1YyNTcuOTgxYzAtMC43Ni0wLjEwNC0xLjMzNC0wLjI4OC0xLjcwN0wyMzAuMTQ5LDEyMC45MzkgICAgeiIgZmlsbD0iI0ZGRkZGRiIvPgoJCTxwYXRoIGQ9Ik00NTcuMTIyLDIyNS40MzhMMzk0LjYsMTczLjQ3NlY1Ni45ODljMC0yLjY2My0wLjg1Ni00Ljg1My0yLjU3NC02LjU2N2MtMS43MDQtMS43MTItMy44OTQtMi41NjgtNi41NjMtMi41NjhoLTU0LjgxNiAgICBjLTIuNjY2LDAtNC44NTUsMC44NTYtNi41NywyLjU2OGMtMS43MTEsMS43MTQtMi41NjYsMy45MDUtMi41NjYsNi41Njd2NTUuNjczbC02OS42NjItNTguMjQ1ICAgIGMtNi4wODQtNC45NDktMTMuMzE4LTcuNDIzLTIxLjY5NC03LjQyM2MtOC4zNzUsMC0xNS42MDgsMi40NzQtMjEuNjk4LDcuNDIzTDMuMTcyLDIyNS40MzhjLTEuOTAzLDEuNTItMi45NDYsMy41NjYtMy4xNCw2LjEzNiAgICBjLTAuMTkzLDIuNTY4LDAuNDcyLDQuODExLDEuOTk3LDYuNzEzbDE3LjcwMSwyMS4xMjhjMS41MjUsMS43MTIsMy41MjEsMi43NTksNS45OTYsMy4xNDJjMi4yODUsMC4xOTIsNC41Ny0wLjQ3Niw2Ljg1NS0xLjk5OCAgICBMMjMwLjE0OSw5NS44MTdsMTk3LjU3LDE2NC43NDFjMS41MjYsMS4zMjgsMy41MjEsMS45OTEsNS45OTYsMS45OTFoMC44NThjMi40NzEtMC4zNzYsNC40NjMtMS40Myw1Ljk5Ni0zLjEzOGwxNy43MDMtMjEuMTI1ICAgIGMxLjUyMi0xLjkwNiwyLjE4OS00LjE0NSwxLjk5MS02LjcxNkM0NjAuMDY4LDIyOS4wMDcsNDU5LjAyMSwyMjYuOTYxLDQ1Ny4xMjIsMjI1LjQzOHoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
                        </a>
                    </li>
                </ul>
            </div>
            @include('mobile.partials.logo')
        </header>
        @yield('content')
        <div style="margin-top: 100px; position: fixed; left: 0; right: 0; top: 0; bottom: -50px; overflow: hidden;">
            <video muted playsinline id="qr-video" style="width: 100%;"></video>
        </div>
    </div>
    <div style="display: none">
        <select id="inversion-mode-select">
            <option value="original">Scan original (dark QR code on bright background)</option>
            <option value="invert">Scan with inverted colors (bright QR code on dark background)</option>
            <option value="both">Scan both</option>
        </select>
        <br>
    </div>
    <input name="bg_lat" style="display: none">
    <input name="bg_lng" style="display: none">
</body>
</html>

{{ Html::script('vendor/admin-lte/plugins/jQuery/jquery-2.2.3.min.js') }}
{{ Html::script("vendor/jSignature/libs/jSignature.min.js") }}
{{ Html::script("vendor/jSignature/libs/modernizr.js") }}
{{ Html::script("vendor/jSignature/libs/flashcanvas.js") }}

<script type="module">

    import QrScanner from "{{ asset('vendor/qr-scanner/qr-scanner.min.js') }}";
    QrScanner.WORKER_PATH = "{{ asset('vendor/qr-scanner/qr-scanner-worker.min.js') }}";
    const video = document.getElementById('qr-video');
    const camQrResult = document.getElementById('cam-qr-result');

    function setResult(label, result) {

        $('[name=tracking]').val(result);
        $('.scanner-search button').trigger('click');
        $('.focus-square').addClass('focused');

        setTimeout(function(){
            $('[name=tracking]').val('');
            $('.focus-square').removeClass('focused');
        }, 1000);


    }

    // ####### Web Cam Scanning #######
    //QrScanner.hasCamera().then(hasCamera => camHasCamera.textContent = hasCamera);
    const scanner = new QrScanner(video, result => setResult(camQrResult, result));
    scanner.start();

    /*document.getElementById('inversion-mode-select').addEventListener('change', event => {
        scanner.setInversionMode(event.target.value);
    });*/

</script>
<script>

    var geolocationEnabled = "{{ Auth::user()->location_enabled }}";

    $(document).ready(function(){
        checkNetworkConnection();
        getMyPosition_bgTask();
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

    $(document).on('click', '.error-close', function(){
        $(this).closest('div').hide();
    })

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

    $(document).bind('change', "#signature", function(e){
        $('[name="signature"]').val('');
        var datapair = $sigdiv.jSignature("getData")
        $('.saved-signature').attr('src', datapair);
        $('[name="signature"]').val(datapair);
    })

    $(document).on('click', '.upload-photo', function(){
        $('[name="attachment"]').trigger('click');
    })

    $(document).on('click', '[data-target="#window-delivery"]', function(e){
        e.preventDefault();
        $('#window-delivery').show();
        $('#window-incidence').hide();
        $('#window-delivery [name=id]').val($(this).data('shipment-id'));
    })

    $(document).on('click', '[data-target="#window-incidence"]', function(e){
        e.preventDefault();
        $('#window-incidence').show();
        $('#window-delivery').hide();
        $('#window-incidence [name=id]').val($(this).data('shipment-id'));
    })

    $(document).on('click', '.scanner-search button',function(e) {
        var code = $('.scanner-search input').val();
        var url = $('[name="target_url"]').val() + '/' + code;

        $('.loading-window').show();

        /*$.get(url, function(data){
            if(data.result) {
                $('#scanner-content').remove();
                $('#main-window').html(data.html)
            } else {
                $('.scanner-result').html(data.html)
            }
        }).error(function () {
            $('.ajax-error').show();
        }).always(function () {
            $('.loading-window').hide();
        });*/

        window.location=url;
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

            var $form = $(this).closest('form');
            var form  = $(this)[0];
            var formData = new FormData(form);

            if($("[name='attachment']").val() == ''){
                formData.delete('attachment');
            }

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

            var action = $(this).attr('href')
            var method = $(this).data('method');
            var target = $(this).data('target');

            $('.loading-window').show();

            URL.change(action);

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
                $('.loading-window').hide();
            });
        } else {
            $('.loading-window, .ajax-error').hide();
            $('.network-error').show();
        }
    });

    function storeCurPosition() {
        navigator.geolocation.getCurrentPosition(function(position) {
            $('[name="bg_lat"]').val(position.coords.latitude)
            $('[name="bg_lng"]').val(position.coords.longitude)
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

            } else {
                disableLocationSetting();
            }
        }
    }
</script>