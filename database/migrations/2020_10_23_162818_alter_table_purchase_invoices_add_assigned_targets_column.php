<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePurchaseInvoicesAddAssignedTargetsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoices', 'assigned_targets')) {
                $table->text('assigned_targets')
                    ->nullable()
                    ->after('target_id');
            }
        });

        Schema::table('invoices_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices_lines', 'assigned_invoice_id')) {
                $table->integer('assigned_invoice_id')
                    ->nullable()
                    ->after('invoice_id');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'doc_total_pending')) {
                $table->decimal('doc_total_pending', 10, 2)
                    ->nullable()
                    ->after('doc_total');
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
        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoices', 'assigned_targets')) {
                $table->dropColumn('assigned_targets');
            }
        });

        Schema::table('invoices_lines', function (Blueprint $table) {
            if (Schema::hasColumn('invoices_lines', 'assigned_invoice_id')) {
                $table->dropColumn('assigned_invoice_id');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'doc_total_pending')) {
                $table->dropColumn('doc_total_pending');
            }
        });
    }
}
