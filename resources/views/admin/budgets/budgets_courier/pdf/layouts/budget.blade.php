<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $documentTitle }}</title>
    <link rel="stylesheet" href="{{ asset('/vendor/admin-lte/bootstrap/css/bootstrap.min.css') }}" media="print"/>
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/helper.css') }}" media="print" />
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/pdf/awb.css') }}" media="print" />
    <style>
        @if(File::exists(public_path() . $background))
        @page {
            size: auto;
            margin-footer: 0;
            margin-header: 7mm;
            header: html_pageHeader;
            footer: html_pageFooter;
            background-image-resize:6;
            background: url("{{ $background }}") no-repeat;
            font-family: 'Arial';
        }
        @else
        @page {
            size: auto;
            margin-footer: 0;
            margin-header: 7mm;
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
<htmlpageheader name="pageHeader" class="header">
    <h4 style="float: right; text-align: right; font-weight: bold; font-size: 16px; margin-top: -120px">{{ trans('admin/budgets.'.$budget->type.'.title', [], 'messages', $budget->locale) }}</h4>
    <p style="float: right; text-align: right; font-size: 12px; font-weight: bold; line-height: 15px; margin-bottom: -30mm">
        {{ trans('admin/budgets.'.$budget->type.'.date', [], 'messages', $budget->locale) }}: {{ $budgetDate->format('d/m/Y') }}<br/>
        {{ trans('admin/budgets.'.$budget->type.'.validity', [], 'messages', $budget->locale) }}: {{ $validityDate->format('d/m/Y') }}<br/>
        {{ trans('admin/budgets.'.$budget->type.'.budget_no', [], 'messages', $budget->locale) }}: {{ $budget->budget_no }}
    </p>
</htmlpageheader>
@include($view)
<htmlpagefooter name="pageFooter" class="header"></htmlpagefooter>
<?php setlocale(LC_ALL, ''); ?>
</body>
</html>