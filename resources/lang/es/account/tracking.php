<?php

return [
    'title' => 'Seguimiento de envío',

    'form' => [
        'button' => 'Procurar envío',
        'label' => 'Código(s) del Envío',
        'tip' => 'Puede buscar por varios códigos en simultáneo. Separe cada código con una coma.'
    ],

    'index' => [
        'title' => 'Escriba en la caja de abajo el código del envío que busca.',
        'subtitle' => 'Si tiene más que un código, puede separarlos por una coma.'
    ],

    'empty' => [
        'title' => 'No ha sido encontrado ningún envío con el código <b>:trk</b>',
        'msg' => 'Compruebe si ha escrito el código correctamente.'
    ],

    'word' => [
        'consult-pod' => 'Consultar Prueba Entrega'
    ],

    'progress' => [
        'pending' => 'Pendiente',
        'accepted' => 'Aceptado',
        'pickup' => 'En recogida',
        'transit' => 'En transito',
        'delivered' => 'Entregado',
        'incidence' => 'Incidencia',
        'returned' => 'Devuelto',
        'canceled' => 'Anulado'
    ]
];
