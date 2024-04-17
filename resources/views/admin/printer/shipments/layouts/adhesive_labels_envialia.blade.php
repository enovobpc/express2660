<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ 'Etiquetas - '.@$trackingCode }}</title>
    <link rel="stylesheet" href="{{ public_path('/vendor/admin-lte/bootstrap/css/bootstrap.min.css') }}" media="print"/>
    <link rel="stylesheet" href="{{ public_path('/assets/admin/css/helper.css') }}" media="print" />
    <link rel="stylesheet" href="{{ public_path('/assets/admin/css/pdf/adhesive_labels.css') }}" media="print" />

</head>
<body>
<?php
setlocale(LC_ALL, 'pt_PT');
?>
<img src="data:image/png;base64,{{ $path }}"/>
<div class="adhesive-label">

    @if($customLogo)
        <div class="adhesive-row" style="margin-top: 0">
            <div class="adhesive-block" style="width: 65mm; height: 10mm; margin-left: 2mm">
                @if(File::exists(public_path(env('APP_LOGO_SM_BLACK', env('APP_LOGO_SM')))))
                <img src="{{ public_path(env('APP_LOGO_SM_BLACK', env('APP_LOGO_SM'))) }}" style="height: 35px;" class="m-t-0"/>
                @endif
            </div>
            <div class="adhesive-block" style="width: 65mm; height: 15mm; margin-top: 2mm">
                <div class="text-right">
                    <span class="fs-10px bold">{{ $url }}</span><br/>
                </div>
            </div>
        </div>
    @endif
</div>
@if($customLogo)
    <div class="fs-7pt m-t-0 text-center bold" style="margin-top: -6mm; left: 0mm; position: absolute; bottom: 0mm; right: 0;">Processado por ENOVO TMS - Software para Transportes e Logística. <span>tms.enovo.pt</span>.</div>
@else
    <div class="fs-8pt m-t-0 text-center bold" style="margin-top: -7mm; left: 0mm; position: absolute; bottom: 0mm; right: 0;">Processado por ENOVO TMS - Software para Transportes e Logística. <span>tms.enovo.pt</span>.</div>
@endif
<?php setlocale(LC_ALL, ''); ?>
</body>
</html>