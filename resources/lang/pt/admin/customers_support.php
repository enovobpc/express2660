<?php

return [

    'status' => [
        '01_pending'  => 'Registado',
        '03_analysis' => 'Em análise',
        '04_wainting-customer' => 'Respondido',
        '02_wainting' => 'Precisa Resposta',
        '98_rejected' => 'Rejeitado',
        '99_no-solution' => 'Sem Solução',
        '90_concluded' => 'Fechado'
    ],

    'status-labels' => [
        '01_pending'  => 'label-default',
        '03_analysis' => 'bg-purple',
        '04_wainting-customer' => 'bg-lime',
        '02_wainting' => 'label-warning',
        '98_rejected' => 'bg-red',
        '99_no-solution' => 'bg-red',
        '90_concluded' => 'label-success',
    ],

    'categories' => [
        'shipments'  => 'Envios',
        'billing'    => 'Faturação',
        'refunds'    => 'Reembolsos',
        'other'      => 'Outro',
        'complaint'  => 'Reclamação',
        'help'       => 'Pedido Ajuda',
        'info'       => 'Informação',
    ],

    'categories-labels' => [
        'shipments'  => 'info',
        'billing'    => 'warning',
        'refunds'    => 'success',
        'other'      => 'info',

        'complaint'  => 'danger',
        'help'       => 'warning',
        'info'       => 'info',
    ],

];
