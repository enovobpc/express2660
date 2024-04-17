<?php

return [
    'title' => 'Cotizador',

    'form' => [
        'groups' => [
            'sender'    => 'Origen',
            'recipient' => 'Destino',
            'type'      => 'Tipo Expédición',
            'packages'  => 'Bultos',
            'date'      => 'Data Recogida',
            'services'  => 'Servicios'
        ],
        'tips' => [
            'bultos' => 'Para obtener un precio más exacto es necesario indicar las dimensiones de cada bulto individual.',
            'vat'    => 'Precios con IVA',
            'empty-prices' => 'Sob Consulta',
            'label-required' => 'Requiere etiqueta',
            'allowed-cod' => 'Admite contra reembolso',
            'allowed-return' => 'Admite retorno',
            'service-info' => 'Información del servicio',
            'geral-info' => 'Información general'
        ]
    ],
    'results' => [
        'empty' => 'No hay servicios disponibles para la consulta seleccionada.',
        'pickup_hour' => 'Antes de las :hour',
        'delivery_hour' => 'Hasta las :hour'
    ]
];
