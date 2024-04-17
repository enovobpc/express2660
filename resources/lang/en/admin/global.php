<?php

return [
    'routes' => [
        'account' => 'account'
    ],

    'word' => [
        'new'    => 'New',
        'remove' => 'Remove',
        'cancel' => 'Cancel',
        'close'  => 'Close',
        'shipment'     => 'Tracking Code',
        'provider_trk' => 'Provider TRK',
        'reference' => 'Reference',
        'sender_name' => 'Sender',
        'recipient_name' => 'Recipient',
        'remittance' => 'Remittance',
        'charge' => 'Charge',
        'serv' => 'Serv.',
        'obs' => 'Obs.',
        'price' => 'Price',
        'total' => 'Total',
        'quantity' => 'Quantity',
        'expenses' => 'Expenses',
        'payment_at_recipient' => 'Pay on Delivery',
        'total_to_pay' => 'Total to Pay',
        'total_price' => 'Total Price',
        'vat' => 'VAT',
        'net_price' => 'Net Price',
        'subtotal' => 'Subtotal',
        'product' => 'Item',
        'price_un' => 'Preço Un.',
        'num-env-rec' => 'Nº Env./Rec.',
        'env-rec' => 'Env./Rec.',
        'pickup-failed' => 'RECOLHA FALHADA',
        'cargo_date' => 'Carga',
        'delivery_date' => 'Descarga',

        'from' => 'From',
        'to' => 'To',
        'vehicle' => 'Vehicle',
        'width_abrv' => 'Width',
        'length_abrv' => 'Length',
        'height_abrv' => 'Height',
        'weight' => 'Weight',
        'qty' => 'Qty',
        'description' => 'Description',
        'dossier' => 'Dossier',
        'dossier_summary' => 'Dossier Summary',
        'goods_to_transport' => 'Goods to transport',
        'followed_by' => 'Followed by',
        'shipped_by' => 'Shipped by',
        'class' => 'Class',
        'letter' => 'Leter',
        'type' => 'Type',
        'goods' => 'Goods',
        'transport' => 'Transport',
        'pickup' => 'Pickup',
        'delivery' => 'Delivery',
        'contact' => 'Contact',
        'datetime' => 'Date and Hour',
        'service_billing' => 'Service Billing',
        'billing_data' => 'Billing details',
        'billing_address' => 'Billing Address',
        'general_conditions' => 'General Service Conditions',
        'expenses' => 'Adicional Expenses',
        'transport_service' => 'Transport service',
        'agreed_price' => 'Agreed price',
        'charging_instructions' => 'Shipment Confirmation',
        'payment_method' => 'Payment condition',
        'customer-portal' => 'Customer Account',
    ],

    'feedback' => [
        'destroy' => [
            'header'  => 'Confirmar remoção',
            'title'   => 'Confirma a remoção do registo selecionado?',
            'success' => 'Registo removido com sucesso.',
            'error'   => 'Não foi possível remover o registo.'
        ]
    ],

    'billing' => [
        'pdf' => [
            'title'      => 'Resumo de Faturação',
            'section01'  => 'Envios e Encargos Associados',
            'section02'  => 'Outros produtos ou serviços',
            'section03'  => 'Avenças Mensais',
            'pickup_tax' => 'Taxa de Recolha. Recolha N.º',
        ]
    ],

    'services' => [
        'unities' => [
            'selectbox' => [
                'weight' => 'Baseado no Peso',
                'volume' => 'Baseado no Nº de Volumes',
                'internacional' => 'Baseado no País',
                'm3'     => 'Baseado nos Metros Cúbicos',
                'km'     => 'Baseado no nº de KM',
                'pallet' => 'Baseado no Nº de Paletes'
            ],
            'labels' => [
                'weight' => 'KG',
                'volume' => 'VOL.',
                'internacional' => 'País',
                'm3'     => 'M3',
                'km'     => 'KM',
                'pallet' => 'PAL'
            ]
        ],
        'features' => [
            'is_collection' => 'Serviço Recolha',
            'is_return'     => 'Serviço Retorno',
            'is_import'     => 'Serviço Importação',
            'is_internacional' => 'Serviço Internacional',
            'is_maritime'   => 'Serviço Marítimo',
            'is_air'        => 'Serviço Aéreo'
        ],
        'priorities-levels' => [
            '1' => 'Nível 1 (Máxima)',
            '2' => 'Nível 2',
            '3' => 'Nível 3',
            '4' => 'Nível 4',
            '5' => 'Nível 5 (Miníma)',
        ],
        'priorities-colors' => [
            '#f90000' => '#f90000', //vermelho vivo
            '#ff841b' => '#ff841b', //laranja
            '#ffce17' => '#ffce17', //amarelo
            '#71c600' => '#71c600', //verde
            '#27A9E1' => '#27A9E1', //azul
        ]
    ],

    'packages_types' => [
        'box'       => 'Caixa',
        'envelope'  => 'Envelope',
        'pallet'    => 'Palete',
        'can'       => 'Lata',
        'jaricam-5' => 'Jaricam 5L',
        'jaricam-25'=> 'Jaricam 25L',
        'barrica'   => 'Barrica',
        'ibc'       => 'IBC',
        'bidon'     => 'Bidon',
        'box10'     => 'Caixa até 10KG',
        'box25'     => 'Caixa até 25KG',
        'custom'    => 'Outros Tipos',
        'multiple'  => 'Vários Tipos',
    ],

    'login-status' => [
        '1' => 'Ativo',
        '0' => 'Bloqueado'
    ],

    'covenants-types' => [
        'fixed'     => 'Avença Fixa',
        'variable'  => 'Avença Variável',
    ],

    'skins' => [
        'skin-orange'           => 'Laranja',
        'skin-dark-orange'      => 'Laranja Escuro',
        'skin-red'              => 'Vermelho',
        'skin-bordo'            => 'Bordô',
        'skin-tarawera'         => 'Tarawera',
        'skin-blue'             => 'Azul',
        'skin-turquoise-blue'   => 'Azul Turquesa',
        'skin-blue-orange'      => 'Azul Escuro + Laranja',
        'skin-blue-yellow'      => 'Azul Escuro + Amarelo',
        'skin-curious-blue'     => 'Azul Vivo + Branco',
        'skin-space-blue'       => 'Azul Escuro + Azul Vivo',
        'skin-green'            => 'Verde',
        'skin-green-lime'       => 'Verde Lima',
        'skin-turquoise'        => 'Verde Turquesa',
        'skin-olive'            => 'Verde Oliva',
        'skin-purple'           => 'Roxo',
        'skin-yellow'           => 'Amarelo',
        'skin-gold'             => 'Dourado',
        'skin-black'            => 'Preto',
        'skin-black-red'        => 'Preto + Vermelho',
        'skin-white'            => 'Branco',
    ],

    'colors' => [

        //orange
        '#FFF176' => '#FFF176',
        '#FFD740' => 'Amarelo',
        '#FFAB00' => '#FFAB00',
        '#ff8100' => '#ff8100',
        '#f95400' => '#f95400',
        '#d35400' => '#d35400',
        '#b33f04' => '#b33f04',


        //red
        '#f90000' => '#f90000',
        '#D50000' => 'D50000',
        '#C62828' => '#C62828',
        '#E53935' => '#E53935',
        '#EF9A9A' => '#EF9A9A',
        '#e74c3c' => '#e74c3c',




        //green
        '#1abc9c' => '#1abc9c',
        '#16a085' => '#16a085',
        '#2ecc71' => '#2ecc71',
        '#AEEA00' => '#AEEA00',
        '#8CC63F' => 'Verde',
        '#48AD01' => '#48AD01',
        '#1B5E20' => '#1B5E20',

        //blue
        '#26C6DA' => '#26C6DA',
        '#6dcefb' => 'Azul Claro',
        '#27A9E1' => 'Azul Claro',
        '#1979cc' => 'Azul',

        '#0D47A1' => '#0D47A1',
        '#1b3d67' => '#1b3d67',
        '#1b3d67' => 'Azul Escuro',

        //purple
        '#9b59b6' => '#9b59b6',
        '#8e44ad' => '#8e44ad',
        '#622599' => 'Roxo',

        //BROWN
        '#BCAAA4' => '#BCAAA4',
        '#8D6E63' => '#8D6E63',
        '#5D4037' => '#5D4037',
        '#3E2723' => '#3E2723',

        //gray
        '#ecf0f1' => '#ecf0f1',
        '#bdc3c7' => '#bdc3c7',
        '#95a5a6' => '#95a5a6',
        '#7f8c8d' => '#7f8c8d',
        '#34495e' => '#34495e',
        '#2c3e50' => '#2c3e50',
    ]
];