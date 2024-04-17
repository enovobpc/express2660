<?php

return [

    'shipments' => [

        'errors' => [
            'store' =>   [
                '-001' => 'Não foi possível gravar. Envio não encontrado.',
                '-002' => 'Não existe nenhum serviço com o código indicado.',
                '-003' => 'O país de origem não é válido.',
                '-004' => 'o país de destino não é válido.',
                '-005' => 'Data inválida. A data deve ser igual ou superior a hoje.',
                '-006' => 'Envio gravado com sucesso. Não foi possível enviar notificação por e-mail.',
                '-007' => 'Envio gravado com sucesso. Não foi possível enviar notificação por e-mail (E-mail inválido).',
                '-008' => 'O estado do envio já não permite a sua edição.',
                '-009' => 'Não existe nenhum departamento com o código indicado.',
                '-010' => 'Dimensões: Comprimento, Largura e Altura são obrigatórios.',
                '-011' => 'Dimensões: O tipo de pacote não é válido.',
                '-012' => 'Referência do artigo é obrigatória',
                '-014' => 'Artigo não encontrado. Verifique a Referência, Número Série ou Lote',
                '-015' => 'Artigo bloqueado.',
                '-016' => 'Só é possível adicionar 1 unidade deste artigo.',
                '-017' => 'Sem stock disponível.',
                '-018' => 'O valor da cobrança é maior do que o permitido.',
                '-019' => 'Criação de envios bloqueada, devido a ter atingido os limites definidos.',
                '-998' => 'Erro ao validar dados.',
                '-999' => 'Erro desconhecido de sistema.',
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
                '-001' => 'É obrigatório indicar o código do envio.',
                '-002' => 'Envio não encontrado. Verifique o código do envio.',
                '-003' => 'Invalid status or status not found. Check status_id field.',
                '-004' => 'Motivo de incidência obrigatório.',
                '-005' => 'Invalid incidence or incidence not found. Check incidence_id field.',
                '-006' => 'O envio já foi lido por outro operador.',
                '-007' => 'Não pode marcar este envio como recolhido.',
                '-008' => 'Operator field is required',
                '-009' => 'Não pode transferir este envio.',
            ],
            'tasks' => [
                '-001' => 'O campo título é obrigatório.',
                '-002' => 'O estado é obrigatório.',
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
                '-001' => 'Sem resultados a apresentar.'
            ],
            'schedule' => [
                '-001' => 'No rescheduling possible'
            ],
            'smsCode' => [
                '-001' => 'The pin is not correct'
            ]
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
            'charge_price'          => 'numeric|nullable',
        ]
    ],

    'logistic' => [

        'errors' => [
            'lists' =>   [
                '-001' => 'No products found.',
            ],
        ]
    ],

    'equipments' => [

        'errors' => [
            'login' =>   [
                '-001' => 'Dados de acesso errados.',
                '-002' => 'Código de autenticação inválido.',
                '-003' => 'Acesso negado: O utilizador não tem permissão para acesso à aplicação.',
                '-998' => 'Excedido o limite de chamadas diário.'
            ],
            'picking' =>   [
                '-001' => 'Não existem equipamentos para dar baixa',
                '-002' => 'A localização não existe em sistema',
                '-003' => 'O equipamento não localizado',
                '-004' => 'A quantidade do equipamento é superior ao stock',
            ],
        ]
    ]
];
