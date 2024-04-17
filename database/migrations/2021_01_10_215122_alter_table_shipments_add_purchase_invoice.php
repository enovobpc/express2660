<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableShipmentsAddPurchaseInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'purchase_invoice_id')) {
                $table->integer('purchase_invoice_id')
                    ->unsigned()
                    ->index()
                    ->nullable()
                    ->after('invoice_key');

                $table->foreign('purchase_invoice_id')
                    ->references('id')
                    ->on('purchase_invoices');
            }


            if (!Schema::hasColumn('shipments', 'dispatcher_id')) {
                $table->integer('dispatcher_id')
                    ->unsigned()
                    ->index()
                    ->nullable()
                    ->after('operator_id');

                $table->foreign('dispatcher_id')
                    ->references('id')
                    ->on('users');
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
        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'purchase_invoice_id')) {
                $table->dropColumn('purchase_invoice_id');
            }

            if (Schema::hasColumn('shipments', 'dispatcher_id')) {
                $table->dropColumn('dispatcher_id');
            }
        });
    }
}
