<?php

return [
    'title' => 'Reembolsos',

    'word' => [
        'confirm-selected' => 'Confirmar selecionados',
        'waiting-reception'  => 'Aguarda recebimento',
        'waiting-devolution' => 'Aguarda devolução',
        'payment-method' => 'Forma Pagamento',
        'payment-date' => 'Data Pagamento',
        'customer' => 'Devolução Confirmada pelo Cliente',
        'customer_agency' => 'Devolvido pela Agência ao Cliente',
        'agency' => 'Recebido na Agência'
    ],

    'filters' => [
        'status' => [
            '' => 'Todos',
            '1' => 'Pendente',
            '2' => 'Aguarda Devolução',
            '3' => 'Entregue',
        ],
        'confirmed' => [
            '' => 'Todos',
            '1' => 'Confirmado',
            '0' => 'Por confirmar'
        ],
        'request-status' => [
            '' => 'Todos',
          /*  '1' => 'Possível Reembolsar',
            '2' => 'Aguarda Devolução',
            '3' => 'Reembolado',*/
            '1' => 'Pendente',
            '4' => 'Disponível Reembolsar',
            '5' => 'Solicitado',
            '3' => 'Reembolsado',
        ],
    ],

    'confirm' => [
        'alert' => [
            'title' => 'Tem :total reembolsos a aguardar que confirme se recebeu o valor.',
            'message' => 'Pode selecionar vários envios e marca-los como confirmados, ou clique sobre o símbolo <i class="fas fa-times-circle"></i> em cada envio.',
        ],
        'modal' => [
            'confirm' => [
                'title'   => 'Confirmar Recepção do Reembolso',
                'message' => 'Pretende anular a confirmação da recepção do valor deste reembolso?',
                'label'   => 'Anular Confirmação',
            ],
            'unconfirm' => [
                'title'   => 'Confirmar Recepção do Reembolso',
                'message' => 'Confirma a recepção do valor deste reembolso?',
                'label'   => 'Confirmar',
            ]
        ],
        'selected' => [
            'title'   => 'Confirmar Recepção do Reembolso',
            'message' => 'Confirma a recepção do valor dos reembolsos selecionados?',
            'label'   => 'Confirmar',

        ]
    ],

    'request' => [
        'modal' => [
            'title' => 'Solicitar reembolso',
            'message' => 'Selecione a forma como pretende que o reembolso lhe seja feito.'
        ],
        'methods' => [
            'transfer' => 'Transferência',
            'money' => 'Numerário',
            'check' => 'Cheque'
        ]
    ],

    'feedback' => [
        'confirm' => [
            'success' => 'Reembolsos confirmados com sucesso.'
        ]
    ]

];
