<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>API Docs - @yield('title')</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="ENOVO TMS API Documentation">
        <meta name="author" content="ENOVO TMS">
        <link rel="shortcut icon" type="image/png" href="{{ asset('/favicon.png') }}"/>
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet">
        <script defer src="{{ asset('/vendor/coderdocs/assets/fontawesome/js/all.min.js') }}"></script>
        <link id="theme-style" rel="stylesheet" href="{{ asset('/vendor/coderdocs/assets/css/theme.css') }}">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.2/styles/atom-one-dark.min.css">
        <style>

            header .btn:hover,
            header .btn:active,
            header .btn:focus {
                box-shadow: none !important;
                text-decoration: none;
                color: #ec1c25 !important;
            }

            header .btn-primary:hover,
            header .btn-primary:active,
            header .btn-primary:focus {
                color: #ffffff !important;
            }

            footer {
                background: #0d3354;
            }

            footer a.theme-link {
                color: #e3e3e3 !important;
                text-decoration: none !important;
            }

            footer a.theme-link:hover {
                opacity: 0.6 !important;
            }

            .card-method-post .card-header {
                background-color: #dff0d8;
                border-color: #d6e9c6;
            }

            .card-method-post .card-title {
                color: #3c763d;
            }

            .card-method-post .badge {
                background-color: #5cb85c;
            }


            .card-method-get .card-header {
                background-color: #d9edf7;
                border-color: #bce8f1;
            }

            .card-method-get .card-title {
                color: #31708f;
            }

            .card-method-get .badge {
                background-color: #5bc0de;
            }

            .card-method-delete .card-header {
                background-color: #f2dede;
                border-color: #ebccd1;
            }

            .card-method-delete .card-title {
                color: #a94442;
            }

            .card-method-delete .badge {
                background-color: #d9534f;
            }

            .btn-sm {
                padding: 5px 10px;
                font-weight: normal;
            }

            .btn-default {
                background: #e9e9e9;
                border: 1px solid #ccc;
                color: #444;
            }

            .docs-top-utilities {
                padding-top: 10px;
            }

            .docs-top-utilities .btn-default {
                background: transparent;
                border: none;
                color: #0d3354;
                font-weight: bold;
            }

            .badge-optional,
            .badge-required {
                font-size: 11px;
                padding: 4px 3px;
                border-radius: 4px;
                color: #777;
                border: 1px solid #ccc;
                background: #fff;
            }

            .badge-required {
                color: #f53e3e;
                border: 1px solid #eb3131;
            }

            .text-muted {
                color: #81868f !important;
            }

            .text-blue {
                color: #0a58ca !important;
            }

            .badge.badge-level {
                background: transparent;
                border: 1px solid #0d3354;
                color: #0d3354;
                padding: 3px 6px;
                font-weight: normal;
                font-size: 12px;
                position: absolute;
                top: 18px;
                margin-left: 4px;
            }

            .badge.badge-level.badge-partners {
                border: 1px solid #f30000;
                color: #fff;
                background: #f30000;
            }

            .badge.badge-level.badge-mobile {
                border: 1px solid #ffb100;
                color: #fff;
                background: #ffb100;
            }


            html, body {
                height: 100%;
                margin: 0;
            }
            .wrapper {
                min-height: 100%;

                /* Equal to height of footer */
                /* But also accounting for potential margin-bottom of last child */
                margin-bottom: -120px;
            }
            footer,
            .push {
                height: 120px;
            }

        </style>
    </head>

    <body>
        <div class="wrapper">
            @yield('content')
            <div class="push"></div>
        </div>
        @include('api.docs.partials.footer')

        <script src="{{ asset('/vendor/prettydocs/plugins/jquery-1.12.3.min.js') }}"></script>
        <script src="{{ asset('/vendor/coderdocs/assets/plugins/popper.min.js') }}"></script>
        <script src="{{ asset('/vendor/coderdocs/assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('/vendor/coderdocs/assets/plugins/smoothscroll.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/highlight.min.js"></script>
        <script src="{{ asset('/vendor/coderdocs/assets/js/highlight-custom.js') }}"></script>
        <script src="{{ asset('/vendor/coderdocs/assets/plugins/simplelightbox/simple-lightbox.min.js') }}"></script>
        <script src="{{ asset('/vendor/coderdocs/assets/plugins/gumshoe/gumshoe.polyfills.min.js') }}"></script>
        <script src="{{ asset('/vendor/coderdocs/assets/js/docs.js') }}"></script>
        <script>
            $('[data-toggle="collapse"]').on('click', function(){
                var targetId = $(this).data('target');
                $('.card-response').slideUp();

                if($(targetId).is(':visible')) {
                    $(targetId).slideUp();
                } else {
                    $(targetId).slideDown();
                }
            })
        </script>
    </body>
</html>


