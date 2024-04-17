<?php

return [
    'title' => 'Track & Trace',

    'form' => [
        'button' => 'Search shipment',
        'label' => 'Shipment Tracking',
        'tip' => 'You can search for several codes at the same time. Separate each code with a comma.'
    ],

    'index' => [
        'title' => 'Type in the box below the shipping code to be searched.',
        'subtitle' => 'If you have more than one code, you can separate them with a comma.'
    ],

    'empty' => [
        'title' => 'No shipment with code <b>:trk</b> was found.',
        'msg' => 'Make sure you have written the code correctly.'
    ],

    'word' => [
        'consult-pod' => 'Show POD'
    ],

    'progress' => [
        'pending' => 'Documented',
        'accepted' => 'Accepted',
        'pickup' => 'In Pickup',
        'transit' => 'In Transit',
        'delivered' => 'Delivered',
        'incidence' => 'Incidence',
        'returned' => 'Devolved',
        'canceled' => 'Canceled'
    ]
];
