<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePurchasePaymentNotesAddDeletedBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_payment_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_payment_notes', 'deleted_by')) {
                $table->integer('deleted_by')
                    ->unsigned()
                    ->nullable()
                    ->index()
                    ->after('user_id');
            }
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoices', 'deleted_by')) {
                $table->integer('deleted_by')
                    ->unsigned()
                    ->nullable()
                    ->index()
                    ->after('created_by');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'popup_notification')) {
                $table->longText('popup_notification')
                    ->nullable()
                    ->after('settings');
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
