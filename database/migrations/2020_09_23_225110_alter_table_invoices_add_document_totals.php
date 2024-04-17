<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableInvoicesAddDocumentTotals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('invoices', 'doc_total')) {
                $table->decimal('doc_total',10,2)
                    ->nullable()
                    ->after('total_discount');
            }

            if (!Schema::hasColumn('invoices', 'doc_vat')) {
                $table->decimal('doc_vat',10,2)
                    ->nullable()
                    ->after('total_discount');
            }

            if (!Schema::hasColumn('invoices', 'doc_subtotal')) {
                $table->decimal('doc_subtotal',10,2)
                    ->nullable()
                    ->after('total_discount');
            }
        });


        $invoices = \App\Models\Invoice::with('lines')->get();
        foreach ($invoices as $invoice) {

            $docTotal = $docSubtotal = $docVat = 0;
            if($invoice->lines) {
                foreach ($invoice->lines as $line) {

                    $vat = $line->subtotal * ($line->tax_rate/100);

                    $docSubtotal+= $line->subtotal;
                    $docVat+= $vat;
                    $docTotal+= $line->subtotal + $vat;
                }
            }

            $invoice->doc_subtotal = $docSubtotal;
            $invoice->doc_vat = $docVat;
            $invoice->doc_total = $docTotal;
            $invoice->save();
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
            if (Schema::hasColumn('invoices', 'target_type')) {
                $table->dropColumn('target_type');
            }

            if (Schema::hasColumn('invoices', 'doc_vat')) {
                $table->dropColumn('doc_vat');
            }

            if (Schema::hasColumn('invoices', 'doc_subtotal')) {
                $table->dropColumn('doc_subtotal');
            }
        });
    }
}
