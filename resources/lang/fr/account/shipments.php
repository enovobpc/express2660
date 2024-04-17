<?php

return [
    'title' => 'Envois',

    'modal-shipment' => [
        'create-shipment' => 'Créer une expéditon',
        'edit-shipment' => 'Editer Expedition',
        'create-pickup' => 'Planifier un enlèvement',
        'edit-pickup' => 'Editer demande d’enlèvement',
        'sender-block' => 'Expéditeur',
        'recipient-block' => 'Destinataire',
        'return-pack' => 'Retour marchandise',
        'write-email' => 'Écrire une adresse e-mail...',
        'tips' => [
            'price-expense' => 'Frais d\'expédition supplémentaires',
            'ref'      => 'Associez une référence personnalisée à cette commande, par exemple, votre facture ou numéro de commande.',
            'weight'   => 'Poids total de l\'envoi.',
            'sms'      => 'Cette option n\'est pas disponible dans votre contrat.',
            'dimensions' => 'Insérer les dimensions et les détails des volumes',
            'max-weight' => 'Le service sélectionné n\'autorise qu\'un maximum de <b class="lbl-total-kg">1</b> kg par envoi.',
            'max-volumes' => 'Le service sélectionné n\'autorise qu\'un maximum de <b class="lbl-total-vol">1</b> volume par envoi.',

            'hour-exceeded' => 'Il n’est plus possible d’organiser un enlèvement aujourd’hui. Date prévu de l’enlèvement et éxpedition: :date',
            'toggle-sender' => 'Echanger lieu de chargement avec lieu de déchargement',
            'cod' => 'Selectionnez cette option si le montant du transport sera payé par le destinataire à la livraison.'
        ],
    ],

    'modal-dimensions' => [
        'new-line' => 'Ajouter une nouvelle ligne',
        'confirm' => [
            'message' => 'Voulez-vous changer le nombre de volumes d\'expédition à',
            'title' => 'Changer le nombre de volumes'
        ]
    ],

    'modal-show' => [
        'title' => 'Details d’expeditions',
        'tabs' => [
            'info' => 'Détails',
            'track' => 'Suivi',
            'dimensions' => 'Marchandise et dimensions',
            'expenses' => 'Prix et taxes',
            'attachments' => 'Documents'
        ],
        'tips' => [
            'prices' => 'Les prix et tarifs indiqués sont nets. Ils peuvent changer jusqu‘à ce que la facture soit émise.'
        ]
    ],

    'modal-attachments' => [
        'title' => 'Ajouter document',
        'edit-title' => 'Modifier document',
        'empty' => 'Il n\'y a pas de pièces jointes pour ce colis.',
        'feedback' => [
            'save' => [
                'success' => 'Document enregistré avec succès.',
                'error' => 'Le document n\'a pas pu être chargé.'
            ],
            'destroy' => [
                'message' => 'Confirmez-vous la suppression du document sélectionné?',
                'success' => 'Document supprimé avec succès.',
                'error'   => 'Une erreur s\'est produite lors de la tentative de suppression du document.'
            ]
        ]
    ],

    'modal-grouped-guide' => [
        'title'   => 'Lettre de voiture groupée',
        'message' => 'Vous souhaitez générer une lettre de voiture groupée pour les envois sélectionnés?',
        'pack-type' => 'Type conditionnement',
        'goods-description' => 'Description marchandise',
        'license-plate' => 'Immatriculation véhicule'
    ],

    'selected' => [
        'print-list' => 'Imprimer Liste',
        'print-label-guides' => 'Imprimer bordereaux et lettres de voiture',
        'print-guides' => 'Lettres de voiture',
        'print-labels' => 'Bordereaux',
        'print-grouped-guide' => 'Créer lettre de voiture groupée',
        'print-list' => 'Imprimer Liste',
        'print-summary' => 'Listagem de Resumo',
        'print-manifest' => 'Manifeste de chargement',
        'pickup-manifest' => 'Manifeste de ramasse'
    ],

    'filters' => [
        'charge' => [
            '' => 'Tous',
            '1' => 'Oui',
            '0' => 'Non'
        ],
        'label' => [
            '' => 'Tous',
            '1' => 'Imprimé',
            '0' => 'A Imprimer'
        ]
    ],

    'print' => [
        'guide' => 'Lettre de voiture',
        'label' => 'Bordereau',
        'labels' => 'Bordereaux',
        'cmr' => 'CMR',
        'grouped-guide' => 'Lettre de voiture groupée',
        'reimbursement-guide' => 'remboursement lettre de voiture',
        'pickup-manifest' => 'Manifeste d’enlèvement'
    ],

    'budget' => [
        'title' => 'Calculer cout de l’envoi',
        'fuel-tax' => 'Taxe Fuel',
        'plus-vat-info' => 'Ajouter la TVA au taux légal en vigueur.',
        'empty-vat-info' => 'Exonéré de TVA.',
        'exceptions-info' => 'D‘autres frais peuvent s‘ajoutés.',
        'addicional-services' => [
            'title'  => 'Services supplémentaires',
            'pickup' => 'Demande d’enlèvement',
            'charge' => 'Expédition de facturation',
            'rguide' => 'Retour Lettre de voiture'
        ],
        'price-overview' => [
            'title' => 'Tarif prévu',
            'base' => 'Valeur de l’envoi',
            'charge' => 'Frais de remboursement',
            'fuel' => 'Taxe Fuel',
            'pickup' => 'Frais d’enlèvement',
            'rguide' => 'Frais retour Lettre de voiture',
            'outstandard' => 'Volume hors norme'
        ],

        'tips' => [
            'kms'        => 'Distance (aller retour) entre notre entrepôt , expéditeur et destinataire.',
            'volumes'    => 'Nombre de pièces ou colis à envoyer',
            'weight-vol' => 'Le poids volumétrique dépend des dimensions des colis. Pour le calcul du prix le poids le plus important est toujours pris en compte entre le poids réel et le poids volumétrique.',
            'weight'     => 'Poids total des pièces ou colis à envoyer',
            'charge'     => 'Si l‘expédition doit être facturée, indiquez le montant à facturer.',
        ]
    ],

    'cargo_manifest' => [
        'signature' => 'Signature',
        'date-time-cargo' => 'Date et heure de chargement',
    ],

    'feedback' => [
        'update' => [
            'success' => 'Demande enregistré avec succès.',
            'error-email' => 'Envoi enregistré avec succès. Envoi de l’ Email impossible à: :emails'
        ],
        'destroy' => [
            'question'  => 'Confirmer suppression de l’envoi?',
            'success'   => 'Envoi supprimé avec succès.',
            'error'     => 'Une erreur s‘est produite lors de la tentative d‘annulation de l’envoi.',
        ]
    ]
];
