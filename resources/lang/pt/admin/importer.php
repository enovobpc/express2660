<?php

return [

    'import_types' => [
        'shipments'              => 'Expedições e Serviços',
        'shipments_dimensions'   => 'Expedições (com Dimensões)',
        'shipments_logistic'     => 'Expedições (com Artigos/Stocks)',
        'shipments_weights'      => 'Expedições (Atualizar Pesos)',
        'shipments_fast'         => 'Expedições (Sem validações)',
        'customers'              => 'Clientes',
        'providers'              => 'Fornecedores',
        'operators'              => 'Colaboradores',
        'prices_table'           => 'Tabela Preços',
        'routes'                 => 'Rotas',
        // 'update_routes'          => 'Atualizar Rotas (Códigos Postais)',
        'fleet_fuel'             => 'Posto Combustível',
        'tolls_logs'             => 'Portagens',
        'providers_agencies'     => 'Agências Fornecedores',

        'logistic_products'      => 'Artigos e Stocks',
        'logistic_stocks'        => 'Atualizar Total Stocks',
        'logistic_locations'     => 'Localizações',
        'reception_orders'       => 'Ordens de Recepção',
        'shipping_orders'        => 'Ordens de Saída',

        'equipments'             => 'Equipamentos',
        'fleet_vehicles'         => 'Viaturas',
        'billing_products'       => 'Artigos Faturação',
    ],

    'date_formats' => [
        'Y-m-d' => 'AAAA-MM-DD',
        'd/m/Y' => 'DD/MM/AAAA',
        'd-m-Y' => 'DD-MM-AAAA',
        'd.m.Y' => 'DD.MM.AAAA',
        'dmY'   => 'DDMMAAAA'
    ],

    'shipments' => [
        "tracking_code" => [
            'name' => 'TRK Plataforma',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "provider_tracking_code" => [
            'name' => 'TRK Fornecedor',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "provider_sender_agency" => [
            'name' => 'Agência Origem',
            'type' => 'Texto (20 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "provider_recipient_agency" => [
            'name' => 'Agência Destino',
            'type' => 'Texto (20 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "date" => [
            'name' => 'Data',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "start_hour" => [
            'name' => 'Hora Início',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "end_hour" => [
            'name' => 'Hora Fim',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "delivery_date" => [
            'name' => 'Data Entrega',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "service_code" => [
            'name' => 'Código de Serviço',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "service_id" => [
            'name' => 'Serviço',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "gls_time" => [
            'name' => 'Código Horário (Apenas GLS Zeta)',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => false,
            'input' => false,
        ],
        "customer_code" => [
            'name' => 'Código Cliente',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "department_code" => [
            'name' => 'Código Departamento',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "customer_id" => [
            'name' => 'Cliente',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "sender_name" => [
            'name' => 'Nome Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_address" => [
            'name' => 'Morada Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_address_2" => [
            'name' => 'Morada Remetente 2',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "sender_zip_code" => [
            'name' => 'Cód. Postal Remetente',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_city" => [
            'name' => 'Localidade Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_country" => [
            'name' => 'País Remetente',
            'type' => 'Texto (4 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_phone" => [
            'name' => 'Telefone Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_attn" => [
            'name' => 'Pessoa Contacto Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_vat" => [
            'name' => 'NIF Remetente',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_name" => [
            'name' => 'Nome Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_address" => [
            'name' => 'Morada Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_address_2" => [
            'name' => 'Morada Destinatário 2',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "recipient_zip_code" => [
            'name' => 'Cód. Postal Destinatário',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_city" => [
            'name' => 'Localidade Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_country" => [
            'name' => 'País Destinatário',
            'type' => 'Texto (4 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_phone" => [
            'name' => 'Telefone Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_email" => [
            'name' => 'E-mail Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_attn" => [
            'name' => 'Pessoa de Contacto',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_vat" => [
            'name' => 'NIF Destinatário',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "volumes" => [
            'name' => 'Volumes',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "weight" => [
            'name' => 'Peso Real',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "original_weight" => [
            'name' => 'Peso Original',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "volumetric_weight" => [
            'name' => 'Peso Volumétrico',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "fator_m3" => [
            'name' => 'Fator de Cubicagem',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "volume_m3" => [
            'name' => 'Volume M3',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "kms" => [
            'name' => 'Km',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "charge_price" => [
            'name' => 'Valor Reembolso',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "total_price_for_recipient" => [
            'name' => 'Valor Portes Destino',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "rpack" => [
            'name' => 'Retorno de Encomenda',
            'type' => 'Boleano',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'true',
            'preview_customer' => true,
        ],
        /*           "rcheck" => [
                'name' => 'Retorno Cheque',
                'type' => 'Boleano',
                'mapping' => true,
                'preview' => true,
                'input'    => false,
                'checkbox' => 'true',
                'preview_customer' => false,
            ],*/
        "rguide" => [
            'name' => 'Retorno Guia Assinada',
            'type' => 'Boleano',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'true',
            'preview_customer' => true,
        ],
        "provider_id" => [
            'name' => 'Fornecedor',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "reference" => [
            'name' => 'Referência',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "reference2" => [
            'name' => 'Referência 2',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "reference3" => [
            'name' => 'Referência 3',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "obs" => [
            'name' => 'Observações',
            'type' => 'Texto (500 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "obs_delivery" => [
            'name' => 'Observações Entrega',
            'type' => 'string',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "obs_internal" => [
            'name' => 'Observações Internas',
            'type' => 'string',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "operator_id" => [
            'name' => 'Código Motorista',
            'type' => 'Texto (6 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "status_code" => [
            'name' => 'Código do Estado',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "status_id" => [
            'name' => 'Estado',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "status_date" => [
            'name' => 'Data Último Estado',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "status_hour" => [
            'name' => 'Hora Último Estado',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        /**"total_price" antes*/
        "shipping_price" => [
            'name' => 'Preço Venda',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "vehicle" => [
            'name' => 'Matrícula Viatura',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "trailer" => [
            'name' => 'Matrícula Reboque',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "expense1" => [
            'name' => 'Taxa Adicional 1',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "expense2" => [
            'name' => 'Taxa Adicional 2',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "expense3" => [
            'name' => 'Taxa Adicional 3',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "is_collection" => [
            'name' => 'É pedido Recolha?',
            'type' => 'Sim/Não (1 ou 0)',
            'mapping' => true,
            'preview' => true,
            'checkbox' => 'true',
        ],
    ],

    'shipments_logistic' => [
        "unique_ref" => [
            'name' => 'ID pedido',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "reference" => [
            'name' => 'Referência',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "reference2" => [
            'name' => 'Referência 2',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "sku" => [
            'name' => 'Artigo - SKU/Referência',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "qty" => [
            'name' => 'Artigo - Quantidade',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "lote" => [
            'name' => 'Artigo - Lote',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "serial_no" => [
            'name' => 'Artigo - Nº Série',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],

        "customer_code" => [
            'name' => 'Código Cliente',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "customer_id" => [
            'name' => 'Cliente',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],

        "sender_name" => [
            'name' => 'Nome Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_address" => [
            'name' => 'Morada Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_address_2" => [
            'name' => 'Morada Remetente 2',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "sender_zip_code" => [
            'name' => 'Cód. Postal Remetente',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_city" => [
            'name' => 'Localidade Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_country" => [
            'name' => 'País Remetente',
            'type' => 'Texto (4 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_phone" => [
            'name' => 'Telefone Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_attn" => [
            'name' => 'Pessoa Contacto Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_name" => [
            'name' => 'Nome Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_address" => [
            'name' => 'Morada Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_address_2" => [
            'name' => 'Morada Destinatário 2',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "recipient_zip_code" => [
            'name' => 'Cód. Postal Destinatário',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_city" => [
            'name' => 'Localidade Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_country" => [
            'name' => 'País Destinatário',
            'type' => 'Texto (4 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_phone" => [
            'name' => 'Telefone Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_email" => [
            'name' => 'E-mail Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_attn" => [
            'name' => 'Pessoa de Contacto',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "service_code" => [
            'name' => 'Código de Serviço',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "service_id" => [
            'name' => 'Serviço',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "charge_price" => [
            'name' => 'Valor Reembolso',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "total_price_for_recipient" => [
            'name' => 'Valor Portes Destino',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "volumes" => [
            'name' => 'Volumes Envio',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "weight" => [
            'name' => 'Peso Envio',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "item_weight" => [
            'name' => 'Peso Artigo',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "rpack" => [
            'name' => 'Retorno de Encomenda',
            'type' => 'Boleano',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'true',
            'preview_customer' => true,
        ],
        "rguide" => [
            'name' => 'Retorno Guia Assinada',
            'type' => 'Boleano',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'true',
            'preview_customer' => true,
        ],
        "provider_id" => [
            'name' => 'Código Fornecedor',
            'type' => 'Texto (max 5 caractéres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "obs" => [
            'name' => 'Observações',
            'type' => 'Texto (500 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "obs_delivery" => [
            'name' => 'Observações Entrega',
            'type' => 'string',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "obs_internal" => [
            'name' => 'Observações Internas',
            'type' => 'string',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "date" => [
            'name' => 'Data',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "delivery_date" => [
            'name' => 'Data Entrega',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "is_collection" => [
            'name' => 'É pedido Recolha?',
            'type' => 'Sim/Não (1 ou 0)',
            'mapping' => true,
            'preview' => true,
            'checkbox' => 'true',
        ],
    ],


    'customers' => [
        "code" => [
            'name' => 'Código Cliente',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "code_abbrv" => [
            'name' => 'Abreviatura',
            'type' => 'Texto (8 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "category" => [
            'name' => 'Categoria de Cliente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "name" => [
            'name' => 'Nome Expedição',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "address" => [
            'name' => 'Morada Expedição',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "zip_code" => [
            'name' => 'Código Postal Expedição',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "city" => [
            'name' => 'Localidade Expedição',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "country" => [
            'name' => 'Código País Expedição',
            'type' => 'Texto (3 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "vat" => [
            'name' => 'Contribuinte',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "phone" => [
            'name' => 'Telefone',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "mobile" => [
            'name' => 'Telemóvel',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "contact_email" => [
            'name' => 'E-mail',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "billing_name" => [
            'name' => 'Nome Faturação',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "billing_address" => [
            'name' => 'Morada Faturação',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "billing_zip_code" => [
            'name' => 'Código Postal Faturação',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "billing_city" => [
            'name' => 'Localidade Faturação',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "billing_country" => [
            'name' => 'Código País Faturação',
            'type' => 'Texto (3 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "billing_email" => [
            'name' => 'E-mail Faturação',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "payment_method" => [
            'name' => 'Condição Pagamento',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "bank_name" => [
            'name' => 'Banco',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true
        ],
        "bank_iban" => [
            'name' => 'IBAN',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true
        ],
        "bank_swift" => [
            'name' => 'Swift',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true
        ],
        "default_payment_method" => [
            'name' => 'Método Pagamento',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true
        ],
        "refunds_email" => [
            'name' => 'E-mail Reembolsos',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "obs" => [
            'name' => 'Observações',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true
        ],
        "route" => [
            'name' => 'Código Rota',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "responsable" => [
            'name' => 'Pessoa Contacto',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "bank_iban" => [
            'name' => 'Banco - IBAN',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "bank_swift" => [
            'name' => 'Banco - Swift',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "mandate_code" => [
            'name' => 'Nº Mandato',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "bank_mandate_date" => [
            'name' => 'Data Mandato',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "requested_by" => [
            'name' => 'Solicitado Por',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "active" => [
            'name' => 'Ativo?',
            'type' => 'Sim/Não (1 ou 0)',
            'mapping' => true,
            'preview' => true,
            'checkbox' => 'true',
        ],
        /*"tracking_code" => [
            'name' => 'TRK Plataforma',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],*/

    ],

    'operators' => [
        "code" => [
            'name' => 'Código Colaborador',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "code_abbrv" => [
            'name' => 'Abreviatura',
            'type' => 'Texto (8 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "name" => [
            'name' => 'Nome Completo',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "vat" => [
            'name' => 'Contribuinte',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "id_card" => [
            'name' => 'Cartão Cidadão - Número',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "id_card_validity" => [
            'name' => 'Cartão Cidadão - Validade',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "ss_card" => [
            'name' => 'Número Segurança Social',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "birthdate" => [
            'name' => 'Data Nascimento',
            'type' => 'Data (formato AAAA-MM-DD)',
            'mapping' => true,
            'preview' => true
        ],
        "gender" => [
            'name' => 'Género',
            'type' => 'Texto (M ou F)',
            'mapping' => true,
            'preview' => true
        ],
        "nacionality" => [
            'name' => 'Nacionalidade',
            'type' => 'Texto (2 catacteres)',
            'mapping' => true,
            'preview' => true
        ],
        "personal_phone" => [
            'name' => 'Telefone Pessoal',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "personal_mobile" => [
            'name' => 'Telemóvel Pessoal',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "personal_email" => [
            'name' => 'E-mail Pessoal',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "professional_phone" => [
            'name' => 'Telefone Empresa',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "professional_mobile" => [
            'name' => 'Telemóvel Empresa',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "professional_email" => [
            'name' => 'E-mail Empresa',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "professional_role" => [
            'name' => 'Cargo Empresa',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "admission_date" => [
            'name' => 'Data Admissão',
            'type' => 'Data (AAAA-MM-DD)',
            'mapping' => true,
            'preview' => true
        ],
        "bank_name" => [
            'name' => 'Banco - Nome',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "bank_iban" => [
            'name' => 'Banco - IBAN',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "bank_swift" => [
            'name' => 'Banco - Swift',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],

        "address" => [
            'name' => 'Residência - Morada',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "zip_code" => [
            'name' => 'Residência - Código Postal',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "city" => [
            'name' => 'Residência - Localidade',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "country" => [
            'name' => 'Residência - Código País',
            'type' => 'Texto (3 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "fiscal_address" => [
            'name' => 'Dados Fiscais - Morada',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "fiscal_zip_code" => [
            'name' => 'Dados Fiscais - Código Postal',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "fiscal_city" => [
            'name' => 'Dados Fiscais - Localidade',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "fiscal_country" => [
            'name' => 'Dados Fiscais - Código País',
            'type' => 'Texto (3 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
    ],

    'providers' => [
        "is_carrier" => [
            'name' => 'É Transportador?',
            'type' => 'Sim/Não (1 ou 0)',
            'mapping' => true,
            'preview' => true,
            'checkbox' => 'true',
        ],
        "code" => [
            'name' => 'Código Fornecedor',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "name" => [
            'name' => 'Nome a apresentar',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "company" => [
            'name' => 'Designação Social',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "address" => [
            'name' => 'Morada Expedição',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "zip_code" => [
            'name' => 'Código Postal Expedição',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "city" => [
            'name' => 'Localidade Expedição',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "country" => [
            'name' => 'Código País Expedição',
            'type' => 'Texto (3 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "vat" => [
            'name' => 'Contribuinte',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "phone" => [
            'name' => 'Telefone',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "mobile" => [
            'name' => 'Telemóvel',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "email" => [
            'name' => 'E-mail',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "attn" => [
            'name' => 'Pessoa Responsável',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "locale" => [
            'name' => 'Língua',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "obs" => [
            'name' => 'Observações',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true
        ],
        "payment_method" => [
            'name' => 'Condição Pagamento',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "route" => [
            'name' => 'Código Rota',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
    ],

    'routes' => [
        'code' => [
            'name' => 'Código',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
            'required' => true,
        ],

        'name' => [
            'name' => 'Nome',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
            'required' => true,
        ],

        'type' => [
            'name' => 'Tipo',
            'type' => 'Caractere (R = Recolha, E = Entrega, Vazio = Recolha e Entrega)',
            'mapping' => true,
            'preview' => true,
            'required' => false,
        ],

        // 'agencies' => [
        //     'name' => 'Agências',
        //     'type' => 'Código ou Nome separados por vírgula (,)',
        //     'mapping' => true,
        //     'preview' => true,
        //     'required' => false,
        // ]
    ],

    'update_routes' => [
        'code' => [
            'name' => 'Código Rota',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
            'required' => true,
        ],

        'zip_code' => [
            'name' => 'Código Postal',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
            'required' => true,
        ]
    ],

    'fleet_fuel' => [
        "provider_code" => [
            'name' => 'Código Fornecedor',
            'type' => 'Texto (6 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "license_plate" => [
            'name' => 'Matricula',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "operator_id" => [
            'name' => 'Código Motorista',
            'type' => 'Texto (5 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "km" => [
            'name' => 'Km',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true
        ],
        "adblue" => [
            'name' => 'AdBlue?',
            'type' => 'Booleano (0 ou 1)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'true',
        ],
        "liters" => [
            'name' => 'Litros',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true
        ],
        "price_per_liter" => [
            'name' => 'Preço por Litro',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true
        ],
        "total" => [
            'name' => 'Total',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true
        ],
        "date" => [
            'name' => 'Data',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "obs" => [
            'name' => 'Observações',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true
        ],
    ],

    'tolls_logs' => [
        "provider_code" => [
            'name' => 'Código Fornecedor',
            'type' => 'Texto (6 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "license_plate" => [
            'name' => 'Matricula',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "entry_date" => [
            'name' => 'Data Entrada',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "exit_date" => [
            'name' => 'Data Saida',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false
        ],
        "entry_point" => [
            'name' => 'Pórtico Entrada',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true
        ],
        "exit_point" => [
            'name' => 'Pórtico Saida',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true
        ],
        "total" => [
            'name' => 'Total',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true
        ],
        "payment_date" => [
            'name' => 'Data Pagamento',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "toll_provider" => [
            'name' => 'Concessionária',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true
        ],
    ],

    'providers_agencies' => [
        "provider" => [
            'name' => 'Fornecedor',
            'type' => 'Texto (50 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "code" => [
            'name' => 'Código',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "name" => [
            'name' => 'Nome',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "company" => [
            'name' => 'Empresa',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "email" => [
            'name' => 'E-mail',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "phone" => [
            'name' => 'Telefone 1',
            'type' => 'Texto (20 caractéres)',
            'mapping' => true,
            'preview' => true
        ],
        "mobile" => [
            'name' => 'Telemóvel',
            'type' => 'Texto (20 caractéres)',
            'mapping' => true,
            'preview' => true
        ],
        "phone2" => [
            'name' => 'Telefone 2',
            'type' => 'Texto (20 caractéres)',
            'mapping' => true,
            'preview' => true
        ],
        "phone3" => [
            'name' => 'Telefone 3',
            'type' => 'Texto (20 caractéres)',
            'mapping' => true,
            'preview' => true
        ],
        "phone4" => [
            'name' => 'Telefone 4',
            'type' => 'Texto (20 caractéres)',
            'mapping' => true,
            'preview' => true
        ],
        "address" => [
            'name' => 'Morada',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true
        ],
        "zip_code" => [
            'name' => 'Cód. Postal',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true
        ],
        "city" => [
            'name' => 'Localidade',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true
        ],
        "country" => [
            'name' => 'País',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true
        ],
        "district" => [
            'name' => 'Distrito',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true
        ],
        "is_active" => [
            'name' => 'Ativo',
            'type' => 'Booleano',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'true',
        ],
        "responsable" => [
            'name' => 'Responsável',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
    ],

    'logistic_products' => [
        "sku" => [
            'name' => 'SKU Produto',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "barcode" => [
            'name' => 'Código Barras Produto',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "name" => [
            'name' => 'Título Artigo',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "description" => [
            'name' => 'Descrição',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],

        "category_name" => [
            'name' => 'Nome Categoria',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "subcategory_name" => [
            'name' => 'Nome Subcategoria',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "family_name" => [
            'name' => 'Nome Família',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "brand_name" => [
            'name' => 'Nome Marca',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "model_name" => [
            'name' => 'Nome Modelo',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "photo_url" => [
            'name' => 'Link fotografia',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "weight" => [
            'name' => 'Peso',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "width" => [
            'name' => 'Comprimento',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "length" => [
            'name' => 'Largura',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "height" => [
            'name' => 'Altura',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "price" => [
            'name' => 'Preço Unitário',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "obs" => [
            'name' => 'Observações',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "stock_total" => [
            'name' => 'Stock Atual',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "stock_min" => [
            'name' => 'Stock Mínimo',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "stock_max" => [
            'name' => 'Stock Máximo',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "stock_status" => [
            'name' => 'Estado Stock',
            'type' => 'Opção',
            'mapping' => true,
            'preview' => true,
        ],
        "unity" => [
            'name' => 'Unidade',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "unities_by_pack" => [
            'name' => 'Unidades por Pacote',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "packs_by_box" => [
            'name' => 'Unidades por caixa',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "boxes_by_pallete" => [
            'name' => 'Unidades por palete',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
    ],

    'reception_orders' => [
        "customer" => [
            'name' => 'Nome cliente',
            'type' => 'Texto (255 caractéres)',
            'mapping' => false,
            'preview' => true,
        ],
        "sku" => [
            'name' => 'SKU',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "name" => [
            'name' => 'Artigo',
            'type' => 'Texto (255 caractéres)',
            'mapping' => false,
            'preview' => true,
        ],
        "lote" => [
            'name' => 'Lote',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "serial_no" => [
            'name' => 'N.º Série',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "qty" => [
            'name' => 'Qtd Receber',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ]
    ],

    'shipping_orders' => [
        "customer" => [
            'name' => 'Nome cliente',
            'type' => 'Texto (255 caractéres)',
            'mapping' => false,
            'preview' => true,
        ],
        "sku" => [
            'name' => 'SKU',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
            'required' => true,
        ],
        "name" => [
            'name' => 'Artigo',
            'type' => 'Texto (255 caractéres)',
            'mapping' => false,
            'preview' => true,
        ],
        "lote" => [
            'name' => 'Lote',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "serial_no" => [
            'name' => 'N.º Série',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "location" => [
            'name' => 'Localização',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
            'required' => true,
        ],
        "qty" => [
            'name' => 'Qtd.',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
            'required' => true,
        ],
        "qty_satisfied" => [
            'name' => 'Qtd. Satisfeita',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "price" => [
            'name' => 'Preço',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ]
    ],

    'logistic_stocks' => [
        "sku" => [
            'name' => 'SKU',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "stock_available" => [
            'name' => 'Stock Total',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "product_location" => [
            'name' => 'Cód Barras Localização',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ]
    ],

    'equipments' => [
        "sku" => [
            'name' => 'SKU Equipamento',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "name" => [
            'name' => 'Nome Equipamento',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "customer_id" => [
            'name' => 'Código Cliente',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "category_id" => [
            'name' => 'Código Categoria',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "location_id" => [
            'name' => 'Código Localização',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "serial_no" => [
            'name' => 'N.º Série',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "lote" => [
            'name' => 'Lote',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "photo_url" => [
            'name' => 'Link fotografia',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "weight" => [
            'name' => 'Peso',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "width" => [
            'name' => 'Comprimento',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "length" => [
            'name' => 'Largura',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "height" => [
            'name' => 'Altura',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        /* "obs" => [
            'name' => 'Observações',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],*/
        "stock_total" => [
            'name' => 'Stock Atual',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "unity" => [
            'name' => 'Unidade',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ]
    ],

    'equipments-outstock' => [
        "sku" => [
            'name' => 'SKU Equipamento',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "serial_no" => [
            'name' => 'N.º Série',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],

        "height" => [
            'name' => 'Altura',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],

        "ot_code" => [
            'name' => 'Código OT',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
    ],

    'prices_table' => [
        "price_table_id" => [
            'name' => 'Tabela Preços',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "service_id" => [
            'name' => 'Codigo Serviço',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "zone" => [
            'name' => 'Codigo Zona',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "min" => [
            'name' => 'Kg Min',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "max" => [
            'name' => 'Kg Max',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "price" => [
            'name' => 'Preço',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],

        "is_adicional" => [
            'name' => 'KG Adicional',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'true',
            'preview_customer' => false,
        ],
    ],

    'shipments_dimensions' => [
        "customer_id" => [
            'name' => 'Cliente',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "sender_name" => [
            'name' => 'Nome Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_address" => [
            'name' => 'Morada Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_address_2" => [
            'name' => 'Morada Remetente 2',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "sender_zip_code" => [
            'name' => 'Cód. Postal Remetente',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_city" => [
            'name' => 'Localidade Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_country" => [
            'name' => 'País Remetente',
            'type' => 'Texto (4 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_phone" => [
            'name' => 'Telefone Remetente',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sender_vat" => [
            'name' => 'NIF Remetente',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "date" => [
            'name' => 'Data',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "delivery_date" => [
            'name' => 'Data Entrega',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "reference" => [
            'name' => 'Referência',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "reference2" => [
            'name' => 'Referência 2',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "reference3" => [
            'name' => 'Referência 3',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "obs" => [
            'name' => 'Observações',
            'type' => 'Texto (500 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "obs_delivery" => [
            'name' => 'Observações Entrega',
            'type' => 'string',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "obs_internal" => [
            'name' => 'Observações Internas',
            'type' => 'string',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "recipient_vat" => [
            'name' => 'NIF Destinatário',
            'type' => 'Texto (15 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_name" => [
            'name' => 'Nome Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_address" => [
            'name' => 'Morada Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_address_2" => [
            'name' => 'Morada Destinatário 2',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => false,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "recipient_zip_code" => [
            'name' => 'Cód. Postal Destinatário',
            'type' => 'Texto (10 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_city" => [
            'name' => 'Localidade Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_country" => [
            'name' => 'País Destinatário',
            'type' => 'Texto (4 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_phone" => [
            'name' => 'Telefone Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "recipient_email" => [
            'name' => 'E-mail Destinatário',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "charge_price" => [
            'name' => 'Valor Reembolso',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "total_price_for_recipient" => [
            'name' => 'Valor Portes Destino',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "qty" => [
            'name' => 'Artigo - Quantidade',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "article_weight" => [
            'name' => 'Artigo - Peso',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "article_width" => [
            'name' => 'Artigo - Comprimento',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "article_length" => [
            'name' => 'Artigo - Largura',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "article_height" => [
            'name' => 'Artigo - Altura',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "article_name" => [
            'name' => 'Artigo - Descrição',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "sku" => [
            'name' => 'Artigo - SKU/Referência',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "assembly" => [
            'name' => 'Montagem',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
    ],

    'shipments_weights' => [

        "tracking_code" => [
            'name' => 'TRK Plataforma',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "provider_tracking_code" => [
            'name' => 'TRK Fornecedor',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "reference" => [
            'name' => 'Referência',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "volumes" => [
            'name' => 'Volumes',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "weight" => [
            'name' => 'Peso Original',
            'type' => 'Decimal',
            'mapping' => false,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],
        "provider_weight" => [
            'name' => 'Peso Total',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => true,
        ],

        /*  "volumetric_weight" => [
            'name' => 'Peso Volumétrico',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ], */
        "fator_m3" => [
            'name' => 'Fator de Cubicagem',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "cost_shipping_price" => [
            'name' => 'Preço Custo Envio',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
        "cost_expenses_price" => [
            'name' => 'Preço Custo Taxas',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
            'input'    => false,
            'checkbox' => 'false',
            'preview_customer' => false,
        ],
    ],

    'logistic_locations' => [
        "color" => [
            'name' => 'Côr',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
        "rack" => [
            'name' => 'Corredor',
            'type' => 'Texto (5 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "bay" => [
            'name' => 'Secção',
            'type' => 'Texto (3 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "level" => [
            'name' => 'Estante',
            'type' => 'Texto (3 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "position" => [
            'name' => 'Posição',
            'type' => 'Texto (3 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "code" => [
            'name' => 'Código',
            'type' => 'Texto (30 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "barcode" => [
            'name' => 'Código Barras',
            'type' => 'Texto (50 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "type_id" => [
            'name' => 'Tipo Estante',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "warehouse_id" => [
            'name' => 'Armazém',
            'type' => 'Texto (255 caractéres)',
            'mapping' => true,
            'preview' => true,
        ],
        "status" => [
            'name' => 'Estado',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
        "width" => [
            'name' => 'Comprimento',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "height" => [
            'name' => 'Altura',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "length" => [
            'name' => 'Largura',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "max_weight" => [
            'name' => 'Peso Máximo',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "max_pallets" => [
            'name' => 'Máximo Paletes',
            'type' => 'Inteiro',
            'mapping' => true,
            'preview' => true,
        ],
        "obs" => [
            'name' => 'Observações',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
    ],

    'fleet_vehicles' => [
        "license_plate" => [
            'name' => 'Matrícula',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
        "name" => [
            'name' => 'Designação',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
        "brand_id" => [
            'name' => 'Marca',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
        "type" => [
            'name' => 'Tipo',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
        "model_id" => [
            'name' => 'Modelo',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
        "fuel" => [
            'name' => 'Combostível',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
        "average_consumption" => [
            'name' => 'Consumo Médio',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        'brand' => [
            'name' => 'Marca',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
        ],
        'brand_model' => [
            'name' => 'Modelo',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
        ],
        'sell_price' => [
            'name' => 'Preço Venda',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        "color" => [
            'name' => 'Cor',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
        "km_initial" => [
            'name' => 'Km Compra',
            'type' => 'Decimal',
            'mapping' => true,
            'preview' => true,
        ],
    ],


    'billing_products' => [
        'reference' => [
            'name' => 'Referência',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'required' => true,
        ],
        'name' => [
            'name' => 'Designação',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
            'required' => true,
        ],
        'short_name' => [
            'name' => 'Designação Curta',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
        ],
        'tax_rate_code' => [
            'name' => 'Taxa IVA',
            'type' => 'Código',
            'mapping' => true,
            'preview' => true,
            'required' => true,
        ],
        'obs' => [
            'name' => 'Observações',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
        ],
        'provider_code' => [
            'name' => 'Fornecedor',
            'type' => 'Código',
            'mapping' => true,
            'preview' => true,
        ],
        'provider_reference' => [
            'name' => 'Ref. Fornecedor',
            'type' => 'Texto (255 caracteres)',
            'mapping' => true,
            'preview' => true,
        ],
        'stock_total' => [
            'name' => 'Stock Total',
            'type' => 'Inteiro/Decimal',
            'mapping' => true,
            'preview' => true,
        ],
        'unity' => [
            'name' => 'Unidade',
            'type' => 'Texto',
            'mapping' => true,
            'preview' => true,
        ],
        'is_service' => [
            'name' => 'É Serviço?',
            'type' => 'Booleano (0 ou 1)',
            'mapping' => true,
            'preview' => true,
            'checkbox' => 'true',
        ],
        'has_stock' => [
            'name' => 'Contém Stock?',
            'type' => 'Booleano (0 ou 1)',
            'mapping' => true,
            'preview' => true,
            'checkbox' => 'true',
        ],
        'is_fleet_part' => [
            'name' => 'Considerar Peça?',
            'type' => 'Booleano (0 ou 1)',
            'mapping' => true,
            'preview' => true,
            'checkbox' => 'true',
        ],
        'is_active' => [
            'name' => 'Ativo?',
            'type' => 'Booleano (0 ou 1)',
            'mapping' => true,
            'preview' => true,
            'checkbox' => 'true',
        ]
    ]
];
