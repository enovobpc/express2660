<?php

return [

    'vat-rates-classes' => [
        'iva'  => 'IVA',
        //'selo' => 'Selo'
    ],

    'vat-rates-subclasses' => [
        'nor' => 'Normal',
        'int' => 'Intermédio',
        'red' => 'Reduzido',
        'ise' => 'Isento',
        'ns'  => 'Não Sujeito',
        'out' => 'Outros'
    ],

    'vat-rates-zones' => [
        'pt'    => 'Portugal Continental',
        'pt-md' => 'Portugal Madeira',
        'pt-ac' => 'Portugal Açores',
        'es'    => 'España',
        'fr'    => 'France',
        'ao'    => 'Angola'
    ],

    'status' => [
        'unpaid' => 'Por Pagar',
        'paid'   => 'Pago'
    ],

    'exemption-reasons' => [
        'pt' => [
            'M01' => 'Artigo 16.º n.º 6 do CIVA (ou similar)',
            'M02' => 'Artigo 6.º do Decreto-Lei n.º 198/90, de 19 de junho',
            'M04' => 'Isento Artigo 13.º do CIVA (ou similar)',
            'M05' => 'Isento Artigo 14.º do CIVA (ou similar)',
            'M06' => 'Isento Artigo 15.º do CIVA (ou similar)',
            'M07' => 'Isento Artigo 9.º do CIVA (ou similar)',
            'M09' => 'IVA - não confere direito a dedução',
            'M10' => 'IVA - Regime de isenção',
            'M11' => 'Regime particular do tabaco',
            'M12' => 'Regime da margem de lucro – Agências de viagens',
            'M13' => 'Regime da margem de lucro – Bens em segunda mão',
            'M14' => 'Regime da margem de lucro – Objetos de arte',
            'M15' => 'Regime da margem de lucro –Objetos de coleção e antiguidades',
            'M16' => 'Isento Artigo 14.º do RITI (ou similar)',
            'M19' => 'Outras Isenções',
            'M20' => 'IVA - Regime de isenção',
            'M21' => 'IVA - Não confere direito à dedução (ou expressão similar)',
            'M25' => 'Mercadorias à consignação',
            'M30' => 'IVA - Autoliquidação (Artigo 2.º n.º 1 alínea i) do CIVA)',
            'M31' => 'IVA - Autoliquidação (Artigo 2.º n.º 1 alínea j) do CIVA)',
            'M32' => 'IVA - Autoliquidação (Artigo 2.º n.º 1 alínea l) do CIVA)',
            'M33' => 'IVA - Autoliquidação (Artigo 2.º n.º 1 alínea M) do CIVA)',
            'M40' => 'IVA – Autoliquidação (Artigo 6.º n.º 6 alínea a) do CIVA, a contrário)',
            'M41' => 'IVA – Autoliquidação (Artigo 8.º n.º 3 do RITI)',
            'M42' => 'IVA – Autoliquidação (Decreto-Lei n.º 21/2007, de 29 de janeiro)',
            'M43' => 'IVA – Autoliquidação (Decreto-Lei n.º 362/99, de 16 de setembro)',
            'M99' => 'Não sujeito; não tributado (ou similar)',
        ]
    ],


    'billing-methods' => [
        '30d' => 'Mensal',
        '15d' => 'Quinzenal',
    ],

    'periods' => [
        '30d' => 'Mensal',
        '15d' => [
            '1q' => '1ª Quinzena',
            '2q' => '2ª Quinzena',
        ],
        '7d' => [
            '1w' => '1ª Semana',
            '2w' => '2ª Semana',
            '3w' => '3ª Semana',
            '4w' => '4ª Semana',
        ]
    ],

    'payment-methods' => [
        'transfer'   => 'Transferência',
        'money'      => 'Numerário',
        'check'      => 'Cheque',
        'mb'         => 'Multibanco',
        'dd'         => 'Débito Direto',
        'settlement' => 'Acerto de Contas',
        'confirming' => 'Confirming',
        'mbw'        => 'MB Way',
    ],

    'payment-conditions' => [
        'sft'   => 'Sem fatura',
        'prt'   => 'A Pronto',
        '3d'    => 'A 3 dias',
        '5d'    => 'A 5 dias',
        '7d'    => 'A 7 dias',
        '10d'   => 'A 10 dias',
        '15d'   => 'A 15 dias',
        '20d'   => 'A 20 dias',
        '30d'   => 'A 30 dias',
        '45d'   => 'A 45 dias',
        '50d'   => 'A 50 dias',
        '55d'   => 'A 55 dias',
        '60d'   => 'A 60 dias',
        '75d'   => 'A 75 dias',
        '80d'   => 'A 80 dias',
        '85d'   => 'A 85 dias',
        '90d'   => 'A 90 dias',
        '120d'  => 'A 120 dias',
        'dbt'   => 'Débito Bancário',
        'wallet' => 'Pré-pagamento'
    ],

    'types-list' => [
        'nodoc'              => 'Sem Documento',
        'invoice'            => 'Fatura',
        'invoice-receipt'    => 'Fatura-Recibo',
        'simplified-invoice' => 'Fatura Simplificada',
        'credit-note'        => 'Nota de Crédito',
        'debit-note'         => 'Nota de Débito',
        'proforma-invoice'   => 'Fatura Proforma',
        'internal-doc'       => 'Documento Interno',
        //'receipt'            => 'Recibo',
        //'transport-guide'    => 'Guia de transporte'
    ],

    'types-list-selectbox' => [
        'nodoc'              => 'Sem Documento',
        'Documentos Fiscais' => [
            'invoice'            => 'Fatura',
            'invoice-receipt'    => 'Fatura-Recibo',
            'simplified-invoice' => 'Fatura Simplificada',
            'credit-note'        => 'Nota de Crédito',
            'debit-note'         => 'Nota de Débito',
        ],
        'Outros Documentos' => [
            'proforma-invoice'   => 'Fatura Proforma',
            'internal-doc'       => 'Documento Interno',
        ]
    ],

    'types-list-purchase' => [
        'provider-expense'              => 'Despesa',
        'provider-invoice'              => 'Fatura',
        'provider-simplified-invoice'   => 'Fatura Simplificada',
        'provider-invoice-receipt'      => 'Fatura-Recibo',
        'provider-credit-note'          => 'Nota Crédito',
        'payment-note'                  => 'Nota de Pagamento',
        'provider-order'                => 'Encomenda',
    ],

    'types-list-purchase-abrv' => [
        'provider-expense'              => 'Despesa',
        'provider-invoice'              => 'Fatura',
        'provider-invoice-receipt'      => 'Fatura-Recibo',
        'provider-simplified-invoice'   => 'Fat. Simplif.',
        'provider-credit-note'          => 'Nota Crédito',
        'provider-devolution-note'      => 'Nota Devolução',
        'payment-note'                  => 'Nota Pagamento',
        'provider-order'                => 'Encomenda',
    ],

    'types' => [
        'invoice'            => 'Fatura',
        'simplified-invoice' => 'Fatura Simplificada',
        'credit-notes'       => 'Nota de Crédito',
        'debit-notes'        => 'Nota de Débito',
        'invoice-receipt'    => 'Fatura-Recibo',
        'receipt'            => 'Recibo',
        'credit-note'        => 'Nota de Crédito',
        'debit-note'         => 'Nota de Débito',
        'regularization'     => 'Regularização',
        'proforma-invoice'   => 'Fatura Proforma',
        'internal-doc'       => 'Documento Interno',
        'sind'               => 'Débito Inicial',
        'sinc'               => 'Crédito Inicial',
        
        'provider-expense'              => 'Despesa',
        'provider-invoice'              => 'Fatura Compra',  //Factura de compra
        'provider-invoice-receipt'      => 'Fatura-Recibo',
        'provider-simplified-invoice'   => 'Fatura Simplificada', //Fatura Simplificada de fornecedor
        'provider-credit-note'          => 'Nota Crédito', //Crédito financeiro de fornecedor
        'provider-order'                => 'Encomenda',
        'payment-note'                  => 'Pagamento'
    ],

    'types-plural' => [
        'invoice'            => 'Faturas',
        'simplified-invoice' => 'Faturas Simplificada',
        'credit-notes'       => 'Notas de Crédito',
        'debit-notes'        => 'Notas de Débito',
        'invoice-receipt'    => 'Faturas-Recibo',
        'receipt'            => 'Recibos',
        'credit-note'        => 'Notas de Crédito',
        'debit-note'         => 'Notas de Débito',
        'regularization'     => 'Regularizações',
        'proforma-invoice'   => 'Faturas Proforma',
        'internal-doc'       => 'Documentos Internos',
        'provider-expense'              => 'Despesas',
        'provider-invoice'              => 'Faturas Compra',  //Factura de compra
        'provider-invoice-receipt'      => 'Faturas-Recibo',
        'provider-simplified-invoice'   => 'Faturas Simplificada', //Fatura Simplificada de fornecedor
        'provider-credit-note'          => 'Notas Crédito', //Crédito financeiro de fornecedor
        'provider-order'                => 'Encomendas',
        'payment-note'                  => 'Pagamentos'
    ],

    'types_code' => [
        'nodoc'              => 'S/DOC',
        'invoice'            => 'FT',
        'simplified-invoice' => 'FS',
        'credit-note'        => 'NC',
        'receipt'            => 'RC',
        'debit-note'         => 'ND',
        'invoice-receipt'    => 'FR',
        'sale-by-money'      => 'VD',
        'transport-guide'    => 'GT',
        'proforma-invoice'   => 'PF',
        'internal-doc'       => 'DI',
        'regularization'     => 'RG',
        'provider-expense'              => 'DP',
        'provider-invoice'              => 'FT',
        'provider-invoice-receipt'      => 'FR',
        'provider-sale-by-money'        => 'FD',
        'provider-simplified-invoice'   => 'FS',
        'provider-credit-note'          => 'NC',
        'provider-order'                => 'EF',
        'payment-note'                  => 'NP'
    ],

    'types_color_text' => [
        'invoice'            => 'Fatura',
        'simplified-invoice' => 'Simplif.',
        'invoice-receipt'    => 'Fatura-Recibo',
        'receipt'            => 'Recibo',
        'credit-note'        => 'Nota Crédito',
        'debit-note'         => 'Nota Débito',
        'regularization'     => 'Regularização',
        'proforma-invoice'   => 'Proforma',
        'internal-doc'       => 'Doc. Interno',
        'nodoc'              => 'Sem Doc.',
        'transport-guide'    => 'Guia Transporte',
    ],

    'types_color' => [
        'invoice'            => '#1ab6e5',
        'simplified-invoice' => '#05d5ff',
        'invoice-receipt'    => '#0058aa',
        'receipt'            => '#afdb2b',
        'credit-note'        => '#f53939',
        'debit-note'         => '#ff0000',
        'regularization'     => '#777777',
        'proforma-invoice'   => '#782e91',
        'internal-doc'       => '#ffe760',
        'nodoc'              => '#777777',
        'transport-guide'    => '#9ca8b4',
    ],

    'targets' => [
        'CustomerBilling' => 'Fatura Mensal',
        'Invoice'         => 'Fatura Individual',
    ],

    'targets-colors' => [
        'CustomerBilling' => '#1c3665',
        'Invoice'   => '#27aae1',
    ],

    'gateway-payment-methods' => [
        'mb'     => 'Multibanco',
        'mbway'  => 'MB Way',
        'visa'   => 'Visa/Mastercard',
        'wallet' => 'Conta Corrente',
        //'paypal' => 'Paypal'
    ],

    'gateway-payment-status' => [
        'pending' => 'Pendente',
        'success' => 'Sucesso',
        'error'   => 'Erro',
        'rejected' => 'Rejeitado'
    ],

    /* 'banks' => [
        'cgd'            => 'Caixa Geral Depositos',
        'santander'      => 'Santander',
        'montepio'       => 'Montepio',
        'novo_banco'     => 'Novo Banco',
        'ActivoBank'     => 'Activo Bank',
        'abanca'         => 'Abanca',
        'cca'            => 'Caixa Crédito Agrícola',
        'bpi'            => 'BPI',
        'bni'            => 'BNI',
        'bcp'            => 'BCP',
        'ctt'            => 'Banco CTT',
        'banco_bic'      => 'Banco BIC',
        'big'            => 'Banco BIG',
        'best'           => 'Banco Best',
        'bankinter'      => 'Bankinter',
        'caixa_bank'     => 'Caixa Bank',
        'banco_invest'   => 'Banco Invest',
        'bpg'            => 'Banco Portugues Gestão',
        'credibom'       => 'Credibom',
        'banco_primus'   => 'Banco Primus',
        'itau'           => 'Itaú BBA',
        'efisa'          => 'Banco Efisa',
        'finantia'       => 'Banco Finantia',
        'efisa'          => 'Banco Atlântico',
    ],

    'banks-code' => [
        'cgd'           => '0035',
        'bcp'           => '0033',
        'ActivoBank'    => '0023',
        'santander'     => '0018',
        'bbva'          => '0019',
        'bpi'           => '0010',
        'ctt'           => '0193',
        'finantia'      => '0048',
        'banco_invest'  => '0014',
        'banco_lj'      => '0235',
        'bpp'           => '0046',
        'banif'         => '0038',
        'bankinter'     => '0269',
        'barclaysbank'  => '0032',
        'best'          => '0065',
        'bni'           => '0191',
        'cbi'           => '0025',
        'cca'           => '0045,0097,0098,5180,5200,5340',
        'montepio'      => '0036',
        'novo_banco'    => '0007',
        'bp'            => '0001',
        'bbe'           => '0008',
        'bbr'           => '0022',
        'bpi'           => '0027',
        'besi'          => '0047',
        'cemah'         => '0059',
        'big'           => '0061',
        'banif'         => '0063',
        'bpg'           => '0064',
        'santander'     => '0073',
        'banco_bic'     => '0079',
        'efisa'         => '0086',
        'bcei'          => '0099',
        'novo_banco'    => '0160',
        'abanca'        => '0170',
        'bpes'          => '0186',
        'efisa'         => '0189',
        'bgc'           => '0244',
        'unicre'        => '0698',
        'agtdp'         => '0781',

    ],

    'banks-swift' => [
        'cgd'           => 'CGDIPTPL',
        'bcp'           => 'BCOMPTPL',
        'ActivoBank'    => 'ACTVPTPL',
        'santander'     => 'TOTAPTPL',
        'bbva'          => 'BBVAPTPL',
        'bpi'           => 'BBPIPTPL',
        'bankinter'     => 'BKBKPTPL',
        'ctt'           => 'CTTVPTPL',
        'finantia'      => 'BFIAPTPL',
        'banco_invest'  => 'IVVSPTPL',
        'banco_lj'      => 'BLJCPTPT',
        'bpp'           => 'CRBNPTPL',
        'banif'         => 'BNIFPTPL',
        'barclaysbank'  => 'BARCPTPL',
        'best'          => 'BESZPTPL',
        'bni'           => 'BNICPTPL',
        'cbi'           => 'CXBIPTPL',
        'cca'           => 'CCCMPTPL',
        'montepio'      => 'MPIOPTPL',
        'novo_banco'    => 'BESCPTPL',
        'bbe'           => 'BAIPPTPL',
        'bbr'           => 'BRASPTPL',
        'besi'          => 'ESSIPTPL',
        'big'           => 'BDIGPTPL',
        'banif'         => 'BNFIPTPL',
        'abanca'        => 'CAGLPTPL',
        'bpg'           => 'BPGPPTPL',
        'banco_bic'     => 'BPNPPTPL',
        'efisa'         => 'EFISPTPL',
        'bpes'          => 'CFESPTPL',
        'efisa'         => 'BAPAPTPL',
        'agtdp'         => 'IGCPPTPL'
    ],
 */
    'sepa-types' => [
        'trf' => 'Transferência',
        'dd'  => 'Débito Direto'
    ],

    'sepa-status' => [
        'editing'   => 'Em edição',
        'pending'   => 'Submetido',
        'concluded' => 'Concluido'
    ],

    'sepa-status-color' => [
        'editing'   => '#00b3f7',
        'pending'   => '#f7c200',
        'concluded' => '#00a314'
    ],

    'sepa-sequence-types' => [
        'RCUR' => 'Recorrente',
        'OOFF' => 'Pag. Pontual',
        'FRST' => 'Primeiro Pagamento',
        'FNAL' => 'Ultimo Pagamento'
    ],

    'sepa-category-types' => [
        'OTHR' => 'Transferência',
        'SUPP' => 'Fornecedores',
        'SALA' => 'Ordenados',
        'SSBE' => 'Prestações Seg. Social',
        'PENS' => 'Pensões Nacional'
    ],

    'sepa-transfers-types' => [
        'REFU' => 'Reembolsos',
        'SUPP' => 'Fornecedores',
        'OTHR' => 'Transferência',
        'SALA' => 'Ordenados',
        'RENT' => 'Rendas',
        'SSBE' => 'Prestações Seg. Social',
        'PENS' => 'Pensões Nacional',
        'GOVT' => 'Reembolsos ao Estado'
    ],

    'items-unities' => [
        'un' => 'Unidades',
        'mt' => 'Metros',
        'm3' => 'Metros Cúbicos',
        'kg' => 'Quilogramas',
        'lt' => 'Litros',
        'cx' => 'Caixas'
    ]
];
