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
    {{--@if($shipment->recipient_country == 'es')
        @include('admin.printer.shipments.labels.label_ctt_es')
    @else--}}
        @include('admin.printer.shipments.labels.label_ctt')
    {{--@endif--}}
</div>
<?php setlocale(LC_ALL, ''); ?>
</body>
</html>