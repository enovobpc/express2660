<?php

return [

    'return' => [
        ''      => 'Sem retorno',
        'pack'  => 'Encomenda',
        'guia'  => 'Guia assinada',
        'check' => 'Cheque'
    ],

    'return_types' => [
        'return' => 'Encomenda',
        'rpack'  => 'Encomenda',
        'rguide' => 'Guia assinada',
        'rcheck' => 'Cheque'
    ],

    'print-options' => [
        ''          => 'Não imprimir nada',
        'all'       => 'Etiquetas + Guia',
        'labels'    => 'Só Etiquetas',
        'guide'     => 'Só Guia Transporte',
        'cmr'       => 'Só CMR'
    ],

    'guides-types' => [
        'guide'         => 'Alvará IMT - Design 1',
        'guide_v2'      => 'Alvará IMT - Design 2',
        'guide_v3'      => 'Alvará IMT - Design 3',
        'guide_v4'      => 'Alvará IMT - Design 4',
        'guide_v4_1'    => 'Alvará IMT - Design 4 (cores)',
        'guide_v5'      => 'Alvará IMT - Design 5',
        'guide_v6'      => 'Alvará IMT - Design 6',
        'guide_anacom'  => 'Licença ICP-ANACOM',
        'cmr'           => 'CMR Internacional',
        'guide_goods'   => 'Guia Mercadoria',
        'guide_custom01' => 'Personalizada 01'
    ],

    'charging-instructions-types' => [
        'charging_instructions_v1'  => 'Layout 1',
        'charging_instructions_v2'  => 'Layout 2',
        'charging_instructions_v3'  => 'Layout 3 (Simples)',
    ],

    'labels-sizes' => [
        'Horizontal' => [
            ''              => '[10x15] Estilo 01 ',
            '15x10-style03' => '[10x15] Estilo 02',
            '10x73'         => '[10x7.3] Estilo 01 ',
            '10x09'         => '[10x9] Estilo 01',
        ],
        'Vertical' => [
            '15x8'          => '[15x08] Estilo 01',
            '15x10'         => '[15x10] Estilo 01',
            '15x10-style01' => '[15x10] Estilo 02',
            '15x10-style02' => '[15x10] Estilo 03'
        ]
    ],

    'schedule' => [
        'frequencies' => [
            'day'   => 'Dias',
            'week'  => 'Semanas',
            'month' => 'Meses',
            'year'  => 'Anos',
        ],

        'month-frequencies' => [
            'day'    => 'No(s) dia(s)',
            'first'  => 'No(a) primeiro(a)',
            'second' => 'No(a) segundo(a)',
            'third'  => 'No(a) terceiro(a)',
            'fourth' => 'N(a)o quarto(a)',
            'fifth'  => 'No(a) quinto(a)',

        ]
    ],

    'incoterms' => [
        'Todos Meios Transporte' => [
            'dap' => 'DAP - Alfandega pelo destino',//'DAP – Entrega no local',
            'ddp' => 'DDP – Alfandega pelo remetente',

            'exw' => 'EXW – À saída da fábrica',
            'cip' => 'CIP – Porte e seguro pagos até',
            'dpu' => 'DPU – Entregue no local de descarga',
            'fca' => 'FCA – Franco transportado',
            'cpt' => 'CPT – Porte pago até',
            'fas' => 'FAS – Franco ao longo do navio',
            'fob' => 'FOB – Franco a bordo',
            'cfr' => 'CFR – Custo e carga',
            'cif' => 'CIF – Custo, seguro e carga',
        ],
        'Transporte Marítimo' => [
            'fas' => 'FAS – Franco ao longo do navio',
            'fob' => 'FOB – Franco a bordo',
            'cfr' => 'CFR – Custo e carga',
            'cif' => 'CIF – Custo, seguro e carga',
        ],
    ],

    'delivery_times' => [
        '00:01' => '1 min',
        '00:02' => '2 min',
        '00:03' => '3 min',
        '00:04' => '4 min',
        '00:05' => '5 min',
        '00:06' => '6 min',
        '00:07' => '7 min',
        '00:08' => '8 min',
        '00:09' => '9 min',
        '00:10' => '10 min',
        '00:15' => '15 min',
        '00:20' => '20 min',
        '00:25' => '25 min',
        '00:30' => '30 min',
        '00:45' => '45 min',
        '01:00' => '1h',
        '01:15' => '1h15',
        '01:30' => '1h30',
        '01:45' => '1h45',
        '02:00' => '2h',
        '02:15' => '2h15',
        '02:30' => '2h30',
        '02:45' => '2h45',
        '03:00' => '3h',
    ],

    'inactivity-reasons' => [
        '1' => 'Estava de baixa por doença',
        '2' => 'Gozava férias anuais',
        '3' => 'Gozava baixa ou período de repouso',
        '4' => 'Conduzia um veículo não abrangido pelo Regulamento (CE)n.º 561/2006 ou pelo AETR',
        '5' => 'Realizava outras atividades profissionais distintas da condução',
        '6' => 'Estava disponível'
    ],

    'billing-zones-types' => [
        'zip_code'      => 'Códigos Postais Destino',
        'country'       => 'País/Grupo Países',
        'distance'      => 'Distância desde remetente',
        'pack_type'     => 'Tipo embalagem',
        'matrix'        => 'Matriz Códigos Postais',
        'pack_zip_code' => 'Cod. Postal + Tipo embalagem',
        'pack_matrix'   => 'Matriz + Tipo embalagem',

    ],

    'services-map-markers' => [
        'marker_blue'           => 'Azul',
        'marker_blue_dark'      => 'Azul Escrto',
        'marker_blue_light'     => 'Azul Claro',
        'marker_turquoise'      => 'Azul Turquesa',
        'marker_green'          => 'Verde',
        'marker_green_light'    => 'Verde Claro',
        'marker_green_olive'    => 'Verde Oliva',
        'marker_orange'         => 'Laranja',
        'marker_yellow'         => 'Amarelo',
        'marker_red'            => 'Vermelho',
        'marker_red_dark'       => 'Vermelho Escuro',
        'marker_red_light'      => 'Vermelho Claro',
        'marker_pink'           => 'Rosa',
        'marker_purple'         => 'Roxo',
        'marker_brown'          => 'Castanho',
        'marker_gray'           => 'Cinzento',
    ],

    'route-details' => [
        'actions' => [
            'start'         => 'Inicio',
            'driving'       => 'Conduzir',
            'pause'         => 'Pausa',
            'not_working'    => 'Fora do horário de trabalho',
            'end'           => 'Concluído',
        ]
    ],

    'containers-types' => [

        '20NOR'  => '20" Standard',
        '20HC'   => '20" HC', //hight cube
        '20HCPW' => '20" HCPW',
        '20OT'   => '20" Open Top',
        '20HC'   => '20" Open Side',
        '20HT'   => '20" Hard Top',
        '20RF'   => '20" Refrigerado', //refrigerdado
        '20FR'   => '20" Flat Rack', //Flat rack

        '40NOR'  => '40" Standard',
        '40HC'   => '40" HC', //hight cube
        '40HCPW' => '40" HCPW',
        '40OT'   => '40" Open Top',
        '40HC'   => '40" Open Side',
        '40HT'   => '40" Hard Top',
        '40RF'   => '40" Reefer', //refrigerdado
        '40FR'   => '40" Flat Rack', 
        
        '45NOR' => '45" Standard',

        'bulk'      => 'Bulk', //transporte granel
        'tank'      => 'Tank',
        'combi'     => 'Combi',
        

    ],
    'adr-classes' => [
        "Classe 1" => [
            "1.1 - Explosivos",
            "1.2 - Gases inflamáveis",
            "1.3 - Gases não inflamáveis, não tóxicos",
            "1.4 - Gases tóxicos",
            "1.5 - Substâncias oxidantes",
            "1.6 - Substâncias perigosas diversas"
        ],
        "Classe 2" => [
            "2.1 - Gases inflamáveis",
            "2.2 - Gases não inflamáveis, não tóxicos",
            "2.3 - Gases tóxicos"
        ],
        "Classe 3" => "Líquidos inflamáveis",
        "Classe 4" => [
            "4.1 - Sólidos inflamáveis, substâncias autoreativas e explosivos desensibilizados",
            "4.2 - Substâncias sujeitas à inflamação espontânea",
            "4.3 - Substâncias que, em contato com a água, emitem gases inflamáveis"
        ],
        "Classe 5" => [
            "5.1 - Substâncias oxidantes",
            "5.2 - Peróxidos orgânicos"
        ],
        "Classe 6" => [
            "6.1 - Substâncias tóxicas",
            "6.2 - Substâncias infecciosas"
        ],
        "Classe 7" => "Material radioativo",
        "Classe 8" => "Materiais corrosivos",
        "Classe 9" => "Substâncias e objetos perigosos diversos"
    ]
];
