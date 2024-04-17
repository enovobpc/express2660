<?php
$cargoMode = app_mode_cargo();
$courierMode = app_mode_courier();
?>
<aside class="main-sidebar hidden-print">
    <section class="sidebar">
        @if (Auth::user()->hasRole(config('permissions.role.platformer')))
            <ul class="sidebar-menu">
                {!! Html::sidebarOption('shipments', __('Envios'), route('admin.shipments.index'), 'shipments', 'fas fa-fw fa-shipping-fast') !!}
                @if (hasModule('collections'))
                    {!! Html::sidebarOption('collections', __('Pedidos de Recolha'), route('admin.pickups.index'), 'shipments', 'fas fa-fw fa-dolly') !!}
                @endif
                {!! Html::sidebarOption('incidences', __('Gestão Incidências'), route('admin.incidences.index'), 'incidences', 'eicon eicon-incidences') !!}
                {!! Html::sidebarOption('traceability', __('Rastreabilidade'), route('admin.traceability.index'), 'traceability', 'fas fa-fw fa-barcode') !!}
                @if (hasModule('delivery_management'))
                    {!! Html::sidebarOption('delivery_management', __('Mapas de Distribuição'), route('admin.trips.index'), 'delivery_management', 'fas fa-fw fa-file-alt') !!}
                @endif
            </ul>
        @else
            <ul class="sidebar-menu">
                {!! Html::sidebarOption('dashboard', __('Painel de Resumo'), route('admin.dashboard'), null, 'fas fa-fw fa-poll-h') !!}

                {{-- ENTIDADES --}}
                @if (Auth::user()->ability(Config::get('permissions.role.admin'), 'customers,providers,users,operators,pickup_points'))
                    {!! Html::sidebarTreeOpen('entities', __('Entidades'), 'fas fa-fw fa-users') !!}
                    {!! Html::sidebarOption('customers', __('Clientes'), route('admin.customers.index'), 'customers') !!}
                    {!! Html::sidebarOption('recipients', __('Destinatários'), route('admin.recipients.index'), 'customers') !!}
                    {!! Html::sidebarOption('pickup_points', __('Pontos Pickup'), route('admin.pickup-points.index'), 'pickup_points') !!}
                    {!! Html::sidebarOption('providers', __('Fornecedores'), route('admin.providers.index'), 'providers') !!}
                    {!! Html::sidebarOption('users', __('Colaboradores'), route('admin.users.index'), 'users') !!}
                    {!! Html::sidebarTreeClose() !!}
                @endif

                @if (Auth::user()->ability(Config::get('permissions.role.admin'), 'shipments,incidences,express_services,delivery_management,traceability,picking_management'))
                    <li class="header">@trans('GESTÃO DE CARGA')</li>

                    <?php
                    $menuName = __('Envios e Serviços');
                    if ($cargoMode) {
                        $menuName =  __('Ordens de Carga');
                    } elseif ($courierMode) {
                        $menuName = __('Gestão de Pedidos');
                    }
                    ?>
                    {!! Html::sidebarOption('shipments', $menuName, route('admin.shipments.index'), 'shipments', 'fas fa-fw fa-shipping-fast') !!}

                    @if (hasModule('collections'))
                        {!! Html::sidebarOption('collections', __('Pedidos de Recolha'), route('admin.pickups.index'), 'shipments', 'fas fa-fw fa-dolly') !!}
                    @endif

                    {!! Html::sidebarOption('delivery_management', app_mode_cargo() ? __('Mapas de Viagem') : __('Mapas de Distribuição'), route('admin.trips.index'), 'delivery_management', 'fas fa-fw fa-copy') !!}

                    @if(hasModule('cargo_planning'))
                        {!! Html::sidebarOption('cargo_planning', __('Timeline'), route('admin.timeline.index'), 'cargo_planning', 'fas fa-fw fa-stream') !!}
                    @endif


                    {!! Html::sidebarOption('traceability', __('Rastreabilidade'), route('admin.traceability.index'), 'traceability', 'fas fa-fw fa-barcode') !!}

                    {!! Html::sidebarTreeOpen('operational', __('Gestão Operacional'), 'fas fa-fw fa-cog') !!}
                    {!! Html::sidebarOption('incidences', __('Gestão Incidências'), route('admin.incidences.index'), 'incidences') !!}

                    {!! Html::sidebarOption('picking_management', __('Gestão Massiva Envios'), route('admin.picking.management.index'), 'traceability') !!}
                    {!! Html::sidebarOption('devolutions', __('Controlo Devoluções'), route('admin.devolutions.index'), 'devolutions') !!}
                    {{-- @if(hasModule('express_services'))
                        {!! Html::sidebarOption('express_services', 'Serviços Expresso', route('admin.express-services.index'), 'express_services') !!}
                    @endif --}}
                    {!! Html::sidebarOption('customer_support', __('Suporte ao Cliente'), route('admin.customer-support.index'), 'customer_support') !!}
                    {!! Html::sidebarTreeClose() !!}


                @endif

                @if (Auth::user()->ability(Config::get('permissions.role.admin'), 'refunds_customers,refunds_operators,cod_control,gateway_payments,devolutions,sepa_transfers,invoices,billing,billing-providers,products_sales,customers_balance,statistics,sepa_transfers,cashier'))
                    <li class="header">@trans('FINANCEIRO')</li>
                    @if (Auth::user()->ability(Config::get('permissions.role.admin'), 'refunds_customers,refunds_operators,cod_control,gateway_payments,devolutions,sepa_transfers,cashier'))
                        {!! Html::sidebarTreeOpen('financial', __('Tesouraria'), 'fas fa-fw fa-hand-holding-usd') !!}
                        {{-- {!! Html::sidebarOption('refunds_operators', 'Conferência Diária Motorista', route('admin.operator-refunds.index'), 'refunds_operators') !!} --}}
                        {!! Html::sidebarOption('refunds_customers', __('Gestão de Reembolsos'), route('admin.refunds.customers.index'), 'refunds_customers') !!}
                        {!! Html::sidebarOption('cod_control', __('Portes no Destino'), route('admin.refunds.cod.index'), 'cod_control') !!}
                        
                        <li class="divider"></li>
                        
                        {!! Html::sidebarOption('allowances', __('Ajudas de Custo'), route('admin.allowances.index'), 'allowances') !!}

                        <li class="divider"></li>
                        @if (hasModule('cashier'))
                            {!! Html::sidebarOption('cashier', __('Fluxos Diários Caixa'), route('admin.cashier.index'), 'cashier') !!}
                        @endif
                        {!! Html::sidebarOption('gateway_payments2', __('Movimentos Bancários'), route('admin.denied'), 'gateway_payments2') !!}
                        {!! Html::sidebarOption('gateway_payments2', __('Reconsiliação Bancária'), route('admin.denied'), 'gateway_payments2') !!}
                        <li class="divider"></li>
                        {!! Html::sidebarOption('gateway_payments', __('Pagamentos Multibanco/Visa'), route('admin.gateway.payments.index'), 'gateway_payments') !!}
                        {!! Html::sidebarOption('sepa_transfers', __('Transferências/Débito Direto'), route('admin.sepa-transfers.index'), 'sepa_transfers') !!}

                        {!! Html::sidebarTreeClose() !!}
                    @endif


                    @if (Auth::user()->ability(Config::get('permissions.role.admin'), 'invoices,billing,billing-providers,customers_balance,statistics'))

                        {!! Html::sidebarTreeOpen('financial', __('Faturação'), 'fas fa-fw ' . Setting::get('app_currency_icon', 'fa-euro-sign')) !!}
                        {!! Html::sidebarOption('invoices', __('Vendas'), route('admin.invoices.index', ['tab' => 'invoices', 'doc_type' => 'invoice']), 'invoices') !!}
                        @if (hasModule('purchase_invoices'))
                            {!! Html::sidebarOption('purchase-invoices', __('Compras'), route('admin.invoices.purchase.index', ['tab' => 'invoices', 'doc_type' => 'provider-invoice']), 'purchase_invoices') !!}
                        @else
                            {!! Html::sidebarOption('invoices', __('Compras'), route('admin.invoices.purchase.index'), 'invoices') !!}
                        @endif
                
                        {!! Html::sidebarOption('customers_balance', __('Contas Corrente'), route('admin.billing.balance.index'), 'customers_balance') !!}
                        @if(hasModule('products'))
                            {!! Html::sidebarOption('products_sales', __('Venda de Artigos'), route('admin.products.sales.index'), 'products_sales') !!}
                        @endif

                        <li class="divider"></li>
                        {!! Html::sidebarOption('billing', __('Faturação Clientes'), route('admin.billing.customers.index'), 'billing') !!}
                        {!! Html::sidebarOption('billing-providers', __('Faturação Terceiros'), route('admin.billing.providers.index'), 'billing_providers') !!}
                        
                        <li class="divider"></li>

                        {!! Html::sidebarOption('statistics', __('Análise Estatística'), route('admin.statistics.index'), 'statistics') !!}

                        {!! Html::sidebarTreeClose() !!}
                    @endif
                @endif

                @if (hasModule('prospection') || hasModule('waybills') || hasModule('logistic') || hasModule('fleet') || hasModule('budgets') || hasModule('maps') || hasModule('cashier'))
                    <li class="header">@trans('FERRAMENTAS DE GESTÃO')</li>

                    @if (hasModule('maps'))
                        {!! Html::sidebarOption('maps', __('Mapa e Localização GPS'), route('admin.maps.index'), 'maps', 'fas fa-fw fa-map') !!}
                    @endif


                    @if (hasModule('fleet') && Auth::user()->ability(Config::get('permissions.role.admin'), 'fleet_vehicles,fleet_fuel_logs,fleet_maintenances,fleet_reminders,fleet_incidences,fleet_history,fleet_providers,fleet_parts,fleet_brands,fleet_stats'))
                        {!! Html::sidebarTreeOpen('fleet', __('Gestão de Frota'), 'fas fa-fw fa-car') !!}
                        {!! Html::sidebarOption('fleet_vehicles', __('Viaturas e Reboques'), route('admin.fleet.vehicles.index'), 'fleet_vehicles') !!}
                        {!! Html::sidebarOption('fleet_checklists', __('Fichas Controlo e Inspeção'), route('admin.fleet.checklists.index'), 'fleet_checklists') !!}
                        {!! Html::sidebarOption('fleet_stats', __('Estatística de Gastos'), route('admin.fleet.stats.index'), 'fleet_stats') !!}
                        <li class="divider"></li>
                        {!! Html::sidebarOption('fleet_accessories', __('Listagem Acessórios'), route('admin.fleet.accessories.index'), 'fleet_accessories') !!}
                        {!! Html::sidebarOption('fleet_fuel_logs', __('Abastecimentos'), route('admin.fleet.fuel.index'), 'fleet_fuel_logs') !!}
                        {!! Html::sidebarOption('fleet_maintenances', __('Manutenções'), route('admin.fleet.maintenances.index'), 'fleet_maintenances') !!}
                        {!! Html::sidebarOption('fleet_costs', __('Custos e Despesas'), route('admin.fleet.expenses.index'), 'fleet_costs') !!}
                        {!! Html::sidebarOption('fleet_usages', __('Histórico Utilizações'), route('admin.fleet.usages.index'), 'fleet_usages') !!}
                        {!! Html::sidebarOption('fleet_reminders', __('Lembretes'), route('admin.fleet.reminders.index'), 'fleet_reminders') !!}
                        {!! Html::sidebarOption('fleet_incidences', __('Ocorrências e Sinistros'), route('admin.fleet.incidences.index'), 'fleet_incidences') !!}
                        <li class="divider"></li>
                        {!! Html::sidebarOption('fleet_parts', __('Gerir Peças e Stocks'), route('admin.fleet.parts.index'), 'fleet_parts') !!}

                        {{-- {!! Html::sidebarOption('fleet_brands', 'Marcas e Modelos', route('admin.fleet.brands.index'), 'fleet_brands') !!} --}}
                        {!! Html::sidebarTreeClose() !!}
                    @endif

                    @if (hasModule('prospection') && Auth::user()->ability(Config::get('permissions.role.admin'), ' v,meetings'))
                        {!! Html::sidebarTreeOpen('prospects', __('Gestão Comercial'), 'fas fa-fw fa-user-tie') !!}
                        {!! Html::sidebarOption('prospects', __('Potenciais Clientes'), route('admin.prospects.index'), 'prospects') !!}
                        {!! Html::sidebarOption('meetings', __('Visitas e Reuniões'), route('admin.meetings.index'), 'meetings') !!}
                        {!! Html::sidebarTreeClose() !!}
                    @endif

                    @if (hasModule('waybills') && Auth::user()->ability(Config::get('permissions.role.admin'), 'air-waybills,air-waybills-models,air-waybills-providers,air-waybills-agents,air-waybills-expenses,air-waybills-goods-types'))
                        {!! Html::sidebarTreeOpen('waybills', __('Cartas de Porte Aéreo'), 'fas fa-fw fa-plane') !!}
                        {!! Html::sidebarOption('air-waybills', __('Cartas de Porte'), route('admin.air-waybills.index'), 'air-waybills') !!}
                        {!! Html::sidebarOption('air-waybills-models', __('Modelos Pré-Preenchidos'), route('admin.air-waybills.models.index'), 'air-waybills-models') !!}
                        {!! Html::sidebarOption('air-waybills-providers', __('Fornecedores Aéreos'), route('admin.air-waybills.providers.index'), 'air-waybills-providers') !!}
                        {!! Html::sidebarOption('air-waybills-agents', __('Agentes'), route('admin.air-waybills.agents.index'), 'air-waybills-agents') !!}
                        {!! Html::sidebarOption('air-waybills-expenses', __('Taxas e Encargos'), route('admin.air-waybills.expenses.index'), 'air-waybills-expenses') !!}
                        {!! Html::sidebarOption('air-waybills-goods-types', __('Tipos de Carga'), route('admin.air-waybills.goods-types.index'), 'air-waybills-goods-types') !!}
                        {!! Html::sidebarTreeClose() !!}
                    @endif

                    @if (hasModule('equipments') && Auth::user()->ability(Config::get('permissions.role.admin'), 'equipments,equipments_locations,equipments_warehouses'))
                        {!! Html::sidebarTreeOpen('logistic', __('Gestão Equipamentos'), 'fas fa-fw fa-toolbox') !!}
                        {!! Html::sidebarOption('equipments', __('Equipamentos'), route('admin.equipments.index'), 'equipments') !!}
                        {!! Html::sidebarOption('equipments_locations', __('Localizações'), route('admin.equipments.locations.index'), 'equipments_locations') !!}
                        {!! Html::sidebarTreeClose() !!}
                    @endif

                    @if (hasModule('logistic') && Auth::user()->ability(Config::get('permissions.role.admin'), 'logistic_shipping_orders,logistic_reception_orders,logistic_products,logistic_locations,logistic_warehouses'))
                        {!! Html::sidebarTreeOpen('logistic', __('Logística e Armazenagem'), 'fas fa-fw fa-cubes') !!}
                        {!! Html::sidebarOption('logistic_products', __('Artigos e Stocks'), route('admin.logistic.products.index'), 'logistic_products') !!}
                        {!! Html::sidebarOption('logistic_shipping_orders', __('Ordens de Saída'), route('admin.logistic.shipping-orders.index'), 'logistic_shipping_orders') !!}
                        {!! Html::sidebarOption('logistic_reception_orders', __('Ordens de Recepção'), route('admin.logistic.reception-orders.index'), 'logistic_reception_orders') !!}
                        {!! Html::sidebarOption('logistic_devolutions', __('Devoluções'), route('admin.logistic.devolutions.index'), 'logistic_devolutions') !!}
                        {!! Html::sidebarOption('logistic_inventories', __('Inventários'), route('admin.logistic.inventories.index'), 'logistic_inventories') !!}
                        <li class="divider"></li>
                        {{-- {!! Html::sidebarOption('logistic_map', 'Mapa de Armazém', route('admin.logistic.map.index'), 'logistic_map') !!} --}}
                        {!! Html::sidebarOption('logistic_locations', __('Locais de Armazenagem'), route('admin.logistic.locations.index'), 'logistic_locations') !!}
                        {!! Html::sidebarOption('logistic_brands', __('Marcas e Categorias'), route('admin.logistic.brands.index'), 'logistic_brands') !!}
                        {!! Html::sidebarTreeClose() !!}
                    @endif

                    @if (hasModule('budgets') && !hasModule('budgets_animals') && !hasModule('budgets_courier') && Auth::user()->ability(Config::get('permissions.role.admin'), 'budgets'))
                        {!! Html::sidebarOption('budgets', __('Gestão de Orçamentos'), route('admin.budgets.index'), null, 'fas fa-fw fa-file-alt') !!}
                    @elseif((hasModule('budgets') || hasModule('budgets_animals') || hasModule('budgets_courier')) && Auth::user()->ability(Config::get('permissions.role.admin'), 'budgets'))
                        {!! Html::sidebarTreeOpen('budgets-group', __('Orçamentos'), 'fas fa-fw fa-file-alt') !!}
                        {!! Html::sidebarOption('budgets', __('Pedidos por E-mail'), route('admin.budgets.index'), 'budgets') !!}
                        {!! Html::sidebarOption('budgets_courier', __('Orçamentos Courier'), route('admin.budgets.courier.index'), 'budgets_courier') !!}
                        {!! Html::sidebarOption('budgets_animals', __('Orçamentos Animais'), route('admin.budgets.animals.index'), 'budgets_animals') !!}
                        {!! Html::sidebarTreeClose() !!}
                    @endif

                    @if (hasModule('files_repository') && Auth::user()->ability(Config::get('permissions.role.admin'), 'files_repository'))
                        {!! Html::sidebarOption('files_repository', __('Arquivo de Ficheiros'), route('admin.repository.index'), null, 'fas fa-fw fa-folder-open') !!}
                    @endif

                    @if (hasModule('events_management'))
                        {!! Html::sidebarOption('events_management', __('Gestão de Eventos'), route('admin.event-manager.index'), null, 'fas fa-fw fa-calendar-week') !!}
                    @endif

                    {{-- WEBSITE --}}
                    @if ((hasModule('website') || hasModule('website_sliders') || hasModule('website_blog') || hasModule('website_customer_requests') || hasModule('website_budget_requests') || hasModule('website_recruitments') || hasModule('website_translations') || hasModule('website_analytics') || hasModule('website_faqs') || hasModule('website_testimonials') || hasModule('website_pages') || hasModule('website_newsletter') || hasModule('website_documents') || hasModule('website_brands')) && Auth::user()->ability(Config::get('permissions.role.admin'), 'sliders,customer_requests,budget_requests,recruitments,admin_translations,blog_posts,log_google_analytics'))
                        {!! Html::sidebarTreeOpen('website', __('Gestão do Site'), 'fas fa-fw fa-globe') !!}
                        {!! Html::sidebarOption('pages', __('Gestor de Páginas'), route('admin.website.pages.index'), 'pages') !!}
                        {!! Html::sidebarOption('sliders', __('Sliders'), route('admin.website.sliders.index'), 'sliders') !!}
                        {!! Html::sidebarOption('blog', __('Notícias e Publicações'), route('admin.website.blog.posts.index'), 'blog') !!}
                        {!! Html::sidebarOption('documents', __('Documentos'), route('admin.website.documents.index'), 'documents') !!}
                        {!! Html::sidebarOption('brands', __('Marcas/Parceiros'), route('admin.website.brands.index'), 'brands') !!}
                        {!! Html::sidebarOption('faqs', __('Perguntas Frequentes'), route('admin.website.faqs.index'), 'faqs') !!}
                        {!! Html::sidebarOption('newsletters', __('Subscritores Newsletter'), route('admin.website.newsletters.subscribers.index'), 'newsletters_subscribers') !!}
                        {!! Html::sidebarOption('testimonials', __('Testemunhos'), route('admin.website.testimonials.index'), 'testimonials') !!}
                        {!! Html::sidebarOption('recruitments', __('Candidaturas Emprego'), route('admin.website.recruitments.index'), 'recruitments') !!}
                        {!! Html::sidebarOption('admin_translations', __('Gestor de Traduções'), route('admin.translations.index'), 'admin_translations', null, ['target' => '_blank']) !!}
                        {!! Html::sidebarOption('log_google_analytics', __('Google Analytics'), route('admin.website.visits.index'), 'log_google_analytics') !!}
                        {!! Html::sidebarTreeClose() !!}
                    @endif
                @endif

                @if (Auth::user()->ability(Config::get('permissions.role.admin'), 'settings,admin_roles,agencies,providers,services,shipping_expenses,billing-zones,prices_tables,prices_tables_view,api'))
                    <li class="header">@trans('CONFIGURAR APLICAÇÃO')</li>
                    {!! Html::sidebarTreeOpen('auxiliar-tables', __('Configurações'), 'fas fa-fw fa-cogs') !!}
                    {!! Html::sidebarOption('admin_settings', __('Definições Gerais'), route('admin.settings.index'), 'admin_settings') !!}
                    {!! Html::sidebarOption('email_accounts', __('Espaço e Contas E-mail'), route('admin.cpanel.emails.index'), 'email_accounts') !!}
                    {!! Html::sidebarOption('agencies', __('Empresas e Centros Logísticos'), route('admin.agencies.index'), 'agencies') !!}
                    <li class="divider"></li>
                    {!! Html::sidebarOption('services', __('Serviços de Transporte'), route('admin.services.index'), 'services') !!}
                    {!! Html::sidebarOption('shipping_expenses', __('Taxas Adicionais'), route('admin.expenses.index'), 'shipping_expenses') !!}
                    {!! Html::sidebarOption('prices_tables', __('Tabelas Preço Gerais'), route('admin.prices-tables.index'), 'prices_tables,prices_tables_view') !!}
                    {!! Html::sidebarOption('billing-zones', __('Zonas Preço e Faturação'), route('admin.billing.zones.index'), 'billing-zones') !!}
                    <li class="divider"></li>
                    {!! Html::sidebarOption('billing-items', __('Artigos e Taxas IVA'), route('admin.billing.items.index'), 'invoices') !!}
                    {{--{!! Html::sidebarOption('billing-series', 'Séries de Faturação', route('admin.billing.series.index'), 'billing-series') !!}
                    --}}{!! Html::sidebarOption('banks', __('Bancos e Modos Pagamento'), route('admin.banks.index'), 'banks') !!}
                    {!! Html::sidebarOption('brands', __('Marcas e Modelos'), route('admin.brands.index'), 'brands') !!}
                    <li class="divider"></li>
                    {!! Html::sidebarOption('zip_codes', __('Zonas e Códigos Postais'), route('admin.zip-codes.agencies.index'), 'zip_codes') !!}
                    @if (!hasModule('fleet'))
                        {!! Html::sidebarOption('vehicles', __('Gerir Viaturas'), route('admin.vehicles.index'), 'vehicles') !!}
                    @endif
                    {!! Html::sidebarOption('routes', __('Rotas Recolha e Entrega'), route('admin.routes.index'), 'routes') !!}
                    {!! Html::sidebarOption('pack_types', __('Tipo Mercadoria e Transporte'), route('admin.pack-types.index'), 'pack_types') !!}
                    {!! Html::sidebarOption('tracking_status', __('Estados Envio e Incidência'), route('admin.tracking.status.index'), 'tracking_status') !!}

                    <li class="divider"></li>
                    {!! Html::sidebarOption('emails', __('Mensagens E-mail'), route('admin.emails.index'), 'emails') !!}
                    @if (hasModule('sms'))
                        {!! Html::sidebarOption('sms', __('Mensagens SMS'), route('admin.sms.index'), 'sms') !!}
                    @endif
                    <li class="divider"></li>
                    {!! Html::sidebarOption('webservices', __('Webservices Globais'), route('admin.webservices.index'), 'webservices') !!}
                    {!! Html::sidebarOption('api', __('Chaves da API'), route('admin.api.index'), 'api') !!}
                    {!! Html::sidebarOption('roles', __('Perfís e Permissões'), route('admin.roles.index'), 'admin_roles') !!}
                    {!! Html::sidebarTreeClose() !!}
                @endif
            </ul>
        @endif
    </section>
</aside>
