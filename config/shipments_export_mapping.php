<?php

return [

    /*===========================================================
     * ENVIALIA
     ===========================================================*/
    'envialia-services' => [
        'REC24'     => '24',
        'PT72'      => '72',
        'ES72'      => '72',
        '72H'       => '72',
        'PT24'      => '24',
        'ES24'      => '24',
        '24H'       => '24',
        'PT14'      => '14',
        'ES14'      => '14',
        '14H'       => '14',
        'PT10'      => '10',
        'ES10'      => '10',
        '10H'       => '10',
        'PT839'     => '830',
        'ES830'     => '830',
        '8H30'      => '830',
        'CC'        => 'CC',
        'RET24H'    => 'RET',
        'DEV'       => 'DEV',
        'DIST'      => '24',
        'RECDIST'   => '24',
        '100'       => '100',
        '200'       => '200',
        'RCS'       => 'RCS',
        '101'       => '101',
        '24ECO'     => '24',
        'RED'       => 'RED',
        '24HPT'     => '72',
        'B2C'       => 'B2C',
        'XL'        => 'XL',
        'AI'        => '24',
        'MI'        => '24',
        'INT-T'     => '200',
        'INT-A'     => '100',
        'INT-BG'    => '101',
    ],

    'envialia-incidences' => [
        //'' => '0000', //atuação automática
        '17' => '9998', //Resolução pelo destinatário - Envio modificado
        '9999' => '9999', //livre
        '01' => 'CA02', //devolucao cargo
        '02' => 'CA03', //devolucao origem
        '04' => 'CA04', //recanalizar
        '06' => 'CA05', //recolhe em delegação
        '07' => 'CA06', //entrega no dia seguinte - nova morada
        '08' => 'CA07', //entrega no dia seguinte - novo horário
        '03' => 'CA08', //Entrega no dia seguinte
        '09' => 'CA09', //Entrega no dia seguinte sem retorno
        '10' => 'CA10', //Anulado/Modificado reembolso - envio dia seguinte
        '11' => 'CA11', //Anulado/Modificado portes - envio dia seguinte
        '12' => 'CA12', //O destinatário aceita custos alfandega - envio dia seguinte
        '13' => 'CA13', //Custos alfandega ao nosso encargo - envio dia seguinte
        '14' => 'CA14', //expedição incompleta no dia seguinte
        '04' => 'CA15', //recanalizar
        '15' => 'CA16', //entrega adicional
        '05' => 'CA17', //Destruir
        '16' => 'CA19', //Entrega reclamada
    ],

    /*===========================================================
     * TIPSA
     ===========================================================*/
    'tipsa-services' => [
        '72H'       => 'MV',
        '19H'       => '19',
        '24H'       => '48',
        '48H'       => '48',
        '14H'       => '14',
        '10H'       => '10',
        '8H30'      => '830',
        'CC'        => 'CC',
        'RET24H'    => '05',
        'DEV'       => '03',
        'DIST'      => '14',
        'RECDIST'   => '14',
        'REC24'     => '48',
        '100'       => '90', //internacional expresso
        '200'       => '92', //DPD classic (em alternativa 91 - INTERNACIONAL STANDARD)
        '101'       => '91',
        'INT-A'     => '90', //internacional expresso
        'INT-T'     => '92', //DPD classic (em alternativa 91 - INTERNACIONAL STANDARD)
        '24ECO'     => '14',
        'ECO'       => '48',
        'RA'        => '20',
        'AI'        => '14',
        'MI'        => '96',
        'RCS'       => 'RCS',
        'DPD'       => '92', //DPD classic
        'RED'       => '20',
        'BAL-M'     => '06',
        '04'        => '04',
        'VAL'       => '04',
    ],

    'tipsa-incidences' => [
        '9999' => '9999', //livre
        '01' => '5', //devolucao cargo
        '02' => '5', //devolucao origem
        '04' => '7', //recanalizar
        '06' => '3', //recolhe em delegação
        '07' => '4', //entrega no dia seguinte - nova morada
        '08' => '1', //entrega no dia seguinte - novo horário
        '03' => '1', //Entrega no dia seguinte
        '09' => '1', //Entrega no dia seguinte sem retorno
        '10' => '', //Anulado/Modificado reembolso - envio dia seguinte
        '11' => '', //Anulado/Modificado portes - envio dia seguinte
        '12' => '', //O destinatário aceita custos alfandega - envio dia seguinte
        '13' => '', //Custos alfandega ao nosso encargo - envio dia seguinte
        '14' => '2', //expedição incompleta no dia seguinte
        '04' => '7', //recanalizar
        '15' => '', //entrega adicional
        '05' => '13', //Destruir
        '16' => '', //Entrega reclamada
    ],


    /*===========================================================
     * GLS ATLAS
     ===========================================================*/
    'gls_zeta-services' => [
        'REC24'     => '7', //recolha
        'RECINT'    => '74', //recolha
        'REC-INT'   => '74', //recolha
        'RINT'      => '74',
        '72H'       => '37', //economy
        '24H'       => '1', //courier
        '14H'       => '1', //courier
        '10H'       => '1', //courier
        '8H30'      => '1', //courier
        '08H30'     => '1', //courier
        'CC'        => '',
        'RET24H'    => '10', //retorno
        'DEV'       => '9',
        'DIST'      => '1', //courier
        'RECDIST'   => '7', //RECOLHA
        '100'       => '74', //EUROBUSINESS
        '200'       => '74', //EUROBUSINESS
        'RCS'       => '',
        '101'       => '76', //EUROBUSINESS SMALL
        'INT-SP'    => '76', //EUROBUSINESS SMALL
        '24ECO'     => '1',  //COURIER
        '19H'       => '1',
        'I19H'      => '1',
        '48H'       => '1',
        'AI'        => '1', //AEREO ILHAS
        'G-AI'      => '1',
        'MI'        => '6', //MARITIMO ILHAS
        'G-MI'      => '6',
        'IT'        => '74',
        'I48H'      => '1',
        'IA'        => '74',
        'G-IA'      => '74',
        'INT-T'     => '74',
        'INT-A'     => '74',
        '300'       => '74',
        '301'       => '76',
        '48H'       => '37',
        'E300'      => '74', //eurobusiness
        'RED'       => '11',
        'B2B'       => '1'
    ],

    'gls_zeta-horarios' => [
        '24H'       => '3',  //ASM 24
        '14H'       => '2',  //ASM14, 5= SABADOS, 11=REC. AGENCIA
        '10H'       => '0',  //ASM10,
        '8h30'      => '30', //ASM 8H30
        '72'        => '18', //ECONOMY
        '19H'       => '3',
        'I19H'      => '3',
        '48H'       => '3',
        'I48H'      => '10',
        'MI'        => '10', //maritimo ilhas
        'G-MI'      => '10',
        'AI'        => '3',
        'G-AI'      => '3',
        'RED'       => '3',
        'B2B'       => '3'
        //''  => '5', //SABADOS
    ],

    'gls_zeta-incidences' => [
        '9999' => '31', //livre
        '01' => '39', //devolucao cargo
        '02' => '39', //devolucao origem
        '04' => '31', //recanalizar
        '06' => '31', //recolhe em delegação
        '07' => '31', //entrega no dia seguinte - nova morada
        '08' => '31', //entrega no dia seguinte - novo horário
        '03' => '31', //Entrega no dia seguinte
        '09' => '31', //Entrega no dia seguinte sem retorno
        '10' => '31', //Anulado/Modificado reembolso - envio dia seguinte
        '11' => '31', //Anulado/Modificado portes - envio dia seguinte
        '12' => '31', //O destinatário aceita custos alfandega - envio dia seguinte
        '13' => '31', //Custos alfandega ao nosso encargo - envio dia seguinte
        '14' => '31', //expedição incompleta no dia seguinte
        '04' => '31', //recanalizar
        '15' => '31', //entrega adicional
        '05' => '46', //Destruir
        '16' => '31', //Entrega reclamada
    ],

    'gls_zeta-incidences-collection' => [
        '9999' => '1031', //livre
        '05'   => '1039', //Destruir
    ],

    /*===========================================================
     * CTT
     ===========================================================*/
    /*
    'EMSF009.01', //10
    'EMSF001.01', //13
    'EMSF028.01', //13 Multiplo
    'ENCF005.01', //19
    'EMSF010.01', //19 Multiplo
    'EMSF021.02', //19 Espanha
    'ENCF008.01', //48
    'EMSF038.02', //48 ESPANHA
    'EMSF015.01', //Cargo
    'ENCF008.02', //EMS Economy
    'EMSF001.02', //EMS Internacional
    'EMSF056.01', //E-segue (Para amanhã)
    'EMSF058.01', //Em ponto CTT
    'EMSF057.01', //Em 2 dias
    'EMSF002.02', //Internacional Premium
    'EMSF070.01', //Carga Paletes
    'EMSF071.01', //Carga Volumes
    'EMSF003.02', //Europa Light
    */

    'ctt-services' => [
        'tartarugaveloz' => [
            '24H'       => 'EMSF028.01', //13M
            'M24H'      => 'EMSF028.01', //13M
            '14H'       => 'EMSF028.01', //13M
            'M14H'      => 'EMSF028.01', //13M
            'TXL'       => 'EMSF038.02', //48
            'MI'        => 'ENCF008.01', //48
            'MMI'       => 'ENCF008.01', //48
            'AI'        => 'ENCF008.01', //48
            'MAI'       => 'ENCF008.01', //48
            'REC24'     => 'EMSF028.01', //13M
        ],
        'log24' => [
            '24H'       => 'ENCF005.01', //19
            'M24H'      => 'EMSF010.01', //19M
            '14H'       => 'EMSF028.01', //13
            'M14H'      => 'EMSF028.01', //13
            'INT-T'     => 'EMSF001.02', //EMS Internacional
        ],
        'viaxl' => [
            '24H'       => 'ENCF005.01', //19
            'M24H'      => 'EMSF010.01', //19M
            '14H'       => 'EMSF028.01', //13
            'M14H'      => 'EMSF028.01', //13
            'INT-T'     => 'EMSF001.02', //EMS Internacional
        ],
        'nmxtransportes' => [
            '24H'       => 'EMSF010.01', //19M
            'M24H'      => 'EMSF010.01', //19M
            '14H'       => 'EMSF028.01', //13M
            'M14H'      => 'EMSF028.01', //13M
            '72H'       => 'ENCF008.01', //48
            'M72H'      => 'ENCF008.01', //48
            'CARGO'     => 'EMSF070.01', //CARGO
            'MCARGO'    => 'EMSF070.01', //CARGO
        ],
        'rlrexpress' => [
            '14H'  => 'EMSF001.01', //13
            'M14H' => 'EMSF028.01', //13M
            '19H'  => 'ENCF005.01', //19
            'M19H' => 'EMSF010.01', //19M
            '72H'  => 'ENCF008.01', //48
            'MI'   => 'ENCF008.01', //48
            'MMI'  => 'ENCF008.01', //48
            'AI'   => 'ENCF005.01', //19
            'MAI'  => 'EMSF010.01', //19M
            'ESG'  => 'EMSF056.01', //E-segue
            'MESG' => 'EMSF056.01', //E-segue
        ],
        'fozpost' => [
            '14H'   => 'EMSF001.01', //13
            'M14H'  => 'EMSF028.01', //13M
            '24H'   => 'ENCF005.01', //19
            'M24H'  => 'EMSF010.01', //19M
            '19M'   => 'EMSF010.01', //19M
            'M19M'  => 'EMSF010.01', //19M
            '72H'   => 'ENCF008.01', //48
            'M72H'  => 'ENCF008.01', //48
            'MI'    => 'ENCF008.01', //48
            'MMI'   => 'ENCF008.01', //48
            'AI'    => 'ENCF005.01', //19
            'MAI'   => 'EMSF010.01', //19 M
            'INT-T' => 'ENCF008.02', //EMS Economy
            'INT-A' => 'EMSF001.02', //EMS Internacional
            'ESG'   => 'EMSF056.01', //E-segue
            'RED'     => 'EMSF058.01', //Ponto Ctt
        ],
        'asfaltolargo' => [
            '14H'  => 'EMSF001.01', //13
            'M14H' => 'EMSF028.01', //13M
            '24H'  => 'ENCF005.01', //19
            'M24H' => 'EMSF010.01', //19M
            '72H'  => 'ENCF008.01', //48
        ],
        'morluexpress' => [
            '24H'       => 'ENCF005.01', //19M
            'M24H'      => 'ENCF005.01', //19M
            '14H'       => 'ENCF005.01', //19M
            'M14H'      => 'ENCF005.01', //19M
            '72H'       => 'ENCF008.02', //Economy
            'M72H'      => 'ENCF008.02', //Economy
        ],
        'entregaki' => [
            '14H'    => 'EMSF001.01', //13
            'M14H'   => 'EMSF028.01', //13M
            '14H'    => 'EMSF001.01', //13
            '13H'    => 'EMSF028.01', //13M
            'M13H'   => 'EMSF028.01', //13M
            'CTT13M' => 'EMSF028.01', //13M
            '24H'    => 'ENCF005.01', //19
            'M24H'   => 'EMSF010.01', //19M
            '72H'    => 'ENCF008.01', //48
            'MI'   => 'ENCF008.01', //48
            'MMI'  => 'ENCF008.01', //48
            'AI'   => 'ENCF005.01', //19
            'MAI'  => 'EMSF010.01',  //19M
            'ESG'  => 'EMSF056.01', //E-segue
            '24SEG' => 'ENCF005.01', //19
            'M24SEG' => 'EMSF010.01', //19M
            'CARGO' => 'EMSF015.01', //Cargo
            'REC24'  => 'ENCF005.01', //19
            'MREC24' => 'EMSF010.01', //19M
            'RECMI'   => 'ENCF008.01', //48
            'MRECMI'  => 'ENCF008.01', //48
            'INT-PR' => 'EMSF002.02', //EMS Internacional Premium
            'ILHASA' => 'ENCF005.01', //19
            'E48H'   => 'EMSF038.02',
            'ME48H'  => 'EMSF038.02',
        ],
        'activos24' => [
            /*'14H'  => 'EMSF001.01', //13
            'M14H' => 'EMSF028.01', //13M
            '24H'  => 'ENCF005.01', //19
            'M24H' => 'EMSF010.01', //19M
            '72H'  => 'ENCF008.01', //48
            'M72H'  => 'ENCF008.01', //48
            'MI'   => 'ENCF008.01', //48
            'MMI'  => 'ENCF008.01', //48
            'AI'   => 'ENCF005.01', //19
            'MAI'  => 'EMSF010.01',  //19M
            'ESG'  => 'EMSF056.01', //E-segue*/

            '24H'  => 'EMSF056.01', //Para amanha
            'M24H' => 'EMSF056.01', //Para amanha
            'MI'   => 'EMSF057.01', //Em 2 dias
            'MMI'  => 'EMSF057.01', //Em 2 dias
            'AI'   => 'EMSF057.01', //Em 2 dias
            'MAI'  => 'EMSF057.01', //Em 2 dias
            '72H'  => 'EMSF057.01', //Em 2 dias
            'M72H' => 'EMSF057.01', //Em 2 dias
        ],
        'aveirofast' => [
            '14H'   => 'EMSF001.01', //13
            'M14H'  => 'EMSF028.01', //13M
            '24H'   => 'EMSF010.01', //19M //E-mail Carla 11/08/21
            'M24H'  => 'EMSF010.01', //19M
            '19M'   => 'EMSF010.01', //19M
            'M19M'  => 'EMSF010.01', //19M
            '72H'   => 'ENCF008.01', //48
            'M72H'  => 'ENCF008.01', //48
            'ES72H' => 'ENCF008.02', //48 ES
            'MI'    => 'ENCF008.01', //48
            'MMI'   => 'ENCF008.01', //48
            'AI'    => 'ENCF005.01', //19
            'MAI'   => 'EMSF010.01', //19 M
            'INT-T' => 'ENCF008.02', //EMS Economy
            'INT-A' => 'EMSF001.02', //EMS Internacional
            'ESG'   => 'EMSF056.01', //E-segue
            'PLTIB'   => 'EMSF015.01', //Palete
            'MPLTIB'  => 'EMSF015.01', //Palete
            '24PLUS'  => 'EMSF056.01', //E-segue
            'M24PLUS' => 'EMSF056.01', //E-segue
            'RED'     => 'EMSF058.01', //Ponto Ctt
        ],
        'gigantexpress' => [
            '10H'   => 'EMSF009.01', //10
            'M10H'  => 'EMSF009.01', //10
            '14H'   => 'EMSF028.01', //13
            'M14H'  => 'EMSF028.01', //13M
            '24H'   => 'EMSF010.01', //19M
            'M24H'  => 'EMSF010.01', //19M
            '72H'   => 'ENCF008.01', //48
            'M72H'  => 'ENCF008.01', //48
            'ES72H' => 'ENCF008.02', //48 ES
            'MI'    => 'ENCF008.01', //48
            'MMI'   => 'ENCF008.01', //48
            'AI'    => 'EMSF010.01', //19 M
            'MAI'   => 'EMSF010.01', //19 M
            'INT-T' => 'ENCF008.02', //EMS Economy
            'INT-A' => 'EMSF001.02', //EMS Internacional
        ],
        'asfaltolargo' => [
            /* '10H'   => 'EMSF009.01', //10
            'M10H'  => 'EMSF009.01', //10
            '14H'   => 'EMSF001.01', //13
            'M14H'  => 'EMSF028.01', //13M*/
            '24H'   => 'ENCF005.01', //19
            'M24H'  => 'EMSF010.01', //19
            '72H'   => 'ENCF008.01', //48
            'M72H'  => 'ENCF008.01', //48
            'ES72H' => 'ENCF008.02', //48 ES
            'MI'    => 'ENCF008.01', //48
            'MMI'   => 'ENCF008.01', //48
            'AI'    => 'ENCF005.01', //19
            'MAI'   => 'EMSF010.01', //19 M
            /*'INT-T' => 'ENCF008.02', //EMS Economy
            'INT-A' => 'EMSF001.02', //EMS Internacional*/
        ],
        'ship2u' => [
            'STD'     => 'ENCF008.01', //48
            'MSTD'    => 'EMSF056.01',
            'ESSTD'   => 'EMSF057.01', //em 2 dias

            'EXPR'    => 'EMSF056.01',
            'MEXPR'   => 'EMSF056.01',
            'B2B'     => 'ENCF005.01', //19
            'MB2B'    => 'EMSF010.01', //19M
            'ESEXPR'  => 'EMSF056.01', //para amanha

            'EXPRI'   => 'EMSF056.01', //para amanha
            'MEXPRI'  => 'EMSF056.01', //para amanha
            'STDI'    => 'ENCF008.01', //48
            'MSTDI'   => 'ENCF008.01', //48

            'P9'     => 'ENCF008.02', //10H
            'P10'    => 'ENCF008.01', //48
            'P12'    => 'ENCF008.01', //48
            'CARGO'  => 'EMSF071.01', //Carga Volumes
            'PAL2U'  => 'EMSF070.01', //Carga Paletes

            'EEXP'   => 'EMSF002.02', //EMS Internacional Premium
            'W2UECO' => 'ENCF008.02', //EMS Economy
            'ESTD'   => 'EMSF001.02', //EMS Internacional
        ],
        'mudacarga' => [
            '24H'  => 'EMSF056.01', //Para amanha
            'M24H' => 'EMSF056.01', //Para amanha
            '72H'  => 'ENCF008.01', //48
            'M72H' => 'ENCF008.01', //48
            'MI'   => 'ENCF008.01', //48
            'MMI'  => 'ENCF008.01', //48
            'AI'   => 'ENCF008.01', //48
            'MAI'  => 'ENCF008.01', //48
        ],
        'xkl' => [
            '24H'  => 'ENCF005.01', //19
            'M24H' => 'EMSF010.01', //19M
            '48H'  => 'ENCF008.01', //48
            'M48H' => 'ENCF008.01', //48
            '72H'  => 'ENCF008.01', //48
            'M72H' => 'ENCF008.01', //48
            'MI'   => 'ENCF008.01', //48
            'MMI'  => 'ENCF008.01', //48
            'AI'   => 'ENCF008.01', //48
            'MAI'  => 'ENCF008.01', //48
        ],
        'scandilog' => [
            'PRG'  => 'EMSF056.01', //Para amanha
            'MPRG' => 'EMSF056.01', //Para amanha
            //''   => 'EMSF057.01', //Em 2 dias
        ],
        'trilhosdinamicos' => [
            '24H'     => 'ENCF005.01', //19
            'M24H'    => 'EMSF010.01', //19M
            '24PLUS'  => 'EMSF056.01', //PARA AMANHA
            'M24PLUS' => 'EMSF056.01', //PARA AMANHA
            '48H'     => 'ENCF008.01', //48
            'M48H'    => 'ENCF008.01', //48
            '72H'  => 'ENCF008.01', //48
            'M72H' => 'ENCF008.01', //48
            'MI'   => 'ENCF008.01', //48
            'MMI'  => 'ENCF008.01', //48
            'AI'   => 'ENCF008.01', //48
            'MAI'  => 'ENCF008.01', //48
            'CARGO' => 'EMSF071.01', //Carga Volumes
            'PAL'  => 'EMSF070.01', //Carga Paletes

        ],
        'transcapital' => [
            // '24H'     => 'ENCF005.01', //19
            '24H'    => 'EMSF010.01', //19M
            '48H'     => 'ENCF008.01', //48
            'M48H'    => 'ENCF008.01', //48
            'MI'      => 'ENCF008.01', //48
            'MMI'     => 'ENCF008.01', //48
            'AI'      => 'ENCF008.01', //48
            'MAI'     => 'ENCF008.01', //48
            'INT-T'   => 'EMSF001.02', //EMS Internacional

        ],
    ],

    'ctt-services-internacional' => [ //inclui espanha
        'tartarugaveloz' => [
            'INT-T'     => 'EMSF001.02', //EMS Internacional
            'INT-A'     => 'EMSF002.02', //EMS INTERNACIONAL PREMIUM
            'E48H'      => 'EMSF038.02', //48 ESPANHA
            'ME48H'     => 'EMSF038.02', //48 ESPANHA
        ],
        'log24' => [
            'INT-T'     => 'EMSF001.02', //EMS Internacional
        ],
        'viaxl' => [
            'INT-T'     => 'EMSF001.02', //EMS Internacional
        ],
        'nmxtransportes' => [
            'INT-T'     => 'EMSF001.02', //EMS Internacional
        ],
        'rlrexpress' => [
            '24H'   => 'ENCF005.01', //19
            'INT-T' => 'ENCF008.02', //EMS
            'INT-A' => 'EMSF001.02', //EMS Internacional
            '24H'   => 'ENCF005.01', //19
            'M24H'  => 'EMSF010.01', //19M
            '19H'   => 'ENCF005.01', //19
            'M19H'  => 'EMSF010.01', //19M
            'ESG'   => 'EMSF056.01', //E-segue
        ],
        'entregaki' => [
            'INT-T'  => 'ENCF008.02', //EMS Economy
            'INT-PR' => 'EMSF002.02', //EMS Internacional Premium
            //'EMSINT' => 'EMSF001.02', //EMS Internacional
            '14H'    => 'EMFS001.01', //13
            'M14H'   => 'EMSF028.01', //13M
            '13H'    => 'EMSF028.01', //13M
            'M13H'   => 'EMSF028.01', //13M
            '24H'    => 'ENCF005.01', //19
            'M24H'   => 'EMSF010.01', //19M
            'ILHASA' => 'ENCF005.01', //19
            'E48H'   => 'EMSF038.02', //48Espanha
            'ME48H'  => 'EMSF038.02', //48Espanha
        ],
        'aveirofast' => [
            'INT-T' => 'ENCF008.02', //EMS Economy
            '14H'   => 'EMSF001.01', //13
            'M14H'  => 'EMSF028.01', //13M
            '24H'   => 'EMSF010.01', //19 M //a pedido carla
            'M24H'  => 'EMSF010.01', //19 M
            '72H'   => 'ENCF008.01', //48
            'M72H'  => 'ENCF008.01', //48
        ],
        'activos24' => [
            /*'14H'  => 'EMSF001.01', //13
            'M14H' => 'EMSF028.01', //13M
            '24H'  => 'ENCF005.01', //19
            'M24H' => 'EMSF010.01', //19M
            '72H'  => 'ENCF008.01', //48
            'M72H'  => 'ENCF008.01', //48
            'MI'   => 'ENCF008.01', //48
            'MMI'  => 'ENCF008.01', //48
            'AI'   => 'ENCF005.01', //19
            'MAI'  => 'EMSF010.01',  //19M
            'ESG'  => 'EMSF056.01', //E-segue*/

            '24H'  => 'EMSF056.01', //Para amanha
            'M24H' => 'EMSF056.01', //Para amanha
            'MI'   => 'EMSF057.01', //Em 2 dias
            'MMI'  => 'EMSF057.01', //Em 2 dias
            'AI'   => 'EMSF057.01', //Em 2 dias
            'MAI'  => 'EMSF057.01', //Em 2 dias
            '72H'  => 'EMSF057.01', //Em 2 dias
            'M72H' => 'EMSF057.01', //Em 2 dias
        ],
        'asfaltolargo' => [
            '14H'  => 'EMSF001.01', //13
            'M14H' => 'EMSF028.01', //13M
            '24H'  => 'ENCF005.01', //19
            'M24H' => 'EMSF010.01', //19M
            '72H'  => 'ENCF008.01', //48
        ],
        'gigantexpress' => [
            '24H'   => 'EMSF010.01', //19M
            'M24H'  => 'EMSF010.01', //19M
        ],
        'ship2u' => [
            'STD'     => 'ENCF008.01', //48
            'MSTD'    => 'EMSF056.01',
            'ESSTD'   => 'EMSF057.01', //em 2 dias

            'EXPR'    => 'EMSF056.01',
            'MEXPR'   => 'EMSF056.01',
            'B2B'     => 'ENCF005.01', //19
            'MB2B'    => 'EMSF010.01', //19M
            'ESEXPR'  => 'EMSF056.01', //para amanha

            'EXPRI'   => 'EMSF056.01', //para amanha
            'MEXPRI'  => 'EMSF056.01', //para amanha
            'STDI'    => 'ENCF008.01', //48
            'MSTDI'   => 'ENCF008.01', //48

            'P9'     => 'ENCF008.02', //10H
            'P10'    => 'ENCF008.01', //48
            'P12'    => 'ENCF008.01', //48
            'CARGO'  => 'EMSF071.01', //Carga Volumes
            'PAL2U'  => 'EMSF070.01', //Carga Paletes

            'EEXP'   => 'EMSF002.02', //EMS Internacional Premium
            'W2UECO' => 'ENCF008.02', //EMS Economy
            'ESTD'   => 'EMSF001.02', //EMS Internacional
        ],
        'mudacarga' => [
            'INT-T'     => 'EMSF001.02', //EMS Internacional
            'INT-A'     => 'EMSF001.02', //EMS Internacional
        ],
        'trilhosdinamicos' => [
            '24H'     => 'EMSF021.02', //19 espanha
            'M24H'    => 'EMSF021.02', //19 espanha
            '24PLUS'  => 'EMSF056.01', //PARA AMANHA
            'M24PLUS' => 'EMSF056.01', //PARA AMANHA
            '48H'     => 'ENCF008.01', //48
            'M48H'    => 'ENCF008.01', //48
            '72H'     => 'ENCF008.01', //48
            'M72H'    => 'ENCF008.01', //48
            'MI'      => 'ENCF008.01', //48
            'MMI'     => 'ENCF008.01', //48
            'AI'      => 'ENCF008.01', //48
            'MAI'     => 'ENCF008.01', //48
            'CARGO'   => 'EMSF071.01', //Carga Volumes
            'PAL'     => 'EMSF070.01', //Carga Paletes
        ],
        'fozpost' => [
            '14H'   => 'EMSF001.01', //13
            'M14H'  => 'EMSF028.01', //13M
            '24H'   => 'ENCF005.01', //19
            'M24H'  => 'EMSF010.01', //19M
            '19M'   => 'EMSF010.01', //19M
            'M19M'  => 'EMSF010.01', //19M
            '72H'   => 'ENCF008.01', //48
            'M72H'  => 'ENCF008.01', //48
            'MI'    => 'ENCF008.01', //48
            'MMI'   => 'ENCF008.01', //48
            'AI'    => 'ENCF005.01', //19
            'MAI'   => 'EMSF010.01', //19 M
            'INT-T' => 'ENCF008.02', //EMS Economy
            'INT-A' => 'EMSF001.02', //EMS Internacional
            'ESG'   => 'EMSF056.01', //E-segue
            'RED'     => 'EMSF058.01', //Ponto Ctt
        ],
    ],

    /*===========================================================
     * CHRONOPOST
     ===========================================================*/
    'chronopost-services' => [
        //Formato: country#service | Valores para country: PT, ES ou INT
        'tartarugaveloz' => [
            'INT#IT'   => '02362501', //Internacional Terrestre
            'INT#E300' => '02362501', //Internacional Terrestre
            'PT#24H'  => '02362503', //Chrono18

            'INT#IT'   => '02277601', //Internacional Terrestre
            'PT#12H'  => '02277602', //Chrono13
            'PT#19H'  => '02277603', //Chrono18
        ],
        'tortugaveloz' => [
            'PT#24H'   => '02483401', //Chrono18
            'PT#10H'   => '02483403', //Chrono10
            'PT#19H'   => '02483401', //Chrono18
            'ES#24H' => '02483404', //Exportação Espanha Terrestre
        ],
        'pontualhd' => [
            'PT#24H'   => '01880001', //Chrono18 + Ilhas
            'PT#72H'   => '01880006', //Express Economy
            'ES#REC24' => '01880005', //Espanha Importação
            'ES#ES24H' => '01880004', //Espanha Exportação
            //'SAB' => '01880002', //Entrega Sábado
            //''    => '01880003' // Home Delivery Ch18 (Predict só por mail)
        ],
        'aveirofast' => [
            'PT#10H'   => '02491404', //Chrono 10
            'PT#10H'   => '02491404', //Chrono 10
            'PT#14H'   => '02491401', //Chrono13
            'PT#24H'   => '02491402', //Chrono18
            'ES#24H'   => '02491407', //expotação espanha
            'PT#INT-T' => '02491408', //internacional
            'ES#REC24' => '02491402', //Espanha Importação
            'PT#300'   => '02491408', //internacional
            //'SAB' => '02491403', //Entrega Sábado
            //''    => '02491405' // Home Delivery Ch18 (Predict só por mail)
        ],
        'gigantexpress' => [
            'PT#10H'   => '02491503', //Chrono10
            'PT#14H'   => '02491501', //Chrono13 AM2
            'PT#24H'   => '02491504', //Chrono18 empresas.
            //'PT#24H'   => '02491505', //Chrono18 particulares
            'PT#72H'   => '01880006', //Express Economy
            'PT#REC24' => '02491506', //pickup
            'PT#REC-INT' => '02491508', //Importação
            'PT#INT-T' => '02491508', //Exportação
            'PT#SAB'    => '02491502',
            //'sabado' => '02491502'
        ],
        'rlrexpress' => [
            'INT#INT-T' => '02346001', //Internacional Terrestre
            'ES#24H'    => '02346002', //expotação espanha
        ],
        'transportesvascosantos' => [
            'PT#24H'    => '02803001', //Chrono 18
            'ES#24H'    => '02803002', //seur exportação
            'INT-T'     => '02803002', //seur exportação
            'RECINT'    => '02803003', //seur importacao
        ],
        'tma' => [
            'PT#8H30'   => '01733601', //Chrono 8
            'PT#24H'    => '01733601', //Chrono 18
            'ES#24H'    => '01733601', //Chrono 18
            'PT#14H'    => '01733604', //Chono 13
            'ES#14H'    => '01733604', //Chono 13
            'PT#10H'    => '01733603', //Chono 10
            'ES#10H'    => '01733603', //Chono 10
            'PT#INT-T'  => '01733605', //Internacional Terreste
            'ES#INT-T'  => '01733605', //Internacional Terreste
            'INT#INT-T' => '01733605', //Internacional Terreste
            'PT#INT-A'  => '01733615', //Internacional Aéreo
            'ES#INT-A'  => '01733615', //Internacional Aéreo
            'INT#INT-A' => '01733615', //Internacional Aéreo
            //01733612 => chrono 18 + devolucao guia
        ],
        'flytime' => [
            'PT#24H'   => '02872501', //Chrono18
            //'PT24H' => '02872503', //DPD 18 particulares
            'ES#24H'   => '02872505', //exportação espanha
            'PT#INT-T' => '02872504', //internacional
        ],
        'entregaki' => [
            '14H'  => '02863401', //chrono 18
            '13H'  => '02863401',
            '24H'  => '02863401',
            '72H'  => '02863401',
            'MI'   => '02863401',
            'AI'   => '02863401',
        ],
        'cellarsconnect' => [
            'PT#INT-A'   => '02916301', //DPD Home 18 + Ilhas
            'ES#INT-A'   => '02916304', //SEUR HOME Export
            'INT#INT-A'  => '02916302', //DPD Home - Export
            'PT#INT-T'   => '02916301', //DPD Home 18 + Ilhas
            'ES#INT-T'   => '02916304', //SEUR HOME Export
            'INT#INT-T'  => '02916302', //DPD Home - Export
        ],
        'okestafetas' => [
            'PT#24H'    => '02998801', //DPD Home 18
            'ES#24H'    => '02998801', //DPD Home 18
            'PT#14H'    => '02998803', //DPD 13
            'ES#14H'    => '02998803', //DPD 13
        ],
        'corridadotempo' => [
            'PT#SBD'    => '01740802', //DPD SABADO
            'PT#24H'    => '01740801',
            'PT#AI'     => '01740801',
            'INT#INT-T' => '01740803',
            'INT#INT-A' => '01740804',
        ],
        'rapidix' => [
            'PT#14H'   => '01752701', //chrono 18
            'ES#14H'   => '01752701', //chrono 18
            'PT#13H'   => '01752706', //chrono 13
            'ES#13H'   => '01752706', //chrono 13
            'PT#24H'   => '01752701', //chrono 18
            'ES#24H'   => '01752701', //chrono 18
            'PUDO#PKPT' => '01752707', //Pickup point
            'PUDO#24H' => '01752707', //entrega em pickup point
            'PT#MI'    => '01752701',
            'PT#AI'    => '01752701',
            'INT#INT-T' => '01752703', //DPD Internacional Terrestre
            'INT#INT-A' => '01752704', //DPD Internacional Aereo
            //01752702 - DPD Espanha Terrestre (B2B)
            //01752705 - DPDSabado (B2B)
            //01752708 - HomeDelivery DPD18 + DPDshop (B2C)
            //01752712 - HomeDelivery DPD18 (B2C)
        ],
        'packbox' => [
            'PT#NVOL'   => '01733701',
            'PT#NVOLU'  => '01733701',
            'PT#INT'    => '01733704',
            'ES#IES'    => '01733706',
            'PT#D+PKU'  => '01733709',
            'PT#PKU'    => '01733711',

            // 01733701 - DPD18 (B2B)
            // 01733702 - DPD18 com devolução de guia de remessa assinada (B2B)
            // 01733703 - DPD Sabado (B2B)
            // 01733704 - DPD Internacional Terrestre
            // 01733706 - DPD Espanha Terrestre
            // 01733708 - DPD18 (B2B) - MAIA
            // 01733709 - HomeDelivery DPD18 + DPDshop (B2C) - SINTRA
            // 01733710 - HomeDelivery DPD18 + DPDshop (B2C) - MAIA
            // 01733711 - DPDshop - entregas em pontos Pickup DPD (B2C) - SINTRA
            // 01733712 - DPD18 com devolução de guia de remessa assinada (B2B) - MAIA
            // 01733713 - DPDshop - entregas em pontos Pickup DPD (B2C) - MAIA
        ],
    ],

    /*===========================================================
     * FEDEX
     ===========================================================*/
    'fedex-services' => [
        '24H'   => 'INTERNATIONAL_PRIORITY',
        'IT'    => 'INTERNATIONAL_PRIORITY',
        'INT-T' => 'INTERNATIONAL_PRIORITY',
        'INT-A' => 'INTERNATIONAL_PRIORITY',
        'EEXP'  => 'INTERNATIONAL_PRIORITY',
        'ESTD'  => 'INTERNATIONAL_ECONOMY',
        'GROUND' => 'FEDEX_GROUND',
    ],

    /*===========================================================
     * NACEX
     ===========================================================*/
    'nacex-services' => [
        '72H'       => '26',
        '19H'       => '08',
        '24H'       => '08',
        '12H'       => '02',
        '13H'       => '02',
        '14H'       => '02',
        '10H'       => '01',
        '8H30'      => '',
        'CC'        => '',
        'RET24H'    => '08',
        'DEV'       => '08',
        'DIST'      => '08',
        'RECDIST'   => '08',
        'REC24'     => '08',
        '100'       => '',
        '200'       => '',
        'RCS'       => '',
        '101'       => '',
        'PLPACK'    => '26', //plus pack
    ],

    /*===========================================================
     * TNT EXPRESS
     ===========================================================*/
    'tnt-express-services' => [
        'MI'   => '15', //EXPRESS (DOMÉSTICO)
        'AI'   => '15', //EXPRESS (DOMÉSTICO)
        '72H'  => '15', //EXPRESS (DOMÉSTICO)
        '24H'  => '15', //EXPRESS (DOMÉSTICO)
        'EXPR' => '15', //EXPRESS (DOMÉSTICO)
        '14H'  => '12', //12H EXPRESS (DOMÉSTICO)
        '10H'  => '10', //10H EXPRESS (DOMÉSTICO)
        '8H30' => '09', //09H EXPRESS (DOMÉSTICO)
        '8H30' => '09', //09H EXPRESS (DOMÉSTICO)

        '200'   => '15N', //EXPRESS (MERCADORIA AVIÃO)
        'INT-A' => '15N',

        //'' => '12N', //12H EXPRESS (MERCADORIA AVIÃO)
        //'' => '10N', //10H EXPRESS (MERCADORIA AVIÃO)
        //'' => '09N', //09H EXPRESS (MERCADORIA AVIÃO)

        //'' => '15D', //EXPRESS (DOCUMENTOS AVIÃO)
        //'' => '12D', //12H EXPRESS (DOCUMENTOS AVIÃO)
        //'' => '10D', //10H EXPRESS (DOCUMENTOS AVIÃO)
        //'' => '09D', //09H EXPRESS (DOCUMENTOS AVIÃO)

        'EEXP'  => '15N',
        'ESTD'  => '48N',
        'STD'   => '48N',
        '100'   => '48N', //ECONOMY EXPRESS (CAMIÃO)
        'INT-T' => '48N',
        //'' => '412', //12H ECONOMY EXPRESS (CAMIÃO)
    ],

    /*===========================================================
     * SEUR
     ===========================================================*/
    'seur-services' => [
        '8H30' => '83', //SEUR 8H30
        '10H'  => '3', //SEUR 10
        '14H'  => '9', //SEUR 13H30
        '24H'  => '1', //SEUR 24
        '48H'  => '15', //SEUR 48
        '72H'  => '13', //SEUR 72
        'MI'   => '17', //MARITIMO
        'REC24' => '1',
        //''     => '077', //CLASSIC
    ],

    /*===========================================================
     * RANGEL
     ===========================================================*/
    'rangel-services' => [
        'PT#24H' => '132', //Rangel 24
        'ES#24H' => '132', //Rangel 24
        'PT#MI'  => '134',
        'ES#MI'  => '134',
        'PT#AI'  => '133',
        'ES#AI'  => '133',
        //'PT#19H' => '2', //Rangel 19
        //'PT#24H' => '1', //RANGEL XL
        //'ES#24H' => '33', //Rangel ES
        //'PT#IA'  => '14', //RIM
        //'ES#IA'  => '14', //RIM
        //'PT#IM'  => '15', //RIA
        //'ES#IM'  => '15', //RIA
        'PT#VOL' => '2', //Rangel 19
        'PT#PAL' => '1',  //XL
    ],


    /*===========================================================
     * CORREOS EXPRESS
     ===========================================================*/
    'correos-services' => [
        '10H'   => '61', //
        '14H'   => '62', //paq 14
        '24H'   => '63', //
        'INT-T' => '60', //Internacional Standard Exportção
        'INT-A' => '56', //Internacional Express Exportação
        'INT-T' => '90', //Internacional Standard
        'INT-A' => '91', //Internacional Express
        'AI'    => '26', //Islas Express
        'MI'    => '79', //Ilhas Standard
        'EXPRI' => '26',
        'STDI'  => '79', //ilhas standard

        //'' => '50', //Internacional Standard Importação
        //'' => '', //Islas Documentación
        //'' => '54', //ENTREGA PLUS
        //'' => '92' //Paq Empresa 14

        //ship2u
        'P14'    => '62',
        'PRIORI' => '26',
        'STDI'   => '79', //ilhas standard
        'BACK2U' => '54', //ENTREGA PLUS
        'EXPRI'  => '26',

    ],

    /*===========================================================
     * DB SCHENKER
     ===========================================================*/
     'db_schenker-services' => [
        '72H'       => 'bookingLand#43',
        '24H'       => 'bookingLand#43',
        '14H'       => 'bookingLand#43', //courier
        '100'       => 'bookingAir#43', //aereo
        '200'       => 'bookingLand#43', //terrestre
        '101'       => 'bookingLand#43', //terrestre
        'AI'        => 'bookingAir#43', //AEREO ILHAS
        'G-AI'      => 'bookingAir#43',
        'MI'        => 'bookingOceanFCL#43', //MARITIMO ILHAS
        'G-MI'      => 'bookingOceanFCL#43',
        'IT'        => 'bookingLand#43',
        'I48H'      => 'bookingLand#43',
        'IA'        => 'bookingLand#43',
        'G-IA'      => 'bookingAir#43',
        'INT-T'     => 'bookingLand#43',
        'INT-A'     => 'bookingAir#43',
        '300'       => 'bookingLand#43',
        '301'       => 'bookingLand#43',
        'E300'      => 'bookingLand#43',
        'PLG'       => 'bookingLand#43', //premium
    ],

    /*===========================================================
     * VASP SERVICES
     ===========================================================*/
    'vasp-services' => [
        '24H'    => '93', //24h
        'MI'     => '93', //24h
        'AI'     => '93', //24h
        '24H'    => '93', //24h
        'PAL'    => '97', //24h palete
        'SAB'    => '104', //Entrega ao sábado
    ],

    /*===========================================================
     * MRW SERVICES
     ===========================================================*/
    'mrw-services' => [
        'PT#10H' => '0000', //Urgente 10
        '' => '0005', //Urgente Hoje
        '' => '0010', //Promoções
        'PT#10H' => '0015', //Urgente 10 Expedição
        '' => '0100', //Urgente 12
        '' => '0105', //Urgente 12 Expedição
        'PT#14H' => '0110', //Urgente 13
        'PT#14H' => '0115', //Urgente 13 Expedição
        'PT#24H' => '0200', //Urgente 19
        'PT#24H' => '0205', //Urgente 19 Expedição
        'ES#24H' => '0205', //
        '' => '0221', //Urgente Funchal
        '' => '0222', //Urgente Porto Santo
        '' => '0230', //BAG 19
        '' => '0235', //BAG 13
        '' => '0370', //Marítimo Baleares
        '' => '0385', //Marítimo Canárias
        '' => '0390', //Marítimo Interinsular
        '' => '0400', //Expresso Documentos
        '' => '0450', //Express 2 Quilos
        '' => '0480', //Caixa Express 3 Quilos
        '' => '0490', //Documentos 13
        '' => '0800', //Ecommerce
        '' => '0810', //Ecommerce Canje
        'INT-T' => 'ECOP',
        'INT-A' => 'ECOP',
    ],

    /*===========================================================
     * UPS SERVICES
     ===========================================================*/
    'ups-services' => [
        'INT-T' => '11', //UPS Standard
        '200'   => '11', //UPS Standard
        'INT-A' => '65', //UPS Worldwide Saver
        '100'   => '65', //UPS Worldwide Saver
    ],

    /*===========================================================
     * DACHSER SERVICES
     ===========================================================*/
    'dachser-services' => [
        '24H'   => '1', //EXPRESS
        'PAL'   => '1', //EXPRESS
        'INT-T' => '3', //EuroEXPRESS
        'INT-A' => '3', //EuroEXPRESS
    ],

    /*===========================================================
     * SENDING
     ===========================================================*/
    'sending-services' => [
        '24H' => '01', //Send Expres
        '10H' => '02', //Send Top 10H
        //'' => '03', //Send Sectorial
        //'' => '08', //Send Ecommerce
        '72H' => '10', //Send Masivo
        'MI' => '18', //Send Maritimo
        //'' => '40', //Send Optica
    ],

    /*===========================================================
     * PALIBEX SERVICES
     ===========================================================*/
    'palibex-services' => [
        '24H' => '01', // EXPRESS
        ''    => '02', // ECONOMICO
        ''    => '03', // ZONA 0
        ''    => '04', // LEVANTE ECONÓMICO
        ''    => '05', // LEVANTE EXPRESS
        ''    => '06', // NORTE ECONÓMICO
        ''    => '07', // NORTE EXPRESS
        ''    => '08', // AM
        ''    => '09', // ZONA 0 AM
        ''    => '10', // LEVANTE AM
        ''    => '11', // NORTE AM
        ''    => '17', // CERCANÍAS
        ''    => '19', // SUR ECONÓMICO
        ''    => '20', // SUR  EXPRESS
        ''    => '24', // AM  10:00
        ''    => '25', // VALLADOLID EXPRES
        ''    => '26', // VALLADOLID ECONOMICO
        ''    => '27', // VALLADOLID AM
        ''    => '32', // ZONA 0 ECONÓMICO
        ''    => '33', // LTL PALIBEX
        ''    => '34', // MARRUECOS 1 HASTA ALMACÉN EN TANGER
        ''    => '35', // MARRUECOS 2 HASTA CLIENTE DESTINO
    ],

    /*===========================================================
     * ENOVO TMS SERVICES
     ===========================================================*/
    'enovo_tms-services' => [

        'trpexpress' => [
            '48H' => '48H',
            '24H' => '48H'
        ],
        'hdtransportes' => [
            '48H' => '24H',
        ],
        'parcel_entregaki' => [
            '24H' => 'ONGOIN'
        ],
        'zttransportes' => [
            'ONGOIN' => '24H'
        ],
        'tpvservice' => [
            '24H' => 'CITY18',
            '14H' => 'CITY13',
        ],
    ],

];
