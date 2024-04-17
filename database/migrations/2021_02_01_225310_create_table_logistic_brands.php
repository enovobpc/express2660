<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLogisticBrands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement("UPDATE shipping_status SET is_traceability = 0");
        DB::statement("UPDATE shipping_status SET is_traceability = 1 WHERE id IN (3,4,7,17,29)");
        try {
            DB::statement("ALTER TABLE customers_support_tickets CHANGE COLUMN attachments inline_attachments longtext DEFAULT NULL");
            DB::statement("ALTER TABLE customers_support_messages CHANGE COLUMN attachments inline_attachments longtext DEFAULT NULL");
        } catch (\Exception $e) {}

        if(env('DB_DATABASE_LOGISTIC')) {
            if (!Schema::connection('mysql_logistic')->hasTable('brands')) {
                Schema::connection('mysql_logistic')->create('brands', function (Blueprint $table) {

                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('source')->index();
                    $table->string('name');
                    $table->integer('customer_id')->unsigned()->nullable();
                    $table->smallInteger('sort');
                    $table->timestamps();
                    $table->softDeletes();
                });
            }

            if (!Schema::connection('mysql_logistic')->hasTable('models')) {
                Schema::connection('mysql_logistic')->create('models', function (Blueprint $table) {

                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('source')->index();
                    $table->string('name');
                    $table->integer('customer_id')->unsigned()->nullable();
                    $table->integer('brand_id')->unsigned()->nullable();
                    $table->smallInteger('sort');
                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('brand_id')
                        ->references('id')
                        ->on('brands');
                });
            }

            if (!Schema::connection('mysql_logistic')->hasTable('families')) {
                Schema::connection('mysql_logistic')->create('families', function (Blueprint $table) {

                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('source')->index();
                    $table->string('name');
                    $table->integer('customer_id')->unsigned()->nullable();
                    $table->smallInteger('sort');
                    $table->timestamps();
                    $table->softDeletes();
                });
            }

            if (!Schema::connection('mysql_logistic')->hasTable('categories')) {
                Schema::connection('mysql_logistic')->create('categories', function (Blueprint $table) {

                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('source')->index();
                    $table->string('name');
                    $table->integer('customer_id')->unsigned()->nullable();
                    $table->integer('family_id')->unsigned()->nullable();
                    $table->smallInteger('sort');
                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('family_id')
                        ->references('id')
                        ->on('families');
                });
            }

            if (!Schema::connection('mysql_logistic')->hasTable('subcategories')) {
                Schema::connection('mysql_logistic')->create('subcategories', function (Blueprint $table) {

                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('source')->index();
                    $table->string('name');
                    $table->integer('customer_id')->unsigned()->nullable();
                    $table->integer('category_id')->unsigned()->nullable();
                    $table->smallInteger('sort');
                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('category_id')
                        ->references('id')
                        ->on('categories');
                });
            }

            if (Schema::connection('mysql_logistic')->hasTable('products')) {


                Schema::connection('mysql_logistic')->table('products', function (Blueprint $table) {



                    /*$table->dropForeign(['category_id']);
                    $table->dropForeign(['subcategory_id']);
                    $table->dropForeign(['family_id']);
                    $table->dropForeign(['brand_id']);*/

                    if (Schema::connection('mysql_logistic')->hasColumn('products', 'category_name')) {
                        $table->dropColumn('category_name');
                    }

                    if (Schema::connection('mysql_logistic')->hasColumn('products', 'category_id')) {
                        $table->dropColumn('category_id');
                    }

                    if (Schema::connection('mysql_logistic')->hasColumn('products', 'subcategory_name')) {
                        $table->dropColumn('subcategory_name');
                    }

                    if (Schema::connection('mysql_logistic')->hasColumn('products', 'subcategory_id')) {
                        $table->dropColumn('subcategory_id');
                    }

                    if (Schema::connection('mysql_logistic')->hasColumn('products', 'family_name')) {
                        $table->dropColumn('family_name');
                    }

                    if (Schema::connection('mysql_logistic')->hasColumn('products', 'family_id')) {
                        $table->dropColumn('family_id');
                    }

                    if (Schema::connection('mysql_logistic')->hasColumn('products', 'brand_name')) {
                        $table->dropColumn('brand_name');
                    }

                    if (Schema::connection('mysql_logistic')->hasColumn('products', 'brand_id')) {
                        $table->dropColumn('brand_id');
                    }

                    if (Schema::connection('mysql_logistic')->hasColumn('products', 'model_name')) {
                        $table->dropColumn('model_name');
                    }
                });

                Schema::connection('mysql_logistic')->table('products', function (Blueprint $table) {
                    if (!Schema::connection('mysql_logistic')->hasColumn('products', 'subcategory_id')) {
                        $table->integer('subcategory_id')
                            ->unsigned()
                            ->index()
                            ->nullable()
                            ->after('filename');

                        $table->foreign('subcategory_id')
                            ->references('id')
                            ->on('subcategories');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('products', 'category_id')) {
                        $table->integer('category_id')
                            ->unsigned()
                            ->index()
                            ->nullable()
                            ->after('filename');

                        $table->foreign('category_id')
                            ->references('id')
                            ->on('categories');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('products', 'family_id')) {
                        $table->integer('family_id')
                            ->unsigned()
                            ->index()
                            ->nullable()
                            ->after('filename');

                        $table->foreign('family_id')
                            ->references('id')
                            ->on('families');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('products', 'model_id')) {
                        $table->integer('model_id')
                            ->unsigned()
                            ->index()
                            ->nullable()
                            ->after('filename');

                        $table->foreign('model_id')
                            ->references('id')
                            ->on('models');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('products', 'brand_id')) {
                        $table->integer('brand_id')
                            ->unsigned()
                            ->index()
                            ->nullable()
                            ->after('filename');

                        $table->foreign('brand_id')
                            ->references('id')
                            ->on('brands');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('products', 'is_obsolete')) {
                        $table->boolean('is_obsolete')
                            ->default(0)
                            ->after('obs');
                    }
                });

                Schema::connection('mysql_logistic')->table('products_history', function (Blueprint $table) {
                    if (!Schema::connection('mysql_logistic')->hasColumn('products_history', 'document_id')) {
                        $table->integer('document_id')
                            ->unsigned()
                            ->index()
                            ->nullable()
                            ->after('document');
                    }
                });

                Schema::connection('mysql_logistic')->table('shipping_orders_lines', function (Blueprint $table) {
                    if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders_lines', 'qty_satisfied')) {
                        $table->integer('qty_satisfied')
                            ->nullable()
                            ->after('qty');
                    }
                });

                Schema::connection('mysql_logistic')->table('shipping_orders', function (Blueprint $table) {
                    if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders', 'qty_satisfied')) {
                        $table->integer('qty_satisfied')
                            ->nullable()
                            ->after('total_qty');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders', 'qty_total')) {
                        $table->integer('qty_total')
                            ->nullable()
                            ->after('total_qty');
                    }
                });

                DB::connection('mysql_logistic')->statement("UPDATE shipping_orders SET qty_total = total_qty");
                DB::connection('mysql_logistic')->statement("ALTER TABLE shipping_orders CHANGE COLUMN user_id user_id int(10) unsigned DEFAULT NULL");

                Schema::connection('mysql_logistic')->table('shipping_orders', function (Blueprint $table) {
                    if (Schema::connection('mysql_logistic')->hasColumn('shipping_orders', 'total_qty')) {
                        $table->dropColumn('total_qty');
                    }
                });
            }
        }

        \App\Models\Permission::where('name', 'logistic_exit_orders')->update(['name' => 'logistic_shipping_orders']);

        $exists = \App\Models\Permission::where(['name' => 'logistic_reception_orders'])->first();
        if(!$exists) {
            \App\Models\Permission::insert([
                'name'          => 'logistic_reception_orders',
                'display_name'  => 'Ordens de Recepção',
                'group'         => 'Módulo de Logística',
                'module'        => 'logistic',
                'created_at'    => date('Y-m-d')
            ]);
        }

        $exists = \App\Models\Permission::where(['name' => 'logistic_maps'])->first();
        if(!$exists) {
            \App\Models\Permission::insert([
                    'name'          => 'logistic_maps',
                    'display_name'  => 'Mapa de Armazém',
                    'group'         => 'Módulo de Logística',
                    'module'        => 'logistic',
                    'created_at'    => date('Y-m-d')
            ]);
        }

        $exists = \App\Models\Permission::where(['name' => 'logistic_brands'])->first();
        if(!$exists) {
            \App\Models\Permission::insert([
                    'name'          => 'logistic_brands',
                    'display_name'  => 'Marcas e Categorias',
                    'group'         => 'Módulo de Logística',
                    'module'        => 'logistic',
                    'created_at'    => date('Y-m-d')
            ]);
        }

        $exists = \App\Models\Permission::where(['name' => 'logistic_devolutions'])->first();
        if(!$exists) {
            \App\Models\Permission::insert([
                'name'          => 'logistic_devolutions',
                'display_name'  => 'Devoluções',
                'group'         => 'Módulo de Logística',
                'module'        => 'logistic',
                'created_at'    => date('Y-m-d')
            ]);
        }

        $exists = \App\Models\Permission::where(['name' => 'logistic_inventories'])->first();
        if(!$exists) {
            \App\Models\Permission::insert([
                'name'          => 'logistic_inventories',
                'display_name'  => 'Inventários',
                'group'         => 'Módulo de Logística',
                'module'        => 'logistic',
                'created_at'    => date('Y-m-d')
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_logistic')->dropIfExists('subcategories');
        Schema::connection('mysql_logistic')->dropIfExists('categories');
        Schema::connection('mysql_logistic')->dropIfExists('families');
        Schema::connection('mysql_logistic')->dropIfExists('models');
        Schema::connection('mysql_logistic')->dropIfExists('brands');
    }
}
