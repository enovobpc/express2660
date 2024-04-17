<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablesAddAssignedInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_fleet')->table('fleet_fuel_log', function (Blueprint $table) {
            if (!Schema::connection('mysql_fleet')->hasColumn('fleet_fuel_log', 'assigned_invoice_id')) {
                $table->integer('assigned_invoice_id')
                    ->unsigned()
                    ->nullable()
                    ->after('provider_id');
            }
        });

        Schema::connection('mysql_fleet')->table('fleet_maintenances', function (Blueprint $table) {
            if (!Schema::connection('mysql_fleet')->hasColumn('fleet_maintenances', 'assigned_invoice_id')) {
                $table->integer('assigned_invoice_id')
                    ->unsigned()
                    ->nullable()
                    ->after('provider_id');
            }
        });

        Schema::connection('mysql_fleet')->table('fleet_expenses', function (Blueprint $table) {
            if (!Schema::connection('mysql_fleet')->hasColumn('fleet_expenses', 'assigned_invoice_id')) {
                $table->integer('assigned_invoice_id')
                    ->unsigned()
                    ->nullable()
                    ->after('provider_id');
            };

            if (!Schema::connection('mysql_fleet')->hasColumn('fleet_expenses', 'type_id')) {
                $table->integer('type_id')
                    ->unsigned()
                    ->nullable()
                    ->after('operator_id');

                $table->dropColumn('type');
            };

        });

        Schema::connection('mysql_fleet')->table('fleet_costs', function (Blueprint $table) {
            if (!Schema::connection('mysql_fleet')->hasColumn('fleet_costs', 'type_id')) {
                $table->integer('type_id')
                    ->unsigned()
                    ->nullable()
                    ->after('type');
            }
        });

        \App\Models\FleetGest\Cost::filterSource()
            ->where('type', 'gas_station')
            ->update(['type_id' => 1]);

        \App\Models\FleetGest\Cost::filterSource()
            ->where('type', 'maintenance')
            ->update(['type_id' => 2]);

        \App\Models\FleetGest\Cost::filterSource()
            ->where('type', 'renting')
            ->update(['type_id' => 5]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_fleet')->table('fleet_costs', function (Blueprint $table) {
            if (Schema::hasColumn('fleet_costs', 'assigned_invoice_id')) {
                $table->dropColumn('assigned_invoice_id');
            }
        });

        Schema::connection('mysql_fleet')->table('fleet_fuel_log', function (Blueprint $table) {
            if (Schema::hasColumn('fleet_fuel_log', 'assigned_invoice_id')) {
                $table->dropColumn('assigned_invoice_id');
            }
        });

        Schema::connection('mysql_fleet')->table('fleet_maintenances', function (Blueprint $table) {
            if (Schema::hasColumn('fleet_maintenances', 'assigned_invoice_id')) {
                $table->dropColumn('assigned_invoice_id');
            }
        });

        Schema::connection('mysql_fleet')->table('fleet_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('fleet_expenses', 'assigned_invoice_id')) {
                $table->dropColumn('assigned_invoice_id');
            }
        });
    }
}
