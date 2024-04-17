<?php

return [

    'currency' => [
        'EUR'     => 'EUR - Euro',
    ],

    'charge-codes' => [
        'PP' => 'PP - Prepaid',
        'CC' => 'CC - Consignee Charges'
    ],

    'unities' => [
        'K' => 'KG',
        'U' => 'UN'
    ],

    'rate_classes' => [
        'M' => 'M - Minimo (fixo)', //o que aparece na tarifa é igual ao total
        'N' => 'N - Normal (<45kg)',
        'Q' => 'Q - Quantity (>45Kg)',
        'C' => 'C - Merc. Especifica',
        'S' => 'S - Search Charge (sobretaxa)',
        'A' => 'A - As Agreed'
    ],

    'customs_status' => [
        'C'   => 'C',
        'T1'  => 'T1',
        'TD'  => 'TD',
        'X'   => 'X',
        'T2L' =>  'T2L',
        'TF'  => 'TF'
    ],

    'expenses' => [
        'types' => [
            'agent'   => 'A - Agente',
            'carrier' => 'C - Transportador',
            'other'   => 'O - Outros Encargos'
        ]
    ],
];
