<?php

return [

    /*===========================================================
     * TIPSA
     ===========================================================*/

    'tipsa' => [

        'shipment' => [
            'V_COD_AGE_CARGO'   => 'provider_cargo_agency',
            'V_COD_AGE_ORI'     => 'provider_sender_agency',
            'V_COD_AGE_DES'     => 'provider_recipient_agency',
            'V_ALBARAN'         => 'provider_tracking_code',
            'V_COD_REC_ASOC'    => 'provider_collection_tracking_code',
            'V_ASOCIADO_RET'    => 'provider_return_tracking_code',
            'V_TIPO_ASOC'       => 'provider_return_type',
            'V_TIPO_ENV'        => 'shipment_type',
            'V_COD_TIPO_SERV'   => 'service',
            'V_DES_SERV'        => 'service_name',
            'D_FECHA'           => 'date',

            'V_NOM_ORI'         => 'sender_name',
            'V_DIR_ORI'         => 'sender_address',
            'V_POB_ORI'         => 'sender_city',
            'V_CP_ORI'          => 'sender_zip_code',
            'V_TLF_ORI'         => 'sender_phone',
            'V_NOM_DES'         => 'recipient_name',
            //        'V_TIPO_VIA_DES'    => '',
            'V_DIR_DES'         => 'recipient_address',
            //        'V_NUM_DES'         => '',
            //        'V_PISO_DES'        => '',
            'V_POB_DES'         => 'recipient_city',
            'V_CP_DES'          => 'recipient_zip_code',
            'V_TLF_DES'         => 'recipient_phone',
            //        'D_FEC_ENTR'        => '03/27/2017 00:00:00',
            'I_BUL'             => 'volumes',
            'V_REF'             => 'reference',
            'F_REEMBOLSO'       => 'charge_price',
            'F_PORTE_DEB'       => 'total_price_for_recipient',
            'F_VALOR'           => 'cost_price',
            //        'V_ULT_TIPO_VIA'    => '',
            //        'V_ULT_DIR'         => '',
            //        'V_ULT_NUM'         => '',
            //        'V_ULT_PISO'        => '',
            'F_ALTO_VOLPES'     => 'height',
            'F_ANCHO_VOLPES'    => 'length',
            'F_LARGO_VOLPES'    => 'width',
            'F_PESO_ORI'        => 'weight',
            'F_PESO_VOLPES'     => 'weight_sorter',
            'F_PESO_MAYOR_4'    => 'biggest_weight',
            'V_COD_CLI'         => 'customer',
            'V_NOM_CLI'         => 'customer_name',
            //        'V_COD_CLI_DEP'     => '',
            //        'V_NOM_DEP'         => '',
            'V_COD_TIPO_EST'    => 'status_id',
            'V_NOM_POD'         => 'receiver',
            //        'D_FEC_HORA_ENTR_POD' => '03/29/2017 12:25:00',
            'V_COD_PAIS'        => 'recipient_country',
            'V_NOM_PAIS'        => 'country_name',
            'B_RETORNO'         => 'return',
            "B_SABADO"          => "sabado"
        ],

        'collection' => [
            'V_COD_AGE_CARGO'   => 'provider_cargo_agency',
            'V_COD_AGE_ORI'     => 'provider_sender_agency',
            'V_COD_AGE_DES'     => 'provider_recipient_agency',
            //'V_COD_AGE_SOL'     => '',
            'V_COD'             => 'provider_tracking_code',
            'V_COD_ENV'         => 'assigned_shipment',
            'D_FEC_HORA_ALTA'   => 'date',
            'V_COD_TIPO_SERV'   => 'service',
            'V_COD_CLI'         => 'customer',

            'V_NOM_ORI'         => 'sender_name',
            'V_DIR_ORI'         => 'sender_address',
            'V_POB_ORI'         => 'sender_city',
            'V_CP_ORI'          => 'sender_zip_code',
            'V_COD_PRO_ORI'     => 'sender_country',

            'V_PERS_CONTACTO'   => 'recipient_attn',
            'V_NOM_DES'         => 'recipient_name',
            'V_DIR_DES'         => 'recipient_address',
            'V_POB_DES'         => 'recipient_city',
            'V_CP_DES'          => 'recipient_zip_code',
            'V_TLF_DES'         => 'recipient_phone',
            'V_COD_PRO_DES'     => 'recipient_country',

            'I_BUL'             => 'volumes',
            'F_PESO'            => 'weight',

            'V_REF'             => 'reference',
            'F_VALOR'           => 'cost_price',
            'V_OBS'             => 'obs',
            'F_ANTICIPO'        => '',
            'V_TIPO_REC'        => 'collection_type',
            'V_COD_ENV'         => 'shipment_tracking_code'
        ],

        'status' => [
            'V_COD_AGE_ORI'     => 'provider_sender_agency',
            'V_COD_AGE_CARGO'   => 'provider_cargo_agency',
            'V_ALBARAN'         => 'provider_tracking_code',
            'V_COD_TIPO_EST'    => 'status',
            'D_FEC_HORA_ALTA'   => 'created_at',
            'I_ID'              => 'tipsa_status_id', //id do estado no programa da tipsa
            'V_COD_USU_ALTA'    => 'provider_user_id',
            'V_COD_AGE_ALTA'    => 'provider_agency_code',
            'B_ULT'             => 'last_status'
            //            'V_COD_REP_ALTA' => '',
            //            'V_COD_CLI_ALTA' => '',
            //            'V_COD_CLI_DEP_ALTA' => '',
            //            'D_FEC_HORA_ALTA_REG' => '', //hora de alteração do registo
        ],

        'incidencias' => [
            'I_ID'              => 'id',
            'V_COD_TIPO_INC'    => 'incidence',
            'T_OBS'             => 'obs',
            'D_FEC_HORA_CIERRE' => 'closed_at',
            'B_RESUELTA'        => 'resolved',
            'D_FEC_HORA_ALTA'   => 'created_at',
            'V_COD_USU_ALTA'    => 'operator',
            'V_COD_AGE_ALTA'    => 'provider_agency_code'
            /*     'V_COD_USU_RES'     => '',
               'V_COD_REP_ALTA'    => '',
               'V_COD_CLI_ALTA'    => '',
               'V_COD_CLI_DEP_ALTA' => '',*/
        ],

    ],

    /*===========================================================
     * ENVIALIA
     ===========================================================*/

    'envialia' => [

        'shipment' => [
            'V_COD_AGE_CARGO'   => 'provider_cargo_agency',
            'V_COD_AGE_ORI'     => 'provider_sender_agency',
            'V_COD_AGE_DES'     => 'provider_recipient_agency',
            'V_ALBARAN'         => 'provider_tracking_code',
            'V_COD_REC_ASOC'    => 'provider_collection_tracking_code',
            'D_FECHA'           => 'date',
            'V_COD_TIPO_SERV'   => 'service',
            'V_COD_CLI'         => 'customer',
            'V_NOM_CLI'         => 'customer_name',

            'V_NOM_ORI'         => 'sender_name',
            'V_DIR_ORI'         => 'sender_address',
            'V_POB_ORI'         => 'sender_city',
            'V_CP_ORI'          => 'sender_zip_code',
            'V_COD_PRO_ORI'     => 'sender_country',

            'V_PERS_CONTACTO'   => 'recipient_attn',
            'V_NOM_DES'         => 'recipient_name',
            'V_DIR_DES'         => 'recipient_address',
            'V_POB_DES'         => 'recipient_city',
            'V_CP_DES'          => 'recipient_zip_code',
            'V_TLF_DES'         => 'recipient_phone',
            'V_COD_PRO_DES'     => 'recipient_country',
            'V_COD_PAIS'        => 'recipient_country',

            //        'D_FEC_ENTR'        => '03/27/2017 00:00:00',
            'I_BUL'             => 'volumes',
            'F_PESO_ORI'        => 'weight',
            'F_PESO_VOLPES'     => 'weight_sorter',
            'F_ALTO_VOLPES'     => 'height',
            'F_ANCHO_VOLPES'    => 'length',
            'F_LARGO_VOLPES'    => 'width',
            'F_M3_VOLPES'       => 'fator_m3',

            'V_REF'             => 'reference',
            'F_REEMBOLSO'       => 'charge_price',
            'F_PORTE_DEB'       => 'total_price_for_recipient',
            'F_VALOR'           => 'cost_price',
            'B_RETORNO'         => 'return',
            'V_OBS'             => 'obs'
        ],

        'collection' => [
            'V_COD_AGE_CARGO'   => 'provider_cargo_agency',
            'V_COD_AGE_ORI'     => 'provider_sender_agency',
            'V_COD_AGE_DES'     => 'provider_recipient_agency',
            'V_COD'             => 'provider_tracking_code',
            'V_COD_ENV'         => 'assigned_shipment',
            'D_FEC_HORA_ALTA'   => 'date',
            'V_COD_TIPO_SERV'   => 'service',
            'V_COD_CLI'         => 'customer',

            'V_NOM_ORI'         => 'sender_name',
            'V_DIR_ORI'         => 'sender_address',
            'V_POB_ORI'         => 'sender_city',
            'V_CP_ORI'          => 'sender_zip_code',
            'V_COD_PRO_ORI'     => 'sender_country',

            'V_PERS_CONTACTO'   => 'recipient_attn',
            'V_NOM_DES'         => 'recipient_name',
            'V_DIR_DES'         => 'recipient_address',
            'V_POB_DES'         => 'recipient_city',
            'V_CP_DES'          => 'recipient_zip_code',
            'V_TLF_DES'         => 'recipient_phone',
            'V_COD_PRO_DES'     => 'recipient_country',

            'I_BUL'             => 'volumes',
            'F_PESO'            => 'weight',

            'V_REF'             => 'reference',
            'F_VALOR'           => 'cost_price',
            'V_OBS'             => 'obs'
        ],

        'status' => [
            'V_COD_AGE_ORI'     => 'provider_sender_agency',
            'V_COD_AGE_CARGO'   => 'provider_cargo_agency',
            'V_ALBARAN'         => 'provider_tracking_code',
            'V_COD_TIPO_EST'    => 'status',
            'D_FEC_HORA_ALTA'   => 'created_at',
            'I_ID'              => 'status_id', //id do estado no programa da tipsa
            'V_COD_USU_ALTA'    => 'provider_user_id',
            'V_COD_AGE_ALTA'    => 'provider_agency_code',
            //            'V_COD_REP_ALTA' => '',
            //            'V_COD_CLI_ALTA' => '',
            //            'V_COD_CLI_DEP_ALTA' => '',
            //            'D_FEC_HORA_ALTA_REG' => '', //hora de alteração do registo
        ],

        'pod' => [
            'V_NOM_POD'           => 'pod_name',
            'D_FEC_HORA_ENTR_POD' => 'created_at',
            'V_OBS_POD'           => 'pod_obs',
            'V_COD_REP_ALTA_POD'  => 'provider_user_id',
            'V_COD_AGE_ALTA_POD'  => 'provider_agency_code',
        ],

        'incidencias' => [
            'I_ID'              => 'id',
            'V_COD_TIPO_INC'    => 'incidence',
            'T_OBS'             => 'obs',
            'D_FEC_HORA_CIERRE' => 'closed_at',
            'B_RESUELTA'        => 'resolved',
            'D_FEC_HORA_ALTA'   => 'created_at',
            'V_COD_USU_ALTA'    => 'operator',
            'V_COD_AGE_ALTA'    => 'provider_agency_code',
            /*       'V_COD_USU_RES'     => '',
            'V_COD_REP_ALTA'    => '',
            'V_COD_CLI_ALTA'    => '',
            'V_COD_CLI_DEP_ALTA' => '',*/
        ]

    ],

    /*===========================================================
     * GLS ZETA
     ===========================================================*/

    'gls_zeta' => [

        'shipment' => [
            'codbar'            => 'provider_tracking_code',
            'codexp'            => 'expedition_code',
            'fecha'             => 'date',

            'codplaza_pag'      => 'provider_cargo_agency',
            'codplaza_org'      => 'provider_sender_agency',
            'codplaza_dst'      => 'provider_recipient_agency',


            'codServicio'     => 'service',
            'servicio'        => 'service_name',
            'codcli'          => 'customer',
            'nmCliente'       => 'customer_name',

            'nombre_org'      => 'sender_name',
            'calle_org'       => 'sender_address',
            'localidad_org'   => 'sender_city',
            'cp_org'          => 'sender_zip_code',
            'codpais_org'     => 'sender_country',

            'nombre_dst'        => 'recipient_name',
            'calle_dst'         => 'recipient_address',
            'localidad_dst'     => 'recipient_city',
            'cp_dst'          => 'recipient_zip_code',
            'tfno_dst'         => 'recipient_phone',
            'codpais_dst'     => 'recipient_country',

            'bultos'            => 'volumes',
            'kgs'               => 'weight',
            'vol'               => 'fator_m3',

            'albaran'             => 'reference',

            'dac'               => 'acuse',
            'retorno'           => 'return',
            'codestado'         => 'status',
            'estado'            => 'status_name',
            'Observacion'       => 'obs',
        ],

        'shipmentByDate' => [
            'codexp'        => 'provider_tracking_code',
            'codplaza_pag'  => 'agency_id',
            'codplaza_org'  => 'sender_agency',
            'codcli'        => 'customer_code',
            'cliente'       => 'customer_name',
            'codservicio'   => 'service',
            'servicio'      => 'service_name',
            'codhorario'    => 'horary',
            'codestado'     => 'status',
            'estado'        => 'status_name',
            'bultos'        => 'volumes',
            'kgs'           => 'weight',
            'nombre_dst'    => 'recipient_name',
            'calle_dst'     => 'recipent_address',
            'cp_dst'        => 'recipient_zip_code',
            'localidad_dst' => 'recipient_city',
            'pais_dst'      => 'recipient_country'
        ],

        'status' => [
            'plaza'             => 'provider_agency_code',
            'tipo'              => 'type',
            'evento'            => 'obs',
            'fecha'             => 'created_at',
            'codigo'            => 'status_id', //id do estado no programa da tipsa
        ],

        'status-collection' => [
            'Fecha'         => 'created_at',
            'Hora'          => 'hour',
            'Tipo'          => 'type',
            'Codigo'        => 'status',
            'Descripcion'   => 'description',
            'Observaciones' => 'obs',
            'codbar'        => 'provider_tracking_code',
            'codexp'        => 'expedition_code',
        ],

        'convert_country' => [
            '351' => 'pt',
            '34'  => 'es'
        ]

    ],

    /*===========================================================
     * GLS INTERNACIONAL
     ===========================================================*/

    'gls' => [

        'status' => [
            'Date'          => 'created_at',
            'LocationName'  => 'city',
            'CountryName'   => 'country',
            'Desc'          => 'description',
            'Code'          => 'status_id',
            'ReasonName'    => 'obs',
        ],

    ],

    /*===========================================================
     * CTT
     ===========================================================*/

    'ctt' => [
        'collection' => [
            //"_CP3Destinatario"  => "recipient_zip_code",
            //"_CP3Exp"           => "sender_zip_code",
            "_CP4Destinatario"  => "recipient_zip_code",
            "_CP4Exp"           => "sender_zip_code",
            "_CodProduto"       => "service_id",
            //"_Contacto"         => "recipient_name",
            //"_ContactoExp"      => "",
            "_Data"             => "date",
            "_Destinatario"     => "recipient_name",
            //"_Dimensao"         => "",
            "_Email"            => "email",
            "_Expedidor"        => "sender_name",
            "_GuiaTransporte"   => "transport_guide",
            "_HoraFim"          => "end_hour",
            "_HoraInicio"       => "start_hour",
            "_IDRecolha"        => "provider_collection_trk",
            "_LocalidadeDest"   => "recipient_city",
            "_LocalidadeExp"    => "sender_city",
            "_MoradaDest"       => "recipient_address",
            "_MoradaExp"        => "sender_address",
            "_ObsObj"           => "obs",
            "_Peso"             => "weight",
            //"_PisoDest"         => "",
            //"_PisoExp"          => "",
            //"_PortaDest"        => "",
            //"_PortaExp"         => "",
            "_QuantObj"         => "volumes",
            //"_SolicitadaPor"    => "", //contrato ctt
            //"_Telefone"         => "",
            "_TelefoneDest"     => "recipient_phone",
            "_TelefoneExp"      => "sender_phone",
            //"_VariosDest"       => ""
        ],

        'status' => [
            '_CodigoEvento'      => 'status_id',
            '_CodigoMotivo'      => 'incidence_id',
            '_CodigoNoEvento'    => 'event_code',
            '_CodigoSituacao'    => '_CodigoSituacao',
            '_DataEvento'        => 'created_at',
            '_DescricaoEvento'   => 'description',
            '_DescricaoNoEvento' => 'obs',
            '_DescricaoSituacao' => 'incidence_obs',
            '_NomeReceptor'      => 'receiver_name',
            '_Ordem'             => 'sort',
            '_PesoReal'          => 'weight',
            '_Valido'            => 'valid',
            '_PesoVolumetrico'   => 'volumetric_weight',
        ],

        'collection-status' => [
            //'_Data'                 => '',
            //'_DataInicioRecolha'    => '',
            'MotivoRecolhaNaoEfetuada' => 'incidence_id',
            '_DataRecolhaEfectuada' => 'created_at',
            '_IDRecolha'            => 'trk',
            '_Observacao'           => 'obs',
            '_QuantObj'             => 'volumes',
            '_RecolhaEfectuada'     => 'status_id',
        ],

        'pudo' => [
            'Code'                  => 'code',
            'PudoEntityDesignation' => 'name',
            'Description'           => 'description',
            'Address'               => 'address',
            'PostalCode'            => 'zip_code',
            'StateOrProvince'       => 'city',
            'CountryCode'           => 'country',
            'EntityType'            => 'type',
        ],
    ],


    /*===========================================================
     * CHRONOPOST
     ===========================================================*/
    'chronopost' => [
        'status' => [
            'trace_Event_CODE'        => 'status_id',
            'trace_Event_Comment'     => 'obs',
            'trace_Event_Date'        => 'created_at',
            'trace_Event_Description' => 'description',
            'trace_Event_Latitude'    => 'latitude',
            'trace_Event_Longitude'   => 'longitude'
        ],
        'pudo' => [
            'address'               => 'address',
            'country'               => 'country',
            'deliverySaturday'      => 'delivery_saturday',
            'deliverySunday'        => 'delivery_sunday',
            'doorNumber'            => 'door',
            'email'                 => 'email',
            'latitude'              => 'latitude',
            'longitude'             => 'longitude',
            'name'                  => 'name',
            'number'                => 'code',
            'phoneNumber'           => 'mobile',
            'postalCode'            => 'zip_code',
            'postalCodeLocation'    => 'city',
        ],
    ],

    /*===========================================================
     * FEDEX
     ===========================================================*/
    'fedex' => [
        'status' => [
            'EventType'        => 'status_id',
            'Address'          => 'obs',
            'Timestamp'        => 'created_at',
            'EventDescription' => 'description',
        ],
    ],

    /*===========================================================
     * NACEX
     ===========================================================*/
    'nacex' => [
        'shipment' => [ //importante: manter a ordem dos campos
            'agencia_origen'        => 'provider_cargo_agency',
            'albaran'               => 'provider_tracking_code',
            'digitalizar'           => 'digitalizar',
            'fecha_alta'            => 'date',
            'referencia'            => 'reference',
            'tracking'              => 'tracking',
            'remitente'             => 'sender_name',
            'direccion_origen'      => 'sender_address',
            'cp_origen'             => 'sender_zip_code',
            'ciudad_origen'         => 'sender_city',
            'agencia_entrega'       => 'provider_recipient_agency',
            'consignatario'         => 'recipient_name',
            'direccion_entrega'     => 'recipient_address',
            'cp_entrega'            => 'recipient_zip_code',
            'ciudad_entrega'        => 'recipient_city',
            'telefono_entrega'      => 'recipient_phone',
            'departamento_cliente'  => 'departament',
            'bultos'                => 'volumes',
            'kilos'                 => 'weight',
            'importe_reembolso'     => 'charge_price',
            'observaciones_datos'   => 'obs',
            'tipo_reembolso'        => 'refund_type',
            'servicio'              => 'service',
            'agencia_estado'        => 'status_agency',
            'fecha_estado'          => 'status_date',
            'hora_estado'           => 'status_hour',
            'observaciones_estado'  => 'obs_status',
            'fecha_sol_incidencia'  => 'incidence_date',
            'hora_sol_incidencia'   => 'incidence_hour',
            'estado_desc'           => 'status_name',
            'valoracion'            => 'total_price',
        ],

        'status' => [ //importante: manter a ordem dos campos
            'Expe_codigo'  => 'expediction',
            'Fecha'        => 'date',
            'Hora'         => 'hour',
            'Observaciones' => 'obs',
            'Estado'       => 'status',
            'Estado_code'  => 'status_code',
            'Origen'       => 'provider_source_agency',
            'Albaran'      => 'provider_tracking_code',
            'exps_rels'    => 'relacion',
        ],

        'status-history' => [ //importante: manter a ordem dos campos
            'Fecha'        => 'date',
            'Hora'         => 'hour',
            'Estado'       => 'status_code',
            'Observaciones' => 'obs',
        ],

        'collection' => [ //importante: manter a ordem dos campos
            'Reco_codigo'  => 'expediction',
            'Fecha'        => 'date',
            'Hora'         => 'hour',
            'Observaciones'=> 'obs',
            'Estado'       => 'status',
            'Estado_code'  => 'status_code',
            'Age_Reco'     => 'sender_agency',
            'Nao_identif'  => 'campo_1'
        ],
    ],

    /*===========================================================
     * SEUR
     ===========================================================*/
    'seur' => [
        'status' => [
            'FECHA_SITUACION'     => 'created_at',
            'DESCRIPCION_CLIENTE_PORTUGUES' => 'obs',
            'SIT1'                => 'type',
            'SIT2'                => 'type_code',
            'SITUACION_CRM'       => 'status'
        ],
    ],

    /*===========================================================
     * VASP
     ===========================================================*/
    'vasp' => [
        'status' => [
            "event"      => "status",
            "date"       => "date",
            "time"       => "time",
            "note"       => "obs",
            "pup"        => "pup",
            "agency"     => "agency",
            "cancelCode" => "cancelCode",
            "hasPod"     => "hasPod",
            "pod"        => "pod",
        ],
    ],

    /*===========================================================
     * RANGEL
     ===========================================================*/
    'rangel' => [
        'status' => [
            "status"      => "status",
            "date"        => "date",
            "obs"         => "obs",
            "status_desc" => "status_name",
            "city"        => "city",
            "country"     => "country",
        ],
    ],

    /*===========================================================
   * CORREOS EXPRESS
   ===========================================================*/
    'correos_express' => [
        'status' => [
            "codEstado"     => "status",
            "descEstado"    => "status_name",
            "fechaEstado"   => "date",
            "horaEstado"    => "hour",
            "codIncEstado"  => "incidence",
            "descIncEstado" => "incidence_name",
        ],

        'status-collections' => [
            "codSituacion"   => "status",
            "descSituacion"  => "status_name",
            "fecSituacion"   => "date",
            "codMotivo"      => "incidence",
            "descMotivo"     => "incidence_name",
        ],
    ],

    /*===========================================================
     * DB SCHENKER
     ===========================================================*/
    'db_schenker' => [
        'status' => [
            "ns2Status"             => "status",
            "ns2Date"               => "date",
            "ns2Time"               => "hour",
            "ns2StatusInfo"         => "status_info",
            "ns2StatusDescription"  => "status_name",
            "ns2LocationName"       => "city",
            "ns2ReasonCode"         => "incidence",
            "ns2CodeDscrptn"        => "incidence_name"
        ],
    ],



    /*===========================================================
     * KEY INVOICE
     ===========================================================*/
    'keyinvoice' => [
        'doc_type' => [
            'invoice'                => '4',  //Fatura
            'credit-note'            => '7',  //Nota de crédito
            'receipt'                => '9',  //Recibo
            'simplified-invoice'     => '32', //Fatura Simplificada
            'invoice-receipt'        => '34', //Fatura-Recibo
            'sale-by-money'          => '5',  //Vendas-a-dinheiro
            'transport-guide'        => '16', //Guia de Transporte
            'regularization'         => '36', //regularização
            'debit-note'             => '8',  //Nota débito
            'proforma-invoice'       => '18', //Nota débito

            'provider-invoice'            => '3',  //Factura de compra
            'provider-invoice-receipt'    => '3',  //Factura Recibo de compra ==> Iguala Fatura Compra
            'provider-sale-by-money'      => '24', //Vendas-a-dinheiro de fornecedor
            'provider-simplified-invoice' => '33', //Fatura Simplificada de fornecedor
            'provider-credit-note'        => '3', //'11', //Crédito financeiro de fornecedor
        ],

        'products' => [
            'Ref'         => 'reference',
            'Name'        => 'name',
            'ShortName'   => 'short_name',
            'TAX'         => 'tax_rate',
            'Comment'     => 'obs',
            'IsService'   =>  'is_service',
            //'HasStocks' =>  '',
            'Price'       =>  'price',
            'TaxIncluded' =>  "tax_included",
            'Active'      =>  'is_active',
            //'ShortDescription' => '',
            'LongDescription' => 'description',
            //'Image'       => 'image',
            //'IdFamily'    => '',
            //'Family'      => ''
            //'FamilyTree'  => ''
            //'VendorRef'     => '',
            //'EAN'           => '',
            //'Stock'         => '',
            //'IdBrand'       => '',
        ]
    ],

    /*===========================================================
     * DELNEXT
     ===========================================================*/
    'delnext' => [
        'status' => [
            "Date"  => "date",
            "Time"  => "time",
            "Description" => "status",
            "Location"    => "obs",
            "status_id"   => "status_id"
        ],
    ],

    /*===========================================================
     * MRW
     ===========================================================*/
    'mrw' => [
        'status' => [
            "Estado"            => "status",
            "EstadoDescripcion" => "status_name",
            "FechaEntrega"      => "delivery_date",
            "HoraEntrega"       => "delivery_time",
            "Intentos"          => "attempts",
            "NumAlbaran"        => "tracking_code",
            "PersonaEntrega"    => "receiver",
            "Publicado"         => "created_at",
        ],
    ],

    /*===========================================================
     * ONTIME
     ===========================================================*/
     'ontime' => [
        'status' => [
            "tipo"              => "status",
            "descripcion"       => "status_name",
            "fecha_evento"      => "delivery_date"
        ],
    ],

    /*===========================================================
     * DHL
      ===========================================================*/
    'dhl' => [
        'status' => [
            "Fecha"          => "date",
            "Hora"           => "hour",
            "Descripcion"    => "description",
            "Codigo"         => "code",
            "CodigoSolucion" => "incidence_code",
            "Ciudad"         => "city"
        ],
    ],


    /*===========================================================
    * VIA DIRECTA
    ===========================================================*/
    'via_directa' => [
        'status' => [
            "Cod_Objeto"    => "tracking",
            "Data_EVENTO"   => "date",
            "Cod_Tracking"  => "status",
            "Data_DEVOLUCAO" => 'devolution',
            "Desc_Tracking" => "obs",
        ],
    ],

    /*===========================================================
    * SAGE X3
    ===========================================================*/
    'sageX3' => [
        //clientes
        'YWSBPC' => [
            "BCGCOD"    => "type",
            "BPCNUM"    => "code",
            'BPCSTA'    => 'active',
            "BPCNAM"    => "name",
            "BPCSHO"    => "sho",
            "CTY"       => "city",
            "POSCOD"    => "zip_code",
            "PTE"       => 'payment_method',
            "CRY"       => "country",
            "LAN"       => "locale",
            "CUR"       => "currency",
            "CRN"       => "vat",
            "EECNUM"    => "eu_vat",
            "BPAADD"    => "address_id",
            "BPAADDLIG" => "address",
            "poscod"    => "zip_code",
            "CTY"       => "city",
            "BPAINV"    => "field_BPAINV",
            "BPAPYR"    => "field_BPAPYR",
            "VACBPR"    => "field_VACBPR",
            "PTE"       => "payment_method",
        ],

        //fornecedores
        'YWSBPS' => [
            "BPSNUM"    => "code",
            "BPSNAM"    => "name",
            "BPSSHO"    => "sho",
            "CTY"       => "city",
            "POSCOD"    => "zip_code",
            "CRY"       => "country",
            "LAN"       => "locale",
            "CUR"       => "currency",
            "CRN"       => "vat",
            "EECNUM"    => "eu_vat",
            "BPAADD"    => "address_id",
            "BPAADDLIG" => "address",
            "poscod"    => "zip_code",
            "CTY"       => "city",
            "BPAINV"    => "field_BPAINV",
            "BPAPYR"    => "field_BPAPYR",
            "VACBPR"    => "field_VACBPR",
            "PTE"       => "payment_method",
            "BSGCOD"    => "category"
        ],

        //artigos
        'YWSITM' => [
            "ITMREF"  => "ref",
            "C2"      => "name",
            "TCLCOD"  => "category",
            "ITMSTA"  => "status",
            "EANCOD"  => "ean",
            "SEAKEY"  => "seakey",
            "DES1AXX" => "description",
            "DES2AXX" => "description_2",
            "DES3AXX" => "description_3",
            "BASPRI"  => "price",
        ],

        //faturas compra
        'YWSPIH' => [
            "NUM"     => "reference",
            "BPR"     => "provider",
            "BPRNAM"  => "billing_name",
            "FCY"     => "agency",
            "ACCDAT"  => "date",
            "PIVTYP"  => "doc_type",
            "STA"       => "type",
            "TOTLINAMT" => "doc_subtotal",
            "TOTTAXAMT" => "doc_vat",
            "AMTATI"    => "doc_total",
            "PTE"       => "payment_condition",
        ],

        //faturas
        'YWSSIH' => [
            "SALFCY"  => "agency",
            "SIVTYP"  => "doc_type",
            "NUM"     => "doc_id",
            "INVDAT"  => "doc_date",
            "BPCINV"  => "customer",
            "ITMREF"  => "item_ref",
            "QTY"     => "qty",
            "GROPRI"  => "subtotal",
            "BPINAM"  => "billing_name",
            "INVREF"  => "reference",
            "AMTNOT"  => "doc_subtotal",
            "AMTTAX"  => "doc_vat",
            "AMTATI"  => "doc_total",
        ]
    ],

    /*===========================================================
     * VIA VERDE
     ===========================================================*/
    'viaverde' => [
        "identificador"  => "identifier",
        "matricula"      => "license_plate",
        "referencia_mb"  => "reference_mb",
        "data_entrada"   => "entry_date",
        "hora_entrada"   => "entry_hour",
        "entrada"        => "entry_point",
        "data_saida"     => "exit_date",
        "hora_saida"     => "exit_hour",
        "saida"          => "exit_point",
        "valor"          => "total",
        "valor_desconto" => "discount",
        "taxa_iva"       => "vat_rate",
        "operador"       => "toll_provider",
        "servico"        => "toll_service",
        "data_pagamento" => "payment_date",
        "cartao_no"      => "card_no",
    ]
];
