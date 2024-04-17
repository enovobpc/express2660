<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $documentTitle }}</title>
    <link rel="stylesheet" href="{{ asset('/vendor/admin-lte/bootstrap/css/bootstrap.min.css') }}" media="print"/>
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/helper.css') }}" media="print" />
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/pdf/awb.css') }}" media="print" />
    <style>
        @page {
            size: auto;
            margin-footer: 0;
            margin-header: 7mm;
            header: html_pageHeader;
            footer: html_pageFooter;
            background-image-resize:6;
            font-family: 'Arial';
            background: url("/uploads/pdf/receipt.png") no-repeat;
        }
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