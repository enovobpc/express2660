<?php

return [

    /*===========================================================
     * FICHEIRO EXCEL NATIVO
     ===========================================================*/

    'excel_nativo' => [
        "data"                       => "date",
        "numero_cliente"             => "customer_code",
        "trk_fornecedor"             => "provider_tracking_code",
        "referencia"                 => "reference",
        "referencia2"                => "reference2",
        "nome_remetente"             => "sender_name",
        "morada_remetente"           => "sender_address",
        "codigo_postal_remetente"    => "sender_zip_code",
        "localidade_remetente"       => "sender_city",
        "pais_remetente"             => "sender_country",
        "contacto_remetente"         => "sender_phone",
        "nome_destinatario"          => "recipient_name",
        "morada_destinatario"        => "recipient_address",
        "codigo_postal_destinatario" => "recipient_zip_code",
        "localidade_destinatario"    => "recipient_city",
        "pais_destinatario"          => "recipient_country",
        "contacto_destinatario"      => "recipient_phone",
        "pessoa_contacto"            => "recipient_attn",
        "volumes"                    => "volumes",
        "peso"                       => "weight",
        "codigo_servico"             => "service_code",
        "reembolso"                  => "charge_price",
        "retorno_encomenda"          => "rpack",
        "retorno_cheque"             => "rcheck",
        "retorno_guia"               => "rguide",
        "observacoes"                => "obs",
        "observacoes2"               => "obs_internal",
    ],

    /*===========================================================
     * GLS REPORT DIARIO
     ===========================================================*/

    'gls' => [
        "nr_envio"              => "provider_tracking_code",
        "referencia"            => "reference",
        "peso_bruto"            => "weight",
        "data_envio"            => "date",
        "nome_destinatario"     => "recipient_name",
        "morada_destinatario"   => "recipient_address",
        "codigo_postal"         => "recipient_zip_code",
        "localidade"            => "recipient_city",
        "estado_envio"          => "gls_status_name",
        "razao"                 => "reason",
        "data_entrega"          => "delivery_date",
        "recetor"               => "receiver",
        "hora_entrega"          => "delivery_hour",
        "devolver"              => "return",
        "reagendar"             => "reschedule",
        "nova_morada"           => "new_address",
        "obs"                   => "obs",
    ],

    'gls-services' => [
        'pt' => '8',
        'es' => '8',
        'md' => '8',
        'ac' => '8',
    ],

    'gls-status' => [
        'Volume recolhido'                      => '3',
        'Em transito'                           => '3',
        'Encomenda em armazem'                  => '3',
        'Recusado pelo destinatario'            => '7',
        'Reagendamento para o proximo dia util' => '6',
        'Morada incompleta/ Insuficiente'       => '9'
    ],

    'gls-webservice-status' => [
        'Date'   => 'date',    //documentado
        'Code'   => 'code',
        'ReasonName' => 'obs'
    ],

    'gls-webservice-status' => [
        '0' => '16', //Os dados do volume foram introduzidos no sistema GLS => entrada em rede
        '1' => '3', //Volume saiu da plataforma da GLS => em transporte
        '2' => '17', //o volume chegou a plataforma GLS => em armazém
        '11' => '4', //O volume estß carregado no veículo de entrega da GLS e irß ser entregue no decorrer do dia. => em distribuicao
        //'' => '4', //em distribuicao
        // '' => '9', //incidencia
        '3' => '5', //entregue;
        //'5' => '7', //devolvido
        //'6' => '11', //recanalizado
        // '7' => '9', //incidencia
        //'8' => '8', //anulado
        //'9' => '9', //incidencia
        //'10' => '6', //pendente e novo envio -> tentativa de entrega
        //'12' => '3', //recolhido na delegação -> em transito
        //'13' => '8', //anulado
        //'14' => '12', //entrega parcial
        //'15' => '3', //transito 72H -> em transito
        // '16' => '2', //pendente de emissão => aceite
    ],

    /*===========================================================
     * ENVIALIA
     ===========================================================*/

    'envialia' => [
        "agencia_origen"         => "provider_sender_agency",
        "agencia_destino"        => "provider_recipient_agency",
        "codigo_de_cliente"      => "customer_id",
        "albaran"                => "provider_tracking_code",
        "fecha"                  => "date",
        "referencia"             => "reference",
        "tipo_servicio"          => "service_type",
        "remitente"              => "sender_name",
        "direccion_remitente"    => "sender_address",
        "poblacion_remitente"    => "sender_city",
        "cp_remitente"           => "sender_zip_code",
        "destinatario"           => "recipient_name",
        "direccion_destinatario" => "recipient_address",
        "poblacion_destinatario" => "recipient_city",
        "cp_destinatario"        => "recipient_zip_code",
        "telefono_destinatario"  => "recipient_phone",
        "reembolso"              => "charge_price",
        "peso_origen"            => "weight_origin",
        "peso_sorter"            => "weight_sorter",
        "m3_sorter"              => "volume_sorter",
        "bultos"                 => "volumes",
        "codigo_estado"          => "envialia_status_id",
        "descripcion_estado"     => "envialia_status_name",
        "fechahora_alta_estado"  => "envialia_status_date",
        "fecha_entrega"          => 'delivery_date',
        "receptor_pod"           => "receiver",
        "observaciones"          => "obs",
        "nombre_repartidor"      => "operator_name",
        "recogida_asociada"      => "provider_return_tracking_code",
        "cod_ult_incidencia"     => "incidence_id",
        "desc_ult_actuacion"     => "last_observation"
    ],

    'envialia-services' => [
        '830' => '8H30',
        '10'  => '10H',
        '14'  => '14H',
        '19'  => '24H',
        '24'  => '24H',
        '72'  => '72H',
        'E72' => '72H',
        'RCS' => 'RCS',
        'RET' => 'RET24',
        'RET' => 'RET',
        'CC'  => 'CC',
        'DEV' => 'DEV',
        'E24' => '24H',
        '100' => '100',
        '200' => '200',
        '101' => '101',
        'E72' => '72H',
        'RED' => 'RED',
        'V14' => 'V14',
        'B2C' => 'B2C',
        'XL'  => 'XL'
    ],

    'envialia-services-collection' => [
        '24'  => 'REC24',
        'RCS' => 'RCS',

        '10'  => '10H',
        '14'  => '14H',
        '19'  => '24H',
        '72'  => '72H',
        'E72' => '72H',
        '200' => '200',
        '100' => '100',
        '101' => '101',
        'B2C' => 'B2C',
        'XL'  => 'XL'
    ],

    'envialia-status' => [
        '0' => '16', //aceite
        '1' => '3', //em transito
        '2' => '4', //em distribuicao
        '3' => '9', //incidencia
        '4' => '5', //entregue;
        '5' => '7', //devolvido
        '6' => '11', //recanalizado
        '7' => '13', //pendente chegada
        '8' => '8', //anulado
        '9' => '9', //incidencia
        '10' => '31', //pendente e novo envio -> aguarda expedicao
        '11' => '33', //em CS destino --> Em armazém (destino)
        '12' => '3', //recolhido na delegação -> em transito
        '13' => '8', //anulado
        '14' => '12', //entrega parcial
        '15' => '3', //transito 72H -> em transito
        '16' => '34', //pendente de emissão => em armazem (origem),

        'R0' => '2', //Solicitada -> Aceite
        'R1' => '21', //Lida pela Agência -> visto pela central
        'R2' => '10', //Atribuida -> a recolher
        'R3' => '9', //Incidência
        'R4' => '14', //Realizada
        'R5' => '22', //Pendente de Atribuição
        'R6' => '18', //Recolha Falhada
        'R7' => '19', //Finalizada -> envio gerado
        'R8' => '8', //Anulada
        'R9' => '20', //Leitura Repartidor -> lido pelo motorista
    ],


    'envialia-incidences' => [
        'D01'  => '1', //ausente
        'D02'  => '1', //ausente
        'D03'  => '16', //Destinatário Desconhecido
        'D04'  => '13', //Não Aceitou Mercadoria
        'D05'  => '3', //morada incorreta
        'D06'  => '13', //Não Aceitou Mercadoria
        'D07'  => '21',
        'D08'  => '24', //não paga reembolso
        'D09'  => '21',
        'D10'  => '2', //encerrado
        'D11'  => '19', //retorno não preparado
        'D12'  => '14', //Recolheram em Armazém
        'D13'  => '15', //envio no dia seguinte
        'EDT'  => '21', //Extravio de Mercadoria
        'EXM'  => '21', //Entrega Adiada pelo Destinatário
        'C01'  => '7', //Falta Expedição ou mercadoria em armazém
        'FD'   => '9', //Dados Insuficientes
        'FL'   => '10', //Feriado Local
        'P01'  => '18', //'Pendente de chegada
        'P02'  => '8', //Volumes em Falta
        'P03'  => '11', //Mal Canalizado
        'P07'  => '12', //Mercadoria em Alfândega
        'P04'  => '4', //Envio Danificado
        'P05'  => '20', //sem documentação
    ],


    /*===========================================================
     * TIPSA
     ===========================================================*/

    'tipsa' => [
        "agencia_origen"         => "provider_sender_agency",
        "agencia_destino"        => "provider_recipient_agency",
        "codigo_de_cliente"      => "customer_id",
        "albaran"                => "provider_tracking_code",
        "fecha"                  => "date",
        "referencia"             => "reference",
        "tipo_servicio"          => "service_type",
        "remitente"              => "sender_name",
        "direccion_remitente"    => "sender_address",
        "poblacion_remitente"    => "sender_city",
        "cp_remitente"           => "sender_zip_code",
        "destinatario"           => "recipient_name",
        "direccion_destinatario" => "recipient_address",
        "poblacion_destinatario" => "recipient_city",
        "cp_destinatario"        => "recipient_zip_code",
        "telefono_destinatario"  => "recipient_phone",
        "reembolso"              => "charge_price",
        "peso_volpes"            => "weight_origin",
        "peso_volpes_volumetrico" => "volume_sorter",
        "bultos"                 => "volumes",
        "codigo_estado"          => "envialia_status_id",
        "descripcion_estado"     => "envialia_status_name",
        "fechahora_alta_estado"  => "envialia_status_date",
        "fecha_entrega"          => 'delivery_date',
        "receptor_pod"           => "receiver",
        "observaciones"          => "obs",
        "nombre_repartidor"      => "operator_name",
        "recogida_asociada"      => "provider_return_tracking_code",
        "cod_ult_incidencia"     => "incidence_id",
        "desc_ult_actuacion"     => "last_observation",

        //        "peso_origen"            => "weight",
        //        "codigo_de_cliente"     => "3896",
        //        "nombre_age_cargo"       => "",
        //        "agencia_origen"         => "",
        //        "nombre_age_origen"      => "",
        //        "nombre_age_destino"     => "",
        //        "descripcion_tipo_serv"  => "",
        //        "via_destinatario"      => "",
        //        "num_destinatario"      => "",
        //        "piso_destinatario"     => "",
        //        "ult_via"               => "",
        //        "ult_direccion"         => "",
        //        "ult_numero"            => "",
        //        "ult_piso"              => "",
        //        "departamento"          => "",
        //        "nombre_dep"            => "",
        //        "cod_ult_incidencia"    => "",
        //        "desc_ult_incidencia"   => "",
        //        "fechahora_alta_estado" => "",
        //        "persona_de_contacto"   => "",
        //        "receptor_pod"          => "",
        //        "dni_pod"               => "",
        //        "fechahora_entrega_pod" => "",
        //        "cobro_cta_cliente"     => "",
        //        "porte_debido"          => "",
        //        "anticipo"              => "",
        //        "valor"                 => "",
        //        "cod_ult_actuacion"     => "",
        //        "desc_ult_actuacion"    => "",
        //        "m3_origen"             => 0.0,
        //        "cod_repartidor"        => "",
        //        "franja_horaria"        => "",
        //        "hora_inicio_concertada" => "",
        //        "hora_fin_concertada"    => "",
        //        "envio_asociado"         => "",
        //        "tipo_de_envio_asociado" => "",
    ],


    'tipsa-services' => [
        '07'  => 'RG',
        '08'  => 'REC24',
        '10'  => '10H',
        '12'  => '14H',
        '14'  => '24H', //ANTES 24ECO
        '24'  => '24H',
        '48'  => '24H',
        '72'  => '72H',
        'MV'  => '72H',
        'E24' => '24H',
        'E48' => '72H',
        'E72' => '72H',
        'RCS' => 'RCS',
        '05'  => 'RET24',
        'CC'  => 'CC',
        '03'  => 'DEV',
        '92EU' => '200',
        '20'  => 'RED',
        'V14' => 'V14',
        '92'  => 'DPD',
        '00'  => '24H',
        'DEV' => '24H',
    ],

    'tipsa-services-collection' => [
        /*'07'  => 'RG',
        '08'  => 'REC24',
        '10'  => 'REC24',
        '14'  => 'REC24',
        '24'  => 'REC24',
        '48'  => 'REC24',
        '72'  => 'REC24',
        'E24' => 'REC24',
        'E48' => 'REC24',
        'E72' => 'REC24',
        'RCS' => 'REC24',
        '05'  => 'REC24',
        'CC'  => 'REC24',
        '03'  => 'REC24',
        '92EU' => 'REC24',
        '20'  => 'REC24',
        'V14' => 'REC24',
        '20'  => 'REC24'*/
        '07'  => 'RG',
        '08'  => 'REC24',
        '10'  => '10H',
        '14'  => '14H',
        '24'  => '24H',
        '48'  => '24H',
        '72'  => '72H',
        'E24' => '24H',
        'E48' => '72H',
        'E72' => '72H',
        'RCS' => 'RCS',
        '05'  => 'RET24',
        'CC'  => 'CC',
        '03'  => 'DEV',
        '92EU' => '200',
        '20'  => 'RED',
        'V14' => 'V14',
        '92'  => 'INT-T'
    ],

    'tipsa-status' => [
        '0' => '2', //aceite
        '1' => '3', //em transito
        '2' => '4', //em distribuicao
        '3' => '5', //entregue,
        '4' => '9', //incidencia
        '5' => '7', //devolvido
        '6' => '9', //falta de expedicao -> incidencia
        '7' => '11', //recanalizado
        '9' => '31', //falta expedicao administrativa => aguarda realização
        '10' => '8', //anulado
        '15' => '12', //entrega parcial

        '11' => '14', //Recogida -> Realizada (pode associar ao 19 - envio gerado)
        '12' => '20', //Leitura Repartidor -> lido pelo motorista
        '13' => '21', //Lida pela Agência -> visto pela central
        '14' => '26', //Disponible en ponto -> Entregue PUDO
    ],

    'tipsa-status-collection' => [
        '1'  => '21', //em transito (na tipsa) = aceite central
        '2'  => '10', //em reparto (tipsa) = a recolher
        '4'  => '18', //incidencia (tipsa) = recolha falhada
        '13' => '39', //visto agencia destino
        '11' => '14', //Recogida -> Realizada (pode associar ao 19 - envio gerado)
        '12' => '20', //Leitura Repartidor -> lido pelo motorista
    ],

    'tipsa-incidences' => [
        'AS'  => '1', //ausente
        'C'   => '2', //encerrado
        'DI'  => '3', //morada incorreta
        'EAD' => '6', //ENTREGA AGENDADA COM O DESTINATARIO
        'EDT' => '4', //ENVIO PARTIDO/DANIFICADO
        'EXM' => '5', //Estravio de mercadoria
        'FE'  => '7', //Falta Expedição
        'FB'  => '8', //Volumes em Falta
        'FD'  => '9', //Dados Insuficientes
        'FL'  => '10', //Feriado Local
        'MC'  => '11', //Mal Canalizado
        'ADU' => '12', //Mercadoria em Alfândega
        'NAM' => '13', //Não Aceitou Mercadoria
        'RD'  => '14', //RECOLHEM NA AGENCIA
    ],


    /*===========================================================
     * CTT EXPRESS
     ===========================================================*/

    'ctt' => [
        'descri_material'       => 'service',
        'descrmaterial'         => 'service',
        'codigo_de_barras'      => 'provider_tracking_code',
        'nome_do_destinatario'  => 'recipient_name',
        'codpost'               => 'recipient_zip_code',
        //'cp4'                   => '',
        'pais_destino'          => 'recipient_country',
        'data_do_documento'     => 'date',
        'data_do_do'            => 'date',
        'qtdfaturada'           => 'volumes',
        'peso_bruto'            => 'weight',
        //'montimposto'           => '',
        'valliq'                => 'cost_price',
        //'no_id_fiscal'          => '',
        //'pedido'                => '',
    ],

    'ctt-services' => [
        '24' => '24H', //24h
        '13' => '14H', //14h
        '19' => '24H', //
        '48' => '24H',
        'Cargo' => 'TXL', //tartaruga XL
        '19H' => '24h', //24H
        'Recolha Não Efectuada Nacional'      => 'RECF',
        'Recolha Não Efectuada Internacional' => 'RECF',
        'Reembolso - Objetos à Cobrança (ES)' => 'REMB',
        'Reembolso - Objetos à Cobrança (PT)' => 'REMB',
        'Despacho Único Aduan'                => 'DUA',
    ],

    'ctt-status' => [
        '2'   => '2',
        'EMO' => '16', //Envio criado. Aguarda processamento
        'EMP' => '36', //recolhido
        'EMA' => '2', //aceite
        'EMB' => '17', //recepção nacional => recebido em armazém
        'TRA' => '22', //em tratamento => em espera
        'EXD' => '9', //Retenção na Expedição Internacional - Retido no país de origem, por falta de falta informação ou documentação do envio, conteúdos em falta ou danificados
        'EXO' => '', //Na Alfândega de exportação
        'EXB' => '9', //Retenção na Alfândega de Exportação
        'EXC' => '', //Saída de Alfândega para exportação
        'EXX' => '9', //Retido por Motivos de Segurança Aérea
        'EMJ' => '', //Chegada ao Trânsito Internacional
        'EMK' => '', //Partida do Trânsito Internacional
        'EMD' => '17', //recepcao internacional -> entrada em armazem
        'EDA' => '17', //RETENÇÃO NA RECEPÇÃO INTERNACIONAL
        'EDB' => '9', //Aguarda Procedimentos Declarativos
        'EME' => '9', //RETENÇÃO NA ALFÂNDEGA
        'EDC' => '3', //SAÍDA DE ALFÂNDEGA - em transporte
        'EDX' => '8', //Cancelado
        'EMR' => '29', //saida de armazem (reempressao de rotolo)
        'EMF' => '3', //expedicao nacional => em transito
        'COB' => '29', //saida de armazem
        'OAU' => '16',
        'EDF' => '22', //aguarda realização
        'EMN' => '11', //reexpedição => recanalizado
        'EMZ' => '4', //em distribuicao
        'EDH' => '26', //Chegada ao Ponto de Entrega
        'EMI' => '5', //entregue,
        'EMH' => '9', //entrega nao conseguida -> incidencia
        'EMW' => '26', //Disponível para levantamento [entregue ponto recolha]
        'EMV' => '73', //em devolucao
        'EMM' => '7', //devolvido
        'EAE' => '22', //Alteração de dados de encomenda
        'ENE' => '',
        'EFF' => '',
        'EZZ' => '5', //Fecho adminsitrativo
        'EMX' => '',
        'EMY' => '3', // Expedição do Posto de Aceitação ==> Em transito
        'EXO' => '10', //a recolher
        'EDD' => '17', //recebido em armazem -> entrada em armazem
        'EMG' => '33', //recepcao posto entrega -> em armazem destino
        'EMC' => '30', //Em alfandega
        'EDG' => '4', //em distribuicao internacional
        'FUA' => '33', //em armazem destino
        'TLF' => '31', // aguarda expedição
        'RCD' => '22',
        'EIF' => '41', // Sem informação
        'Concluída'     => '14', //realizada
        'Por Confirmar' => '21', //lida pela central
        'Não efectuada' => '18',  //falhada
        'TLF' => '9',
        'RCD' => '17'
    ],

    'ctt-status-collection' => [
        'Concluída'     => '14', //realizada
        'Por Confirmar' => '21', //lida pela central
        'Não efectuada' => '18'  //falhada
    ],

    'ctt-incidences' => [

        'EMH-10' => '3',  //Endereço Incorrecto Ou Insuficiente. N/A
        'EMH-11' => '1',  //Destinatário Ausente, Empresa Encerrada. N/A
        'EMH-12' => '16', //Destinatário Desconhecido Na Morada. N/A
        'EMH-13' => '13', //Recusado. N/A
        'EMH-14' => '6',  //Entrega adiada pelo Destinatário
        'EMH-15' => '2',  //Destinatário Em Greve. N/A - Encerrado
        'EMH-16' => 'Falha Na Distribuição - Sem Tentativa De Entrega.', //Falha Na Distribuição - Sem Tentativa De Entrega. N/A
        'EMH-17' => '11', //Errado Encaminhamento - Mal canalizado
        'EMH-18' => '4',  //Objecto Danificado. N/A
        'EMH-21' => '29', //Pagamento Indisponível. - não tem dinheiro para pagar
        'EMH-22' => '21', //Objecto Não Reclamado. Devolvido
        'EMH-23' => 'Destinatário Falecido.', //Destinatário Falecido.
        'EMH-24' => 'Morada Inacessível - Sem Tentativa De Entrega.',  //Morada Inacessível - Sem Tentativa De Entrega.
        'EMH-25' => '14', //Destinatário Solicitou Levantamento Na Loja. Avisado na Loja
        'EMH-26' => '10', //Feriado Local. N/A
        'EMH-27' => 'Objecto Não Localizado.',  //Objecto Não Localizado.
        'EMH-28' => 'Destinatario Mudou-Se.',  //Destinatario Mudou-Se. N/A
        'EMH-29' => 'Destinatario Tem Apartado / Cci. Avisado no Apartado', //Destinatario Tem Apartado / Cci. Avisado no Apartado
        'EMH-50' => 'Pedido De Alteração. Reexpedido',  //Pedido De Alteração. Reexpedido
        'EMH-56' => 'Objecto Sem Distribuição Domiciliária. Aguarda nova tentativa de entrega', //Objecto Sem Distribuição Domiciliária. Aguarda nova tentativa de entrega
        'EMH-60' => '8',  //Remessa Incompleta. N/A
        'EMH-61' => 'Caixa De Correio Inacessível.',  //Caixa De Correio Inacessível. N/A
        'EMH-62' => 'Covid 19 - Local Interdito.', //Covid 19 - Local Interdito. N/A
        'EMH-63' => 'Objeto Com Cobrança - Multibanco Indisponível.', //Objeto Com Cobrança - Multibanco Indisponível.




        'EMH-10' => '3', //Endereço Incorrecto Ou Insuficiente. N/A
        'EMH-11' => '1', //Destinatário Ausente, Empresa Encerrada. N/A
        'EMH-12' => '', //Destinatário Desconhecido Na Morada. N/A
        'EMH-13' => '13', //Recusado. N/A
        'EMH-14' => '', //Entrega adiada pelo Destinatário
        'EMH-15' => '', //Destinatário Em Greve. N/A - Encerrado
        'EMH-16' => '', //Falha Na Distribuição - Sem Tentativa De Entrega. N/A
        'EMH-17' => '', //Errado Encaminhamento - Mal canalizado
        'EMH-18' => '4', //Objecto Danificado. N/A
        'EMH-21' => '29', //Pagamento Indisponível. - não tem dinheiro para pagar
        'EMH-22' => '', //Objecto Não Reclamado. Devolvido
        'EMH-23' => '', //Destinatário Falecido. N/A
        'EMH-24' => '', //Morada Inacessível - Sem Tentativa De Entrega. N/A
        'EMH-25' => '', //Destinatário Solicitou Levantamento Na Loja. Avisado na Loja
        'EMH-26' => '10', //Feriado Local. N/A
        'EMH-27' => '5', //Objecto Não Localizado. N/A
        'EMH-28' => '', //Destinatario Mudou-Se. N/A
        'EMH-29' => '', //Destinatario Tem Apartado / Cci. Avisado no Apartado
        'EMH-50' => '', //Pedido De Alteração. Reexpedido
        'EMH-56' => '', //Objecto Sem Distribuição Domiciliária. Aguarda nova tentativa de entrega
        'EMH-60' => '8', //Remessa Incompleta. N/A
        'EMH-61' => '', //Caixa De Correio Inacessível. N/A
        'EMH-62' => '', //Covid 19 - Local Interdito. N/A
        'EMH-63' => '', //Objeto Com Cobrança - Multibanco Indisponível. Aguarda nova tentativa de entrega
    ],

    'ctt-motivos' => [
        'EDF-1'  => 'Covid 19 - Local Interdito.',
        'EDF-3'  => 'Receção Fora De Tempo.',
        'EDF-5'  => 'Perdeu Ciclo De Entrega/Expedição.',
        'EDF-6'  => 'Documentação Em Falta.',
        'EDF-8'  => 'Entrega Em Grande Superfície/Entrega Com Marcação.',
        'EDF-9'  => 'Feriado Local/Tolerância De Ponto.',
        'EDF-10' => 'Destinatário Encerrado Temporariamente.',
        'EDF-11' => 'Morada De Entrega Em Averiguação.',
        'EDF-12' => 'Reembalamento.',
        'EDF-14' => 'Conteúdo Danificado E/Ou Em Falta.',
        'EDF-15' => 'Tratamento De Devolução.',
        'EDF-16' => 'Aguarda Agendamento Para Entrega.',
        'EDF-18' => 'Conteúdo Proibido.',
        'EDF-20' => 'Força Maior.',
        'EDF-24' => 'Aguarda Envio Para Refugo.',
        'EDF-30' => 'Remessa Incompleta.',
        'EDF-42' => 'Objeto Fora De Formato. Objeto fora de formato',
        'EDF-44' => 'Falta De Msg De Pré Aviso Alfandegária.',
        'EAE-80' => 'Alteração Morada de Entrega. Pedido pelo Destinatário',
        'EAE-81' => 'Alteração Ponto Ctt de Entrega. Pedido pelo Destinatário',
        'EAE-82' => 'Alteração Data de Entrega. Pedido pelo Destinatário',
        'EAE-83' => 'Alteração Janela Horária de Entrega. Pedido pelo Destinatário',
        'EAE-84' => 'Alteração Prazo de Levantamento Em Ponto Ctt. Pedido pelo Destinatário',
        'EAE-85' => 'Alteração Nome do Destinatário. Pedido pelo Destinatário',
        'EAE-86' => 'Alteração Nome do Remetente. Pedido pelo Remetente',
        'EAE-87' => 'Alteração +1 Tentativa de Entrega. Pedido pelo Destinatário',
        'EAE-88' => 'Alteração Cobrança. Pedido pelo Remetente',
        'EAE-89' => 'Alteração Opção de Não Entrega. Pedido pelo Destinatário',
        'EAE-90' => 'Alteração Data de Recolha. Pedido pelo Remetente',
        'EAE-91' => 'Alteração Morada de Recolha. Pedido pelo Remetente',
        'EAE-92' => 'Alteração Período de Recolha. Pedido pelo Remetente',
    ],

    /*===========================================================
     * GLS ZETA
     ===========================================================*/
    'gls_zeta' => [
        "codestado"    => 'status',
        //"codexp"       => 273594560.0,
        "codbar"       => "provider_tracking_code",
        "codplaza_org" => 'provider_sender_agency',
        "codplaza_dst" => 'provider_recipient_agency',
        "codplaza_pag" => 'provider_cargo_agency',
        "referencia"   => "reference",
        "fecha"        => 'date',

        "bultos"       => 'volumes',
        "kgs"          => 'weight',
        "vol"          => 'volumetric_weight',
        "horario"      => 'horary',
        "codservicio"  => 'service',
        "reembolso"    => 'charge_price',
        "debidos"      => 'total_price_for_recipient',
        "codcli"       => 'customer',
        //"departamento_org" => null,
        "nombre_org"    => "sender_name",
        "calle_org"     => "sender_address",
        "cp_org"        => "sender_zip_code",
        "localidad_org" => "sender_city",
        "tfno_org"      => "sender_phone",
        //"departamento_dst" => null
        "nombre_dst"    => "recipient_name",
        "calle_dst"     => "recipient_address",
        "cp_dst"        => "recipient_zip_code",
        "localidad_dst" => "recipient_city",
        "pais_dst"      => 'recipient_country',
        "tfno_dst"      => 'recipient_phone',

        //"dac"          => "SIN RCS",
        //"columna"      => -1.0,
        //"kgsvol_cli"   => 0.2,
        //"nivel"        => 0.0
        //"movil_dst" => null
        //"dmail_dst" => null
        //"columnanew" => 23.0,
        //"zona" => 0.0
        //"ordencalidad" => 230.0
        //"fpentrega" => Carbon {#3976 ▶}
        //"nemonico_org" => "P34"
        //"nemonico_dst" => "P49"
        //"nemonico_pag" => "P49"

        //"seguro" => 0.0
        //"refn" => "21680410412"
        //"zonared" => 0.0
    ],

    'gls_zeta-status' => [
        '-10' => '16',  //GRABADO
        '0'   => '2',    //MANIFESTADA
        '1'   => '9',    //RETENIDA EN DELEGACION
        '2'   => '3',    //EN TRANSITO A DESTINO
        '3'   => '3',    //EN TRANSITO A DESTINO
        //'3'   => '33', //EN DELEGACION DESTINO ==> comentado em 28/03/2022
        '5'   => '8',    //ANULADA
        '6'   => '4',    //EN REPARTO
        '7'   => '5',    //ENTREGADO
        '8'   => '12',    //ENTREGA PARCIAL
        '9'   => '33',    //EN DELEGACION
        '10'  => '7',    //DEVUELTA
        '11'  => '9',    //PENDIENTE DATOS, EN DELEGACION
        '12'  => '7',    //DEVUELTA AL CLIENTE
        '13'  => '7',    //POSIBLE DEVOLUCION
        '14'  => '7',    //SOLICITUD DE DEVOLUCION
        '15'  => '7',    //EN DEVOLUCION
        '16'  => '34',    //EN DELEGACION ORIGEN
        '17'  => '9',    //DESTRUIDO POR ORDEN DEL CLIENTE
        '18'  => '9',    //RETENIDO POR ORDEN DE PAGA
        '19'  => '33',    //EN PLATAFORMA DE DESTINO
        '20'  => '9',    //PERDIDA / ROTURA
        '21'  => '11',    //RECANALIZADA (A EXTINGUIR)
        '22'  => '26',    //ENTREGADO EN ASM PARCELSHOP
        '25'  => '43',  //RECEPCIONADO EN PS

        '50'  => '2',    //PRECONFIRMADA ENTREGA
        '51'  => '8',    //ENTREGA ANULADA (DEVUELTA)
        '57'  => '9',  //INCIDENCIA
        '90'  => '8',    //CERRADO DEFINITIVO
        '91'  => '9',    //CON INCIDENCIA
    ],

    'gls_zeta-collections-status' => [
        '0' => '8', //ANULADA
        '1' => '24', //SOLICITADA
        '2' => '19', //REALIZADA COM EXITO
        '3' => '18', //RECOLHA FALHADA
        '4' => '21', //RECEBIDA
        '5' => '18', //RECOLHA COM INCIDENCIA
        '6' => '10', //RECOLHIDO NO CLIENTE
        '7' => '14', //RECOLHA REALIZADA
        '9' => '10', //ATRIBUIDA AO MOTORISTA
        '16' => '10', //POR ETIQUETAR
        '10' => '16', //PRECONFIRMAR,
        '50' => '9', //INCIDENCIA,
        '57' => '9', //INCIDENCIA
    ],

    'gls_zeta-services' => [
        '0' => '10H',     //ASM 10
        '1' => '24H',     //COURIER
        '2' => '14H',     //ASM 14
        '3' => '24H',     //ASM24
        '8' => 'REC24',     //RECOGIDA CRUZADA
        '12' => 'MI',   //INTERNACIONAL EXPRESS => MARITIMO ILHAS
        '18' => '24H',   //ECONOMY
        '37' => '24H',   //ECONOMY
        '74' => 'INT-T',   //EUROBUSINESS PARCEL
        '76' => 'INT-T',   //EUROBUSINESS SMALL PARCEL
        '7'  => 'REC24',   //RECOGIDA
        '78' => 'REC24',    //RECOLHA
        '10' => 'RET',    //RETORNO
        '9'  => 'DEV',    //DEVOLUCION
        '32' => 'CC',   //RC.SELLADA => RCS
        '14' => '14H',


        /*
        '5',//	BICI
        '6',//	CARGA
        '8',//	RECOGIDA CRUZADA
        '11',//	IBEX
        '12',//	INTERNACIONAL EXPRESS
        '13',//	INTERNACIONAL ECONOMY
        '14',//	DISTRIBUCION PROPIA
        '15',//	OTROS PUENTES
        '16',//	PROPIO AGENTE
        '17',//	RECOGIDA SIN MERCANCIA
        '19',//	OPERACIONES RED
        '20',//	CARGA MARITIMA
        '21',//	GLASS
        '22',//	EURO SMALL
        '23',//	PREPAGO
        '24',//	OPTIPLUS
        '25',//	EASYBAG
        '26',//	CORREO INTERNO
        '27',//	14H SOBRES
        '28',//	24H SOBRES
        '29',//	72H SOBRES
        '30',//	ASM0830
        '31',//	CAN MUESTRAS

        '33',//	RECANALIZA
        '34',//	INT PAQUET
        '35',//	dPRO
        '36',//	Int. WEB
        '38',//	SERVICIOS RUTAS
        '39',//	REC. INT
        '40',//	SERVICIO LOCAL MOTO
        '41',//	SERVICIO LOCAL FURGONETA
        '42',//	SERVICIO LOCAL F. GRANDE
        '43',//	SERVICIO LOCAL CAMION
        '44',//	SERVICIO LOCAL
        '45',//	RECOGIDA MEN. MOTO
        '46',//	RECOGIDA MEN. FURGONETA
        '47',//	RECOGIDA MEN. F.GRANDE
        '48',//	RECOGIDA MEN. CAMION
        '49',//	RECOGIDA MENSAJERO
        '50',//	SERVICIOS ESPECIALES
        '51',//	REC. INT WW
        '52',//	COMPRAS
        '53',//	MR1
        '54',//	EURO ESTANDAR
        '55',//	INTERC. EUROESTANDAR
        '56',//	RECOGIDA ECONOMY
        '57',//	REC. INTERCIUDAD ECONOMY
        '58',//	RC. PARCEL SHOP
        '59',//	ASM BUROFAX
        '60',//	ASM GO
        '66',//	ASMTRAVELLERS*/
    ],

    'gls_zeta-incidences' => [
        '0' => '26', //RETENIDA
        '2' => '23', //METEOROLOGIA
        '3' => '8', //FALTA EXPEDICION COMPLETA
        '4' => '8', //FALTAN BULTOS
        '8' => '7', //CLASIFICACION EN PLATAFORMA
        '9' => '1', //AUSENTE
        '10' => '13', //NO ACEPTA EXPEDICION
        '11' => '29', //NO ACEPTA P.DEBIDO Y/O REEMBOLSO
        '12' => '9', //FALTAN DATOS
        '13' => '3', //DIRECCION INCORRECTA
        '14' => '', //CAMBIO DOMICILIO
        '15' => '2', //AUSENTE SEGUNDA VEZ
        '18' => '28', //RETRASO EN RUTA NACIONAL
        '19' => '19', //RETORNO NO PREPARADO
        '20' => '5', //ROBADA PARTE DE LA MERCANCIA
        '21' => '4', //DETERIORADA
        '22' => '21', //CONTACTANDO CON DESTINATARIO
        '23' => '5', //PERDIDA / ROTURA
        '24' => '22', //EN AEROPUERTO
        '25' => '12', //EN ADUANA
        '26' => '2', //CERRADO POR VACACIONES
        '28' => '16', //DESCONOCIDO
        '29' => '9', //MAL DOCUMENTADA
        '40' => '10', //FESTIVO
        '41' => '2', //FUERA HORARIO COMERCIAL
        '42' => '21', //NO ACEPTA FIRMA ALBARAN DAC
        '43' => '21', //FALTA ALBARAN DAC
        '44' => '26', //DPTO. INSULAR: RETENIDA
        '45' => '24', //NO TIENE DINERO
        '48' => '28', //NO ENLAZA
        '53' => '',
        '55' => '2', //cerrado dia hoy
        // '57' => '', //VEIDULO INAPROPRIADO
        '63' => '28', //MASIVO EN REPARTO
        '64' => '28', //EXCEDIDO TIEMPO ESPERA GG.SS
        '69' => '28', //ENTREGA EN MAX 72H
        '70' => '28', //DNI NO COINCIDENTE
        '71' => '28', //CLASIFICACION RED
        '72' => '26', //RET. EN INSULAR FALTA DOCUMENTACION
        '73' => '28', //TRANSITO MARITIMO SEMANAL
        '74' => '28', //AEREO +24H
        '75' => '28', //EN TRANSITO POR AVERIA
        '77' => '27', //TELEFONO INCORRECTO
        '78' => '7', //CLASIFICACION EN PLATAFORMA
        '80' => '28', //CLIENTE SOLICITA DEVOLUCION
        '81' => '28', //FACILITADA SOLUCIÓN POR EL CLIENTE
        '82' => '20', //RETENIDA INT (Falta Documentos) --> envio sem documentos
        '83' => '22', //RETENIDA INT (Exceso Peso/Med) --> excede peso ou medidas
        '85' => '25', //FALTA DE TIEMPO
        '96' => '9', // INCIDENCIA - FACILITADA SOLUCIÓN DESTINATARIO
        '93' => '21',
    ],

    'gls_zeta-incidences-collections' => [
        '3' => '26', //recolha duplicada
        '4' => '23',
        '50' => '25',
        '51' => '',
        '52' => '',
        '53' => '',
        '54' => '1', //AUSENTE
        '55' => '2', //cerrado dia hoy
        '56' => '',
    ],

    /*===========================================================
     * CHRONOPOST
     ===========================================================*/

    'chronopost-status' => [
        '700' => '2',  //aceite (Informação interactiva de previsão de entrega)
        'COL' => '14', //recolha realizada
        '410' => '17', //recebido em armazém
        'PEC' => '17', //recebido em armazém (nacional)
        'INB' => '17', //recebido em armazém (internacional)
        'HUB' => '3',  //em transporte
        'OFD' => '4',  //em distribuicao
        'POD' => '5',  //entregue
        '506' => '9', //incidencia
        '510' => '9', //incidencia
        '401' => '9', //incidencia
        '402' => '13' //pendente chegada ao armazem
    ],

    /*===========================================================
     * FEDEX
     ===========================================================*/

    'fedex-services' => [
        '24' => '24H', //24h
    ],

    'fedex-status' => [
        'AA' => '30', //Em aeroporto
        'AD' => '5', //At Delivery => Na entrega
        'AF' => '17', //At FedEx Facilit => No armazém fedex
        'AP' => '26', //At Pickup => No ponto de recolha
        'CA' => '8', //Shipment cancelled
        'CH' => '9', //location changed
        'DE' => '9', //Delivery Exception
        'DL' => '5', //Delivered
        'DP' => '29', //Departed FedEx location ==> saida armazem
        'DR' => '', //Vehicle Furnished, Not Used
        'DS' => '29', //Vehicle Dispatched => saida armazem
        'DY' => '9', //Delay
        'EA' => '', //Enroute to Airport Delay
        'ED' => '4', //Enroute to Delivery
        'EO' => '4', //Enroute to Origin Airport
        'EP' => '10', //Enroute to Pickup => A recolher
        'FD' => '17', //At FedEx Destination => 'Entrada em armazém
        'HL' => '13', //Hold At Location => Espera no local
        'IT' => '4', //In Transit
        'LO' => '29', //Left Origin => saida de armazem
        'OC' => '16', //entrada em rede
        'OD' => '5', //Out for Delivery => saiu para entrega
        'PF' => '4', //Plane in Flight => Em transporte
        'PL' => '30', //Plane Landed  => No aeroporto
        'PU' => '14', //Picked Up => recolhido
        'RS' => '7', //Return to Shipper => devolvido
        'SE' => '9', //Shipment Exception
        'SF' => '', //At Sort Facility
        'SP' => '', //Split Status - Multiple Statuses
        'TR' => '11', //Transfer => Recanalizado
        'AR' => '17', //At FedEx Facilit => No armazém fedex
        'CD' => '22' //waiting
    ],

    'fedex-incidences' => [
        'CH' => '9', //location changed
        'DE' => '21', //Delivery Exception
        'DY' => '9', //Delay
        'SE' => '21', //Shipment Exception
    ],

    /*===========================================================
     * EXPRESSO 24
     ===========================================================*/
    'expresso24-status' => [
        'I' => '9', //Incidencia
        'E' => '5', // Entregue
        'X' => '9', //reexpedida
        'D' => '7', //devolvido
        '3' => '4', //em distribuicao
        'P' => '16', //provisório = documentado
        'T' => '3' //TRANSITO (ilhas ou espanha
    ],

    /*===========================================================
     * NACEX
     ===========================================================*/

    'nacex' => [
        "nr_envio"              => "provider_tracking_code",
        "referencia"            => "reference",
        "peso_bruto"            => "weight",
        "data_envio"            => "date",
        "nome_destinatario"     => "recipient_name",
        "morada_destinatario"   => "recipient_address",
        "codigo_postal"         => "recipient_zip_code",
        "localidade"            => "recipient_city",
        "estado_envio"          => "gls_status_name",
        "razao"                 => "reason",
        "data_entrega"          => "delivery_date",
        "recetor"               => "receiver",
        "hora_entrega"          => "delivery_hour",
        "devolver"              => "return",
        "reagendar"             => "reschedule",
        "nova_morada"           => "new_address",
        "obs"                   => "obs",
    ],

    'nacex-services' => [
        '08' => '8', //nacex 19H
        'E'    => '', //EURONACEX TERRESTRE	Resto de países - INTERNACIONAL
        'F'    => '', //SERVICIO AEREO	Resto de países - INTERNACIONAL
        'G' => '', //EURONACEX ECONOMY	Resto de países - INTERNACIONAL
        'H' => '', //PLUSPACK EUROPA	Resto de países - INTERNACIONAL
        '01' => '', //NACEX 10:00H	España, Portugal, Andorra - NACIONAL
        '02' => '', //NACEX 12:00H	España, Portugal, Andorra - NACIONAL
        '03' => '', //INTERDIA	España, Portugal, Andorra - NACIONAL
        '04' => '', //PLUS BAG 1	España, Portugal, Andorra - NACIONAL
        '05' => '', //PLUS BAG 2	España, Portugal, Andorra - NACIONAL
        '06' => '', //VALIJA	España, Portugal, Andorra - NACIONAL
        '07' => '', //VALIJA IDA Y VUELTA	España, Portugal, Andorra - NACIONAL
        '08' => '8', //NACEX 19:00H	España, Portugal, Andorra - NACIONAL
        '09' => '', //PUENTE URBANO	España, Portugal, Andorra - NACIONAL
        '10' => '', //DEVOLUCION ALBARAN CLIENTE	España, Portugal, Andorra - NACIONAL
        '11' => '', //NACEX 08:30H	España, Portugal, Andorra - NACIONAL
        '12' => '', //DEVOLUCION TALON	España, Portugal, Andorra - NACIONAL
        '14' => '', //DEVOLUCION PLUS BAG 1	España, Portugal, Andorra - NACIONAL
        '15' => '', //DEVOLUCION PLUS BAG 2	España, Portugal, Andorra - NACIONAL
        '17' => '', //DEVOLUCION E-NACEX	España, Portugal, Andorra - NACIONAL
        '21' => '', //NACEX SABADO	España, Portugal, Andorra - NACIONAL
        '22' => '', //CANARIAS MARITIMO	España, Portugal, Andorra - NACIONAL
        '24' => '', //CANARIAS 24H	España, Portugal, Andorra - NACIONAL
        '26' => '', //PLUS PACK	España, Portugal, Andorra - NACIONAL
        '27' => '', //E-NACEX	España, Portugal, Andorra - NACIONAL
        '28' => '', //PREMIUM	España, Portugal, Andorra - NACIONAL
        '29' => '', //NX-SHOP VERDE	España, Portugal, Andorra - NACIONAL
        '30' => '', //NX-SHOP NARANJA	España, Portugal, Andorra - NACIONAL
        '31' => '', //E-NACEX SHOP	España, Portugal, Andorra - NACIONAL
        '33' => '', //C@MBIO	España, Portugal, Andorra - NACIONAL
        '48' => '', //CANARIAS 48H	España, Portugal, Andorra - NACIONAL
        '88' => '', //INMEDIATO	España, Portugal, Andorra - NACIONAL
        '90' => '', //NACEX.SHOP	España, Portugal, Andorra - NACIONAL
        '91' => '', //SWAP	España, Portugal, Andorra - NACIONAL
        '95' => '', //	RETORNO SWAP	España, Portugal, Andorra - NACIONAL
        '96' => '', //DEV. ORIGEN	España, Portugal, Andorra - NACIONAL
    ],

    'nacex-status' => [
        '1'   => '14', //RECOGIDO
        '2'   => '3', //TRANSITO
        '3'   => '4', //REPARTO
        '4'   => '5', //OK
        '9'   => '16', //ANULADA
        'Ver código incidencia' => '9', //entrada em rede
    ],
    
    'nacex-status-collection' => [
        '11'   => '21',//PENDIENTE
        '12'   => '21', //LIDO
        '13'   => '18', //RECHAZADA (rejeitada)
        '14'   => '2', //CONFIRMADA
        '15'   => '20', //ASIGNADA A MENSAJERO PARA RECOGER
        '16'   => '14', //RECOGIDA
        '17'   => '9', //INCIDENCIA RECOGIDA
        '18'   => ''
    ],

    /*===========================================================
     * TNT EXPRESS
     ===========================================================*/

    'tnt-express-status' => [
        'OK'   => '5', //entregue
        'OD'   => '4', //distribuição
        'OF'   => '4', //em distribuicao
        'AS'   => '14', //recebido em ponto de transito
        'TR'   => '3', //transito
        'OS'   => '3', //transito
        'TUL'  => '3', //transito
        'IS'   => '9', //armazem tnt
        'IR'   => '17',
        'PU'   => '14', //recolhido
        'LAO'  => '9', //Delay. Recovery Action Underway
        'CI'   => '17', //documentado
        'IS'   => '17', //recebido em armazém
        'HH'   => '31', //aguarda em armazém
        'MRC'  => '30', //em alfandega.
        'ID'   => '30', //em alfandega.
        'PAC'  => '30',  //em alfandega.
        'NR'   => '30', //em alfandega.
        'LAN'  => '31',
        'RES'  => '5', //entrege,
        'DH'   => '3', //transito
        'UP'   => '30', //em alfandega.
        'HI'   => '9', //em incidencia.
        'CCI'  => '3', //transito
        'RC'   => '3', //transito
        'MOO'  => '3', //transito
        'PC'   => '31', // Aguarda Expedição
        'CNB'  => '9', //em incidencia.
        'LP'   => '12', //entrega parcial
        'NH'   => '9', //em incidencia.
        'HW'   => '17', //em armazem
        'DNR'  => '9', //em incidencia.
        'RU'   => '22',  // aguarda realização
        'HMF'  => '9', //em incidencia.
        'WAS'  => '9', //em incidencia.
        'RWA'  => '31', // Aguarda Expedição
        'NPD'  => '5', //entregue
        'FR'   => '11',
        'CA'   => '22',
        'NT'   => '22',
        'WA'   => '9',
        'FCR'  => '30',
        'IG'   => '30',
        'ITC'  => '31', // Aguarda Expedição
        'HO'   => '3',
        'BSH'  => '9',
        'CR'   => '9',
        'CO'   => '9',
        'DN'   => '5', //entregue
        'UPR'  => '30', //em alfandega.
        'ATL'  => '5', // entregue
        'MTO'  => '31',
        'MLA'   => '33',
        'RTS'  => '7',
        'ORD'  => '31',
        'MOB'  => '3', //transito 
        'HOT'  => '22', //Aguarda
        'AN'   => '9',
        'MLH'  => '9',
        'MR'   => '9',
        'CNC'  => '9',
        'CRD'  => '9',
        'WAT'  => '22',
        'TUC'  => '3',
        'PNR'  => '9'
    ],

    'tnt-express-incidences' => [
        'LAO' => '9', //Delay
        'HI'   => '9', //em incidencia.
        'CNB'  => '9', //em incidencia.
        'NH'   => '9', //em incidencia.
        'DNR'  => '9', //em incidencia.
        'HMF'  => '9', //em incidencia.
        'WAS'  => '9', //em incidencia.
        'WA'   => '9', //Delay
        'BSH'  => '9',
        'CO'   => '9',
        'CR'   => '9',
        'AN'   => '9',
        'DG'   => '9',
        'CNC'  => '4',
        'MLH'  => '9',
        'MR'   => '9',
        'CRD'  => '13'
    ],

    /*===========================================================
     * SEUR
     ===========================================================*/

    'seur-status' => [
        'L003' => '5', //entregue
        'C001' => '4', //distribuição
        'C003' => '4', //distribuição
        'W999' => '3', //transito
        'W089' => '3', //recebido internacional
        'R1'   => '24',
        'R2'   => '2',
        'R2'   => '',
        'R3'   => '',
        'R4'   => '',
        'R5'   => '19',
    ],

    'seur-incidences' => [
        'I528'  => '1', //ausente
        'I523'  => '1', //ausente
        'I520'  => '16', //Destinatário Desconhecido
        ''  => '13', //Não Aceitou Mercadoria
        'I521'  => '3', //morada incorreta
        ''  => '13', //Não Aceitou Mercadoria
        ''  => '24', //não paga reembolso
        ''  => '2', //encerrado
        ''  => '19', //retorno não preparado
        ''  => '14', //Recolheram em Armazém
        ''  => '15', //envio no dia seguinte
        ''  => '21', //Extravio de Mercadoria
        ''  => '21', //Entrega Adiada pelo Destinatário
        'I511'  => '7', //Falta Expedição ou mercadoria em armazém
        ''   => '9', //Dados Insuficientes
        ''   => '10', //Feriado Local
        ''  => '18', //'Pendente de chegada
        ''  => '8', //Volumes em Falta
        ''  => '11', //Mal Canalizado
        ''  => '12', //Mercadoria em Alfândega
        ''  => '4', //Envio Danificado
        ''  => '20', //sem documentação
    ],

    /*===========================================================
     * VASP
     ===========================================================*/

    'vasp-status' => [
        'DONE'      => '5', //entregue
        'CONFIRMED' => '17', //em armazem
        'TRANSIT'   => '3', //em transporte
        'DISTRIBUTION' => '4' //distribuicao
    ],

    'vasp-incidences' => [
        'I528'  => '1', //ausente

    ],

    /*===========================================================
     * RANGEL
     ===========================================================*/

    'rangel-status' => [
        '00' => '5', //entregue
        '01' => '9', //Entrega impossível
        '09' => '5', //Entrega Bem Sucedida - Com Danos
        '14' => '', //Gerada Devolução ao expedidor
        '16' => '', //Reagendamento de Entrega
        '17' => '', //Solicitação Entrega em Data Posterior
        '18' => '', //Desvio internacional
        '20' => '', //Aguarda Decisão Administrativa
        '21' => '', //BULK PLANE OR TRUCK
        '22' => '', //Reagendamento de Entrega
        '23' => '', //Solicitação Entrega em Data Posterior
        '26' => '', //Entrega Parcial
        '27' => '', //Re-entregar
        '28' => '', //Não saiu para Distribuição
        '29' => '', //Rota re-atribuída
        '36' => '', //Recebido Envio Parcial
        '37' => '', //Envio Reembalado
        '38' => '', //Segunda Volta
        '39' => '', //Entrega com Dev. Parcial


        '67' => '', //Saída para Agente
        '68' => '3', //Em trânsito
        '70' => '', //Chegada Camião
        '71' => '17', //Entrada em Armazém
        '72' => '', //Chegada de doc/non-duty
        '77' => '', //Pacote sai do país
        'AE' => '', //Reagendamento de Entrega
        'AM' => '', //Alteração de Morada
        'NA' => '8', //Entrega anulada
        'DA' => '', //Data Entry - Por ficheiro
        'DD' => '', //Aguarda inst. do destinatário
        'DE' => '', //Devolver ao expedidor
        'ED' => '', //Entregue em Deposito
        'EE' => '', //Aguarda inst. do expedidor
        'IP' => '', //Domestic Linehaul Arrival
        'NM' => '', //Não manifestado
        'AO' => '4', //Em distribuição no dia seguinte
        'OD' => '4', //Em distribuição
        'RD' => '', //Recepcionado em Deposito
        'RM' => '', //Retido com marcação
        'SA' => '', //Solicitação Agendamento Entrega
        'SD' => '', //Saida de Deposito
        'T3' => '', //Em distribuição (3 dias)
        'T5' => '', //Em distribuição (5 dias)
        'VC' => '', //Volume consolidado num novo envio

    ],

    'rangel-incidences' => [
        '02' => '', //RELEASE SIGNATURE ON FILE
        '03' => '', //Endereço incorrecto
        '04' => '', //Entrega a terceiros
        '05' => '', //Impossível localizar
        '06' => '', //Condições Carga/Descarga
        '07' => '', //Envio recusado pelo Destinatário
        '08' => '', //Destinatário (empresa) ausente
        '10' => '', //Entrega Recusada - Com Danos
        '11' => '', //Envio Recusado pelo Destinatário - Fora da Data
        '12' => '', //Rota mal atribuída
        '13' => '', //Envio Recusado pelo Destinatário - Não Encomendado
        '15' => '', //Empresa fechada - Greve
        '42' => '', //Feriado/Férias
        '43' => '', //Envio não Pronto
        '44' => '', //Na Plataforma
        '45' => '', //Identificados Danos no Envio
        '50' => '', //Falta de documentação
        '57' => '', //Envio manifestado não recolhido
        '58' => '', //Envio abandonado
        '59' => '', //Retido para Levantamento em Armazém
        '60' => '', //Aguarda desalfandegamento
        '63' => '', //Retido para DUTIES / TAXES
        '65' => '', //Envio liberto
        '66' => '', //Rota Incorrecta
        '74' => '', //Envio sem Guia de Transporte
        '84' => '', //CHEGADA FORA DO HORÁRIO
        '88' => '', //Avaria / Atraso
        '89' => '', //ACIDENTE
        '93' => '', //Recusa de Pagamento pelo Destinatário
        '19' => '', //Plataforma Errada
        '24' => '', //Envio Furtado
        '25' => '', //Envio Extraviado
        '32' => '', //Atraso na Chegada do Arrasto
        '33' => '', //Atraso
        '34' => '', //Destruído por indicação do cliente
        'DC' => '', //Danos Chegada
        'X8' => '', //Destinatário (Particular) ausente',
    ],

    /*===========================================================
    * CORREOS EXPRESS
    ===========================================================*/

    'correos_express-status' => [
        '1' => '16', //SIN RECEPCION
        '2' => '36', //EN ARRASTRE ==> recolhido
        '3' => '17', //TRAMO ORIGEN
        '4' => '3', //TRANSITO
        '5' => '11', //MAL TRANSITADO
        '6' => '33', //DELEGACION DESTINO
        '7' => '17', //TRAMO DESTINO
        '8' => '4', //EN REPARTO
        '9' => '9', //REPARTO FALLIDO
        '10' => '33', //ALMACEN ESTACIONADO
        '11' => '31', //NUEVO REPARTO
        '12' => '5', //ENTREGADO
        '13' => '23', //NO LOCALIZADO
        '14' => '3', //REEXPEDIDO
        //'15' => '', //ALM. REGULADOR
        '16' => '9', //DESTRUIDO
        '17' => '7', //DEVUELTO
        '18' => '11', //TRANSFERIDO PROVEEDOR
        '19' => '8', //ANULADO
        '20' => '3', //PROVEEDOR/TRANSITO
        '21' => '33', //PROVEEDOR/DELEGACION
        '22' => '33', //PROVEEDOR/ESTACIONADO
        '23' => '4', //PROVEEDOR/EN REPARTO
        '24' => '13', //PROVEEDOR/SIN RECEPCION
        '25' => '3', //PROVEEDOR/ARRASTRE
        '26' => '9', //PROVEEDOR/MAL TRANSITADO
        '27' => '34', //PROVEEDOR/DELEGACION ORIGEN
        '28' => '33', //PROVEEDOR/DELEGACION DESTINO
        '29' => '9', //PROVEEDOR/REPARTO FALLIDO
        '30' => '31', //PROVEEDOR/NUEVO REPARTO
        '31' => '9', //PROVEEDOR/NO LOCALIZADO
        '32' => '7', //PROVEEDOR/INFORMADA DEVOLUCION
        '33' => '30', //PROVEEDOR/CESION ALM. ADUANA
        '34' => '5', //ENTREGADO/PROVEEDOR
        '35' => '5', //ENTREGADO EN PUNTO CONCERTADO
        '36' => '17', //RECEPCIONADO EN OFICINA
        '37' => '9', //PARALIZADO
        //'38' => '', //DEPOSITADO EN OFICINA
        //'39' => '', //DISPONIBLE EN OFICINA
        '40' => '7', //DEVUELTO EN OFICINA
        '41' => '3', //TRANSITO ESTACIONADOS
        '42' => '36', //RECOGIDO'
    ],

    'correos_express-status-collections' => [
        '0' => '10', //RECOGIDA EN CURSO
        '2' => '', //
        '3' => '2', //RECOGIDA REGISTRADA
    ],

    'correos_express-incidences' => [
        '1' => '16', //Dirección Incorrecta
        '2' => '3', //Dirección Incompleta
        '3' => '3', //Entrega Incorrecta
        '4' => '1', //Cerrado Vacaciones
        '5' => '10', //Fiesta Local
        '6' => '1', //Persona de contacto Ausente
        '7' => '21', //Área Inaccesible/Restringida
        '8' => '13', //Paquete Rehusado
        '9' => '24', //Rehusado No paga Importe
        '10' => '6', //Cambio de fecha por el destinatario
        '11' => '21', //Tiempo espera excesivo para efectuar el servicio
        '12' => '22', //Excede dimensiones/peso
        '13' => '21', //Cita previa necesaria
        '14' => '21', //Mercancía peligrosa
        '15' => '21', //País sin servicio/Mercancía restringida
        '16' => '21', //Trámite aduana/Inspección aduanera
        '17' => '20', //Falta documentación/incompleta
        '18' => '21', //Espera de autorización dest. para despacho(aduana)
        '19' => '21', //Problemas climatológicos/causa de fuerza mayor
        '20' => '14', //Instrucciones Cliente: recogerán en nave.
        '21' => '31', //Sin tiempo para efectuar el servicio
        '22' => '21', //Error asignación ruta
        '23' => '8', //Falta Contenido
        '24' => '21', //Avería
        '25' => '21', //Robo
        '26' => '21', //Hurto
        '27' => '21', //Mercancía no cargada problemas de capacidad
        '28' => '8', //Expedición Incompleta
        '29' => '4', //Dañado
        '30' => '21', //Retraso vuelo importación
        '31' => '21', //Problema carga fichero
        '32' => '21', //Mercancía ya recogida
        '33' => '30', //Mal Embalado
        '34' => '27', //Cliente no tiene mercancía
        '35' => '21', //Importe Reembolso Erróneo
        '36' => '5', //Pérdida
        '37' => '31', //Sin tiempo por transmisión informática
        '38' => '27', //Cliente no tiene preparada mercancía
        '39' => '13', //Destinatario no recepciona en horario de tarde
        '40' => '21', //Retraso vuelo/carguero
        '41' => '21', //Sin efectivo en el servicio
        '42' => '21', //Paralizado por cliente
        '43' => '21', //Entrega Flexible
        '44' => '21', //ENI-Envío No Identificado
        '45' => '21', //Pendiente para reparto
        '46' => '21', //Mercancía recibida tarde Cliente
        '47' => '21', //Causa de fuerza mayor
        '48' => '21', //Avería Clasificador
        '49' => '21', //Mercancía recibida tarde Cex
        '50' => '21', //Punto de Conveniencia sin Hueco
    ],

    /*===========================================================
     * DB SCHENKER
     ===========================================================*/
    'db_schenker-status' => [
        'ENT' => '2',  //aceite (Informação interactiva de previsão de entrega)
        'NCL' => '9', //not collected => incidence
        'COL' => '36', //recolhido
        'ENM' => '17', //recebido em armazém
        'MAN' => '3',  //em transporte
        'DOT' => '4',  //em distribuicao
        'DLV' => '5',  //entregue
        '506' => '9', //incidencia
        '510' => '9', //incidencia
        'NLO' => '9', //incidencia
        'DIS' => '9',
        'NDL' => '9',
    ],

    'db_schenker-incidences' => [
        'LC'  => '27', //Late By Customer => mercadoria nao preparada
        'FM'  => '21', //Force Majeure => outras incidencias
        'CA'  => '21', //Not Loaded => Outras incidencias
        'CD'  => '21',
        'CL'  => '10'
    ],

    /*===========================================================
     * DELNEXT
     ===========================================================*/
    /*'delnext-status' => [
        'Pending'                          => '2',  //aceite
        'Parcel in the collection process' => '10',
        'Shipment Voided'                  => '8',
        'Parcel in the Warehouse'          => '34',
        'Parcel received by Delnext. In preparation to be sent.' => '17', //entrada armazem origem
        'Parcel transferred: will arrive to Italy soon' => '16', //entrada em rede
        'The order left the central warehouse.'         => '29', //saida de armazem
        'The order is in transit.'                      => '3',
        'Shipment In Transit.'                          => '3',
        'In transit.'                                   => '3',
        'Shipment Received At Transit Point.'           => '29',
        'Your order has arrived at the Delnext distribution warehouse.' => '17',
        'Released by Customs' => '30', //em alfandega
        'In distribution.' => '4',
        'Parcel in distribution, will be delivered today' => '4',
        'Received at the warehouse closest to the destination.' => '17',
        'Delivered' => '5',
        'Delivered.' => '5'
    ],*/

    'delnext-status' => [
        '1' => '1',
        '2' => '',
        '3' => '5',
        '4' => '',
        '5' => '8',
        '6' => '3',
        '7' => '',
        '8' => '',
        '9' => '7', //devolvido
        '10' => '',
        '11' => '',
        '12' => '',
        '13' => '10',
        '14' => '9', //morada incorreta
        '15' => '',
        '16' => '',
        '17' => '',
        '18' => '',
        '19' => '4',
        '20' => '9',
        '22' => '36',
    ],

    /*===========================================================
    * MRW
    ===========================================================*/
    'mrw-status' => [
        '00' => '5',  //entregue
        '01' => '9', //incidencia
        '02' => '9', //incidencia
        '03' => '9', //morada incompleta
        '04' => '9', //morada incorreta
        '05' => '9', //rejeitado
        '06' => '9', //nao aceita reembolso
        '07' => '', //recolha pendente no destino ==> rec. falhada?
        '08' => '7', //devolvido
        '09' => '9', //entrega adiada
        '10' => '9',
        '11' => '3', //reenviado transporte
        '12' => '',
        '13' => '17', //chegada a armazem
        '14' => '32', //retido em armazem
        '15' => '9',
        '16' => '4', //distribuição
        '17' => '36', //recolhido
        '19' => '4', //em fila de espera para entrega
        '31' => '9',
        '33' => '9',
        '35' => '9',
        '41' => '9',
        '45' => '9',
        '47' => '16', //recolha pendente => entrada em rede
        '48' => '37', //pendente motorista
        '49' => '36', //recolhido
        '57' => '29', //Saída de FQ origem a PLT => saida de armazem
        '58' => '17', //chegada armazem
        '59' => '17', //chegada armazem
        '79' => '29', //Passou pela plataforma
        '72' => '36',
        '73' => '31', //aguarda nova expedicao
        '90' => '', //error de ponte
        '91' => '9', //Não recebido na mrw destino
        '93' => '23', //extraviado
        '96' => '9',
    ],

    'mrw-incidences' => [
        '01' => '1', //destinatario ausente
        '02' => '16', //desconhecido
        '03' => '3', //morada incompleta
        '04' => '3', //morada incorreta
        '05' => '13', //rejeitado
        '06' => '24', //nao aceita reembolso
        '09' => '6', //entrega adiada
        '10' => '6', //entrega adiada
        '15' => '8', //incompleto
        '18' => '27', //mercadoria não preparada
        '39' => '4', //danificado
        '33' => '15', //ACORDADA nova entrega
        '35' => '32', //pendente reembolso
        '41' => '2', //encerrado
        '45' => '6', //entrega adiada
        '91' => '18', //Não recebido na mrw destino
        '96' => '19', //atraso na rota
    ],

    /*===========================================================
    * DHL
    ===========================================================*/
    'dhl-status' => [
        'R'   => '5', //entregue
        'ZZZ' => 'ZZZ', //incidencia
        'A'   => '4', //distribuicao
        'Recogido en' => '36', //recolhido
        'Llegada a'   => '17', //chegada a armazem
        'Salida de'   => '3',  //em transporte,
        'Tránsito en' => '3', // em transporte
        'EC' => '27', //agendado com o destinatário
        'PC' => '6', //tentativa de entrega
        'CR' => '9',
        'GS' => '44', //em preparação
        'RT' => '7', //devolvido
        'CE' => '31', //aguarda expedição
        'DI' => '9',
        'MR' => '9',
        'NA' => '9',
        'EI' => '9',
        'CV' => '9',
        'FM' => '9',
        'EE' => '9',
        'NH' => '9',
        'FS' => '16',
    ],

    'dhl-incidences' => [
        'CR' => '2', //encerrado
        'DI' => '3', //morada incorreta
        'MR' => '8', //expedição incompleta
        'NA' => '15', //envio no dia seguinte
        'EI' => '8', //expedicao incompleta
        'CV' => '2',
        'CLI' => '1', // Destinatario ausente: no se ha podido realizar la entrega
    ],

    /*===========================================================
    * ONTIME
    ===========================================================*/

    'ontime' => [
        "agencia_origen"         => "provider_sender_agency",
        "agencia_destino"        => "provider_recipient_agency",
        "codigo_de_cliente"      => "customer_id",
        "albaran"                => "provider_tracking_code",
        "fecha"                  => "date",
        "referencia"             => "reference",
        "tipo_servicio"          => "service_type",
        "remitente"              => "sender_name",
        "direccion_remitente"    => "sender_address",
        "poblacion_remitente"    => "sender_city",
        "cp_remitente"           => "sender_zip_code",
        "destinatario"           => "recipient_name",
        "direccion_destinatario" => "recipient_address",
        "poblacion_destinatario" => "recipient_city",
        "cp_destinatario"        => "recipient_zip_code",
        "telefono_destinatario"  => "recipient_phone",
        "reembolso"              => "charge_price",
        "peso_origen"            => "weight_origin",
        "peso_sorter"            => "weight_sorter",
        "m3_sorter"              => "volume_sorter",
        "bultos"                 => "volumes",
        "codigo_estado"          => "envialia_status_id",
        "descripcion_estado"     => "envialia_status_name",
        "fechahora_alta_estado"  => "envialia_status_date",
        "fecha_entrega"          => 'delivery_date',
        "receptor_pod"           => "receiver",
        "observaciones"          => "obs",
        "nombre_repartidor"      => "operator_name",
        "recogida_asociada"      => "provider_return_tracking_code",
        "cod_ult_incidencia"     => "incidence_id",
        "desc_ult_actuacion"     => "last_observation"
    ],

    'ontime-services' => [
        '68' => '68 - PT PT',
        '69' => '69 - PT PT',
        '26' => '26 - PT ES',
        '27' => '27 - PT ES',
        '70' => '70 - PT ES',
        '79' => '79 - PT ES',
        '19' => '19'
    ],
    
    'ontime-status' => [
        'ORIG'           => '2', //Aceite
        'CERR'           => '3', //Em transito
        'PEND'           => '33', //Armazém destino
        'ASIG'           => '4', //Em distribuição
        'EFEC'           => '5', //entregue
        'ENTR'           => '5', //entregue
        '4303'           => '31', //Em espera
        '4405'           => '11', //Recanalizado, "envio no destino errado"
        '5101'           => '9', //Incidência
        '7301'           => '6', //Tentativa de entrega
        '7401'           => '41', //Sem "info", "falta de documentação"
        '8104'           => '30', //Em alfândega
        'ANUL'           => '8', //Anulado
        'COCE'           => '27', //Agendado
        'DGEN'           => '7', //Devolvido
        'ENPA'           => '12', //Entregue parcial
        'ENAP'           => '6', //Tentativa de entrega
        'LLEG'           => '26', //Entregue num ponto de recolha (Pickup) 
        'REPA'           => '4', //Em distribuição
        'CPCE'           => '3', //Em transporte
        'NMAI'           => '2',
        'ALTA'           => '2'
    ],

    'ontime-incidences' => [
    ],

    /*===========================================================
    * UPS
    ===========================================================*/
    'ups-status' => [
        '9E'   => '5', //entregue
        'KM'   => '5', //entregue
        'KB'   => '5', //entregue
        'MP'   => '16', //PICKUP MANIFEST RECEIVED (Entreda em rede
        'PU'   => '36', //PICKUP SCAN (Recolhido)
        'LC'   => '3', //documentado
        'AR'   => '17', //arrival scan //entrada em armazem
        'DP'   => '29', //departure scan
        'DS'   => '17', //destination scan
        'OF'   => '4', //out for delivery
        'OR'   => '17', //origin scan ==> em armazém
        'VK'   => '34', //em armazem a espera para ser entregue
        'SR'   => '34',
        'EP'   => '29',
        '08'   => '3',
        'OT'   => '29',
        'IP'   => '31',
        'FS'   => '5',
        '5R'   => '43',
        '2Q'   => '43',
        'ZP'   => '26',
        'GI'   => '13',
        'AD'   => '9'
    ],

    /*'ups-status' => [
        'KM'   => '5', //entregue
        'KB'   => '', //UPS INTERNAL ACTIVITY CODE
        'DJ'   => '', //ADVERSE WEATHER CONDITIONS CAUSED THIS DELAY
        'L1'   => '', //THE RECEIVER'S LOCATION WAS CLOSED ON THE 2ND DELIVERY ATTEMPT. A 3RD DELIVERY ATTEMPT WILL BE MADE
        'FS'   => '',
        'KB'   => '',
        'KB'   => '',
        'KB'   => '',
        'KB'   => '',
        'KB'   => '',
    ],*/


    /*===========================================================
     * VIA DIRECTA
     ===========================================================*/
    'via_directa-status' => [
        '3'   => '16', //entrada em rede
        '10'  => '5', //entregue
        '4'   => '3',  //transito
        '90'    => '73', //troca->recusado
        'dev' => '7',
    ],

    /*===========================================================
     * INTEGRA2
     ===========================================================*/
    'integra2-status' => [
        'Recogido en'  => '36',
        'En tránsito'  => '3',
        'Entregado en' => '5',
    ],

    /*===========================================================
     * WE PICKUP
     ===========================================================*/
    'wepickup-status' => [
        '1' => '16', //criado
        '2' => '16', //planeado
        '3' => '3', //transito
        '4' => '4', //distribuicao
        '5' => '9', //nao entregue
        '6' => '9', //ausente
        '7' => '9', //Desconhecido na Morada
        '8' => '5', //entregue
        '9' => '12', //Entrega Parcial
        '10' => '7', //Devolvido ao Remetente
        '11' => '7', //devolvido
        '12' => '9', //extraviado
        '13' => '9', //perda total
        '14' => '9', //Recusado
        '15' => '33', //Armazém
        '16' => '9', //Morada Incorrecta
        '17' => '11', //ReCanalizada
        '18' => '9', //nova morada
        '19' => '9', //danificado
        '20' => '8', //anulado
        '28' => '', //Disponivel para Levantamento
        '32' => '2', //recepção => aceite
        '33' => '36', //processado => recolhido
        '34' => '33', //agendado => em armazem
        '41' => '4', //Distribuição
        '53' => '36', //Distribuição
        '60' => '31'
    ],

    /*===========================================================
     * Skynet
     ===========================================================*/
    'skynet-status' => [
        '2' => '1', // Na Plataforma Origem
        '3' => '17', // Em Armazem
        '4' => '4', // Distribuição
        '5' => '5', // Entregue
        '6' => '22', // Retida
        '7' => '3', // Em Transito
    ],

    'skynet-status-incidence' => [
        // 21 - outra incidencia
        '2' => '3', //LOCAL N ACESSIVEL                                                               
        '3' => '1', //TEMPO ESPERA EXCEDIDO                                                           
        '4' => '24', //R N PAGA TRANSPORTE                                                             
        '5' => '1', //DESTINATARIO AUSENTE                                                            
        '6' => '21', //RECUSADO P/DESTINATARIO                                                         
        '7' => '9', //ENDERECO INSUFICIENTE                                                           
        '13' => '2', //FECHO FERIAS/FERIADOS                                                           
        '15' => '21', //S/DINHEIRO/PAG.INDISP.                                                          
        '16' => '24', //R N PAGA REEMBOLSO                                                              
        '17' => '24', //R FORA DO PRAZO                                                                 
        '18' => '13', //R N ENCOMENDADO                                                                 
        '19' => '9', //R FALTA DOCUMENTOS                                                              
        '20' => '8', //R FALTA VOLUMES                                                                 
        '21' => '21', //R DEVOLUCAO PARCIAL                                                             
        '22' => '13', //CANCELADO ORD EXPEDIDOR                                                         
        '30' => '14', //ESPERA LEVANTAMENTO NO ARMAZEM                                                  
        '37' => '6', //AGUARDA MARCACAO                                                                
        '39' => '9', //NÃO UTILIZAR FALTA DOCUMENTOS                                                   
        '41' => '21', //PIQUETE GREVE BLOQ. VIAT.                                                       
        '69' => '4', //MERCADORIA DETERIORADA                                                          
        '70' => '4', //MERCADORIA AMOLGADA/ AMASSADA                                                   
        '71' => '4', //MERCADORIA PARTIDA                                                              
        '72' => '4', //MERCADORIA PERFURADA                                                            
        '73' => '4', //EMBALAGEM/MERCADORIA MOLHADA                                                    
        '74' => '4', //PRODUTO DERRAMADO                                                               
        '75' => '4', //EMBALAGEM VIOLADA                                                               
        '76' => '4', //EMBALAGEM DANIFICADA                                                            
        '77' => '4', //MERCADORIA SUJA                                                                 
        '78' => '4', //MERCADORIA RISCADA
    ],

    /*===========================================================
     * Palibex
     ===========================================================*/
    'palibex-status' => [
        'reco' => '16', // Entrada em rede
    ],
];
