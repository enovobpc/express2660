<?php

return [
    'title' => 'Seguimento de envio',

    'form' => [
        'button' => 'Procurar envio',
        'label' => 'Código(s) do Envio',
        'tip' => 'Pode procurar por vários códigos em simultâneo. Separe cada código por vírgula.'
    ],

    'index' => [
        'title' => 'Escreva na caixa abaixo o código do envio a procurar.',
        'subtitle' => 'Se tiver mais do que um código, pode separa-los por vírgula.'
    ],

    'empty' => [
        'title' => 'Não foi encontrado nenhum envio com o código <b>:trk</b>',
        'msg' => 'Verifique se escreveu o código corretamente.'
    ],

    'word' => [
        'consult-pod' => 'Consultar Comprovativo Entrega'
    ],

    'progress' => [
        'pending' => 'Documentado',
        'accepted' => 'Aceite',
        'pickup' => 'Em recolha',
        'transit' => 'Em transito',
        'delivered' => 'Entregue',
        'incidence' => 'Incidência',
        'returned' => 'Devolvido',
        'canceled' => 'Anulado'
    ]
];
