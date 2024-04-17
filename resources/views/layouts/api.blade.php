<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <title>API Documentation</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ENOVO">
    <link rel="shortcut icon" type="image/png" href="{{ asset('/favicon.png') }}"/>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    {!! Html::style('/vendor/prettydocs/plugins/bootstrap/css/bootstrap.min.css') !!}
    {!! Html::style('/vendor/prettydocs/plugins/prism/prism.css') !!}
    {!! Html::style('/vendor/prettydocs/plugins/elegant_font/css/style.css') !!}
    {!! Html::style('/vendor/prettydocs/css/styles.css') !!}
    {!! Html::style('/assets/css/helper.css') !!}
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="body-orange">
    <style>
        .body-orange .doc-menu > li.active > a {
            color: {{ env('APP_MAIL_COLOR_PRIMARY') }};
            border-color: {{ env('APP_MAIL_COLOR_PRIMARY') }};
        }

        .body-orange .doc-sub-menu > li > a:hover,
        .body-orange .doc-sub-menu > li.active > a {
            color: {{ env('APP_MAIL_COLOR_PRIMARY') }};
        }

        .body-orange .promo-block {
            background: {{ env('APP_MAIL_COLOR_PRIMARY') }};
        }

        .btn-orange, a.btn-orange {
            background: {{ env('APP_MAIL_COLOR_PRIMARY') }};
            border: 1px solid {{ env('APP_MAIL_COLOR_PRIMARY') }};
            color: #fff !important;
        }
    </style>
    <div class="page-wrapper">
        @include('api.partials.header')
        <div class="doc-wrapper">
            <div class="container">
                @yield('content')
            </div>
        </div>
        @include('api.partials.promo_block')
    </div>
    @include('api.partials.footer')
    {!! Minify::javascript([
                '/vendor/prettydocs/plugins/jquery-1.12.3.min.js',
                '/vendor/prettydocs/plugins/bootstrap/js/bootstrap.min.js',
                '/vendor/prettydocs/plugins/prism/prism.js',
                '/vendor/prettydocs/plugins/jquery-scrollTo/jquery.scrollTo.min.js',
                '/vendor/prettydocs/plugins/jquery-match-height/jquery.matchHeight-min.js',
                '/vendor/prettydocs/js/main.js',
            ])->withFullUrl()
    !!}
    </body>
</html>

