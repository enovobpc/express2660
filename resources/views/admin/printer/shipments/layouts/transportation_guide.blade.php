<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>{{ 'Guia de Transporte - '.$shipment->tracking_code }}</title>
        <link rel="stylesheet" href="{{ public_path('/vendor/bootstrap/dist/css/bootstrap.min.css') }}" media="print"/>
        <link rel="stylesheet" href="{{ public_path('/assets/admin/css/helper.css') }}" media="print" />
        <link rel="stylesheet" href="{{ public_path('/assets/admin/css/pdf/transportation_guide.css') }}" media="print" />
        <style>
            @page {
                margin: 0;
                size: auto;
                margin-footer: 0;
                margin-header: 0;
            }
        </style>
    </head>
    <body>
        <?php setlocale(LC_ALL, 'pt_PT'); ?>
        @include($view)
        <?php setlocale(LC_ALL, ''); ?>
    </body>
</html>