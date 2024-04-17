<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Branch20220426013 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //ATUALIZAR ESTADOS E CONFIGURAR DEFINICOES GERAIS CONFORME


        //update status
        $status = \App\Models\ShippingStatus::where('id', '37')->first();
        if($status) {
            $status->name = 'Atribuido Motorista';
            $status->save();
        }

        $status = \App\Models\ShippingStatus::where('id', '20')->first();
        if($status) {
            $status->name  = 'Aceite Motorista';
            $status->color = '#0D47A1';
            $status->save();
        }

        //update permissions
        \App\Models\Permission::whereIn('name', ['hidden_billing_chart','operator-interface'])->forceDelete();

        \App\Models\Permission::where('name', 'events_management')
            ->update(['group' => 'Módulo Equipamentos', 'module' => 'events']);

        \App\Models\Permission::where('name', 'logistic_exit_orders')
            ->update(['name' => 'logistic_shipping_orders']);

        $permission = \App\Models\Permission::where('name', 'logistic_reception_orders')->first();
        if(!$permission) {
            \App\Models\Permission::insert([
                'name'         => 'logistic_reception_orders',
                'display_name' => 'Ordens de Recepção',
                'group'        => 'Módulo de Logística',
                'module'       => 'logistic',
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }

        $permission = \App\Models\Permission::where('name', 'logistic_devolutions')->first();
        if(!$permission) {
            \App\Models\Permission::insert([
                'name'         => 'logistic_devolutions',
                'display_name' => 'Devoluções',
                'group'        => 'Módulo de Logística',
                'module'       => 'logistic',
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }

        $permission = \App\Models\Permission::where('name', 'logistic_inventories')->first();
        if(!$permission) {
            \App\Models\Permission::insert([
                'name'         => 'logistic_inventories',
                'display_name' => 'Inventários',
                'group'        => 'Módulo de Logística',
                'module'       => 'logistic',
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }

        $permission = \App\Models\Permission::where('name', 'allowances')->first();
        if(!$permission) {
            \App\Models\Permission::insert([
                'name'         => 'allowances',
                'display_name' => 'Ajudas de Custo',
                'group'        => 'Controlo Financeiro',
                'module'       => 'allowances',
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        }


        Schema::table('shipments', function (Blueprint $table) {

            if (!Schema::hasColumn('shipments', 'pickup_route_id')) {
                $table->integer('pickup_route_id')
                    ->unsigned()
                    ->nullable()
                    ->after('dispatcher_id');
            }


            if (!Schema::hasColumn('shipments', 'inbound_date')) {
                $table->timestamp('inbound_date')
                    ->nullable()
                    ->after('pickuped_date');
            }

            if (!Schema::hasColumn('shipments', 'end_hour_pickup')) {
                $table->string('end_hour_pickup', 5)
                    ->nullable()
                    ->after('end_hour');
            }

            if (!Schema::hasColumn('shipments', 'start_hour_pickup')) {
                $table->string('start_hour_pickup', 5)
                    ->nullable()
                    ->after('end_hour');
            }

        });

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'delivery_manifest_id')) {
                $table->integer('delivery_manifest_id')
                    ->unsigned()
                    ->nullable()
                    ->after('pickup_route_id');

                $table->foreign('delivery_manifest_id')
                    ->references('id')
                    ->on('delivery_manifests');

            }
        });

        Schema::table('services', function (Blueprint $table) {

            if (!Schema::hasColumn('services', 'price_per_pack')) {
                $table->boolean('price_per_pack')
                    ->default(0)
                    ->after('price_per_volume');
            }
        });


        Schema::table('delivery_manifests', function (Blueprint $table) {

            if (!Schema::hasColumn('delivery_manifests', 'obs')) {
                $table->text('obs')
                    ->after('auxiliar_id');
            }

            if (!Schema::hasColumn('delivery_manifests', 'assistants')) {
                $table->string('assistants', 500)
                    ->nullable()
                    ->after('auxiliar_id');
            }

            if (!Schema::hasColumn('delivery_manifests', 'is_internacional')) {
                $table->boolean('is_internacional')
                    ->default(0)
                    ->after('end_kms');
            }

            if (!Schema::hasColumn('delivery_manifests', 'is_spain')) {
                $table->boolean('is_spain')
                    ->default(0)
                    ->after('end_kms');
            }

            if (!Schema::hasColumn('delivery_manifests', 'is_nacional')) {
                $table->boolean('is_nacional')
                    ->nullable()
                    ->default(1)
                    ->after('end_kms');
            }

            if (!Schema::hasColumn('delivery_manifests', 'start_country')) {
                $table->string('start_country', 5)
                    ->nullable()
                    ->after('start_location');
            }

            if (!Schema::hasColumn('delivery_manifests', 'end_country')) {
                $table->string('end_country', 5)
                    ->nullable()
                    ->after('end_location');
            }

            if (!Schema::hasColumn('delivery_manifests', 'kms')) {
                $table->decimal('kms', 10,2)
                    ->nullable()
                    ->after('end_kms');
            }
        });

        Schema::table('delivery_manifests', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_manifests', 'auxiliar_id')) {
                $table->dropForeign('delivery_manifests_auxiliar_id_foreign');
                $table->dropColumn('auxiliar_id');
            }
        });

        \DB::table('delivery_manifests')->where('id', '>=', 1)->update(['start_country' => 'pt', 'end_country' => 'pt']);
        //\App\Models\Trip\Trip::where('id', '>=', 1)->update(['start_country' => 'pt', 'end_country' => 'pt']);


        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'resignation_date')) {
                $table->date('resignation_date')
                    ->nullable()
                    ->after('admission_date');
            }
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('purchase_invoices', 'code')) {
                $table->string('code', 12)
                    ->nullable()
                    ->index()
                    ->after('description');
            }
        });

        $invoices = \App\Models\PurchaseInvoice::orderBy('id', 'asc')->get();
        foreach ($invoices as $invoice) {
            $invoice->setCode();
        }

        Schema::table('purchase_payment_notes', function (Blueprint $table) {

            if (!Schema::hasColumn('purchase_payment_notes', 'reference')) {
                $table->string('reference', 255)
                    ->nullable()
                    ->after('code');
            }
        });

        Schema::table('cashier_movements', function (Blueprint $table) {

            if (!Schema::hasColumn('cashier_movements', 'type_id')) {
                $table->integer('type_id')
                    ->unsigned()
                    ->nullable()
                    ->after('source');

                $table->foreign('type_id')
                    ->references('id')
                    ->on('purchase_invoices_types');
            }

            if (!Schema::hasColumn('cashier_movements', 'created_by')) {
                $table->integer('created_by')
                    ->unsigned()
                    ->nullable()
                    ->after('obs');

                $table->foreign('created_by')
                    ->references('id')
                    ->on('users');
            }
        });

        \DB::statement("ALTER TABLE cashier_movements MODIFY payment_method VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL");
        \DB::statement("UPDATE cashier_movements SET created_by = operator_id where created_by is null");


        Schema::table('cashier_movements', function (Blueprint $table) {
            if (Schema::hasColumn('cashier_movements', 'operator_id')) {
                $table->dropForeign('cashier_movements_operator_id_foreign');
                $table->dropColumn('operator_id');
            }
        });

        Schema::table('cashier_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('cashier_movements', 'operator_id')) {
                $table->integer('operator_id')
                    ->unsigned()
                    ->nullable()
                    ->after('customer_id');

                $table->foreign('operator_id')
                    ->references('id')
                    ->on('users');
            }
        });

        if (!Schema::hasTable('allowances')) {
            Schema::create('allowances', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->integer('year')->index();
                $table->integer('month')->index();
                $table->integer('operator_id')->unsigned();
                $table->longtext('shipments');
                $table->longtext('delivery_manifests');
                $table->decimal('allowance_price', 10, 2)->nullable();
                $table->decimal('weekend_price', 10, 2)->nullable();
                $table->decimal('total_price', 10, 2)->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('operator_id')
                    ->references('id')
                    ->on('users');
            });
        }

        if(env('DB_DATABASE_FLEET')) {

            Schema::connection('mysql_fleet')->table('fleet_parts', function (Blueprint $table) {

                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_parts', 'reference')) {
                    $table->string('reference', 255)
                        ->nullable()
                        ->after('category');
                }

                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_parts', 'provider_id')) {
                    $table->integer('provider_id')
                        ->unsigned()
                        ->nullable()
                        ->after('category');
                }

                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_parts', 'purchase_invoice')) {
                    $table->string('purchase_invoice', 255)
                        ->nullable()
                        ->after('name');
                }

                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_parts', 'cost_price')) {
                    $table->decimal('cost_price', 10, 2)
                        ->nullable()
                        ->after('name');
                }

                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_parts', 'stock_total')) {
                    $table->decimal('stock_total', 10, 2)
                        ->nullable()
                        ->after('name');
                }

                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_parts', 'brand_name')) {
                    $table->string('brand_name', 255)
                        ->nullable()
                        ->after('name');
                }

                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_parts', 'obs')) {
                    $table->text('obs')
                        ->nullable()
                        ->after('source_owner');
                }
            });

            \DB::connection('mysql_fleet')->statement("UPDATE fleet_vehicles SET iuc_date=null where type='trailer'");
            \DB::connection('mysql_fleet')->statement("ALTER TABLE fleet_vehicles MODIFY type VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL");


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
}
