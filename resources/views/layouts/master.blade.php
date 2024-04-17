<!DOCTYPE html>
<html lang="{{ Lang::locale() }}">
    <head>
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-1QMQTXZGSP"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
        
          gtag('config', 'G-1QMQTXZGSP');
        </script>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image/png" href="{{ asset('/favicon.png') }}"/>
        <title>@yield('title')2660 Express - Logistica e Distribuição B2B & B2C</title>
        <meta http-equiv="content-language" content="{{ Lang::locale() }}">
        <meta name="author" content="ENOVO">
        <meta name="og:url" content="{{ Request::fullUrl() }}">
        <meta name="google-site-verification" content="W6-Zc0Y1f9C-Y6PlOqoDNjgjOpe3q_khPDqhMBjiYFc" />
        @yield('metatags')

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        {{-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet"> --}}
        {{ Html::style('/assets/admin/fonts/exo_2.css') }}
        {{ Html::style('/vendor/font-awesome/css/all.min.css') }}
        {{ Html::style('/vendor/flag-icon-css/css/flag-icon.min.css') }}
        {{ Html::style('/vendor/bootstrap/dist/css/bootstrap.min.css') }}
        {{ Html::style('/vendor/iCheck/skins/minimal/yellow.css') }}

    <!-- Vendor CSS Files -->
        <link href="{{ asset('assets/website/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/website/vendor/icofont/icofont.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/website/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/website/vendor/animate.css/animate.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/website/vendor/venobox/venobox.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/website/vendor/owl.carousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/website/vendor/aos/aos.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/website/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/bootstrap-sweetalert/dist/sweetalert.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
        
        
        <link rel="stylesheet" href="jquery-ui.min.css">
        <script src="external/jquery/jquery.js"></script>
        <script src="jquery-ui.min.js"></script>
        
        <!-- Template Main CSS File -->
        <link href="{{ asset('assets/website/css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/website/css/helper.css') }}" rel="stylesheet">

        @yield('styles')
    </head>
    <body class="{{ @$bodyClass }}">
        <div class="wrapper content">
            @include('website.partials.header')
            @include('website.partials.seo')
            @yield('content')
            <div class="push"></div>
        </div>
        @include('website.partials.footer')

{{--        @if(!@$_COOKIE['modalvideo'])
        @include('website.partials.video_popup')
        @endif--}}

        {!! Minify::javascript([
            '/vendor/jQuery/jquery-3.4.0.min.js',
            '/vendor/bootstrap/dist/js/bootstrap.min.js',
            '/vendor/datatables/jquery.dataTables.min.js',
            '/vendor/datatables/dataTables.bootstrap.min.js',
            '/vendor/pace/pace.min.js',
            '/vendor/bootstrap-growl/jquery.bootstrap-growl.min.js',
            '/vendor/iCheck/icheck.min.js',
            '/vendor/select2/dist/js/select2.min.js',
            '/vendor/select2/dist/js/i18n/pt.js',
            '/vendor/select2-multiple/select2-multiple.js',
            '/vendor/magicsuggest/magicsuggest-min.js',
            '/vendor/datepicker/bootstrap-datepicker.js',
            '/vendor/datepicker/locales/bootstrap-datepicker.pt.js',
            '/vendor/bootbox/bootbox.js',
            '/vendor/jasny-bootstrap/js/fileinput.js',
            '/vendor/jquery-ujs/src/rails.js',
            '/vendor/moment/moment.min.js',
            '/vendor/push.js/bin/push.js',
            '/vendor/pusher/pusher.min.js',
            '/vendor/js-cookie/src/js.cookie.js',
            '/vendor/jquery-mask-plugin/dist/jquery.mask.js',
            '/vendor/jsvat/jsvat.js',

            '/assets/admin/js/helper.js',
            '/assets/admin/js/validator.js',

            '/assets/website/js/helper.js',
            '/assets/website/js/app.js',
            '/assets/js/account.js',

            //load json files
            '/assets/admin/json/zipcodes-regex.js'

            ])->withFullUrl()
        !!}

        {{--<script src="{{ asset('assets/website/vendor/jquery/jquery.min.js') }}"></script>--}}
        <script src="{{ asset('assets/website/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/website/vendor/jquery.easing/jquery.easing.min.js') }}"></script>
        <script src="{{ asset('assets/website/vendor/jquery-sticky/jquery.sticky.js') }}"></script>
        <script src="{{ asset('assets/website/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
        <script src="{{ asset('assets/website/vendor/venobox/venobox.min.js') }}"></script>
        <script src="{{ asset('assets/website/vendor/waypoints/jquery.waypoints.min.js') }}"></script>
        <script src="{{ asset('assets/website/vendor/owl.carousel/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('assets/website/vendor/aos/aos.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap-sweetalert/dist/sweetalert.min.js') }}"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&callback=initMap&libraries=&v=weekly"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

        <!-- Template Main JS File -->
        <script src="{{ asset('assets/website/js/main.js') }}" async></script>

        <script>

            {{--@if(!@$_COOKIE['modalvideo'])
            $('#modal-video').modal('show');
            @endif

            $('.close-modal-video').on('click', function () {
                $('')
            })--}}

            $("#menu-{{ @$menuOption }}").addClass('active');
            $("#menu-{{ @$sidebarActiveOption }}").addClass('active');
            $('a[href="#tab-{{ Request::get("tab") }}"]').trigger('click');
        </script>
        <script>
            $(document).ready(function(){
                @if (Session::has('success'))
                $.bootstrapGrowl("<i class='fas fa-check'></i> {{ Session::get('success') }}&nbsp;&nbsp;", {type: 'success', align: 'center', width: 'auto', delay: 8000});
                @endif

                @if (Session::has('error'))
                $.bootstrapGrowl("<i class='fas fa-exclamation-circle'></i> {{ Session::get('error') }}&nbsp;&nbsp;", {type: 'error', align: 'center', width: 'auto', delay: 8000});
                @endif

                @if (Session::has('warning'))
                $.bootstrapGrowl("<i class='fas fa-exclamation-triangle'></i> {{ Session::get('warning') }}&nbsp;&nbsp;", {type: 'warning', align: 'center', width: 'auto', delay: 8000});
                @endif

                @if (Session::has('info'))
                $.bootstrapGrowl("<i class='fas fa-info-circle'></i> {{ Session::get('info') }}&nbsp;&nbsp;", {type: 'info', align: 'center', width: 'auto', delay: 8000});
                @endif
            })
        </script>
        <script>
            $(function() {
                    $(window).on("scroll", function() {
                        if($(window).scrollTop() > 50) {
                            $(".nav").addClass("scroll");
                            $(".logo_nav").attr("src","/assets/img/logo/logo_sm.png");                       

                        } else {
                            //remove the background property so it comes transparent again (defined in your css)
                            $(".nav").removeClass("scroll");
                            $(".logo_nav").attr("src","/assets/img/logo/logo_sm.png");                        

                            if ($("#navbarNav").hasClass("show")) {
                                $(".navbar-short").parent().parent().addClass("scroll");
                                $(".logo_nav").attr("src","/assets/img/logo/logo_sm.png");
                            }
                        }
                    });
                });
        </script>
        
        @yield('scripts')
        @include('website.partials.analytics')
    </body>
</html>