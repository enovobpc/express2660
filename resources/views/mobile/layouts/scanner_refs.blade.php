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
        {{ Html::style('assets/mobile/css/style.css?v='.time()) }}
        <style>
            header,
            .home-button .button-circle,
            .footer-buttons{
                background: {{ env('APP_MAIL_COLOR_PRIMARY') }};
            }

            .mdl-layout__content {
                -ms-flex: 0 1 auto;
                position: relative;
                overflow-y: auto;
                overflow-x: hidden;
                display: block;
                -webkit-flex-grow: 1;
                -ms-flex-positive: 1;
                flex-grow: 1;
                z-index: 1;
                -webkit-overflow-scrolling: touch;
            }

            #container {
                width: 640px;
                margin: 20px auto;
                padding: 10px;
            }

            #interactive.viewport {
                width: 640px;
                height: 480px;
            }


            #interactive.viewport canvas, video {
                float: left;
                width: 640px;
                height: 480px;
            }

            #interactive.viewport canvas.drawingBuffer, video.drawingBuffer {
                margin-left: -640px;
            }

            .controls fieldset {
                border: none;
                margin: 0;
                padding: 0;
            }

            .controls .input-group {
                float: left;
            }

            .controls .input-group input, .controls .input-group button {
                display: block;
            }

            .controls .reader-config-group {
                float: right;
            }

            .controls .reader-config-group label {
                display: block;
            }

            .controls .reader-config-group label span {
                width: 9rem;
                display: inline-block;
                text-align: right;
            }

            .controls:after {
                content: '';
                display: block;
                clear: both;
            }


            #result_strip {
                margin: 10px 0;
                border-top: 1px solid #EEE;
                border-bottom: 1px solid #EEE;
                padding: 10px 0;
            }

            #result_strip > ul {
                padding: 0;
                margin: 0;
                list-style-type: none;
                width: auto;
                overflow-x: auto;
                overflow-y: hidden;
                white-space: nowrap;
            }

            #result_strip > ul > li {
                display: inline-block;
                vertical-align: middle;
                width: 160px;
            }

            #result_strip > ul > li .thumbnail {
                padding: 5px;
                margin: 4px;
                border: 1px dashed #CCC;
            }

            #result_strip > ul > li .thumbnail img {
                max-width: 140px;
            }

            #result_strip > ul > li .thumbnail .caption {
                white-space: normal;
            }

            #result_strip > ul > li .thumbnail .caption h4 {
                text-align: center;
                word-wrap: break-word;
                height: 40px;
                margin: 0px;
            }

            #result_strip > ul:after {
                content: "";
                display: table;
                clear: both;
            }


            .scanner-overlay {
                display: none;
                width: 640px;
                height: 510px;
                position: absolute;
                padding: 20px;
                top: 50%;
                margin-top: -275px;
                left: 50%;
                margin-left: -340px;
                background-color: #FFF;
                -moz-box-shadow: #333333 0px 4px 10px;
                -webkit-box-shadow: #333333 0px 4px 10px;
                box-shadow: #333333 0px 4px 10px;
            }

            .scanner-overlay > .header {
                position: relative;
                margin-bottom: 14px;
            }

            .scanner-overlay > .header h4, .scanner-overlay > .header .close {
                line-height: 16px;
            }

            .scanner-overlay > .header h4 {
                margin: 0px;
                padding: 0px;
            }

            .scanner-overlay > .header .close {
                position: absolute;
                right: 0px;
                top: 0px;
                height: 16px;
                width: 16px;
                text-align: center;
                font-weight: bold;
                font-size: 14px;
                cursor: pointer;
            }


            @media (max-width: 603px) {

                #container {
                    width: 300px;
                    margin: 10px auto;
                    -moz-box-shadow: none;
                    -webkit-box-shadow: none;
                    box-shadow: none;
                }

                #container form.voucher-form input.voucher-code {
                    width: 180px;
                }
            }
            @media (max-width: 603px) {

                .reader-config-group {
                    width: 100%;
                }

                .reader-config-group label > span {
                    width: 50%;
                }

                .reader-config-group label > select, .reader-config-group label > input {
                    max-width: calc(50% - 2px);
                }

                #interactive.viewport {
                    width: 300px;
                    height: 300px;
                    overflow: hidden;
                }


                #interactive.viewport canvas, video {
                    margin-top: -50px;
                    width: 300px;
                    height: 400px;
                }

                #interactive.viewport canvas.drawingBuffer, video.drawingBuffer {
                    margin-left: -300px;
                }


                #result_strip {
                    margin-top: 5px;
                    padding-top: 5px;
                }

                #result_strip ul.thumbnails > li {
                    width: 150px;
                }

                #result_strip ul.thumbnails > li .thumbnail .imgWrapper {
                    width: 130px;
                    height: 130px;
                    overflow: hidden;
                }

                #result_strip ul.thumbnails > li .thumbnail .imgWrapper img {
                    margin-top: -25px;
                    width: 130px;
                    height: 180px;
                }
            }
            @media (max-width: 603px) {

                .overlay.scanner {
                    width: 640px;
                    height: 510px;
                    padding: 20px;
                    margin-top: -275px;
                    margin-left: -340px;
                    background-color: #FFF;
                    -moz-box-shadow: none;
                    -webkit-box-shadow: none;
                    box-shadow: none;
                }

                .overlay.scanner > .header {
                    margin-bottom: 14px;
                }

                .overlay.scanner > .header h4, .overlay.scanner > .header .close {
                    line-height: 16px;
                }

                .overlay.scanner > .header .close {
                    height: 16px;
                    width: 16px;
                }
            }


            #main-window {
                margin-top: 57px;
                overflow: hidden !important;
            }

         /*   .readed-codes {
                margin-top: -200px;
                height: 200px;
                background: #fff;
            }
*/
            .input-block {
                position: relative;
            }

            .remove-input {
                position: absolute;
                z-index: 11111;
                top: 0;
                right: 0;
                border: 40px;
                margin: 8px 10px;
                background: #e22525;
                border-radius: 30px;
                padding-top: 0;
                line-height: 25px;
                width: 27px;
                height: 27px;
                text-align: center;
                color: #fff;
            }

            .add-read-code, .submit-btn{
                text-align: center;
                font-size: 16px;
                padding: 10px;
                background: {{ env('APP_MAIL_COLOR_PRIMARY') }};
                color: #fff;
                width: 70%;
                text-transform: uppercase;
                border: none;
                float: left;
            }
            .submit-btn {
                border-left: 1px solid #fff;
                width: 30%;
            }
        </style>
    </head>
    <body>
        <div style="position: relative; z-index: 999999999997">
            @include('mobile.partials.loading')
            @include('mobile.partials.network_error')
            @include('mobile.partials.ajax_error')
            @include('mobile.pages.delivery')
        </div>
        <div class="scanner-result" style="position: relative; z-index: 999999999999"></div>
        <div id="main-window"></div>
        <div class="window" id="scanner-content">
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
        </div>
    </body>
