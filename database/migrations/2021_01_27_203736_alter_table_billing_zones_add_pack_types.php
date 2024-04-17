<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBillingZonesAddPackTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'ldm')) {
                $table->decimal('ldm', 10, 2)
                    ->nullable()
                    ->after('kms');
            }
        });

        DB::statement("ALTER TABLE services CHANGE COLUMN unity unity ENUM('weight','volume','internacional','m3','pallet','km','hours','ldm') DEFAULT NULL");


        Schema::table('pack_types', function (Blueprint $table) {
            if (!Schema::hasColumn('pack_types', 'is_active')) {
                $table->boolean('is_active')
                    ->default(1)
                    ->after('name');
            }
        });

        Schema::table('billing_zones', function (Blueprint $table) {
            DB::statement("ALTER TABLE billing_zones CHANGE COLUMN unity unity ENUM('country', 'zip_code', 'route', 'distance', 'pack_type') DEFAULT NULL");
        });

        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'customer_id')) {
                $table->integer('customer_id')
                    ->unsigned()
                    ->nullable()
                    ->after('category_id');

                $table->foreign('customer_id')
                    ->references('id')
                    ->on('customers');
            }
        });


        Schema::table('customers_assigned_services', function (Blueprint $table) {
            if (!Schema::hasColumn('customers_assigned_services', 'adicional_unity')) {
                $table->decimal('adicional_unity', 10, 2)
                    ->nullable()
                    ->after('price');
            }

            if (!Schema::hasColumn('customers_assigned_services', 'is_adicional')) {
                $table->boolean('is_adicional')
                    ->default(0)
                    ->after('price');
            }
        });

        Schema::table('providers_assigned_services', function (Blueprint $table) {
            if (!Schema::hasColumn('providers_assigned_services', 'adicional_unity')) {
                $table->decimal('adicional_unity', 10, 2)
                    ->nullable()
                    ->after('price');
            }

            if (!Schema::hasColumn('providers_assigned_services', 'is_adicional')) {
                $table->boolean('is_adicional')
                    ->default(0)
                    ->after('price');
            }
        });

        DB::statement("UPDATE customers_assigned_services SET adicional_unity = 1, is_adicional = 1 WHERE max >= 99999");
        DB::statement("UPDATE providers_assigned_services SET adicional_unity = 1, is_adicional = 1 WHERE max >= 99999");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_zones', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'purchase_invoice_id')) {
                $table->dropColumn('purchase_invoice_id');
            }

            if (Schema::hasColumn('shipments', 'dispatcher_id')) {
                $table->dropColumn('dispatcher_id');
            }
        });
    }
}
