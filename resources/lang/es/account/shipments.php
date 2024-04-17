<?php

return [
    'title' => 'Envíos',

    'modal-shipment' => [
        'create-shipment' => 'Crear Expedición',
        'edit-shipment' => 'Editar Expedición',
        'create-pickup' => 'Crear Pedido de Recogida',
        'edit-pickup' => 'Editar Pedido de Recogida',
        'sender-block' => 'Local Recogida',
        'recipient-block' => 'Local Descarga',
        'return-pack' => 'Devolución de Pedido',
        'write-email' => 'Escriba um email...',
        'save-address' => 'Guardar nueva dirección',
        'tips' => [
            'price-expense' => 'Encargos Extra con el Envío',
            'ref'      => 'Asocie una referencia personalizada a este pedido, por ejemplo, su número de factura o pedido.',
            'weight'   => 'Peso total de expedición.',
            'sms'      => 'Esta opción no está disponible en su contrato.',
            'dimensions' => 'Introducir dimensiones y detalles de los volúmenes.',
            'max-weight' => 'El servicio seleccionado solamente permite un máximo de <b class="lbl-total-kg">1</b>kg por expedición.',
            'max-volumes' => 'El servicio seleccionado solamente permite <b class="lbl-total-vol">1</b> volúmenes.',

            'hour-exceeded' => 'Ya no es posible que el conductor efectue la recogida de sus volúmenes hoy. Fecha prevista para para la recogida y expedición: :date',
            'toggle-sender' => 'Trocar local de carga com o local de descarga',
            'cod' => 'Active esta opción si el precio del transporte será pagado por el destinatário en el acto de entrega.'
        ],
    ],

    'modal-dimensions' => [
        'new-line' => 'Añadir nueva línea',
        'confirm' => [
            'message' => 'Pretende cambiar el número de volúmenes de la expedición para',
            'title' => 'Cambiar número volúmenes'
        ]
    ],

    'modal-show' => [
        'title' => 'Detalles de Expedición',
        'tabs' => [
            'info' => 'Detalles',
            'track' => 'Seguimiento Envío',
            'dimensions' => 'Volúmenes y Dimensiones',
            'expenses' => 'Precios y Tarifas',
            'attachments' => 'Anexos'
        ],
        'tips' => [
            'prices' => 'Los precios y tarifas presentadas son netos. Pueden sufrir alteraciones hasta la fecha de emisión de la factura.'
        ]
    ],

    'modal-attachments' => [
        'title' => 'Agregar Anexo',
        'edit-title' => 'Editar Anexo',
        'empty' => 'No hay anexos para este envío.',
        'feedback' => [
            'save' => [
                'success' => 'Anexo guardado con éxito.',
                'error' => 'No fue posible cargar el documento.'
            ],
            'destroy' => [
                'message' => '¿Confirma la eliminación del anexo seleccionado?',
                'success' => 'Anexo eliminado con éxito.',
                'error'   => 'Ha ocurrido un error al intentar eliminar el anexo.'
            ]
        ]
    ],


    'modal-grouped-guide' => [
        'title'   => 'Guía de transporte agrupada',
        'message' => '¿Pretende generar una guía de transporte agrupada para los envíos seleccionados?',
        'pack-type' => 'Tipo Paquete',
        'goods-description' => 'Descripción Mercancía',
        'license-plate' => 'Matrícula Vehículo' 
    ],

    'selected' => [
        'print-list' => 'Imprimir Lista',
        'print-label-guides' => 'Imprimir Etiquetas y Guías',
        'print-guides' => 'Guías de Transporte',
        'print-labels' => 'Etiquetas',
        'print-grouped-guide' => 'Crear Guía Agrupada',
        'print-list' => 'Imprimir Lista',
        'print-summary' => 'Lista de Resumen',
        'print-manifest' => 'Manifiesto de Carga',
        'pickup-manifest' => 'Manifiesto de Recogida'
    ],

    'filters' => [
        'charge' => [
            '' => 'Todos',
            '1' => 'Con cobro',
            '0' => 'Sin cobro'
        ],
        'label' => [
            '' => 'Todos',
            '1' => 'Impreso',
            '0' => 'Por Imprimir'
        ]
    ],

    'print' => [
        'guide' => 'Guía Transporte',
        'label' => 'Etiqueta',
        'labels' => 'Etiquetas',
        'cmr' => 'CMR',
        'grouped-guide' => 'Guía Agrupada',
        'reimbursement-guide' => 'Guía Reembolso',
        'pickup-manifest' => 'Manifiesto de Recogida'
    ],

    'budget' => [
        'title' => 'Calcular costo de envío',
        'fuel-tax' => 'Tasa Combustible',
        'plus-vat-info' => 'Añade IVA a la tasa legal en vigor.',
        'empty-vat-info' => 'Exento de IVA.',
        'exceptions-info' => 'Pueden ser añadidas otras tasas.',
        'addicional-services' => [
            'title'  => 'Servicios adicionales',
            'pickup' => 'Pedido de Regida',
            'charge' => 'Envío a Cobro',
            'rguide' => 'Retorno Guía'
        ],
        'price-overview' => [
            'title' => 'Precio Previsto',
            'base' => 'Valor del envío',
            'charge' => 'Tasa de Reembolso',
            'fuel' => 'Taaa Combustible',
            'pickup' => 'Tasa de Recogida',
            'rguide' => 'Tasa Devolución Guía',
            'outstandard' => 'Volumen Fora Norma'
        ],

        'tips' => [
            'kms'        => 'Distancia (ida y vuelta) entre nuestro almazén, remetente y destinatário.',
            'volumes'    => 'Número de objetos o paquetes a enviar',
            'weight-vol' => 'El peso volumétrico deriva de las dimensiones de los volúmenes. Para el cálculo del precio es siempre considerado el peso más grande entre el peso real de los volúmenes y el peso volumétrico.',
            'weight'     => 'Peso total de los objetos o paquetes a enviar',
            'charge'     => 'Si el envío va al cobro, indique el valor a ser cobrado.',
        ]
    ],

    'cargo_manifest' => [
        'signature' => 'Firma',
        'date-time-cargo' => 'Día y Hora de Carga',
    ],

    'feedback' => [
        'update' => [
            'success' => 'Pedido guardado con éxito.',
            'error-email' => 'Envío guardado con éxito. No ha sido posible enviar el email para: :emails'
        ],
        'destroy' => [
            'question'  => '¿Confirma la anulación de este envío?',
            'success'   => 'Envío anulado con éxito.',
            'error'     => 'Ha ocurrido un error al intentar anular el envío.',
        ]
    ]
];
