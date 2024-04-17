<?php

return array(

    /**
     * Administrator role, to access all areas off the page without permission
     */
    'role' => [
        'admin'           => 'administrator',
        'agency'          => 'agencia',
        'seller'          => 'comercial',
        'cashier_manager' => 'gestor-de-caixa',
        'platformer'      => 'plataformista',
        'operator'        => 'operador',
    ],


    /*
    |--------------------------------------------------------------------------
    | Application Permissions
    |--------------------------------------------------------------------------
    |
    | Here you may specify all your applications permissions, after add a new
    | permission run the command to sync them.
    | The permission name must be unique.
    |
    */

    'list' => [
        [
            "name" => "admin_users",
            "display_name" => "Administração - Utilizadores",
        ],
        [
            "name" => "admin_roles",
            "display_name" => "Administração - Perfis e permissões",
        ],
        [
            "name" => "log_errors",
            "display_name" => "Registos - Registo de erros",
        ],
        [
            "name" => "log_deleted_files",
            "display_name" => "Registos - Ficheiros removidos",
        ],
        [
            "name" => "log_google_analytics",
            "display_name" => "Registos - Visitas",
        ],
        [
            "name" => "admin_translations",
            "display_name" => "Administração - Gestor de Traduções",
        ],
        [
            "name" => "admin_settings",
            "display_name" => "Administração - Definições Gerais",
        ],
        [
            "name" => "sliders",
            "display_name" => "Sliders",
        ],
        [
            "name" => "customer_requests",
            "display_name" => "Pedidos de Cliente",
        ],
        [
            "name" => "recruitments",
            "display_name" => "Pedidos de Recrutamento",
        ],
        [
            "name" => "budget_requests",
            "display_name" => "Pedidos de Orçamento",
        ],
        [
            "name" => "customers",
            "display_name" => "Clientes",
        ],
        [
            "name" => "prospects",
            "display_name" => "Prospects/Contactos",
        ],
        [
            "name" => "providers",
            "display_name" => "Fornecedores",
        ],
        [
            "name" => "agencies",
            "display_name" => "Agências",
        ],
        [
            "name" => "status",
            "display_name" => "Estados de Envio",
        ],
        [
            "name" => "customers_types",
            "display_name" => "Tipos de Cliente",
        ],
        [
            "name" => "services",
            "display_name" => "Serviços",
        ],
        [
            "name" => "customers",
            "display_name" => "Clientes",
        ],
        [
            "name" => "shipments",
            "display_name" => "Envios e Recolhas",
        ],
        [
            "name" => "billing",
            "display_name" => "Financeiro",
        ],
        [
            "name" => "refunds_control",
            "display_name" => "Controlo de Cobranças",
        ],
        [
            "name" => "incidences_types",
            "display_name" => "Tipos de Incidência",
        ],
        [
            "name" => "import_methods",
            "display_name" => "Métodos de Importação",
        ],
        [
            "name" => "notifications",
            "display_name" => "Notificações",
        ],
        [
            "name" => "webservices_log",
            "display_name" => "Registo dos Webservices",
        ],
        [
            "name" => "cashier",
            "display_name" => "Caixas e Terminais de Venda",
        ],
        [
            "name" => "cashier_terminals",
            "display_name" => "Criar e Gerir terminais de venda",
        ],
        [
            "name" => "cashier_central",
            "display_name" => "Aceder à Caixa Central",
        ],
        [
            "name" => "products",
            "display_name" => "Productos - Gerir Produtos e Stocks",
        ],
        [
            "name"         => "shipping_expenses",
            "display_name" => "Encargos dos Envios",
        ],
        [
            "name"         => "edit_shipments",
            "display_name" => "Editar Envios",
        ],
        [
            "name"         => "payments_at_recipient",
            "display_name" => "Controlo de Pagamentos no Destino"
        ],
        [
            "name"         => "customer_covenants",
            "display_name" => "Clientes - Avenças Mensais"
        ],
        [
            "name"         => "products_sales",
            "display_name" => "Produtos - Venda de Produtos"
        ],
        [
            "name"         => "support_tickets",
            "display_name" => "Suporte Técnico - Consultar e Criar Tickets"
        ],
        [
            "name"         => "support_tickets_manage",
            "display_name" => "Suporte Técnico - Administrar Tickets"
        ],

        [
            "name"         => "fleet_brands",
            "display_name" => "Gestão Frota - Marcas e Modelos"
        ],
        [
            "name"         => "fleet_providers",
            "display_name" => "Gestão Frota - Fornecedores"
        ],
        [
            "name"         => "fleet_vehicles",
            "display_name" => "Gestão Frota - Viaturas"
        ],
        [
            "name"         => "fleet_parts",
            "display_name" => "Gestão Frota - Peças e Serviços"
        ],
        [
            "name"         => "fleet_fuel_logs",
            "display_name" => "Gestão Frota - Abastecimentos"
        ],
        [
            "name"         => "fleet_tolls_log",
            "display_name" => "Gestão Frota - Portagens"
        ],
        [
            "name"         => "fleet_incidences",
            "display_name" => "Gestão Frota - Sinistros"
        ],
        [
            "name"         => "fleet_reminders",
            "display_name" => "Gestão Frota - Lembretes"
        ],
        [
            "name"         => "fleet_maintenances",
            "display_name" => "Gestão Frota - Manutenções e Despesas"
        ],
        [
            "name"         => "fleet_stats",
            "display_name" => "Gestão Frota - Histórico"
        ],

        [
            "name"         => "news",
            "display_name" => "Notícias"
        ],
        [
            "name"         => "meetings",
            "display_name" => "Clientes - Visitas"
        ],
        [
            "name"         => "deliveries",
            "display_name" => "Distribuição"
        ],
        [
            "name"         => "calendar_events",
            "display_name" => "Agenda - Eventos"
        ],
        [
            "name"         => "prices_tables",
            "display_name" => "Tabelas de Preços"
        ],
        [
            "name"         => "blog_posts",
            "display_name" => "Website - Notícias",
        ],
        [
            "name"         => "express_services",
            "display_name" => "Serviços Expresso",
        ],
        [
            "name"         => "budgets",
            "display_name" => "Gestão de Orçamentos",
        ],
        [
            "name"         => "logistic_warehouses",
            "display_name" => "Logística - Armazéns",
        ],
        [
            "name"         => "logistic_locations",
            "display_name" => "Logística - Localizações",
        ],
        [
            "name"         => "logistic_products",
            "display_name" => "Logística - Produtos",
        ],
        [
            "name"         => "logistic_shipping_orders",
            "display_name" => "Logística - Ordens de Saída",
        ],
        [
            "name"         => "customers-messages",
            "display_name" => "Mensagens a Clientes",
        ],
        [
            "name"         => "backups",
            "display_name" => "Backups",
        ],
        [
            "name"         => "block_cost_prices",
            "display_name" => "Ocultar Preços Custo",
        ],
        [
            "name"         => "air-waybills",
            "display_name" => "Emitir Cartas de Porte",
        ],
        [
            "name"         => "air-waybills-agents",
            "display_name" => "Gerir Agentes",
        ],
        [
            "name"         => "air-waybills-providers",
            "display_name" => "Gerir Fornecedores",
        ],
        [
            "name"         => "air-waybills-models",
            "display_name" => "Modelos Pré-preenchidos",
        ],
        [
            "name"         => "air-waybills-expenses",
            "display_name" => "Gerir Encargos",
        ],
        [
            "name"         => "air-waybills-goods-types",
            "display_name" => "Gerir Tipos Carga",
        ],
        [
            "name"         => "air-waybills-invoices",
            "display_name" => "Faturar Cartas de Porte",
        ],
        [
            "name"         => "billing-zones",
            "display_name" => "Gerir Zonas de Faturação",
        ],
        [
            "name"         => "operator-refunds",
            "display_name" => "Reembolsos por motorista",
        ],
    ],

);
