<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ 'Etiquetas - '.$tracking }}</title>
    <link rel="stylesheet" href="{{ public_path('/vendor/admin-lte/bootstrap/css/bootstrap.min.css') }}" media="print"/>
    <link rel="stylesheet" href="{{ public_path('/assets/admin/css/helper.css') }}" media="print" />
    <link rel="stylesheet" href="{{ public_path('/assets/admin/css/pdf/adhesive_labels.css') }}" media="print" />

</head>
<body>
<?php setlocale(LC_ALL, 'pt_PT'); ?>

<div class="adhesive-label">
    @include('admin.printer.shipments.labels.label_vasp')
</div>
<?php setlocale(LC_ALL, ''); ?>
</body>
</html>