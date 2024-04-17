<?php

return [

    'locations' => [
        'status' => [
            'free' => 'Livre',
            'filled' => 'Preenchida',
            'partially_filled' => 'Parcialmente Preenchida',
            'inactive' => 'Inativa'
        ],

        'status-labels' => [
            'free' => 'success',
            'filled' => 'danger',
            'partially_filled' => 'warning',
            'inactive' => 'default'
        ],
    ],

    'products' => [
        'status' => [
            'available' => 'Disponível',
            'lowstock'  => 'Stock Reduzido',
            'outstock'  => 'Sem Stock',
            'blocked'   => 'Bloqueado'
        ]
    ],

    'history' => [
        'actions' => [
            'add'        => 'Adicionado',
            'reception'  => 'Recepção',
            'transfer'   => 'Transferência',
            'order_out'  => 'Ordem Saída',
            'devolution' => 'Devolução',
            'adjustment' => 'Ajuste Stock',
            'inventory'  => 'Inventário'
        ],

        'actions-labels' => [
            'add'        => 'label-success',
            'reception'  => 'label-success',
            'transfer'   => 'label-info',
            'order_out'  => 'label-danger',
            'devolution' => 'label-warning',
            'devolution' => 'label-warning',
            'adjustment' => 'bg-purple',
            'inventory'  => 'bg-purple'
        ],
    ],
];
