<?php

return [
    'title' => 'Shipments & Pickups',

    'modal-shipment' => [
        'create-shipment' => 'Create Shipment',
        'edit-shipment' => 'Edit Shipment',
        'create-pickup' => 'Create Pickup',
        'edit-pickup' => 'Edit Pickup Request',
        'sender-block' => 'Pickup',
        'recipient-block' => 'Recipient',
        'return-pack' => 'Return Pack',
        'write-email' => 'Write email address...',
        'tips' => [
            'price-expense' => 'Extra Shipping Charges',
            'ref'      => 'Associate a personalized reference with this shipment, for example, your invoice or order number.',
            'weight'   => 'Total weight of the shipment.',
            'sms'      => 'This option is not available in your contract.',
            'dimensions' => 'Insert dimensions and details of volumes',
            'max-weight' => 'The selected service only allows a maximum of <b class="lbl-total-kg">1</b>kg per shipment.',
            'max-volumes' => 'The selected service only allows <b class="lbl-total-vol">1</b> volumes per shipment',
            'hour-exceeded' => 'It is no longer possible for the driver to collect your packages today. Expected date for collection and dispatch: :date',
            'toggle-sender' => 'Exchange loading location with unloading location',
            'cod' => 'Activate this option if the shipping price is paid by the recipient at the time of delivery.'
        ],
    ],

    'modal-dimensions' => [
        'new-line' => 'Add new line',
        'confirm' => [
            'message' => 'Do you want to change the number of shipment volumes to',
            'title' => 'Change number of volumes'
        ]
    ],

    'modal-show' => [
        'title' => 'Shipping Details',
        'tabs' => [
            'info' => 'Details',
            'track' => 'Track & Trace',
            'dimensions' => 'Packs and Dimensions',
            'expenses' => 'Price and Expenses',
            'attachments' => 'Attachments'
        ],
        'tips' => [
            'prices' => 'Prices and rates shown are net. They may change until the invoice is issued.'
        ]
    ],

    'modal-attachments' => [
        'title' => 'Add attachment',
        'edit-title' => 'Edit attachment',
        'empty' => 'There are no attachments for this shipment.',
        'feedback' => [
            'save' => [
                'success' => 'Attachment saved successfuly.',
                'error' => 'The document could not be loaded.'
            ],
            'destroy' => [
                'message' => 'Confirms the removal of the selected attachment?',
                'success' => 'Attachment successfully removed.',
                'error'   => 'An error occurred while trying to remove the attachment.'
            ]
        ]
    ],

    'modal-grouped-guide' => [
        'title'   => 'Gruped Transport Guide',
        'message' => 'Do you want to generate a grouped waybill for the selected shipments?',
        'pack-type' => 'Pack Type',
        'goods-description' => 'Goods Description',
        'license-plate' => 'License Plate'
    ],

    'selected' => [
        'print-list' => 'Print list',
        'print-label-guides' => 'Print Labels and Guides',
        'print-guides' => 'Transport Guides',
        'print-labels' => 'Labels',
        'print-grouped-guide' => 'Create Grouped Guide',
        'print-list' => 'Print List',
        'print-summary' => 'Print Summary',
        'print-manifest' => 'Cargo Manifest',
        'pickup-manifest' => 'Pickup Manifest'
    ],

    'filters' => [
        'charge' => [
            '' => 'All',
            '1' => 'With COD',
            '0' => 'Without COD'
        ],
        'label' => [
            '' => 'All',
            '1' => 'Printed',
            '0' => 'Not printed'
        ]
    ],

    'print' => [
        'guide' => 'Transport Guide',
        'label' => 'Label',
        'labels' => 'Label',
        'cmr' => 'CMR',
        'grouped-guide' => 'Grouped Guide',
        'reimbursement-guide' => 'Refund Guide',
        'pickup-manifest' => 'Pickup Manifest'
    ],

    'budget' => [
        'title' => 'Calculate shipping cost',
        'fuel-tax' => 'Fuel Excharge',
        'plus-vat-info' => 'Plus VAT',
        'empty-vat-info' => 'Exempt from VAT.',
        'exceptions-info' => 'Other fees may be added.',
        'addicional-services' => [
            'title'  => 'Adicional services',
            'pickup' => 'Pickup request',
            'charge' => 'COD',
            'rguide' => 'Guide Return'
        ],
        'price-overview' => [
            'title' => 'Expected Price',
            'base' => 'Shipment Price',
            'charge' => 'Refund Excharge',
            'fuel' => 'Fuel Excharge',
            'pickup' => 'Pickup Excharge',
            'rguide' => 'Guide Return Excharge',
            'outstandard' => 'Out of standard'
        ],

        'tips' => [
            'kms'        => 'Distance (round trip) between our warehouse, sender and recipient.',
            'volumes'    => 'Number of objects or packages to send',
            'weight-vol' => 'The volumetric weight is derived from the dimensions of the volumes. When calculating the price, the highest weight between the actual weight of the volumes and the volumetric weight is always considered.',
            'weight'     => 'Total weight of objects or packages to be sent',
            'charge'     => 'Indicate the price to be charged to the recipient',
        ]
    ],

    'cargo_manifest' => [
        'signature' => 'Signature',
        'date-time-cargo' => 'Cargo date and hour',
    ],

    'feedback' => [
        'update' => [
            'success' => 'Shipment successfully saved.',
            'error-email' => 'Sending saved successfully. The email could not be sent to: :emails'
        ],
        'destroy' => [
            'question'  => 'Do you confirm the cancellation of this shipment?',
            'success'   => 'Submission canceled successfully.',
            'error'     => 'An error occurred while trying to cancel.',
        ]
    ]
];
