<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableInvoicesAddAssignedReceiptColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'assigned_receipt')) {
                $table->string('assigned_receipt')
                    ->nullable()
                    ->after('doc_series_id');
            }
        });

        $balanceRows = \App\Models\CustomerBalance::whereNotNull('assigned_receipt')->get();
        foreach ($balanceRows as $balanceRow) {

            $invoice = \App\Models\Invoice::where('customer_id', $balanceRow->customer_id)
                ->where('doc_id', $balanceRow->doc_id)
                ->where('doc_series_id', $balanceRow->doc_serie_id)
                ->first();

            if($invoice) {
                $invoice->assigned_receipt = $balanceRow->assigned_receipt;
                $invoice->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'assigned_receipt')) {
                $table->dropColumn('assigned_receipt');
            }
        });
    }
}
