<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>{{ 'Etiquetas Localização '.$location->code }}</title>
        <link rel="stylesheet" href="{{ asset('/vendor/admin-lte/bootstrap/css/bootstrap.min.css') }}" media="print"/>
        <link rel="stylesheet" href="{{ asset('/assets/admin/css/helper.css') }}" media="print" />
        <link rel="stylesheet" href="{{ asset('/assets/admin/css/pdf/adhesive_labels.css') }}" media="print" />
      
    </head>
    <body>
        <?php setlocale(LC_ALL, 'pt_PT'); ?>
        @include($view)
        <?php setlocale(LC_ALL, ''); ?>
    </body>
</html>