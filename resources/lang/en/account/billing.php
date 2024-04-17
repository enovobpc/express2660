<?php

return [
    'title' => 'Billing and Extracts',

    'tabs' => [
        'invoices' => 'Invoices',
        'extracts' => 'Monthly Statements',
    ],

    'word' => [
        'price-avg' => 'Average Price per Shipment',
        'weight-avg' => 'Average Weight per Shipment',
        'unpaid' => 'Unpaid',
        'expired-docs' => 'Expired Docs',
        'expired-days' => ':days days overdue',
        'customer-detail' => 'Customer billing summary',
        'empty-billing' => 'No invoices issued yet.'
    ],

    'filters' => [
        'sense' => [
            '' => 'All',
            'debit' => 'Debits',
            'credit' => 'Credits'
        ],

        'paid' => [
            '' => 'Todos',
            '1' => 'Paid',
            '0' => 'Unpaid'
        ]
    ],

    'last-update' => [
        'hours' => 'Updated :time hours ago',
        'minutes' => 'Updated less than 1 hour ago'
    ],

    'tip-archive' => 'Information for the past few months is displayed. If you want previous information, please request it from customer support.'

];
