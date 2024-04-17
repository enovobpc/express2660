<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePurchaseInvoicesTypesAddColor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_invoices_types', function (Blueprint $table) {

            if (!Schema::hasColumn('purchase_invoices_types', 'color')) {
                $table->string('color', 8)
                    ->after('name');
            }

            if (!Schema::hasColumn('purchase_invoices_types', 'target_type')) {
                $table->string('target_type', 50)
                    ->after('name');
            }
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('purchase_invoices', 'is_scheduled')) {
                $table->string('is_scheduled', 10)
                    ->nullable()
                    ->after('ignore_stats');
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
        Schema::table('purchase_invoices_types', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoices_types', 'color')) {
                $table->dropColumn('color');
            }

            if (Schema::hasColumn('purchase_invoices_types', 'target_type')) {
                $table->dropColumn('target_type');
            }
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoices', 'is_scheduled')) {
                $table->dropColumn('is_scheduled');
            }
        });
    }
}
