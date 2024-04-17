<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableInvoicesAddSettleMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'settle_obs')) {
                $table->string('settle_obs', 500)
                    ->nullable()
                    ->after('delete_reason');
            }

            if (!Schema::hasColumn('invoices', 'settle_date')) {
                $table->date('settle_date')
                    ->nullable()
                    ->after('delete_reason');
            }

            if (!Schema::hasColumn('invoices', 'settle_method')) {
                $table->string('settle_method', 25)
                    ->nullable()
                    ->after('delete_reason');
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
            if (Schema::hasColumn('invoices', 'settle_method')) {
                $table->dropColumn('settle_method');
            }

            if (Schema::hasColumn('invoices', 'settle_date')) {
                $table->dropColumn('settle_date');
            }

            if (Schema::hasColumn('invoices', 'settle_obs')) {
                $table->dropColumn('settle_obs');
            }
        });
    }
}
