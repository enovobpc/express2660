<?php

return [

    'billing-methods' => [
        '30d' => 'Mensuel',
        '15d' => 'Bihebdomad',
    ],

    'periods' => [
        '30d' => 'Mensuel',
        '15d' => [
            '1q' => '1ª Quinzaine',
            '2q' => '2ª Quinzaine',
        ],
        '7d' => [
            '1w' => '1ª Semaine',
            '2w' => '2ª Semaine',
            '3w' => '3ª Semaine',
            '4w' => '4ª Semaine',
        ]
    ],


    'payment-methods' => [
        'transfer'   => 'Transferência',
        'money'      => 'Numerário',
        'check'      => 'Cheque',
        'dd'         => 'Débito Direto',
        'settlement' => 'Acerto de Contas',
        'confirming' => 'Confirming',
    ],

    'payment-conditions' => [
        'sft'   => 'Sem fatura',
        'prt'   => 'A Pronto',
        '3d'    => 'A 3 jours',
        '5d'    => 'A 5 jours',
        '7d'    => 'A 7 jours',
        '10d'   => 'A 10 jours',
        '15d'   => 'A 15 jours',
        '20d'   => 'A 20 jours',
        '30d'   => 'A 30 jours',
        '45d'   => 'A 45 jours',
        '50d'   => 'A 50 jours',
        '55d'   => 'A 55 jours',
        '60d'   => 'A 60 jours',
        '75d'   => 'A 75 jours',
        '80d'   => 'A 80 jours',
        '85d'   => 'A 85 jours',
        '90d'   => 'A 90 jours',
        '120d'  => 'A 120 jours',
        'dbt'   => 'Débito Bancário',
        'wallet' => 'Pré-pagamento'
    ],

    'status' => [
        'unpaid' => 'Impayé',
        'paid'   => 'Payé'
    ],

    'types-list' => [
        'nodoc'              => 'Pas de facture',
        'invoice'            => 'Facture',
        'credit-note'        => 'Note de crédit',
        'proforma-invoice'   => 'Facture proforma',
        'internal-doc'       => 'Document interne',
        //'receipt'            => 'Recibo',
        //'transport-guide'    => 'Guia de transporte'
    ],

    'types-list-selectbox' => [
        'nodoc'              => 'Pas de facture',
        'Documents fiscaux' => [
            'invoice'            => 'Facture',
            'credit-note'        => 'Note de crédit',
        ],
        'Autres documents' => [
            'proforma-invoice'   => 'Facture proforma',
            'internal-doc'       => 'Document interne',
        ]
    ],

    'types-list-purchase' => [
        'provider-invoice'              => 'Facture',
        'provider-credit-note'          => 'Note de crédit',
    ],

    'types-list-purchase-abrv' => [
        'provider-invoice'              => 'Facture',
        'provider-credit-note'          => 'Note de crédit',
        'provider-devolution-note'      => 'Note de retour',
    ],

    'types' => [
        'invoice'            => 'Facture',
        'credit-notes'       => 'Nota de Crédito',
        'debit-notes'        => 'Nota de Débito',
        'invoice-receipt'    => 'Fatura-Recibo',
        'receipt'            => 'Recibo',
        'credit-note'        => 'Nota de Crédito',
        'debit-note'         => 'Nota de Débito',
        'proforma-invoice'   => 'Fatura Proforma',
        'internal-doc'       => 'Documento Interno',
        'provider-invoice'              => 'Fatura de compra',  //Factura de compra
        'provider-invoice-receipt'      => 'Fatura-Recibo compra',
        'provider-simplified-invoice'   => 'Fatura Simplificada fornecedor', //Fatura Simplificada de fornecedor
        'provider-credit-note'          => 'Nota de Crédito fornecedor', //Crédito financeiro de fornecedor
    ],

    'types_code' => [
        'nodoc'              => 'S/DOC',
        'invoice'            => 'FT',
        'credit-note'        => 'NC',
        'receipt'            => 'RC',
        'debit-note'         => 'ND',
        'invoice-receipt'    => 'FR',
        'sale-by-money'      => 'VD',
        'transport-guide'    => 'GT',
        'proforma-invoice'   => 'PF',
        'internal-doc'       => 'DI',
        'provider-invoice'              => 'FFT',
        'provider-invoice-receipt'      => 'FFR',
        'provider-sale-by-money'        => 'FVD',
        'provider-simplified-invoice'   => 'FFS',
        'provider-credit-note'          => 'FNC',
    ],

    'exemption-reasons' => [
        'M05' => 'M05 - Art. 14º CIVA',
        'M01' => 'M01 - Art. 6º CIVA',
        'M07' => 'M05 - Art. 9º CIVA',
    ],

    'targets' => [
        'CustomerBilling' => 'Facture mensuelle',
        'Invoice'         => 'Facture individuelle',
    ],

    'targets-colors' => [
        'CustomerBilling' => '#1c3665',
        'Invoice'   => '#27aae1',
    ],

    'gateway-payment-methods' => [
        'visa'   => 'Visa/Mastercard',
        'wallet' => 'Conta Corrente',
        //'paypal' => 'Paypal'
    ],
];
