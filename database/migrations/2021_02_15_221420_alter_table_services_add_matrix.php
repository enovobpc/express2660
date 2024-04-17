<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableServicesAddMatrix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'session_id')) {
                $table->string('session_id')
                    ->nullable()
                    ->after('ip');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'matrix_arr')) {
                $table->longText('matrix_arr')
                    ->nullable()
                    ->after('week_days');
            }

            if (!Schema::hasColumn('services', 'matrix_zones')) {
                $table->string('matrix_zones', 1000)
                    ->nullable()
                    ->after('week_days');
            }

            if (!Schema::hasColumn('services', 'matrix_to')) {
                $table->longText('matrix_to')
                    ->nullable()
                    ->after('week_days');
            }

            if (!Schema::hasColumn('services', 'matrix_from')) {
                $table->longText('matrix_from')
                    ->nullable()
                    ->after('week_days');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'goods_price')) {
                $table->decimal('goods_price', 10, 2)
                    ->nullable()
                    ->after('charge_price');
            }

            if (!Schema::hasColumn('shipments', 'incoterm')) {
                $table->string('incoterm', 50)
                    ->nullable()
                    ->after('packaging_type');
            }
        });

        Schema::table('customers_recipients', function (Blueprint $table) {

            if (!Schema::hasColumn('customers_recipients', 'always_cod')) {
                $table->boolean('always_cod')
                    ->default(0)
                    ->after('obs');
            }
        });

        DB::statement("ALTER TABLE services CHANGE COLUMN unity unity ENUM('weight','volume','internacional','m3','pallet','km','hours','services','ldm','advalor') DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'session_id')) {
                $table->dropColumn('session_id');
            }
        });
    }
}
