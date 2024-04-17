<?php

return [
    'title' => 'Reembolsos',

    'word' => [
        'confirm-selected' => 'Confirmar seleccionados',
        'waiting-reception'  => 'Esperando Recepción',
        'waiting-devolution' => 'Esperando Devolución',
        'payment-method' => 'Forma Pago',
        'payment-date' => 'Fecha Pago',
        'customer' => 'Devolución Confirmada por el Cliente',
        'customer_agency' => 'Devuelto por la Agencia del Cliente',
        'agency' => 'Recebido en la Agencia'
    ],

    'filters' => [
        'status' => [
            '' => 'Todos',
            '1' => 'Pendiente',
            '2' => 'Esperando Devolución',
            '3' => 'Entregado',
        ],
        'confirmed' => [
            '' => 'Todos',
            '1' => 'Confirmado',
            '0' => 'Por confirmar'
        ],
        'request-status' => [
            '' => 'Todos',
          /*  '1' => 'Posible Reembolsar',
            '2' => 'Esperando Devolución',
            '3' => 'Reembolsado',*/
            '1' => 'Pendiente',
            '4' => 'Disponible Reembolsar',
            '5' => 'Solicitado',
            '3' => 'Reembolsado',
        ],
    ],

    'confirm' => [
        'alert' => [
            'title' => 'Tiene :total reembolsos para esperar que confirme si ha recibido el valor.',
            'message' => 'Puede seleccionar varios envíos y marcarlos como confirmados, o haga clic en el símbolo <i class="fas fa-times-circle"></i> en cada envío.',
        ],
        'modal' => [
            'confirm' => [
                'title'   => 'Confirmar Recepción del Reembolso',
                'message' => '¿Pretende anular la confirmación de la recepción del valor de este reembolso?',
                'label'   => 'Anular Confirmación',
            ],
            'unconfirm' => [
                'title'   => 'Confirmar Recepción del Reembolso',
                'message' => '¿Confirma la recepción del valor de este reembolso?',
                'label'   => 'Confirmar',
            ]
        ],
        'selected' => [
            'title'   => 'Confirmar Recepción del Reembolso',
            'message' => '¿Confirma la recepción del valor de los reembolsos seleccionados?',
            'label'   => 'Confirmar',

        ]
    ],

    'request' => [
        'modal' => [
            'title' => 'Solicitar reembolso',
            'message' => 'Seleccione la forma cómo pretende que el reembolso se haga.'
        ],
        'methods' => [
            'transfer' => 'Transferencia',
            'money' => 'Efectivo',
            'check' => 'Cheque'
        ]
    ],

    'feedback' => [
        'confirm' => [
            'success' => 'Reembolsos confirmados con éxito.'
        ]
    ]

];
