<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Branch2021100401 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('shipping_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('shipping_expenses', 'base_price')) {
                $table->text('base_price')
                    ->nullable()
                    ->after('trigger_value');
            }
        });

        Schema::table('webservices_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('webservices_configs', 'mapping_services')) {
                $table->text('mapping_services')
                    ->nullable()
                    ->after('endpoint');
            }
        });


        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'other_name')) {
                $table->text('other_name')
                    ->nullable()
                    ->after('obs');
            }
        });


        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'zones_provider')) {
                $table->text('zones_provider')
                    ->nullable()
                    ->after('zones');
            }

            if (!Schema::hasColumn('services', 'webservice_mapping')) {
                $table->text('webservice_mapping')
                    ->nullable()
                    ->after('matrix_arr');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'status_date')) {
                $table->timestamp('status_date')
                    ->nullable()
                    ->after('delivery_date');
            }

            if (!Schema::hasColumn('shipments', 'without_pickup')) {
                $table->boolean('without_pickup')
                    ->default(0)
                    ->after('incoterm');
            }

            if (!Schema::hasColumn('shipments', 'conferred_weight')) {
                $table->decimal('conferred_weight', 8, 2)
                    ->nullable()
                    ->after('customer_weight');
            }

            if (!Schema::hasColumn('shipments', 'conferred_volumes')) {
                $table->integer('conferred_volumes')
                    ->nullable()
                    ->after('volumes');
            }

            if (!Schema::hasColumn('shipments', 'operator_conferred_at')) {
                $table->timestamp('operator_conferred_at')
                    ->nullable()
                    ->after('devolution_conferred');
            }

            if (!Schema::hasColumn('shipments', 'customer_weight')) {
                $table->decimal('provider_weight', 8, 2)
                    ->nullable()
                    ->after('customer_weight');
            }

            if (!Schema::hasColumn('shipments', 'at_guide_key')) {
                $table->string('at_guide_key')
                    ->nullable()
                    ->after('invoice_key');
            }

            if (!Schema::hasColumn('shipments', 'at_guide_serie')) {
                $table->string('at_guide_serie')
                    ->nullable()
                    ->after('invoice_key');
            }

            if (!Schema::hasColumn('shipments', 'at_guide_codeat')) {
                $table->string('at_guide_codeat')
                    ->nullable()
                    ->after('invoice_key');
            }

            if (!Schema::hasColumn('shipments', 'cod_method')) {
                $table->string('cod_method')
                    ->nullable()
                    ->after('refund_method');
            }

            if (!Schema::hasColumn('shipments', 'at_guide_doc_id')) {
                $table->integer('at_guide_doc_id')
                    ->nullable()
                    ->after('invoice_key');
            }
        });

        DB::update('update shipments set status_date = (select created_at from shipments_history where shipment_id = shipments.id order by created_at desc limit 0,1)');

        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'fuel_tax')) {
                $table->decimal('fuel_tax', 10, 2)
                    ->nullable()
                    ->after('expenses_delivery');
            }
        });


        Schema::table('services_volumetric_factor', function (Blueprint $table) {
            if (!Schema::hasColumn('services_volumetric_factor', 'factor_provider')) {
                $table->decimal('factor_provider', 8, 2)
                    ->nullable()
                    ->after('factor');
            }
        });

        DB::update('update services_volumetric_factor set factor_provider = factor');


        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'settle_obs')) {
                $table->text('settle_obs')
                    ->nullable()
                    ->after('settle_method');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('invoices', 'assigned_invoice_id')) {
                $table->integer('assigned_invoice_id')
                    ->unsigned()
                    ->nullable()
                    ->after('settle_obs');
            }
        });

        Schema::table('pack_types', function (Blueprint $table) {

            if (!Schema::hasColumn('pack_types', 'length')) {
                $table->decimal('length', 10, 2)
                    ->nullable()
                    ->after('name');
            }

            if (!Schema::hasColumn('pack_types', 'height')) {
                $table->decimal('height', 10, 2)
                    ->nullable()
                    ->after('name');
            }

            if (!Schema::hasColumn('pack_types', 'width')) {
                $table->decimal('width', 10, 2)
                    ->nullable()
                    ->after('name');
            }

            if (!Schema::hasColumn('pack_types', 'weight')) {
                $table->decimal('weight', 10, 2)
                    ->nullable()
                    ->after('name');
            }

            if (!Schema::hasColumn('pack_types', 'description')) {
                $table->string('description')
                    ->nullable()
                    ->after('name');
            }
        });

        if (!Schema::hasTable('equipments_warehouses')) {
            Schema::create('equipments_warehouses', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('code', '25');
                $table->string('name')->nullable();
                $table->string('company')->nullable();
                $table->string('address')->nullable();
                $table->string('zip_code')->nullable();
                $table->string('city')->nullable();
                $table->string('country')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('mobile')->nullable();
                $table->string('responsable')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('equipments_locations')) {
            Schema::create('equipments_locations', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->integer('warehouse_id')->unsigned();
                $table->string('code')->nullable();
                $table->string('name')->nullable();
                $table->string('color', 15)->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('warehouse_id')
                    ->references('id')
                    ->on('equipments_warehouses');
            });
        }

        if (!Schema::hasTable('equipments_categories')) {
            Schema::create('equipments_categories', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('code')->nullable();
                $table->string('name')->nullable();
                $table->integer('sort')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('equipments')) {
            Schema::create('equipments', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('sku')->index();
                $table->string('name');
                $table->integer('customer_id')->unsigned()->nullable();
                $table->integer('category_id')->unsigned()->nullable();
                $table->integer('warehouse_id')->unsigned()->nullable();
                $table->integer('location_id')->unsigned()->nullable();
                $table->text('description');
                $table->string('serial_no');
                $table->string('lote');
                $table->decimal('width', 10, 2);
                $table->decimal('height', 10, 2);
                $table->decimal('length', 10, 2);
                $table->decimal('weight', 10, 2);
                $table->decimal('stock_total', 10, 2);
                $table->string('filepath');
                $table->string('filename');
                $table->integer('created_by')->unsigned()->nullable();
                $table->boolean('is_active')->default(1);
                $table->string('status')->index();
                $table->timestamp('last_update')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('customer_id')
                    ->references('id')
                    ->on('customers');

                $table->foreign('category_id')
                    ->references('id')
                    ->on('equipments_categories');

                $table->foreign('warehouse_id')
                    ->references('id')
                    ->on('equipments_warehouses');

                $table->foreign('location_id')
                    ->references('id')
                    ->on('equipments_locations');

                $table->foreign('created_by')
                    ->references('id')
                    ->on('users');
            });
        }

        if (!Schema::hasTable('equipments_history')) {
            Schema::create('equipments_history', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('action')->index();
                $table->integer('equipment_id')->unsigned();
                $table->integer('location_id')->unsigned()->nullable();
                $table->integer('operator_id')->unsigned()->nullable();
                $table->string('ot_code')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('equipment_id')
                    ->references('id')
                    ->on('equipments');

                $table->foreign('location_id')
                    ->references('id')
                    ->on('equipments_locations');

                $table->foreign('operator_id')
                    ->references('id')
                    ->on('users');
            });
        }


        //insere permissões
        $permissionsRoles = [
            [
                'name'          => 'equipments_warehouses',
                'display_name'  => 'Gerir Armazéns',
                'group'         => 'Gestão de Equipamentos',
                'module'        => 'equipments'
            ],
            [
                'name'          => 'equipments_locations',
                'display_name'  => 'Gerir Localizações',
                'group'         => 'Gestão de Equipamentos',
                'module'        => 'equipments'
            ],
            [
                'name'          => 'equipments',
                'display_name'  => 'Gerir Equipamentos',
                'group'         => 'Gestão de Equipamentos',
                'module'        => 'equipments'
            ],
            [
                'name'          => 'equipments_categories',
                'display_name'  => 'Gerir Categorias',
                'group'         => 'Gestão de Equipamentos',
                'module'        => 'equipments'
            ],
        ];

        foreach ($permissionsRoles as $permissionRole) {
            $permission = \App\Models\Permission::where('name', $permissionRole['name'])->first();

            if (!$permission) {
                $permission = new \App\Models\Permission();
                $permission->name         = $permissionRole['name'];
                $permission->display_name = $permissionRole['display_name'];
                $permission->group        = $permissionRole['group'];
                $permission->module       = $permissionRole['module'];
                $permission->save();
            }
        }

        //regista clientes na base de dados central
        $customers = \App\Models\Customer::whereNull('customer_id')->get();
        foreach ($customers as $customer) {
            $customer->storeOnCoreDB();
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
