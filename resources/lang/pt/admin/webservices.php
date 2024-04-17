<?php

/*
    |--------------------------------------------------------------------------
    | Webservice services
    |--------------------------------------------------------------------------
    */
return [
    'services' => [
        'envialia' => [
            '830'   => '8H30',
            '10'    => '10H',
            '14'    => '14H',
            '24'    => '24H',
            '72'    => '72H',
            '24'    => '24H',
            'CC'    => 'CC - Correio Interno',
            'DEV'   => 'DEV',
            '100'   => '100 - Int. Aéreo',
            '101'   => '101 - Int. Bag',
            '200'   => '200 - Int. Terrestre',
            'B2C'   => 'B2C',
        ],

        'tipsa' => [
            '830'   => '830 - Serviço 8H30',
            '10'    => '10 - Serviço 10H',
            '14'    => '14 - Serviço 14H',
            '48'    => '48 - Serviço 48H',
            '24'    => 'MV - Massivo',
            'CC'    => 'CC - Correio Interno',
            '05'    => '05 - Retorno',
            '03'    => '03 - Devolução',
            '90'    => '90 - Internacional Expresso',
            '91'    => '91 - Internacional Standard',
            '92'    => '92 - Internacional DPD Classic',
            '96'    => '96 - Marítimo Ilhas',
        ],

        //Formato codigo GLS: <servico>#<horario>
        'gls_zeta' => [
            '1#3'   => 'Courier 24H',
            '37#18' => 'Economy (72H)',
            '1#2'   => 'Courier 14H',
            '1#0'   => 'Courier 10H',
            '1#30'  => 'Courier 8H30',
            '74#1'  => 'Eurobusiness',
            '76#1'  => 'Eurobusiness Small',
            '6#10'  => 'Islas Maritimo',
            '1#5'   => 'Sábado',
            '1#11'  => 'Rec. Delegacion',
            '7'     => 'Recogida',
            '10'    => 'Retorno',

            '1#19'  => 'Parcel Shop',
            '8#0'   => 'R.INTERCIUDAD 10:00 Service',
            '8#2'   => 'R.INTERCIUDAD 14:00 Service',
            '8#3'   => 'R.INTERCIUDAD Business Parcel',
            '8#5'   => 'R.INTERCIUDAD Saturday Service',
            '8#11'  => 'R.INTERCIUDAD Rec. En Agencia',
            '8#19'  => 'R.INTERCIUDAD Parcel Shop',
            '30#18' => '08:30 Service',
            '56#18' => 'R.INTERCIUDAD Economy Service',
            '69#18' => 'R.INTERCIUDAD 08:30 Service',
            '74#3'  => '74#3',
            '75#18' => 'R.INTERCIUDAD (75#18)',
            '76#3'  => '76#3',
            '77#3'  => 'R.INTERCIUDAD (77#3)',
            '77#18' => 'R.INTERCIUDAD (77#18)',

            '7#3'   => 'Recogida (7#3)',
            '7#2'   => 'Recogida (7#2)',
            '7#0'   => 'Recogida (7#0)',
            '68#18' => 'Recogida (68#18)',
            '7#11'  => 'Recogida (7#11)',
            '7#5'   => 'Recogida (7#5)',
            '75#3'  => 'R.INTERCIUDAD (75#3)',
            '79#3'  => 'Recogida (79#3)',
            '91#4'  => 'Recogida (91#4)',
        ],

        'ctt_expresso' => [
            'EMSF009.01' => '10',
            'EMSF001.01' => '13',
            'EMSF028.01' => '13M',
            'ENCF005.01' => '19',
            'EMSF010.01' => '19M',
            'EMSF021.02' => '19 Espanha',
            'ENCF008.01' => '48',
            'EMSF056.01' => 'E-segue (Para amanhã)',
            'EMSF058.01' => 'Em ponto CTT',
            'EMSF057.01' => 'Em 2 dias',
            'ENCF008.02' => 'EMS Economy',
            'EMSF001.02' => 'EMS Internacional',
            'EMSF002.02' => 'Internacional Premium',
            'EMSF070.01' => 'Carga Paletes',
            'EMSF071.01' => 'Carga Volumes',
            'EMSF015.01' => 'Cargo (descontinuado)',
            'EMSF038.02' => '48 Espanha (descontinuado)',
            'EMSF003.02' => 'Europa Light',
        ],

        'ctt' => [
            'EMSF009.01' => '10',
            'EMSF001.01' => '13',
            'EMSF028.01' => '13M',
            'ENCF005.01' => '19',
            'EMSF010.01' => '19M',
            'EMSF021.02' => '19 Espanha',
            'ENCF008.01' => '48',
            'EMSF056.01' => 'E-segue (Para amanhã)',
            'EMSF058.01' => 'Em ponto CTT',
            'EMSF057.01' => 'Em 2 dias',
            'ENCF008.02' => 'EMS Economy',
            'EMSF001.02' => 'EMS Internacional',
            'EMSF002.02' => 'Internacional Premium',
            'EMSF070.01' => 'Carga Paletes',
            'EMSF071.01' => 'Carga Volumes',
            'EMSF015.01' => 'Cargo (descontinuado)',
            'EMSF038.02' => '48 Espanha (descontinuado)',
        ],

        'chronopost' => [],

        'fedex' => [
            'INTERNATIONAL_PRIORITY' => 'Internacional Priority',
            'INTERNATIONAL_ECONOMY'  => 'Internacional Economy',
            'GROUND'                 => 'Fexex Ground',
        ],

        'nacex' => [
            '11' => 'NACEX 08:30H', //NACEX 08:30H	España, Portugal, Andorra - NACIONAL
            '01' => 'NACEX 10:00H', //NACEX 10:00H	España, Portugal, Andorra - NACIONAL
            '02' => 'NACEX 12:00H', //NACEX 12:00H	España, Portugal, Andorra - NACIONAL
            '08' => 'NACEX 19:00H', //NACEX 19:00H	España, Portugal, Andorra - NACIONAL
            '26' => 'PLUS PACK', //PLUS PACK	España, Portugal, Andorra - NACIONAL
            '11#1' => 'NACEX 08:30H BAG', //NACEX 08:30H BAG Serviço 11 + tipo 1 (bag)
            '01#1' => 'NACEX 10:00H BAG', //NACEX 10:00H	España, Portugal, Andorra - NACIONAL
            '08#1' => 'NACEX 19:00H BAG', //NACEX 19:00H	España, Portugal, Andorra - NACIONAL
            
            'E'  => 'EURONACEX TERRESTRE', //EURONACEX TERRESTRE	Resto de países - INTERNACIONAL
            'F'  => 'INTERNACIONAL AEREO', //SERVICIO AEREO	Resto de países - INTERNACIONAL
            'G'  => 'EURONACEX ECONOMY', //EURONACEX ECONOMY	Resto de países - INTERNACIONAL
            'H'  => 'PLUSPACK EUROPA', //PLUSPACK EUROPA	Resto de países - INTERNACIONAL
            '03' => 'INTERDIA', //INTERDIA	España, Portugal, Andorra - NACIONAL

            '04' => 'PLUS BAG 1', //PLUS BAG 1	España, Portugal, Andorra - NACIONAL
            '05' => 'PLUS BAG 2', //PLUS BAG 2	España, Portugal, Andorra - NACIONAL

            '06' => 'VALIJA', //VALIJA	España, Portugal, Andorra - NACIONAL
            '07' => 'VALIJA IDA Y VUELTA', //VALIJA IDA Y VUELTA	España, Portugal, Andorra - NACIONAL
            
            '09' => 'PUENTE URBANO', //PUENTE URBANO	España, Portugal, Andorra - NACIONAL
            '10' => 'DEVOLUCION', //DEVOLUCION ALBARAN CLIENTE	España, Portugal, Andorra - NACIONAL
            
            '12' => 'DEVOLUCION TALON', //DEVOLUCION TALON	España, Portugal, Andorra - NACIONAL
            '14' => 'DEVOLUCION PLUS BAG 1', //DEVOLUCION PLUS BAG 1	España, Portugal, Andorra - NACIONAL
            '15' => 'DEVOLUCION PLUS BAG 2', //DEVOLUCION PLUS BAG 2	España, Portugal, Andorra - NACIONAL
            '17' => 'DEVOLUCION E-NACEX', //DEVOLUCION E-NACEX	España, Portugal, Andorra - NACIONAL
            '21' => 'NACEX SABADO', //NACEX SABADO	España, Portugal, Andorra - NACIONAL
            '22' => 'CANARIAS MARITIMO', //CANARIAS MARITIMO	España, Portugal, Andorra - NACIONAL
            '24' => 'CANARIAS 24H', //CANARIAS 24H	España, Portugal, Andorra - NACIONAL
            
           
            '28' => 'PREMIUM', //PREMIUM	España, Portugal, Andorra - NACIONAL
            '29' => 'NX-SHOP VERDE', //NX-SHOP VERDE	España, Portugal, Andorra - NACIONAL
            '30' => 'NX-SHOP NARANJA', //NX-SHOP NARANJA	España, Portugal, Andorra - NACIONAL
            '31' => 'E-NACEX SHOP', //E-NACEX SHOP	España, Portugal, Andorra - NACIONAL
            '33' => 'C@MBIO', //C@MBIO	España, Portugal, Andorra - NACIONAL
            '48' => 'CANARIAS 48H', //CANARIAS 48H	España, Portugal, Andorra - NACIONAL
            '88' => 'INMEDIATO', //INMEDIATO	España, Portugal, Andorra - NACIONAL
            '27' => 'E-NACEX', //E-NACEX	España, Portugal, Andorra - NACIONAL
            '90' => 'NACEX.SHOP', //NACEX.SHOP	España, Portugal, Andorra - NACIONAL
            '91' => 'SWAP', //SWAP	España, Portugal, Andorra - NACIONAL
            '95' => 'RETORNO SWAP', //	RETORNO SWAP	España, Portugal, Andorra - NACIONAL
            '96' => 'DEV. ORIGEN', //DEV. ORIGEN	España, Portugal, Andorra - NACIONAL
        ],

        'tnt_express' => [
            '15'   => 'Express (Domestico)', //EXPRESS (DOMÉSTICO)
            '12'   => '12H Express (Domestico)', //12H EXPRESS (DOMÉSTICO)
            '10'   => '10H Express (Domestico)', //10H EXPRESS (DOMÉSTICO)
            '09'   => '09H Express (Domestico)', //09H EXPRESS (DOMÉSTICO)
            '48N'  => 'Economy Express (Camião)',
            '412'  => 'Economy Express 12h (Camião)',
            '15N'  => 'Express (Avião)',
            '12N'  => '12H Express (Avião)',
            '10N'  => '10H Express (Avião)',
            '09N'  => '09H Express (Avião)',
            '15D'  => 'Express (Docs Avião)', //EXPRESS (DOCUMENTOS AVIÃO)
            '12D'  => '12H Express (Docs Avião)', //12H EXPRESS (DOCUMENTOS AVIÃO)
            '10D'  => '10H Express (Docs Avião)', //10H EXPRESS (DOCUMENTOS AVIÃO)
            '09D'  => '09H Express (Docs Avião)', //09H EXPRESS (DOCUMENTOS AVIÃO)
        ],

        'seur' => [
            '83' => 'SEUR 8H30',
            '3'  => 'SEUR 10',
            '9'  => 'SEUR 13H30',
            '1'  => 'SEUR 24',
            '15' => 'SEUR 48',
            '13' => 'SEUR 72',
            '17' => 'MARITIMO',
        ],

        'correos_express' => [
            '61' => '10H',
            '62' => '14H',
            '63' => '24H',
            '92' => 'Paq Empresa 14',
            '93' => 'Paq Empresa 24',
            '26' => 'Ilhas Express',
            '46' => 'Ilhas Documentação',
            '54' => 'Entrega Plus',
            '60' => 'Internacional Standard Exportação',
            '56' => 'Internacional Express Exportação',
            '91' => 'Internacional Express',
            '50' => 'Internacional Standard Importação',
            '79' => 'Ilhas Standard',
            '90' => 'Internacional Standard',
            '91' => 'Internacional Express',
        ],

        'db_schenker' => [
            'bookingLand#43'     => 'Terrestre Normal',
            'bookingLand#44'     => 'Terrestre Premium',
            'bookingAir#43'      => 'Aéreo Normal',
            'bookingAir#44'      => 'Aéreo Premium',
            'bookingOceanFCL#43' => 'Marítimo FCL Normal',
        ],

        'vasp' => [
            '93'  => 'VASP 24',
            '97'  => 'VASP PAL',
            'AI'  => 'AEREO ILHAS',
            'MI'  => 'MARITIMO ILHAS',
            '104' => 'VASP SÁBADO',
        ],

        'mrw' => [
            '0015' => 'Urgente 10 Expedição',
            '0105' => 'Urgente 12 Expedição',
            '0115' => 'Urgente 13 Expedição',
            '0205' => 'Urgente 19 Expedição',
            '0000' => 'Urgente 10',
            '0100' => 'Urgente 12',
            '0110' => 'Urgente 13',
            '0200' => 'Urgente 19',
            '0005' => 'Urgente Hoje',

            //'0221' => 'Urgente Funchal',
            //'0222' => 'Urgente Porto Santo',
            //'0223' => 'Urgente Açores',
            'MARPT' => 'Urgente Ilhas PT', //o model do webservice depois é que detecta se é Funchal ou porto santo


            '0230' => 'BAG 19',
            '0235' => 'BAG 13',
            '0370' => 'Marítimo Baleares',
            '0385' => 'Marítimo Canárias',
            '0390' => 'Marítimo Interinsular',
            '0400' => 'Expresso Documentos',
            '0450' => 'Express 2 Quilos',
            '0480' => 'Caixa Express 3 Quilos',
            '0490' => 'Documentos 13',
            '0800' => 'Ecommerce',
            '0010' => 'Promoções',
            '0810' => 'Ecommerce Canje',
        ],

        'ups' => [
            '11' => 'UPS Standard', //UPS Standard
            '65' => 'UPS Worldwide Saver', //UPS Standard
        ],

        'dachser' => [
            '1'   => 'Express',
            '3'   => 'EuroEXPRESS'
        ],

        'delnext' => [
            '1' => 'Internacional',
            '2' => 'Ilhas Aéreo',
            '3' => 'Nacional/Espanha/Marítimo'
        ],

        'sending' => [
            '01' => 'Send Expres',
            '02' => 'Send Top 10H',
            '03' => 'Send Sectorial',
            '08' => 'Send Ecommerce',
            '10' => 'Send Masivo',
            '18' => 'Send Maritimo',
            '40' => 'Send Optica'
        ],

        'ontime' => [
            '68' => 'PLT 700 Portugal',
            '69' => 'PLT 1200 Portugal',
            '26' => 'Palet Express',
            '27' => '27 - PT ES',
            '70' => '70 - PT ES',
            '79' => 'Paquetaria Express',
            '19' => '19'
        ]
    ],


    /**
     * Valores Esperados
     *  Código do Pais (campo 3)
     *   10=Portugal
     *   11=Espanha
     *  Código Divisa Reembolso (campo 20)
     *   900=Euro
     *  Tipo de Reembolso (campo 21)
     *   0=Sem Gestão
     *   1=Dinheiro
     *   2=Cheque
     *  Gestão de comprovativos (campo 22)
     *   1=Sem Gestão
     *   2=Devolver
     *   3=Arquivo
     *  Código de Incotermo (campo 28)
     *   1=Facturar
     *   2=Portes a pagar pelo Destinatário
     *  Código do Tipo de Serviço (campo 29)
     *   70 = Courrier Nacional
     *   73 = Courrier Ilhas Marítimo
     *   74 = Courrier Ilhas Aéreo
     *  Horas Grandes Superfícies (campo 30)
     *   0=Entrega Normal
     *   1=Entrega Grande Superfície
     *  Formato Data Acordada do Serviço (campo 31)
     *   AAAA/MM/DD
     *  Código Vol - SSCC (campo 36)
     *   Deverá ser utilizado caso seja necessário leitura dos códigos de barras de barras das etiquetas do cliente
     */

    'expected_values' => [
        'shynet' => [
            'country_code' => [
                'pt' => '10',
                'es' => '11',
            ],
            'charge_currency' => [
                'euro' => '900',
            ],
            'charge_type' => [
                '' => '0', // Sem Gestão
                'money' => '1',
                'check' => '2',
            ],
            'comprovativos' => [
                '' => '0', // Sem Gestão
                'return' => '1', // Devolver
                'arquive' => '2', // Arquivo
            ],
            'incotermo' => [
                'invoice' => '1', //1=Facturar
                'payment_at_recipient' => '2', //2=Portes a pagar pelo Destinatário
            ],
            'service_type' => [
                'national' => '70', // 70 = Courrier Nacional
                'ocean' => '73', // 73 = Courrier Ilhas Marítimo
                'air' => '74', // 74 = Courrier Ilhas Aéreo
            ],
            'big_superficie_hour' => [
                'normal' => '0', // 0=Entrega Normal
                'big' => '1', // 1=Entrega Grande Superfície
            ],
        ],
    ],
];
