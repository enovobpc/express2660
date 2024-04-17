<?php
    $bgExists = File::exists(public_path() . '/uploads/pdf/bg_h.png');
?>
<!DOCTYPE html>
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
                margin-header: 0mm;
                header: html_pageHeader;
                footer: html_pageFooter;
                page-break-after:always;
                background: url("/uploads/pdf/bg_h.png") no-repeat;
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

       {{-- @if(File::exists(public_path() . '/uploads/pdf/bg_v.png'))
            <htmlpageheader name="pageHeader" class="header">
                @if($documentTitle)
                <h1 class="text-uppercase text-right document-title line-height-1p4">
                     <b>{{ $documentTitle }}</b><br/>
                     <small class="bigger-150">{{ @$documentSubtitle }}</small>
                 </h1>
                @endif
            </htmlpageheader>
            @include($view)
            <htmlpagefooter name="pageFooter" class="header"></htmlpagefooter>
        @else
            <htmlpageheader name="pageHeader" class="header">
                <div style="background: {{ env('APP_MAIL_COLOR_PRIMARY') }}; height: 5mm;"></div>
                @if(File::exists(public_path() . '/assets/img/logo/logo_sm.png'))
                <img src="{{ asset('assets/img/logo/logo_sm.png') }}" style="float: left; margin: 15px 0 0 30px; max-width: 220px; max-height: 40px"/>
                @else
                <img src="{{ asset('assets/img/default/logo/logo_sm.png') }}" style="float: left; margin: 15px 0 0 30px; max-width: 220px; max-height: 40px"/>
                @endif
                @if($documentTitle)
                    <h1 class="text-uppercase text-right document-title line-height-1p4" style="margin-right: 8mm; margin-top: -3mm">
                        <b>{{ $documentTitle }}</b><br/>
                        <small class="bigger-150">{{ @$documentSubtitle }}</small>
                    </h1>
                @endif
            </htmlpageheader>
            <div style="margin: 8mm">
                @include($view)
            </div>
            <htmlpagefooter name="pageFooter" class="header"></htmlpagefooter>
        @endif--}}

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