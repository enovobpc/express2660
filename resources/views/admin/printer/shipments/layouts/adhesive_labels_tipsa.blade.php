<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ 'Etiquetas - '.$shipment->tracking_code }}</title>
    <link rel="stylesheet" href="{{ public_path('/vendor/admin-lte/bootstrap/css/bootstrap.min.css') }}" media="print"/>
    <link rel="stylesheet" href="{{ public_path('/assets/admin/css/helper.css') }}" media="print" />
    <link rel="stylesheet" href="{{ public_path('/assets/admin/css/pdf/adhesive_labels.css') }}" media="print" />

</head>
<body>
<?php setlocale(LC_ALL, 'pt_PT'); ?>

<div class="adhesive-label">
    <img src="data:image/jpg;base64,{{ $path }}"/>
</div>
{{--@if($customLogo)
    <div class="font-size-7pt m-t-0 text-center bold" style="margin-top: -6mm; left: 0mm; position: absolute; bottom: 0mm; right: 0;">Processado por QUICKBOX - Software para Transportes e Logística. <span>www.quickbox.pt</span>.</div>
@else
    <div class="font-size-8pt m-t-0 text-center bold" style="margin-top: -7mm; left: 0mm; position: absolute; bottom: 0mm; right: 0;">Processado por QUICKBOX - Software para Transportes e Logística. <span>www.quickbox.pt</span>.</div>
@endif--}}
<?php setlocale(LC_ALL, ''); ?>
</body>
</html>