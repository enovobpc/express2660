<?php

return [
    'title' => 'Rastreio de Frete',

    'form' => [
        'button' => 'Procurar frete',
        'label' => 'Código(s) do frete',
        'tip' => 'Pode procurar por vários códigos em simultâneo. Separe cada código por vírgula.'
    ],

    'index' => [
        'title' => 'Escreva na caixa abaixo o código do frete a procurar.',
        'subtitle' => 'Se tiver mais do que um código, pode separa-los por vírgula.'
    ],

    'empty' => [
        'title' => 'Não foi encontrado nenhum frete com o código <b>:trk</b>',
        'msg' => 'Verifique se escreveu o código corretamente.'
    ],

    'word' => [
        'consult-pod' => 'Consultar Comprovante Entrega'
    ],

    'progress' => [
        'pending' => 'Registrado',
        'accepted' => 'Aceite',
        'pickup' => 'Em coleta',
        'transit' => 'Em transito',
        'delivered' => 'Entregue',
        'incidence' => 'Ocorrência',
        'returned' => 'Devolvido',
        'canceled' => 'Anulado'
    ]
];
