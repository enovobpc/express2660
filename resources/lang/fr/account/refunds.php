<?php

return [
    'title' => 'Remboursement',

    'word' => [
        'confirm-selected' => 'Confirmer selections',
        'waiting-reception'  => 'Attente reçu',
        'waiting-devolution' => 'Attente retour',
        'payment-method' => 'Methode de Paiement',
        'payment-date' => 'Date de paiement',
        'customer' => "Retourné à l'expéditeur",
        'customer_agency' => "Retourné à l'expéditeur",
        'agency' => 'Reçu en agence'
    ],

    'filters' => [
        'status' => [
            '' => 'Tous',
            '1' => 'En attente',
            '2' => 'Attente retour',
            '3' => 'Livré',
        ],
        'confirmed' => [
            '' => 'Tous',
            '1' => 'Confirmé',
            '0' => 'A confirmer'
        ],
        'request-status' => [
            '' => 'Tous',
          /*  '1' => 'Remboursement Possible',
            '2' => 'Attente retour',
            '3' => 'Remboursé',*/
            '1' => 'En attente',
            '4' => 'Remboursement disponible',
            '5' => 'Demandé',
            '3' => 'Remboursé',
        ],
    ],

    'confirm' => [
        'alert' => [
            'title' => 'Tem :total remboursements en attente que vous confirmiez avoir reçu le montant.',
            'message' => 'Vous pouvez sélectionner plusieurs envois et les marquer comme confirmés, ou cliquer sur le symbole <i class="fas fa-times-circle"></i> sur chaque envoi.',
        ],
        'modal' => [
            'confirm' => [
                'title'   => 'Confirmer remboursement',
                'message' => 'Vous souhaitez annuler la confirmation de réception du montant de ce remboursement?',
                'label'   => 'Annuler confirmation',
            ],
            'unconfirm' => [
                'title'   => 'Confirmer remboursement',
                'message' => 'Confirmer montant du remboursement?',
                'label'   => 'Confirmer',
            ]
        ],
        'selected' => [
            'title'   => 'Confirmer remboursement',
            'message' => 'Confirmer montant des remboursements sélectionnés?',
            'label'   => 'Confirmer',

        ]
    ],

    'request' => [
        'modal' => [
            'title' => 'Demander remboursement',
            'message' => 'Sélectionnez le type de remboursement souhaité.'
        ],
        'methods' => [
            'transfer' => 'Virement',
            'money' => 'Espèces',
            'check' => 'Chèque'
        ]
    ],

    'feedback' => [
        'confirm' => [
            'success' => 'Remboursements confirmés avec succès.'
        ]
    ]

];
