<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableInvoicesAddDeletedFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('invoices', 'credit_note_id')) {
                $table->integer('credit_note_id')
                    ->unsigned()
                    ->nullable()
                    ->after('is_deleted');
            }

            if (!Schema::hasColumn('invoices', 'delete_user')) {
                $table->integer('delete_user')
                    ->unsigned()
                    ->nullable()
                    ->after('is_deleted');
            }

            if (!Schema::hasColumn('invoices', 'delete_date')) {
                $table->timestamp('delete_date')
                    ->nullable()
                    ->after('is_deleted');
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
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'delete_date')) {
                $table->dropColumn('delete_date');
            }

            if (Schema::hasColumn('invoices', 'delete_user')) {
                $table->dropColumn('delete_user');
            }

            if (Schema::hasColumn('invoices', 'credit_note_id')) {
                $table->dropColumn('credit_note_id');
            }
        });
    }
}
