<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCustomersAddDefaultService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {

            if (!Schema::hasColumn('customers', 'default_printer_labels')) {
                $table->string('default_printer_labels')
                    ->nullable()
                    ->after('default_print');
            }

            if (!Schema::hasColumn('customers', 'default_printer_a4')) {
                $table->string('default_printer_a4')
                    ->nullable()
                    ->after('default_print');
            }

            if (!Schema::hasColumn('customers', 'default_service')) {
                $table->integer('default_service')
                    ->nullable()
                    ->after('default_print');
            }
        });

        \DB::statement("ALTER TABLE customers CHANGE COLUMN default_print default_print ENUM('all', 'label','guide','cmr') DEFAULT NULL");

        Schema::table('shipments', function (Blueprint $table) {

            if (!Schema::hasColumn('shipments', 'sender_vat')) {
                $table->string('sender_vat')
                    ->nullable()
                    ->after('sender_attn');
            }

            if (!Schema::hasColumn('shipments', 'recipient_vat')) {
                $table->string('recipient_vat')
                    ->nullable()
                    ->after('recipient_attn');
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
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'default_service')) {
                $table->dropColumn('default_service');
            }

            if (Schema::hasColumn('customers', 'default_printer_labels')) {
                $table->dropColumn('default_printer_labels');
            }

            if (Schema::hasColumn('customers', 'default_printer_a4')) {
                $table->dropColumn('default_printer_a4');
            }

        });

        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'sender_vat')) {
                $table->dropColumn('sender_vat');
            }

            if (Schema::hasColumn('shipments', 'recipient_vat')) {
                $table->dropColumn('recipient_vat');
            }

        });
    }
}
