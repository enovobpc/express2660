<?php
$bgExists = File::exists(public_path() . '/uploads/pdf/bg_v.png');
?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $documentTitle }}</title>
    <link rel="stylesheet" href="{{ asset('/vendor/bootstrap/dist/css/bootstrap.min.css') }}" media="print"/>
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/helper.css') }}" media="print" />
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/pdf/main.css') }}" media="print" />
    <style>
        @if($bgExists)
@page {
            size: auto;
            margin-footer: 0;
            margin-header: 7mm;
            header: html_pageHeader;
            footer: html_pageFooter;
            page-break-after:always;
            background: url("/uploads/pdf/bg_v.png") no-repeat;
            background-image-resize: 6;
        }
        @else
@page {
            size: auto;
            margin-left: 0;
            margin-right: 0;
            margin-footer: 0;
            margin-header: 0;
            page-break-after:always;
            header: html_pageHeader;
            footer: html_pageFooter;
        }
        @endif
    </style>
</head>
<body>
<?php setlocale(LC_ALL, 'pt_PT'); ?>
@include('admin.layouts.pdf.header')

@if($bgExists)
    @include($view)
@else
    <div style="margin: 8mm">
        @include($view)
    </div>
@endif

<htmlpagefooter name="pageFooter" class="header"></htmlpagefooter>
<?php setlocale(LC_ALL, ''); ?>
</body>
</html>