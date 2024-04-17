<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Branch2022112107 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'billing_discount_value')) {
                $table->decimal('billing_discount_value', 10, 2)
                    ->nullable()
                    ->after('billing_reference');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'delivery_manifest_id')) {
                $table->integer('delivery_manifest_id')
                    ->unsigned()
                    ->nullable()
                    ->after('pickup_route_id')
                    ->index();
            }

            if (!Schema::hasColumn('shipments', 'is_insured')) {
                $table->boolean('is_insured')
                    ->nullable()
                    ->after('total_expenses_cost');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'delivery_manifest_code')) {
                $table->string('delivery_manifest_code', 15)
                    ->nullable()
                    ->after('delivery_manifest_id');
            }
        });

        if (env('DB_DATABASE_FLEET')) {
            Schema::connection('mysql_fleet')->table('fleet_vehicles', function (Blueprint $table) {
                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_vehicles', 'tachograph_date')) {
                    $table->date('tachograph_date')
                        ->nullable()
                        ->after('iuc_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->removeColumn('billing_discount_value');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->removeColumn('delivery_manifest_id');
        });
    }
}
