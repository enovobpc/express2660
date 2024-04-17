<?php

use App\Models\Agency;
use App\Models\Customer;
use App\Models\IncidenceType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Traceability\Event;
use App\Models\Traceability\Location;
use App\Models\Traceability\ShipmentTraceability;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Dev0102 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("update shipments set cod = 'D' where payment_at_recipient=1");
        \DB::statement("update shipments set tags = '[\"charge\"]' where charge_price>0.00");
        \DB::statement("update shipments set tags = '[\"charge\"]' where charge_price>0.00");
        \DB::statement("update shipments set tags = '[\"rguide\"]' where has_return like '%rguide%'");
        \DB::statement("update shipments set tags = '[\"rpack\", \"charge\"]' where charge_price>0.00 and has_return like '%rpack%'");
        \DB::statement("update shipments set tags = '[\"rguide\", \"charge\"]' where charge_price>0.00 and has_return like '%rguide%'");


        if(Setting::get('app_mode') == 'cargo' || Setting::get('app_mode') == 'freight') {
            Setting::set('shipment_adicional_addr_mode', 'pro');
        }
        
        Setting::set('shipment_save_other_fullreset', 1);
        Setting::set('mobile_app_register_user_logs', 1);

        if(hasModule('fleet')) {
            Setting::set('mobile_app_menu_fuel', 1);
        }

        Schema::dropIfExists('shipments_pallets');
        
        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoices', 'sort')) {
                $table->string('sort', 20)
                    ->nullable()
                    ->index()
                    ->after('deleted_by');
            }
        });
        
        Schema::table('shipments', function (Blueprint $table) {
            
            if (!Schema::hasColumn('shipments', 'count_discharges')) {
                $table->integer('count_discharges')
                    ->default(1)
                    ->nullable()
                    ->after('recipient_latitude');
            }

            if (!Schema::hasColumn('shipments', 'container_seal')) {
                $table->string('container_seal')
                    ->nullable()
                    ->after('packaging_type');
            }

            if (!Schema::hasColumn('shipments', 'container_code')) {
                $table->string('container_code')
                    ->nullable()
                    ->after('packaging_type');
            }
            if (!Schema::hasColumn('shipments', 'container_type')) {
                $table->string('container_type')
                    ->nullable()
                    ->after('packaging_type');
            }

            if (!Schema::hasColumn('shipments', 'ship_code')) {
                $table->string('ship_code')
                    ->nullable()
                    ->after('packaging_type');
            }
            
            if (!Schema::hasColumn('shipments', 'adr_onu')) {
                $table->string('adr_onu')
                    ->nullable()
                    ->after('packaging_type');
            }

            if (!Schema::hasColumn('shipments', 'adr_class')) {
                $table->string('adr_class')
                    ->nullable()
                    ->after('packaging_type');
            }

            if (!Schema::hasColumn('shipments', 'liters')) {
                $table->decimal('liters', 10, 2)
                    ->nullable()
                    ->index()
                    ->after('ldm');
            }
        });

        Schema::table('customers', function (Blueprint $table) {

            if (!Schema::hasColumn('customers', 'balance_total')) {
                $table->decimal('balance_total', 10, 2)
                    ->default(0)
                    ->index()
                    ->after('balance_divergence');
            }

            if (!Schema::hasColumn('customers', 'balance_total_credit')) {
                $table->decimal('balance_total_credit', 10, 2)
                    ->default(0)
                    ->index()
                    ->after('balance_divergence');
            }

            if (!Schema::hasColumn('customers', 'balance_total_debit')) {
                $table->decimal('balance_total_debit', 10, 2)
                    ->default(0)
                    ->index()
                    ->after('balance_divergence');
            }

            if (!Schema::hasColumn('customers', 'balance_expired_count')) {
                $table->integer('balance_expired_count')
                    ->default(0)
                    ->after('balance_divergence');
            }

            if (!Schema::hasColumn('customers', 'balance_unpaid_count')) {
                $table->integer('balance_unpaid_count')
                    ->default(0)
                    ->after('balance_divergence');
            }
        });


        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'locale')) {
                $table->string('locale', 5)
                    ->default('pt')
                    ->after('popup_notification');
            }
        });

        
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'estimated_delivery_finish')) {
                $table->timestamp('estimated_delivery_finish')
                    ->nullable()
                    ->index()
                    ->after('estimated_delivery_time_min');
            }

            if (!Schema::hasColumn('shipments', 'keywords')) {
                $table->longtext('keywords')
                    ->nullable()
                    ->after('is_insured');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'end_delivery')) {
                \DB::statement("update shipments set estimated_delivery_finish = end_delivery");

                $table->dropColumn('end_delivery');
            }
        });

        if (!Schema::hasTable('shipments_traceability_events')) {
           
            Schema::create('shipments_traceability_events', function (Blueprint $table) {
                
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('source');
                $table->string('name');
                $table->string('action', 15)->nullable();
                $table->integer('agency_id')->unsigned()->nullable();
                $table->integer('status_id')->unsigned()->nullable();
                $table->integer('location_id')->unsigned()->nullable();
                $table->integer('sort')->unsigned()->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('status_id')
                    ->references('id')
                    ->on('shipping_status');

                $table->foreign('agency_id')
                      ->references('id')
                      ->on('agencies');

            });
        }


        $agencies = Agency::get();
        foreach($agencies as $agency) {
            $event = Event::firstOrNew([
                'action'    => 'in',
                'agency_id' => $agency->id
            ]);

            if(!$event->exists) {
                $event->source    = config('app.source');
                $event->name      = $agency->print_name . ' - Chegadas armazém';
                $event->action    = 'in';
                $event->agency_id = $agency->id;
                $event->status_id = '17';
                $event->save();
            }

            $event = Event::firstOrNew([
                'action'    => 'in',
                'agency_id' => $agency->id,
                'status_id' => 4
            ]);

            if(!$event->exists) {
                $event->source    = config('app.source');
                $event->name      = $agency->print_name . ' - Distribuição';
                $event->action    = 'in';
                $event->agency_id = $agency->id;
                $event->status_id = 4;
                $event->save();
            }

            $event = Event::firstOrNew([
                'action'    => 'out',
                'agency_id' => $agency->id,
                'status_id' => 3
            ]);
            
            if(!$event->exists) {
                $event->source    = config('app.source');
                $event->name      = $agency->print_name . ' - Saídas armazém';
                $event->action    = 'out';
                $event->agency_id = $agency->id;
                $event->status_id = 3;
                $event->save();
            }
        }


        if (!Schema::hasTable('shipments_traceability_locations')) {
           
            Schema::create('shipments_traceability_locations', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('source');
                $table->string('code');
                $table->string('name');
                $table->integer('agency_id')->unsigned()->nullable();
                $table->integer('sort')->unsigned()->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('agency_id')
                      ->references('id')
                      ->on('agencies');

            });
        }

        if (Schema::hasTable('delivery_manifests_periods') && !Schema::hasTable('trips_periods')) {
            Schema::rename('delivery_manifests_periods', 'trips_periods');
        }

        if (Schema::hasTable('delivery_manifests_expenses') && !Schema::hasTable('trips_expenses')) {
            Schema::rename('delivery_manifests_expenses', 'trips_expenses');
        }

        if (Schema::hasTable('delivery_manifests_shipments') && !Schema::hasTable('trips_shipments')) {
            Schema::rename('delivery_manifests_shipments', 'trips_shipments');
        }

        if (Schema::hasTable('delivery_manifests') && !Schema::hasTable('trips')) {
            Schema::rename('delivery_manifests', 'trips');
        }

        if (!Schema::hasTable('trips_vehicles')) {
            Schema::create('trips_vehicles', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('trip_id')->unsigned();
                $table->string('vehicle')->nullable();
                $table->string('trailer')->nullable();
                $table->integer('operator_id')->unsigned()->nullable();
                $table->timestamp('start_at')->nullable();
                $table->timestamp('end_at')->nullable();
                $table->integer('start_kms')->nullable();
                $table->integer('end_kms')->nullable();
                $table->decimal('consumption', 10, 2)->nullable();
                $table->text('obs')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('operator_id')
                      ->references('id')
                      ->on('users');
            });
        }

        if (!Schema::hasTable('trips_history')) {
            Schema::create('trips_history', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('trip_id')->unsigned();
                $table->integer('trip_vehicle_id')->unsigned()->nullable();
                $table->integer('operator_id')->unsigned()->nullable();
                $table->string('action')->nullable();
                $table->timestamp('date')->nullable();
                $table->string('location')->nullable();
                $table->text('obs')->nullable();
                $table->string('target')->nullable();
                $table->string('target_id')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('operator_id')
                      ->references('id')
                      ->on('users');

                $table->foreign('trip_id')
                      ->references('id')
                      ->on('trips');

            });
        }

        Schema::table('shipments', function (Blueprint $table) {

            if (Schema::hasColumn('shipments', 'delivery_manifest_id') && !Schema::hasColumn('shipments', 'trip_id')) {
                Schema::table('shipments', function (Blueprint $table) {
                    $table->renameColumn('delivery_manifest_id', 'trip_id');
                });
            }

            if (Schema::hasColumn('shipments', 'delivery_manifest_code') && !Schema::hasColumn('shipments', 'trip_code')) {
                Schema::table('shipments', function (Blueprint $table) {
                    $table->renameColumn('delivery_manifest_code', 'trip_code');
                });
            }
        });

        Schema::table('trips_shipments', function (Blueprint $table) {
            if (Schema::hasColumn('trips_shipments', 'delivery_manifest_id')) {
                Schema::table('trips_shipments', function (Blueprint $table) {
                    $table->renameColumn('delivery_manifest_id', 'trip_id');
                });
            }
        });
        
        Schema::table('trips_expenses', function (Blueprint $table) {

            \DB::statement("ALTER TABLE trips_expenses CHANGE COLUMN type type VARCHAR(255)");

            if (Schema::hasColumn('trips_expenses', 'delivery_manifest_id')) {
                Schema::table('trips_expenses', function (Blueprint $table) {
                    $table->renameColumn('delivery_manifest_id', 'trip_id');
                });
            }
        });

        Schema::table('allowances', function (Blueprint $table) {
            if (Schema::hasColumn('allowances', 'delivery_manifests')) {
                Schema::table('allowances', function (Blueprint $table) {
                    $table->renameColumn('delivery_manifests', 'trips');
                });
            }
        });

        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'vehicles')) {
                $table->string('vehicles', 255)
                    ->nullable()
                    ->index()
                    ->after('trailer');
            }
        });


        $agencies = Agency::get();
        foreach($agencies as $agency) {

            $location = Location::firstOrNew([
                'code'      => 'ARM',
                'agency_id' => $agency->id,
            ]);

            $location->code = 'ARM';
            $location->name = 'Localização Geral';
            $location->save();

            $location = Location::firstOrNew([
                'code'      => 'INC',
                'agency_id' => $agency->id,
            ]);

            $location->code = 'INC';
            $location->name = 'Zona Incidências';
            $location->save();


            $location = Location::firstOrNew([
                'code'      => 'DEV',
                'agency_id' => $agency->id,
            ]);
            $location->name = 'DEV';
            $location->name = 'Zona Devoluções';
            $location->save();
        }


        Schema::table('shipments_traceability', function (Blueprint $table) {
           
            if (!Schema::hasColumn('shipments_traceability', 'location_id')) {
                $table->integer('location_id')
                    ->nullable()
                    ->unsigned()
                    ->after('agency_id');

                $table->foreign('location_id')
                      ->references('id')
                      ->on('shipments_traceability_locations');
            }
            
            if (!Schema::hasColumn('shipments_traceability', 'event_id')) {
                $table->integer('event_id')
                    ->nullable()
                    ->unsigned()
                    ->after('shipment_id');

                $table->foreign('event_id')
                    ->references('id')
                    ->on('shipments_traceability_events');
            }
        });

        if (Schema::hasTable('service_types') && !Schema::hasTable('transport_types')) {
            Schema::rename('service_types', 'transport_types');
        }

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'transport_type_id')) {
                $table->integer('transport_type_id')
                    ->nullable()
                    ->unsigned()
                    ->index()
                    ->after('trip_code');

                $table->foreign('transport_type_id')
                    ->references('id')
                    ->on('transport_types');
            }
        });

        if (!Schema::hasColumn('services', 'transport_type_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->integer('transport_type_id')
                    ->unsigned()
                    ->nullable()
                    ->after('provider_id');
            });
        }

        if (Schema::hasColumn('services', 'billing_item_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('billing_item_id');
            });
        }
        
        if (!Schema::hasColumn('operators_tasks', 'transport_type_id')) {
            Schema::table('operators_tasks', function (Blueprint $table) {
                $table->integer('transport_type_id')
                    ->unsigned()
                    ->nullable()
                    ->after('customer_id');
            });
        }

        if (!Schema::hasColumn('transport_types', 'code')) {
            Schema::table('transport_types', function (Blueprint $table) {
                $table->string('code', 15)
                    ->nullable()
                    ->after('source');
            });
        }

        if (Schema::hasColumn('services', 'service_type_id')) {
            \DB::statement("update services set transport_type_id = service_type_id");
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('service_type_id');
            });
        }

        if (Schema::hasColumn('operators_tasks', 'service_type_id')) {
            \DB::statement("update operators_tasks set transport_type_id = service_type_id");
            Schema::table('operators_tasks', function (Blueprint $table) {
                $table->dropColumn('service_type_id');
            });
        }

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'payment_bank_id')) {
                $table->integer('payment_bank_id')
                    ->nullable()
                    ->unsigned()
                    ->after('payment_method');
            }

            if (!Schema::hasColumn('invoices', 'customer_balance')) {
                $table->decimal('customer_balance', 10,2)
                    ->nullable()
                    ->after('doc_total_credit');
            }

            if (!Schema::hasColumn('invoices', 'doc_total_balance')) {
                $table->decimal('doc_total_balance', 10,2)
                    ->nullable()
                    ->after('doc_total_credit');
            }

            if (!Schema::hasColumn('invoices', 'sort')) {
                $table->string('sort', 20)
                    ->nullable()
                    ->index()
                    ->after('created_by');
            }

            if (Schema::hasColumn('invoices', 'balance')) {
                $table->dropColumn('balance');
            }
        });


        //atualiza todas as faturas
        \DB::statement('update invoices set doc_total_debit  = doc_total where doc_type in ("invoice", "invoice-receipt", "simplified-invoice", "proforma-invoice", "internal-doc", "nodoc", "debit-note")');
        \DB::statement('update invoices set doc_total_credit = doc_total where doc_type in ("credit-note", "regularization", "receipt")');
        \DB::statement('update invoices set doc_total_credit = (doc_total*-1) where doc_type in ("invoice-receipt", "simplified-invoice")');
        \DB::statement('update invoices set doc_total_balance = COALESCE((COALESCE(doc_total_credit, 0) + COALESCE(doc_total_debit, 0)), 0)');
        \DB::statement('update invoices set is_settle=1 where doc_type="receipt" or doc_type="regularization"');
        

        \App\Models\Invoice::identifyRemovesDuplicates(true);


        if (!Schema::hasTable('bank_movements')) {
           
            Schema::create('bank_movements', function (Blueprint $table) {
                
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('source');
                $table->integer('company_id')->unsigned()->nullable();
                $table->string('target')->nullable();
                $table->integer('target_id')->nullable();
                $table->integer('bank_id')->unsigned()->nullable();
                $table->integer('payment_method_id')->unsigned()->nullable();
                $table->integer('customer_id')->unsigned()->nullable();
                $table->integer('provider_id')->unsigned()->nullable();
                $table->decimal('total', 10, 2);
                $table->date('date');

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('company_id')
                    ->references('id')
                    ->on('banks');

                $table->foreign('bank_id')
                    ->references('id')
                    ->on('banks');

                $table->foreign('payment_method_id')
                    ->references('id')
                    ->on('banks');

                $table->foreign('customer_id')
                    ->references('id')
                    ->on('customers');

                $table->foreign('provider_id')
                    ->references('id')
                    ->on('providers');
            });
        }


            
        Schema::table('permissions', function (Blueprint $table) {

            if (!Schema::hasColumn('permissions', 'subgroup_sort')) {
                $table->integer('subgroup_sort')
                    ->default(999)
                    ->after('group');
            }

            if (!Schema::hasColumn('permissions', 'subgroup')) {
                $table->string('subgroup', 255)
                    ->nullable()
                    ->after('group');
            }


            if (!Schema::hasColumn('permissions', 'group_sort')) {
                $table->integer('group_sort')
                    ->default(999)
                    ->after('group');
            }
        });


        $this->updatePermissions();


        if (env('DB_DATABASE_FLEET')) {
            Schema::connection('mysql_fleet')->table('fleet_vehicles', function (Blueprint $table) {
                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_vehicles', 'increase_roof')) {
                    $table->boolean('increase_roof')
                        ->nullable()
                        ->after('tires_others');
                }
            });
        }

        Schema::table('operators_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('operators_tasks', 'is_pickup')) {
                $table->boolean('is_pickup')
                    ->default(0)
                    ->after('transport_type_id');
            }
        });


        Setting::set('mobile_app_download_guide', 1);


        if(Setting::get('app_mode') == 'cargo') {

            Setting::set('mobile_app_download_cmr', 1);
            $source = config('app.source');
            

            if(IncidenceType::count() > 10) {
                IncidenceType::where('id', '>=', 1)->forceDelete();

                IncidenceType::insert([
                    [
                        'source'           => $source,
                        'name'             => 'Atraso de horário',
                        'name_en'          => 'Time delay',
                        'operator_visible' => 1,
                        'is_shipment'      => 1,
                        'is_pickup'        => 1,
                        'is_active'        => 1,
                        'sort'             => 1
                    ],
                    [
                        'source'           => $source,
                        'name'             => 'Avaria ou Acidente',
                        'name_en'          => 'Breakdown or accident',
                        'operator_visible' => 1,
                        'is_shipment'      => 1,
                        'is_pickup'        => 1,
                        'is_active'        => 1,
                        'sort'             => 2,
                    ],
                    [
                        'source'           => $source,
                        'name'             => 'Local Encerrado  ',
                        'name_en'          => 'Place Closed',
                        'operator_visible' => 1,
                        'is_shipment'      => 1,
                        'is_pickup'        => 1,
                        'is_active'        => 1,
                        'sort'             => 3,
                    ],
                    [
                        'source'           => $source,
                        'name'             => 'Carga excede dimensões/peso',
                        'name_en'          => 'Load exceeds weight or dimensions',
                        'operator_visible' => 1,
                        'is_shipment'      => 1,
                        'is_pickup'        => 1,
                        'is_active'        => 1,
                        'sort'             => 4,
                    ],
                    [
                        'source'           => $source,
                        'name'             => 'Carga danificada ',
                        'name_en'          => 'Load damaged',
                        'operator_visible' => 1,
                        'is_shipment'      => 1,
                        'is_pickup'        => 1,
                        'is_active'        => 1,
                        'sort'             => 5,
                    ],
                    [
                        'source'           => $source,
                        'name'             => 'Outros incidentes',
                        'name_en'          => 'Another incidents',
                        'operator_visible' => 1,
                        'is_shipment'      => 1,
                        'is_pickup'        => 1,
                        'is_active'        => 1,
                        'sort'             => 6,
                    ],

                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    public function updatePermissions() {

        Permission::whereIn('name',[
            'mass-update',
            'refunds_control',
            'payments_at_recipient',
            'operator-refunds-control',
            'express_services',
            'refunds_agencies',
            'support_tickets_manage',
            'support_tickets',
            'support_tickets_manage',
            'cashier_terminals',
            'cashier_central',
            'billing_agencies',
            'admin_users',
            'import_methods',
            'modules',
            'webservices_log',
            'log_errors',
            'news',
            'backups',
            'users_expenses',
            'products'
            ])->delete();

            Permission::where('name', 'customers_support')->update(['name' => 'customer_support']);
            Permission::where('name', 'cargo_planning')->update(['display_name' => 'Timeline', 'group' => 'Gestão de Recolhas e Expedições', 'subgroup' => '']);
            Permission::where('name', 'delivery_management')->update(['display_name' => 'Mapas Viagem/Distribuição']);
            Permission::where('group', 'Controlo Financeiro')->update(['group' => 'Tesouraria']);
            Permission::where('group', 'Módulo Produtos')->update(['group' => 'Tesouraria']);
            Permission::where('group', 'Pagamentos MB/Visa')->update(['group' => 'Tesouraria']);
            Permission::where('group', 'Mobile App')->update(['group' => 'Envios e Recolhas']);
            Permission::where('group', 'Envios e Recolhas')->update(['group' => 'Gestão de Recolhas e Expedições']);
            Permission::where('group', 'Módulo de Logística')->update(['group' => 'Armazenagem e Stocks']);
            Permission::where('name', 'events_management')->update(['group' => 'Outros Módulos']);
            Permission::where('name', 'calendar_events')->update(['group' => 'Outros Módulos']);
            Permission::where('name', 'cashier')->update(['display_name' => 'Caixa Diária', 'group' => 'Tesouraria']);
            Permission::where('name', 'products_sales')->update(['display_name' => 'Venda Produtos', 'group' => 'Tesouraria']);
            Permission::where('group', 'Arquivo de Ficheiros')->update(['group' => 'Outros Módulos']);
            Permission::where('group', 'Módulo de Apoio Cliente')->update(['group' => 'Outros Módulos']);
            Permission::where('group', 'Mapas e Localização')->update(['group' => 'Outros Módulos']);
            Permission::where('name', 'api')->update(['display_name' => 'Gerir chaves API', 'group' => 'Configurações']);
            Permission::where('group', 'Módulo de Gestão de Frota')->update(['group' => 'Gestão de Frota']);
            Permission::where('name', 'purchase_invoices')->update(['display_name' => 'Faturas Compra']);
            Permission::where('name', 'invoices')->update(['display_name' => 'Faturas Venda']);
            Permission::where('name', 'customer_covenants')->update(['display_name' => 'Avenças Mensais']);
            Permission::where('name', 'admin_translations')->update(['group' => 'Gerir Website', 'display_name' => 'Gestor traduções', 'module' => 'website_pages']);
            Permission::where('name', 'admin_settings')->update(['display_name' => 'Gerir Configurações Gerais']);
            Permission::where('name', 'webservices')->update(['display_name' => 'Webservices Globais']);
            Permission::where('name', 'banks')->update(['display_name' => 'Gerir Bancos', 'group' => 'Tesouraria', 'subgroup' => 'Configurações']);
            Permission::where('name', 'payment_methods')->update(['display_name' => 'Métodos Pagamento', 'group' => 'Tesouraria', 'subgroup' => 'Configurações']);
            Permission::where('name', 'payment_conditions')->update(['display_name' => 'Condições Pagamento', 'group' => 'Tesouraria', 'subgroup' => 'Configurações']);
            Permission::where('name', 'routes')->update(['display_name' => 'Rotas Recolha e Entrega','group' => 'Gestão de Recolhas e Expedições', 'subgroup' => 'Configurações']);
            Permission::where('name', 'vehicles')->update(['display_name' => 'Gerir Viaturas','group' => 'Gestão de Recolhas e Expedições', 'subgroup' => 'Configurações']);
            Permission::where('name', 'app')->update(['display_name' => 'App Mobile Motorista', 'subgroup' => 'App Mobile e Mapas']);
            Permission::where('name', 'maps')->update(['display_name' => 'Mapa e Localização GPS', 'group' => 'Gestão de Recolhas e Expedições','subgroup' => 'App Mobile e Mapas']);
            Permission::where('name', 'admin_roles')->update(['display_name' => 'Gerir Perís e Permissões']);
            Permission::where('name', 'edit_shipments')->update(['display_name' => 'Editar Serviços']);
            Permission::where('name', 'pickup_points')->update(['display_name' => 'Gerir Pontos PUDO', 'group' => 'Gestão de Recolhas e Expedições']);
            Permission::where('name', 'customer-messages')->update(['display_name' => 'Mensagens Massivas Clientes', 'group' => 'Notificações E-mail e SMS', 'subgroup' => 'Mensagens E-mail']);
            Permission::where('name', 'sms')->update(['group' => 'Notificações E-mail e SMS', 'subgroup' => 'Mensagens SMS']);
            Permission::where('name', 'sms_packs')->update(['group' => 'Notificações E-mail e SMS', 'subgroup' => 'Mensagens SMS']);
            Permission::where('name', 'notifications')->update(['display_name' => 'Ver alertas do sistema', 'group' => 'Notificações E-mail e SMS', 'subgroup' => 'Alertas e Notificações Sistema']);
            Permission::where('name', 'mailing_lists')->update(['display_name' => 'Listas de e-mails', 'group' => 'Notificações E-mail e SMS', 'subgroup' => 'Mensagens E-mail']);
            Permission::where('name', 'prospects')->update(['display_name' => 'Potenciais Clientes']);
            Permission::where('name', 'meetings')->update(['display_name' => 'Visitas e Reuniões']);
            Permission::where('name', 'email_accounts')->update(['display_name' => 'Criar/Gerir contas e-mail']);
            Permission::where('name', 'agencies')->update(['display_name' => 'Gerir Centros Logísticos']);
            Permission::where('name', 'companies')->update(['display_name' => 'Gerir Empresas Sistema']);
            Permission::where('name', 'zip_codes')->update(['display_name' => 'Zonas e Códigos Postais']);
            Permission::where('name', 'pack_types')->update(['display_name' => 'Tipos de Embalagem']);
            Permission::where('name', 'tracking_status')->update(['display_name' => 'Estados de Envio']);
            Permission::where('name', 'services')->update(['display_name' => 'Serviços de Transporte']);
            Permission::where('name', 'transport_types')->update(['subgroup' => 'Configurações']);
            Permission::where('name', 'shipments')->update(['display_name' => 'Ordens Carga/Envios Serviços']);
            Permission::where('name', 'export_shipments')->update(['display_name' => 'Exportar Serviços']);
            Permission::where('name', 'incidences')->update(['display_name' => 'Gestão Incidências']);
            Permission::where('name', 'devolutions')->update(['display_name' => 'Gestão Devoluções', 'group' => 'Notificações E-mail e SMS', 'subgroup' => '']);
            Permission::where('name', 'customer_support')->update(['display_name' => 'Suporte Cliente', 'group' => 'Entidades']);
            Permission::where('name', 'refunds_customers')->update(['display_name' => 'Gestão de Reembolsos', 'subgroup' => 'Portes e Reembolsos']);
            Permission::where('name', 'cod_control')->update(['display_name' => 'Gestão Portes Destino', 'subgroup' => 'Portes e Reembolsos']);
            Permission::where('name', 'refunds_operators')->update(['display_name' => 'Conf. Reembolsos Motorista', 'subgroup' => 'Portes e Reembolsos']);
            Permission::where('name', 'sepa_transfers')->update(['display_name' => 'Conf. Reembolsos Motorista', 'subgroup' => 'Pagamentos Automáticos']);
            Permission::where('name', 'gateway_payments')->update(['display_name' => 'Pagamentos Automáticos', 'subgroup' => 'Pagamentos Automáticos']);
            Permission::where('name', 'purchase_invoices')->update(['subgroup' => 'Faturação Vendas e Compras']);
            Permission::where('name', 'invoices')->update(['subgroup' => 'Faturação Vendas e Compras']);
            Permission::where('name', 'customers_balance')->update(['subgroup' => 'Faturação Vendas e Compras']);
            Permission::where('name', 'purchase_invoices')->update(['subgroup' => 'Faturação Vendas e Compras']);
            Permission::where('name', 'vat_rates')->update(['subgroup' => 'Configurações']);
            Permission::where('name', 'billing-zones')->update(['subgroup' => 'Configurações']);
            Permission::where('name', 'shipping_expenses')->update(['display_name' => 'Gerir Taxas Adicionais', 'subgroup' => 'Tabelas de Preço e Tarifários']);
            Permission::where('name', 'statistics')->update(['subgroup' => 'Faturação Vendas e Compras']);
            Permission::where('name', 'billing')->update(['subgroup' => 'Faturação Vendas e Compras']);
            Permission::where('name', 'billing_providers')->update(['subgroup' => 'Faturação Vendas e Compras']);
            Permission::where('name', 'customer_covenants')->update(['subgroup' => 'Tabelas de Preço e Tarifários']);
            Permission::where('name', 'prices_tables_view')->update(['subgroup' => 'Tabelas de Preço e Tarifários']);
            Permission::where('name', 'prices_tables')->update(['subgroup' => 'Tabelas de Preço e Tarifários']);
            Permission::where('name', 'prices_tables')->update(['subgroup' => 'Tabelas de Preço e Tarifários']);

            Permission::where('group', 'Gestão de Equipamentos')->update(['group' => 'Outros Módulos', 'subgroup' => 'Gestão de Equipamentos']);
            Permission::where('group', 'Módulo de Orçamentos')->update(['group' => 'Outros Módulos', 'subgroup' => 'Módulo de Orçamentos']);
            Permission::where('group', 'Cartas de Porte Aéreo')->update(['group' => 'Outros Módulos', 'subgroup' => 'Cartas de Porte Aéreo']);
            Permission::where('group', 'Gerir Website')->update(['group' => 'Outros Módulos', 'subgroup' => 'Gestão de Website']);

            Permission::where('name', 'licenses')->update(['display_name' => 'Alertas Licença ENOVO']);
            


            Permission::where('name', 'brands')->update([
                'display_name'  => 'Marcas e Modelos',
                'module'        => 'invoices',
                'group'         => 'Faturação',
                'subgroup'      => 'Configurações'
            ]);
            
        
        //configura subgrupos
        $arr = [
            'services',
            'incidences_types',
            'zip_codes',
            'tracking_status',
            'pack_types',
        ];

        Permission::whereIn('name', $arr)->update(['group' => 'Gestão de Recolhas e Expedições', 'subgroup' => 'Configurações']);
        
        if(!Permission::where('name', 'brands')->first()) {
            Permission::insert([
                'name'          => 'brands',
                'display_name'  => 'Marcas e Modelos',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'module'        => 'invoices',
                'group'         => 'Faturação',
                'subgroup'      => 'Emitir documentos Venda e Compra'
            ]);
        }


        if(!Permission::where('name', 'companies')->first()) {
            Permission::insert([
                'name'          => 'companies',
                'display_name'  => 'Gerir Empresas Sistema',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'module'        => 'base',
                'group'         => 'Configurações',
                'subgroup'      => ''
            ]);
        }

        if(!Permission::where('name', 'email_accounts')->first()) {
            Permission::insert([
                'name'          => 'email_accounts',
                'display_name'  => 'Gerir contas e-mail',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'module'        => 'base',
                'group'         => 'Notificações E-mail e SMS',
                'subgroup'      => 'Mensagens E-mail'
            ]);
        }

        if(!Permission::where('name', 'users_attendance')->first()) {
            Permission::insert([
                'name'          => 'users_attendance',
                'display_name'  => 'Gestão Horários Trabalho',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'module'        => 'base',
                'group'         => 'Entidades',
                'subgroup'      => 'Recursos Humanos'
            ]);
        }

        if(!Permission::where('name', 'emails')->first()) {
            Permission::insert([
                'name'          => 'emails',
                'display_name'  => 'Consultar envio E-mails',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'module'        => 'base',
                'group'         => 'Notificações E-mail e SMS',
                'subgroup'      => 'Envio de E-mail'
            ]);
        }

        if(!Permission::where('name', 'transport_types')->first()) {
            Permission::insert([
                'name'          => 'transport_types',
                'display_name'  => 'Tipos de Transporte',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'module'        => 'base',
                'group'         => 'Gestão de Recolhas e Expedições',
                'subgroup'      => 'Configurações'
            ]);
        }

        if(!Permission::where('name', 'bank_movements')->first()) {
            Permission::insert([
                'name'          => 'bank_movements',
                'display_name'  => 'Movimentos Bancários',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'module'        => 'treasury',
                'group'         => 'Tesouraria'
            ]);
        }

        if(!Permission::where('name', 'bank_reconciliation')->first()) {
            Permission::insert([
                'name'          => 'bank_reconciliation',
                'display_name'  => 'Reconsiliação Bancária',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'module'        => 'treasury',
                'group'         => 'Tesouraria'
            ]);
        }


        //Ordena grupos
        Permission::where('group', 'Entidades')->update(['group_sort' => '1']);
        Permission::where('group', 'Gestão de Recolhas e Expedições')->update(['group_sort' => '2']);
        Permission::where('group', 'Faturação')->update(['group_sort' => '3']);
        Permission::where('group', 'Tesouraria')->update(['group_sort' => '4']);
        Permission::where('group', 'Gestão de Frota')->update(['group_sort' => '5']);
        Permission::where('group', 'Armazenagem e Stocks')->update(['group_sort' => '6']);
        Permission::where('group', 'Notificações E-mail e SMS')->update(['group_sort' => '7']);
        Permission::where('group', 'Outros Módulos')->update(['group_sort' => '8']);
        Permission::where('group', 'Configurações')->update(['group' => 'Configurações Gerais','group_sort' => '12']);


        //ordena subgrupos
        Permission::where('group', 'Gestão de Recolhas e Expedições')
                ->where('subgroup', 'App Mobile e Mapas')->update(['subgroup_sort' => '2']);

                Permission::where('group', 'Gestão de Recolhas e Expedições')
                ->where('subgroup', '')->update(['subgroup_sort' => '1']);

        Permission::where('group', 'Gestão de Recolhas e Expedições')
                ->where('subgroup', 'Configurações')->update(['subgroup_sort' => '3']);




        
        Permission::where('group', 'Ferramentas de Administrador')->update(['group_sort' => '14']);
        
        $role = Role::filterSource()->findOrFail(2); //gerencia

        $permissions = Permission::whereNotIn('name',[
            'admin_users',
            'log_errors',
            'admin_translations',
            'import_methods',
            'api',
            'webservices_log',
            'news',
            'modules',
            'prices_tables_view'
            ])
            ->pluck('id')
            ->toArray();

        $role->perms()->sync($permissions);

        Role::where('name','financeiro')->update(['display_name' => 'Contabilidade']);
        Role::where('name','balcao')->update(['display_name' => 'Atendimento Balcão']);
        Role::where('name','plataformista')->update(['display_name' => 'Gestor Armazém']);


        //atualiza todas as contas correntes
        $customers = Customer::has('invoices')->get();
        foreach($customers as $customer) {
            $customer->updateBalance();
        }
    }
}
