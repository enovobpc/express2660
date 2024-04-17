<?php

return [
    'title' => 'Facturación y extracto',

    'tabs' => [
        'invoices' => 'Cuenta corriente',
        'extracts' => 'Extractos Mensuales',
    ],

    'word' => [
        'price-avg' => 'Precio Medio por Envío',
        'weight-avg' => 'Peso Medio por Envío',
        'unpaid' => 'Por liquidar',
        'expired-docs' => 'Docs. Vencidos',
        'expired-days' => ':days días de mora',
        'customer-detail' => 'Resumen de faturación por cliente',
        'empty-billing' => 'Aún no se han emitido facturas.'
    ],

    'filters' => [
        'sense' => [
            '' => 'Todos',
            'debit' => 'Débitos',
            'credit' => 'Créditos'
        ],

        'paid' => [
            '' => 'Todos',
            '1' => 'Liquidado',
            '0' => 'Por Liquidar'
        ]
    ],

    'last-update' => [
        'hours' => 'Actualizado hace :time horas',
        'minutes' => 'Actualizado hace menos de 1 hora'
    ],

    'tip-archive' => 'Es presentada la información de los últimos meses. Si pretende información anterior, por favor, que la solicite junto al suporte al cliente.'

];
