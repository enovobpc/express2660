<?php

return [
    'title' => 'Fretes',

    'modal-shipment' => [
        'create-shipment' => 'Criar Frete',
        'edit-shipment' => 'Editar Frete',
        'create-pickup' => 'Criar Pedido de Coleta',
        'edit-pickup' => 'Editar Pedido de Coleta',
        'sender-block' => 'Local Coleta',
        'recipient-block' => 'Local Entrega',
        'return-pack' => 'Retorno de Encomenda',
        'write-email' => 'Escreva um e-mail...',
        'save-address' => 'Gravar novo endereço',
        'tips' => [
            'price-expense' => 'Encargos Extra com o Frete',
            'ref'      => 'Associe uma referência personalizada a este pedido, por exemplo, o seu número de fatura ou encomenda.',
            'weight'   => 'Peso total da expedição.',
            'sms'      => 'Esta opção não está disponível no seu contrato.',
            'dimensions' => 'Inserir dimensões e detalhes dos volumes',
            'max-weight' => 'O serviço selecionado apenas permite um máximo de <b class="lbl-total-kg">1</b>kg por expedição.',
            'max-volumes' => 'O serviço selecionado apenas permite <b class="lbl-total-vol">1</b> volumes',

            'hour-exceeded' => 'Já não é possível o motorista efectuar a recolha dos seus volumes hoje. Data prevista para recolha e expedição: :date',
            'toggle-sender' => 'Trocar local de carga com o local de descarga',
            'cod' => 'Ative esta opção se o preço do transporte for pago pelo destinatário no ato da entrega.'
        ],
    ],

    'modal-dimensions' => [
        'new-line' => 'Adicionar nova linha',
        'confirm' => [
            'message' => 'Pretende alterar o número de volumes do frete para',
            'title' => 'Alterar número volumes'
        ]
    ],

    'modal-show' => [
        'title' => 'Detalhes do Frete',
        'tabs' => [
            'info' => 'Detalhes',
            'track' => 'Seguimento Frete',
            'dimensions' => 'Volumes e Dimensões',
            'expenses' => 'Preços e Taxas',
            'attachments' => 'Anexos'
        ],
        'tips' => [
            'prices' => 'Os preços e taxas apresentadas são liquidos. Podem sofrer alterações até à data de emissão do faturamento.'
        ]
    ],

    'modal-attachments' => [
        'title' => 'Adicionar Anexo',
        'edit-title' => 'Editar Anexo',
        'empty' => 'Não Há anexos para este frete.',
        'feedback' => [
            'save' => [
                'success' => 'Anexo gravado com sucesso.',
                'error' => 'Não foi possível carregar o documento.'
            ],
            'destroy' => [
                'message' => 'Confirma a remoção do anexo selecionado?',
                'success' => 'Anexo removido com sucesso.',
                'error'   => 'Ocorreu um erro ao tentar remover o anexo.'
            ]
        ]
    ],


    'modal-grouped-guide' => [
        'title'   => 'Guia de transporte agrupada',
        'message' => 'Pretende gerar uma guia de transporte agrupada para os envios selecionados?',
        'pack-type' => 'Tipo Embalagem',
        'goods-description' => 'Descrição Conteúdos',
        'license-plate' => 'Matrícula Viatura'
    ],

    'selected' => [
        'print-list' => 'Imprimir Listagem',
        'print-label-guides' => 'Imprimir Labels e Guias',
        'print-guides' => 'Guias Transporte',
        'print-labels' => 'Labels',
        'print-grouped-guide' => 'Criar Guia Agrupada',
        'print-list' => 'Imprimir Listagem',
        'print-summary' => 'Listagem de Resumo',
        'print-manifest' => 'Manifesto de Carga',
        'pickup-manifest' => 'Manifesto de Recolha'
    ],

    'filters' => [
        'charge' => [
            '' => 'Todos',
            '1' => 'Com cobrança',
            '0' => 'Sem cobrança'
        ],
        'label' => [
            '' => 'Todos',
            '1' => 'Impresso',
            '0' => 'Por Imprimir'
        ]
    ],

    'print' => [
        'guide' => 'Guia Transporte',
        'label' => 'Label',
        'labels' => 'Labels',
        'cmr' => 'CMR',
        'grouped-guide' => 'Guia Agrupada',
        'reimbursement-guide' => 'Guia Reembolso',
        'pickup-manifest' => 'Manifesto de Recolha'
    ],

    'budget' => [
        'title' => 'Calcular custo de envio',
        'fuel-tax' => 'Taxa Combústivel',
        'plus-vat-info' => 'Acresce IVA à taxa legal em vigor.',
        'empty-vat-info' => 'Isento de IVA.',
        'exceptions-info' => 'Podem acrescer outras taxas.',
        'addicional-services' => [
            'title'  => 'Serviços adicionais',
            'pickup' => 'Pedido de Recolha',
            'charge' => 'COD',
            'rguide' => 'Retorno Guia'
        ],
        'price-overview' => [
            'title' => 'Preço Previsto',
            'base' => 'Valor do frete',
            'charge' => 'Taxa de Reembolso',
            'fuel' => 'Taxa Combustível',
            'pickup' => 'Taxa de Recolha',
            'rguide' => 'Taxa Retorno Guia',
            'outstandard' => 'Volume Fora Norma'
        ],

        'tips' => [
            'kms'        => 'Distância (ida e volta) entre o nosso armazém, remetente edestinatário.',
            'volumes'    => 'Número de objetos ou pacotes a enviar',
            'weight-vol' => 'O peso volumétrico deriva das dimensões dos volumes. Para o cálculo do preço é sempre considerado o peso maior entre o peso real dos volumes e o peso volumétrico.',
            'weight'     => 'Peso total dos objetos ou pacotes a enviar',
            'charge'     => 'Se o frete possui COD, indique o valor a ser cobrado.',
        ]
    ],

    'cargo_manifest' => [
        'signature' => 'Assinatura',
        'date-time-cargo' => 'Data e Hora de Carga',
    ],

    'feedback' => [
        'update' => [
            'success' => 'Pedido gravado com sucesso.',
            'error-email' => 'Frete gravado com sucesso. Não foi possível enviar o e-mail para: :emails'
        ],
        'destroy' => [
            'question'  => 'Confirma a anulação deste frete?',
            'success'   => 'Frete anulado com sucesso.',
            'error'     => 'Ocorreu um erro ao tentar anular o frete.',
        ]
    ]
];
