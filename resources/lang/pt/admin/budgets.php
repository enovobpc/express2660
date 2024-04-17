<?php

return [

    'types' => [
        'courier' => 'Carga Geral',
        'animals' => 'Transporte Animais',
    ],

    'status' => [
        'pending'  => 'Registado',
        'accepted' => 'Aceite',
        'rejected' => 'Rejeitado',
        'outdated' => 'Rejeitado (Fora Validade)',
        'wainting-customer' => 'Respondido Cliente',
        'wainting' => 'Precisa Resposta',
        'no-solution' => 'Sem Solução',
        'concluded' => 'Concluído'
    ],

    'status-labels' => [
        'pending'  => 'default',
        'accepted' => 'success',
        'rejected' => 'danger',
        'outdated' => 'danger',
        'wainting' => 'warning',
        'wainting-customer' => 'info',
        'no-solution' => 'danger',
        'concluded' => 'success',
    ],

    'provider-status' => [
        'requested' => 'Solicitado',
        'answered'  => 'Novas Respostas',
    ],

    'provider-status-labels' => [
        'requested' => 'default',
        'answered'  => 'info',
    ],

    'courier' => [
        'title' => '',
        'date' => 'Data',
        'validity' => 'Validade',
        'budget_no' => 'Orçamento N.º',
        'email' => 'E-mail',
        'phone' => 'Telefone',
        'value' => 'VALOR (EUR)',
        'dear'  => 'Caro(a)',
        'section_animals'   => 'DADOS DA MERCADORIA',
        'section_transport' => 'DADOS TRANSPORTE',
        'section_services'  => 'SERVIÇOS',
        'section_transport_info' => 'INFORMAÇÕES DE TRANSPORTE',
        'section_payment_info' => 'INFORMAÇÕES DE PAGAMENTO',
        'section_geral_conditions' => 'CONDIÇÕES GERAIS',
        'description' => 'Descrição',
        'service' => 'Serviço',
        'weight' => 'Peso Bruto',
        'volumetric_weight' => 'Peso Taxável',
        'volumes' => 'N.º Volumes',
        'dimensions' => 'Cubicagem (CxLxA)',
        'total_weight' => 'Peso total',
        'vat' => 'IVA',
        'pickup_address' => 'Local Recolha',
        'delivery_address' => 'Local Entrega',
        'source_airport' => 'Origem',
        'destination_airport' => 'Destino',
        'notes' => 'Notas',
        'total' => 'TOTAL (EUR)',
        'total_net' => 'Total Líquido',
        'item' => 'Artigo',
        'qty' => 'Qtd',
        'price' => 'Preço Un.',
        'subtotal' => 'Subtotal'
    ],

    'animals' => [
        'title' => 'COTAÇÃO DE TRANSPORTE DE ANIMAIS VIVOS',
        'date' => 'Data',
        'validity' => 'Validade',
        'budget_no' => 'Orçamento N.º',
        'email' => 'E-mail',
        'phone' => 'Telefone',
        'value' => 'VALOR (EUR)',
        'dear'  => 'Caro(a)',
        'section_animals'   => 'DADOS DOS ANIMAIS',
        'section_transport' => 'DADOS TRANSPORTE',
        'section_services'  => 'SERVIÇOS E PRODUTOS',
        'section_transport_info' => 'INFORMAÇÕES DE TRANSPORTE',
        'section_payment_info' => 'INFORMAÇÕES DE PAGAMENTO',
        'section_geral_conditions' => 'CONDIÇÕES GERAIS',
        'name' => 'Nome/Raça',
        'specie' => 'Espécie',
        'age' => 'Idade',
        'weight' => 'Peso',
        'weight_box' => 'Peso Caixa',
        'dog' => 'Cão',
        'cat' => 'Gato',
        'other' => 'Outro',
        'total_weight' => 'Peso total',
        'vat' => 'IVA',
        'pickup_address' => 'Local Recolha',
        'delivery_address' => 'Local Entrega',
        'source_airport' => 'Aeroporto Origem',
        'destination_airport' => 'Aeroporto Destino',
        'notes' => 'Notas',
        'total' => 'TOTAL (EUR)',
        'total_net' => 'Total Líquido',
        'item' => 'Artigo',
        'qty' => 'Qtd',
        'price' => 'Preço Un.',
        'subtotal' => 'Subtotal'
    ]

];
