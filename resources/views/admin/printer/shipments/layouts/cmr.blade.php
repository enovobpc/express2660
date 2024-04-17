<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CMR - Declaração de Expedição Internacional</title>
    <link rel="stylesheet" href="{{ public_path('/vendor/admin-lte/bootstrap/css/bootstrap.min.css') }}" media="print"/>
    <link rel="stylesheet" href="{{ public_path('/assets/admin/css/helper.css') }}" media="print" />
    <link rel="stylesheet" href="{{ public_path('/assets/admin/css/pdf/main.css') }}" media="print" />
    <style>
        @if(File::exists(public_path('/uploads/pdf/cmr.png')))
         @page {
            size: auto;
            margin-footer: 0;
            margin-header: 0;
            header: html_pageHeader;
            footer: html_pageFooter;
            background-image-resize:6;
            font-family: 'Arial';
            background: url("/uploads/pdf/cmr.png") no-repeat
        }
        @else
        @page {
            size: auto;
            margin-footer: 0;
            margin-header: 0;
            header: html_pageHeader;
            footer: html_pageFooter;
            background-image-resize:6;
            font-family: 'Arial';
        }
        @endif

    </style>
</head>
<body>
<?php setlocale(LC_ALL, 'pt_PT'); ?>

    <htmlpageheader name="pageHeader" class="header"></htmlpageheader>
    @include($view)
    <htmlpagefooter name="pageFooter" class="header"></htmlpagefooter>
<?php setlocale(LC_ALL, ''); ?>
</body>
</html>