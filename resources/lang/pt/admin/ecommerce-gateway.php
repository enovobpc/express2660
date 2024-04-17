<?php

return [
    'methods' => [
        'PrestaShop'  => 'PrestaShop',
        'WooCommerce' => 'WooCommerce',
        'Shopify'     => 'Shopify'
    ],
    'mapping' => [
        'prestashop' => [
            'carriers' => [
                'id'   => 'code',
                'name' => 'name'
            ],
            'status' => [
                'id'   => 'code',
                'name' => 'name'
            ]
        ]
    ]
];