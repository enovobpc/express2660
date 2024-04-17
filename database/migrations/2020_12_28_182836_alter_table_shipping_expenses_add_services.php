<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableShippingExpensesAddServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipping_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('shipping_expenses', 'trigger_value')) {
                $table->decimal('trigger_value', 10, 2)
                    ->nullable()
                    ->after('trigger_arr');
            }

            if (!Schema::hasColumn('shipping_expenses', 'trigger_services')) {
                $table->string('trigger_services')
                    ->nullable()
                    ->after('trigger_arr');
            }

            if (!Schema::hasColumn('shipping_expenses', 'provider_code')) {
                $table->string('provider_code')
                    ->nullable()
                    ->after('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipping_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_expenses', 'trigger_value')) {
                $table->dropColumn('trigger_value');
            }

            if (Schema::hasColumn('shipping_expenses', 'trigger_services')) {
                $table->dropColumn('trigger_services');
            }

            if (Schema::hasColumn('shipping_expenses', 'provider_code')) {
                $table->dropColumn('provider_code');
            }
        });
    }
}
