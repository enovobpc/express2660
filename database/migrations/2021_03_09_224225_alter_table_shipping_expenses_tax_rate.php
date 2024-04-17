<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableShippingExpensesTaxRate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments_assigned_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments_assigned_expenses', 'tax_rate')) {
                $table->string('tax_rate', 5)
                    ->nullable()
                    ->after('cost_price');
            }
        });

        Schema::table('shipping_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('shipping_expenses', 'tax_rate')) {
                $table->string('tax_rate', 5)
                    ->nullable()
                    ->after('unity');
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
        //
    }
}
