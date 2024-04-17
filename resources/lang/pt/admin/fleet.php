<?php

return [

    'brands' => [
        'types' => [
            'car'           => 'Marca de Automóveis',
            'truck'         => 'Marca de Camiões',
            'motorbikes'    => 'Marca de Motociclos',
            'nautical'      => 'Marca Nautica',
            'trailer'       => 'Marca de Reboques'
        ]
    ],

    'providers' => [
        'types' => [
            'mechanic'       => 'Mecânico',
            'insurer'        => 'Seguradora',
            'gas_station'    => 'Posto de Combustível',
            'car_inspection' => 'Centro de Inspeções',
            'maintenance'    => 'Manutenção',
            'incidence'      => 'Sinistro',
            'other'          => 'Outro'
        ]
    ],

    'accessories' => [
        'types' => [
            'extinguisher'   => 'Extintor',
            'straps'         => 'Cintas aperto',
            'gps'            => 'GPS portátil',
            'other'          => 'Outro'
        ]
    ],

    'usages-logs' => [
        'types' => [
            'break'      => 'Descanso',
            'driving'    => 'Condução',
            'works'      => 'Outros Trabalhos',
            'available'  => 'Disponibilidade',
            'outsourced' => 'Subcontratado'
        ],
        
        'types-color' => [
            'break'        => '#ffdd00',
            'driving'      => '#030f91',
            'works'        => '#b00202',
            'available'    => '#56ba00',
            'outsourced'   => '#f59002'
        ]
    ],

    'costs' => [
        'types' => [
            'mechanic'       => 'Oficinas e Mecânicos',
            'insurer'        => 'Seguradora',
            'gas_station'    => 'Posto de Combustível',
            'car_inspection' => 'Centro de Inspeções',
            'expenses'       => 'Despesas Gerais',
            'tolls'          => 'Portagens',
            'other'          => 'Outro'
        ]
    ],

    'fixed-costs' => [
        'types' => [
            'income'            => 'Prestação',
            'renting'           => 'Renting',
            'iuc'               => 'Imposto',
            'insurance'         => 'Seguro',
            'ipo'               => 'Inspeção',
            'salary'            => 'Vencimento',
            'administrative'    => 'Custos Administrativos',
            'tires'             => 'Desgaste Pneus',
            'wear'              => 'Desgaste Geral',
            'others'            => 'Outros',
        ]
    ],

    'fuel' => [
        'gasoline' => 'Gasolina',
        'diesel'   => 'Gasóleo',
        'gpl'      => 'GPL',
        'electric' => 'Eléctrico',
        'hibride-gasoline' => 'Hibrido a Gasolina',
        'hibride-diesel'   => 'Hibrido a Gasóleo'
    ],

    'vehicles' => [
        'status' => [
            'operacional' => 'Operacional',
            'damaged'     => 'Avariado',
            'maintenance' => 'Em manutenção',
            'sold'        => 'Vendido',
            'slaughter'   => 'Abatido',
            'inactive'    => 'Inativo',
        ],
        'status-color' => [
            'operacional' => 'label-success',
            'damaged'     => 'label-danger',
            'maintenance' => 'label-warning',
            'sold'        => 'label-default',
            'slaughter'   => 'label-default',
            'inactive'    => 'label-default',
        ],
        'types' => [
            'moto'      => 'Motociclo',
            'car'       => 'Carro',
            'small-van' => 'Carrinha',
            'van'       => 'Furgão',
            'mini-tir'  => 'Mini-TIR',
            'truck'     => 'Camião TIR',
            'forklift'  => 'Empilhador',
            'trailer'   => 'Reboque',
            'bus'       => 'Autocarro',
            'tractor'   => 'Tractor',
        ],
        'categories' => [
            'AM'  => 'AM',
            'A'   => 'A',
            'A1'  => 'A1',
            'A2'  => 'A2',
            'B'   => 'B',
            'B1'  => 'B1',
            'BE'  => 'B+E',
            'C'   => 'C',
            'C1'  => 'C1',
            'CE'  => 'C+E',
            'C1E' => 'C1+E',
            'D'   => 'D',
            'D1'  => 'D1',
            'DE'  => 'D+E',
            'D1E' => 'D1+E',
        ]
    ],

    'trailers' => [
        'status' => [
            'operacional' => 'Operacional',
            'damaged'     => 'Avariado',
            'maintenance' => 'Em Manutenção',
            'sold'        => 'Vendido',
            'slaughter'   => 'Abatido',
            'inactive'    => 'Inativo',
        ],
        'status-color' => [
            'operacional' => 'label-success',
            'damaged'     => 'label-danger',
            'maintenance' => 'label-warning',
            'sold'        => 'label-default',
            'slaughter'   => 'label-default',
            'inactive'    => 'label-default',
        ],
        'types' => [
            'normal'      => 'Lona',
            'hard'        => 'Rígido',
            'platform'    => 'Estrado',
            'mega'        => 'Mega',
            'semimega'    => 'Semi-Mega',
            'frigo'       => 'Frigorifíco',
            'cistern'     => 'Cisterna',
            'tipper'      => 'Basculante',
            'pcontainers' => 'Porta-containers',
            'small-van'   => 'Porta Bobines',
        ]
    ],

    'stats' => [
        'metrics' => [
            'yearly'  => 'Por ano',
            'monthly' => 'Por mês',
            'daily'   => 'Por dia'
        ],

        'daily' => [
            ''    => 'Especificar datas...',
            '30d' => 'Últimos 30 dias',
            '15d' => 'Últimos 15 dias',
            '7d'  => 'Últimos 7 dias',
            '1d'  => 'Ontem',
            '0'   => 'Hoje'
        ],
    ],

    'parts' => [
        'categories' => [
            'oils'          => 'Óleos e Fluidos',
            'filters'       => 'Filtros',
            'tyres'         => 'Pneus',
            'straps'        => 'Correias, Correntes e Roletes',
            'cooling'       => 'Refrigeração do Motor',
            'exaust'        => 'Sistema de Escape',
            'fuel'          => 'Sistema de Combustível',
            'electrical'    => 'Sistema Eléctrico e Iluminação',
            'breaks'        => 'Sistema Travagem',
            'gearing'       => 'Embraiagem e Caixa Velocidades',
            'engine'        => 'Peças do Motor',
            'glasses'       => 'Vidros',
            'ignition'      => 'Sistema Ignição',
            'suspension'    => 'Amortecedores e Suspensão',
            'steering'      => 'Direção',
            'ac'            => 'Aquecimento e Ar Condicionado',
            'bearings'      => 'Rolamentos',
            'others'        => 'Outras Peças'
        ],
    ],

    'checklists' => [
        'items-types' => [
            'input'   => 'Texto Livre',
            'numeric' => 'Numérico'
        ]
    ],
    
];
