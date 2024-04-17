<?php

return [

    'shipments' => [

        'errors' => [
            'store' =>   [
                '-001' => 'Unable to update. Envio não encontrado.',
                '-002' => 'Service code does not exist. Check service codes list.',
                '-003' => 'Sender country not found.',
                '-004' => 'Recipient country not found.',
                '-005' => 'Invalid date. Date must be equal to or greater than today.',
                '-006' => 'Shipment saved successfully. Could not send email to recipient.',
                '-007' => 'Shipment saved successfully. Could not send email to recipient due invalid email address.',
                '-008' => 'Unable to update this shipment due they status.',
                '-009' => 'There is no department with the indicated code.',
                '-010' => 'Dimensions: Width, Height or Length are required',
                '-011' => 'Dimensions: Type is not recognised',
                '-012' => 'SKU is required.',
                '-014' => 'Item Not found. Check SKU, Serial No or Lote',
                '-015' => 'Item bloqued.',
                '-016' => 'It is only possible to order 1 unit of the item.',
                '-017' => 'No stock available.',
                '-998' => 'Error while validating fields.',
                '-999' => 'Unknown Server Error.',
            ],
            'update' => [
                '-001' => 'Envio não encontrado. Verifique o código do envio.',
                '-002' => 'Did not indicate any fields to update.',
            ],
            'destroy' =>   [
                '-001' => 'Envio não encontrado.',
                '-002' => 'O estado do envio já não permite a sua anulação.',
            ],
            'lists' =>   [
                '-001' => 'Sem envios.',
                '-002' => 'Tem de indicar pelo menos um parâmetro.',
            ],
            'show' =>   [
                '-001' => 'Envio não encontrado.',
            ],
            'history' =>   [
                '-001' => 'Envio não encontrado.',
            ],
            'labels' =>   [
                '-001' => 'Envio não encontrado.',
                '-999' => 'Erro de servidor desconhecido.',
            ],
            'login' =>   [
                '-001' => 'Dados de acesso errados.',
                '-002' => 'Código de autenticação inválido.',
                '-003' => 'Acesso negado: O utilizador não tem permissão para acesso à aplicação.',
                '-998' => 'Excedido o limite de chamadas diário.'
            ],
            'status' => [
                '-001' => 'Tracking field is required.',
                '-002' => 'Envio não encontrado. Check tracking field.',
                '-003' => 'Invalid status or status not found. Check status_id field.',
                '-004' => 'Motivo de incidência obrigatório.',
                '-005' => 'Invalid incidence or incidence not found. Check incidence_id field.',
                '-006' => 'Shipment already readed by operator',
                '-007' => 'You cant mark this shipment as pickuped',
                '-008' => 'Operator field is required',
                '-009' => 'Não pode transferir este envio.',
            ],
            'tasks' => [
                '-001' => 'Title is required.',
                '-002' => 'Status is required.',
            ],
            'incidences' => [
                '-001' => 'Envio não encontrado. Verifique o campo código envio.',
                '-002' => 'Invalid action code. Check actions list.',
                '-003' => 'Soluction not found. Check solution code field.',
                '-004' => 'Shipment is not in incidence status.',
            ],
            'fleet' => [
                '-001' => 'Sem resultados encontrados.',
                '-002' => 'O campo viatura é obrigatório.',
                '-003' => 'O campo Km é obrigatório.',
                '-004' => 'End km cannot be less or equal than start km.',
                '-005' => 'Field type is required'
            ],
            'locations' => [
                '-001' => 'No results found.'
            ],
        ],

        'attributes' => [
            'service'           => 'Serviço',
            'agency_id'         => 'Agência Pagadora',
            'sender_agency_id'  => 'Agência de Origem',
            'recipient_agency_id' => 'Agência de Destino',
            'service_id'        => 'Serviço',
            'provider_id'       => 'Fornecedor',
            'sender_name'       => 'Nome do Remetente',
            'sender_address'    => 'Morada do Remetente',
            'sender_zip_code'   => 'Código Postal do Remetente',
            'sender_city'       => 'Localidade do Remetente',
            'sender_country'    => 'País do Remetente',
            'sender_phone'      => 'Telefone do Remetente',
            'recipient_name'    => 'Nome do Destinatário',
            'recipient_address' => 'Morada do Destinatário',
            'recipient_zip_code'=> 'Código Postal do Destinatário',
            'recipient_city'    => 'Localidade do Destinatário',
            'recipient_country' => 'País do Destinatário',
            'recipient_phone'   => 'Telefone do Destinatário',
            'volumes'           => 'Volumes',
            'weight'            => 'Peso',
            'charge_price'      => 'Cobrança',
        ],

        'rules' => [
            'date'                  => 'date',
            'service'               => 'required',
            'agency_id'             => 'required',
            'sender_name'           => 'required',
            'sender_address'        => 'required',
            'sender_zip_code'       => 'required',
            'sender_city'           => 'required',
            'sender_country'        => 'required',
            'sender_phone'          => 'required',
            'recipient_name'        => 'required',
            'recipient_address'     => 'required',
            'recipient_zip_code'    => 'required',
            'recipient_city'        => 'required',
            'recipient_country'     => 'required',
            'recipient_phone'       => 'required',
            'volumes'               => 'numeric',
            'weight'                => 'numeric',
            'charge_price'          => 'numeric',
        ]
    ],

    'logistic' => [

        'errors' => [
            'lists' =>   [
                '-001' => 'No products found.',
            ],
        ]
    ]
];
