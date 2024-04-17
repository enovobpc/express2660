<?php

return [

    'objectives' => [
        '01' => 'Visita',
        '02' => 'Cobrar',
        '03' => 'Dar orçamento',
        '04' => 'Propôr negócio',
        '05' => 'Entregar material',
        '06' => 'Entregar campanha',
        '07' => 'Entregar tabela',
        '08' => 'Acompanhar obra',
        ],
    'occurrences' => [
        '05' => 'Visita Regular',
        '06' => 'Seguimento dos Orçamentos',
        '01' => 'Pediu orçamento',
        '02' => 'Pediu devolução',
        '03' => 'Fez reclamação ou queixa',
        '04' => 'Entreguei tabela',
    ],
    'charges' => [
        '01' => 'Nada a cobrar',
        '02' => 'Não apareceu',
        '03' => 'Não tinha forma',
        '04' => 'Pagou',
        '05' => 'Propôs datado na data...',
        '06' => 'Vai fazer transferência dia...',
        '07' => 'Quer que passe dia...',
    ],
    'durations' => [
        300   => '5 minutos',
        900   => '15 minutos',
        1800  => '30 minutos',
        2700  => '45 minutos',
        3600  => '1 hora',
        5400  => '1 hora e meia',
        7200  => '2 horas',
        9000  => '2 horas e meia',
        10800 => '3 horas',
    ],
    'places' => [
        1 => 'Nas instalações do cliente',
        2 => 'Nas nossas instalações',
        3 => 'Telefónicamente',
        4 => 'Café/Restaurante',
        5 => 'Outro',
    ],
    'status' => [
        'scheduled'   => 'Agendado',
        'rescheduled' => 'Re-agendado',
        'concluded'   => 'Realizado',
        'canceled'    => 'Cancelado'
    ],
    'status-labels' => [
        'scheduled'   => 'label-warning',
        'rescheduled' => 'label-info',
        'concluded'   => 'label-success',
        'canceled'    => 'label-danger'
    ],
];
