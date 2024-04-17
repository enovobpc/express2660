<?php

return [

    'envialia' => [
        'fields' => [
            "codigo"              => "provider_code",

            "agencia_que_carga"   => "cargo_agency_id",
            "age_carga"           => "cargo_agency_id", //outra alternativa, depende do ficheiro carregado
            "agencia_que_soporta" => "agency_id",
            "age_soporta"          => "agency_id",
            "tipo_de_cargo"       => "expense_code",
            "tipo_cargo"          => "expense_code",

            "precio_soportado"    => "cost_price",
            "total_precio"        => "cost_price",

            "unidades"            => "qty",
            "fecha"               => "date",
            "codigo_de_envio"     => "provider_tracking_code",
            "anulado"             => "canceled",

            "envio_relacionado"     => "provider_tracking_code",
            "recogida_relacionada"  => "provider_collection_tracking_code",

            "cod_cliente"           => "customer_code",

            "anulado"               => "canceled",
        ]
    ],
];
