<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePurchaseInvoicesPaymentNotesAddVat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_payment_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_payment_notes', 'billing_country')) {
                $table->string('billing_country', 5)
                    ->nullable()
                    ->after('provider_id');
            }

            if (!Schema::hasColumn('purchase_payment_notes', 'billing_city')) {
                $table->string('billing_city')
                    ->nullable()
                    ->after('provider_id');
            }

            if (!Schema::hasColumn('purchase_payment_notes', 'billing_zip_code')) {
                $table->string('billing_zip_code')
                    ->nullable()
                    ->after('provider_id');
            }
            if (!Schema::hasColumn('purchase_payment_notes', 'billing_address')) {
                $table->string('billing_address')
                    ->nullable()
                    ->after('provider_id');
            }
            if (!Schema::hasColumn('purchase_payment_notes', 'billing_name')) {
                $table->string('billing_name')
                    ->nullable()
                    ->after('provider_id');
            }

            if (!Schema::hasColumn('purchase_payment_notes', 'billing_code')) {
                $table->string('billing_code')
                    ->nullable()
                    ->after('provider_id');
            }
            
            if (!Schema::hasColumn('purchase_payment_notes', 'vat')) {
                $table->string('vat')
                    ->nullable()
                    ->after('provider_id');
            }
        });

        $paymentNotes = \App\Models\PurchasePaymentNote::with('provider')->get();
        foreach ($paymentNotes as $paymentNote) {
            $paymentNote->vat               = @$paymentNote->provider->vat;
            $paymentNote->billing_code      = @$paymentNote->provider->code;
            $paymentNote->billing_name      = @$paymentNote->provider->company;
            $paymentNote->billing_address   = @$paymentNote->provider->address;
            $paymentNote->billing_zip_code  = @$paymentNote->provider->zip_code;
            $paymentNote->billing_city      = @$paymentNote->provider->city;
            $paymentNote->billing_country   = @$paymentNote->provider->country;
            $paymentNote->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_payment_notes', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_payment_notes', 'vat')) {
                $table->dropColumn('vat');
            }

            if (Schema::hasColumn('purchase_payment_notes', 'billing_code')) {
                $table->dropColumn('billing_code');
            }

            if (Schema::hasColumn('purchase_payment_notes', 'billing_name')) {
                $table->dropColumn('billing_name');
            }

            if (Schema::hasColumn('purchase_payment_notes', 'billing_address')) {
                $table->dropColumn('billing_address');
            }

            if (Schema::hasColumn('purchase_payment_notes', 'billing_zip_code')) {
                $table->dropColumn('billing_zip_code');
            }

            if (Schema::hasColumn('purchase_payment_notes', 'billing_city')) {
                $table->dropColumn('billing_city');
            }

            if (Schema::hasColumn('purchase_payment_notes', 'billing_country')) {
                $table->dropColumn('billing_country');
            }
        });
    }
}
