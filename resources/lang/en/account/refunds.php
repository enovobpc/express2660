<?php

return [
    'title' => 'Refunds',

    'word' => [
        'confirm-selected' => 'Confirm selected',
        'waiting-reception'  => 'Pending',
        'waiting-devolution' => 'Awaiting refund',
        'payment-method' => 'Payment Method',
        'payment-date' => 'Payment Date',
        'customer' => 'Customer Confirmed Return',
        'customer_agency' => 'Returned by the Agency to the Client',
        'agency' => 'Received at the Agency'
    ],

    'filters' => [
        'status' => [
            '' => 'All',
            '1' => 'Pending',
            '2' => 'Awaiting refund',
            '3' => 'Refunded',
        ],
        'confirmed' => [
            '' => 'All',
            '1' => 'Confirmed',
            '0' => 'Unconfirmed'
        ],
        'request-status' => [
            '' => 'All',
          /*  '1' => 'Possível Reembolsar',
            '2' => 'Aguarda Devolução',
            '3' => 'Reembolado',*/
            '1' => 'Pending',
            '4' => 'Refund Available',
            '5' => 'Requested',
            '3' => 'Refunded',
        ],
    ],

    'confirm' => [
        'alert' => [
            'title' => 'You have :total refunds waiting to confirm that you received the amount.',
            'message' => 'You can select several shipments and mark them as confirmed, or click on the <i class="fas fa-times-circle"></i> symbol in each shipment.',
        ],
        'modal' => [
            'confirm' => [
                'title'   => 'Confirm Refund Receipt',
                'message' => 'Do you want to cancel confirmation of receipt of this refund amount?',
                'label'   => 'Undo Confirmation',
            ],
            'unconfirm' => [
                'title'   => 'Confirm Refund Receipt',
                'message' => 'Do you confirm receipt of the amount of this refund?',
                'label'   => 'Confirm',
            ]
        ],
        'selected' => [
            'title'   => 'Confirm Refund Receipt',
            'message' => 'Do you confirm receipt of the selected refund amount?',
            'label'   => 'Confirm',

        ]
    ],

    'request' => [
        'modal' => [
            'title' => 'Refund request',
            'message' => 'Select how you want the refund to be made to you.'
        ],
        'methods' => [
            'transfer' => 'Transfer',
            'money' => 'Money',
            'check' => 'Check'
        ]
    ],

    'feedback' => [
        'confirm' => [
            'success' => 'Refunds confirmed successfully.'
        ]
    ]

];