</html>

{{ Html::script('vendor/admin-lte/plugins/jQuery/jquery-2.2.3.min.js') }}
<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
{{ Html::script("vendor/quagga/dist/quagga.min.js") }}
{{ Html::script("https://webrtc.github.io/adapter/adapter-latest.js") }}
{{--{{ Html::script("assets/mobile/js/scanner_refs.js?v=5") }}--}}

<script>

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

    $(document).on('click', '.error-close', function(){
        $(this).closest('div').hide();
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

    $('.add-read-code').on('click', function(){
        $('.readed-codes').append('<div class="input-block"><input name="reference[]"/><div class="remove-input">✕</div></div>')
    })

    $(document).on('click', '.remove-input', function(){
        $(this).closest('.input-block').remove();
    })



    $(function() {
        var resultCollector = Quagga.ResultCollector.create({
            capture: true,
            capacity: 20,
            blacklist: [{
                code: "WIWV8ETQZ1", format: "code_93"
            }, {
                code: "EH3C-%GU23RK3", format: "code_93"
            }, {
                code: "O308SIHQOXN5SA/PJ", format: "code_93"
            }, {
                code: "DG7Q$TV8JQ/EN", format: "code_93"
            }, {
                code: "VOFD1DB5A.1F6QU", format: "code_93"
            }, {
                code: "4SO64P4X8 U4YUU1T-", format: "code_93"
            }],
            filter: function(codeResult) {
                // only store results which match this constraint
                // e.g.: codeResult
                return true;
            }
        });
        var App = {
            init: function() {
                var self = this;

                Quagga.init(this.state, function(err) {
                    if (err) {
                        return self.handleError(err);
                    }
                    //Quagga.registerResultCollector(resultCollector);
                    App.attachListeners();
                    App.checkCapabilities();
                    Quagga.start();
                });
            },
            handleError: function(err) {
                console.log(err);
            },
            checkCapabilities: function() {
                var track = Quagga.CameraAccess.getActiveTrack();
                var capabilities = {};
                if (typeof track.getCapabilities === 'function') {
                    capabilities = track.getCapabilities();
                }
                this.applySettingsVisibility('zoom', capabilities.zoom);
                this.applySettingsVisibility('torch', capabilities.torch);
            },
            updateOptionsForMediaRange: function(node, range) {
                console.log('updateOptionsForMediaRange', node, range);
                var NUM_STEPS = 6;
                var stepSize = (range.max - range.min) / NUM_STEPS;
                var option;
                var value;
                while (node.firstChild) {
                    node.removeChild(node.firstChild);
                }
                for (var i = 0; i <= NUM_STEPS; i++) {
                    value = range.min + (stepSize * i);
                    option = document.createElement('option');
                    option.value = value;
                    option.innerHTML = value;
                    node.appendChild(option);
                }
            },
            applySettingsVisibility: function(setting, capability) {
                // depending on type of capability
                if (typeof capability === 'boolean') {
                    var node = document.querySelector('input[name="settings_' + setting + '"]');
                    if (node) {
                        node.parentNode.style.display = capability ? 'block' : 'none';
                    }
                    return;
                }
                if (window.MediaSettingsRange && capability instanceof window.MediaSettingsRange) {
                    var node = document.querySelector('select[name="settings_' + setting + '"]');
                    if (node) {
                        this.updateOptionsForMediaRange(node, capability);
                        node.parentNode.style.display = 'block';
                    }
                    return;
                }
            },
            initCameraSelection: function(){
                var streamLabel = Quagga.CameraAccess.getActiveStreamLabel();

                return Quagga.CameraAccess.enumerateVideoDevices()
                    .then(function(devices) {
                        function pruneText(text) {
                            return text.length > 30 ? text.substr(0, 30) : text;
                        }
                        var $deviceSelection = document.getElementById("deviceSelection");
                        while ($deviceSelection.firstChild) {
                            $deviceSelection.removeChild($deviceSelection.firstChild);
                        }
                        devices.forEach(function(device) {
                            var $option = document.createElement("option");
                            $option.value = device.deviceId || device.id;
                            $option.appendChild(document.createTextNode(pruneText(device.label || device.deviceId || device.id)));
                            $option.selected = streamLabel === device.label;
                            $deviceSelection.appendChild($option);
                        });
                    });
            },
            attachListeners: function() {
                var self = this;

                self.initCameraSelection();
                $(".controls").on("click", "button.stop", function(e) {
                    e.preventDefault();
                    Quagga.stop();
                    self._printCollectedResults();
                });

                $(".controls .reader-config-group").on("change", "input, select", function(e) {
                    e.preventDefault();
                    var $target = $(e.target),
                        value = $target.attr("type") === "checkbox" ? $target.prop("checked") : $target.val(),
                        name = $target.attr("name"),
                        state = self._convertNameToState(name);

                    console.log("Value of "+ state + " changed to " + value);
                    self.setState(state, value);
                });
            },
            _printCollectedResults: function() {
                var results = resultCollector.getResults(),
                    $ul = $("#result_strip ul.collector");

                results.forEach(function(result) {
                    var $li = $('<li><div class="thumbnail"><div class="imgWrapper"><img /></div><div class="caption"><h4 class="code"></h4></div></div></li>');

                    $li.find("img").attr("src", result.frame);
                    $li.find("h4.code").html(result.codeResult.code + " (" + result.codeResult.format + ")");
                    $ul.prepend($li);
                });
            },
            _accessByPath: function(obj, path, val) {
                var parts = path.split('.'),
                    depth = parts.length,
                    setter = (typeof val !== "undefined") ? true : false;

                return parts.reduce(function(o, key, i) {
                    if (setter && (i + 1) === depth) {
                        if (typeof o[key] === "object" && typeof val === "object") {
                            Object.assign(o[key], val);
                        } else {
                            o[key] = val;
                        }
                    }
                    return key in o ? o[key] : {};
                }, obj);
            },
            _convertNameToState: function(name) {
                return name.replace("_", ".").split("-").reduce(function(result, value) {
                    return result + value.charAt(0).toUpperCase() + value.substring(1);
                });
            },
            detachListeners: function() {
                $(".controls").off("click", "button.stop");
                $(".controls .reader-config-group").off("change", "input, select");
            },
            applySetting: function(setting, value) {
                var track = Quagga.CameraAccess.getActiveTrack();
                if (track && typeof track.getCapabilities === 'function') {
                    switch (setting) {
                        case 'zoom':
                            return track.applyConstraints({advanced: [{zoom: parseFloat(value)}]});
                        case 'torch':
                            return track.applyConstraints({advanced: [{torch: !!value}]});
                    }
                }
            },
            setState: function(path, value) {
                var self = this;

                if (typeof self._accessByPath(self.inputMapper, path) === "function") {
                    value = self._accessByPath(self.inputMapper, path)(value);
                }

                if (path.startsWith('settings.')) {
                    var setting = path.substring(9);
                    return self.applySetting(setting, value);
                }
                self._accessByPath(self.state, path, value);

                console.log(JSON.stringify(self.state));
                App.detachListeners();
                Quagga.stop();
                App.init();
            },
            inputMapper: {
                inputStream: {
                    constraints: function(value){
                        if (/^(\d+)x(\d+)$/.test(value)) {
                            var values = value.split('x');
                            return {
                                width: {min: parseInt(values[0])},
                                height: {min: parseInt(values[1])}
                            };
                        }
                        return {
                            deviceId: value
                        };
                    }
                },
                numOfWorkers: function(value) {
                    return parseInt(value);
                },
                decoder: {
                    readers: function(value) {
                        if (value === 'ean_extended') {
                            return [{
                                format: "ean_reader",
                                config: {
                                    supplements: [
                                        'ean_5_reader', 'ean_2_reader'
                                    ]
                                }
                            }];
                        }
                        return [{
                            format: value + "_reader",
                            config: {}
                        }];
                    }
                }
            },
            state: {
                inputStream: {
                    type : "LiveStream",
                    constraints: {
                        width: {min: 640},
                        height: {min: 480},
                        facingMode: "environment",
                        aspectRatio: {min: 1, max: 2}
                    }
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: 2,
                frequency: 10,
                decoder: {
                    readers : [{
                        format: "code_128_reader",
                        config: {}
                    }]
                },
                locate: true
            },
            lastResult : null
        };

        App.init();

        Quagga.onProcessed(function(result) {
            var drawingCtx = Quagga.canvas.ctx.overlay,
                drawingCanvas = Quagga.canvas.dom.overlay;

            if (result) {
                if (result.boxes) {
                    drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                    result.boxes.filter(function (box) {
                        return box !== result.box;
                    }).forEach(function (box) {
                        Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
                    });
                }

                if (result.box) {
                    Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
                }

                if (result.codeResult && result.codeResult.code) {
                    Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
                }
            }
        });

        Quagga.onDetected(function(result) {
            var code = result.codeResult.code;

            $(document).find('.input-block:last-child input').val(code);
        });

    });

/*

    $(document).on('submit', '.ajax-form',function (e) {
        e.preventDefault();

        if($('[name="shipment_id"]').val() == '') {
            alert('Selecione qual o serviço.');
            return false;
        }

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
*/
</script>