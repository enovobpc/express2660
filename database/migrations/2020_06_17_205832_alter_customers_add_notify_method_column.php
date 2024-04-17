<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomersAddNotifyMethodColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {

            if (!Schema::hasColumn('customers', 'shipping_status_notify_method')) {
                $table->string('shipping_status_notify_method', 25)
                    ->nullable()
                    ->after('shipping_status_notify');
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
            if (Schema::hasColumn('customers', 'shipping_status_notify_method')) {
                $table->dropColumn('shipping_status_notify_method');
            }
        });
    }
}
