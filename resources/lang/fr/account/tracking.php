<?php

return [
    'title' => 'Suivi de l’expedition',

    'form' => [
        'button' => 'Rechercher envoi',
        'label' => 'Numéro(s) de suivi de l’envoi',
        'tip' => 'Vous pouvez rechercher plusieurs codes simultanément. Séparez chaque code par une virgule.'
    ],

    'index' => [
        'title' => 'Saisissez votre numéro de suivi.',
        'subtitle' => 'Si plusieurs codes, vous pouvez les séparer par une virgule.'
    ],

    'empty' => [
        'title' => 'Aucun envoi trouvé avec ce code <b>:trk</b>',
        'msg' => 'Vérifiez votre numéro de suivi.'
    ],

    'word' => [
        'consult-pod' => 'Voir preuve de livraison'
    ],

    'progress' => [
        'pending' => 'En attente',
        'accepted' => 'Accepté',
        'pickup' => 'Enlèvement',
        'transit' => 'En transit',
        'delivered' => 'Livré',
        'incidence' => 'Incident',
        'returned' => 'Retour',
        'canceled' => 'Annulé'
    ]
];

