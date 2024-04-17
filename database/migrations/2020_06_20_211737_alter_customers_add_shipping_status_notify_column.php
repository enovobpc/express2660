<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomersAddShippingStatusNotifyColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {

            if (!Schema::hasColumn('customers', 'customer_sms_text')) {
                $table->text('customer_sms_text')->nullable()->after('shipping_status_notify');
            }

            if (!Schema::hasColumn('customers', 'shipping_services_notify')) {
                $table->string('shipping_services_notify')->nullable()->after('shipping_status_notify');
            }

            if (!Schema::hasColumn('customers', 'shipping_status_notify_recipient')) {
                $table->string('shipping_status_notify_recipient', 25)->nullable()->after('shipping_status_notify');
            }

            if (!Schema::hasColumn('customers', 'hide_incidences_menu')) {
                $table->boolean('hide_incidences_menu')->default(0)->after('show_reference');
            }
        });


        try {
            DB::table('permissions')->insert([
                'name' => 'customers_support',
                'display_name' => 'Pedidos de Suporte',
                'module' => 'customers_support',
                'group' => 'Módulo de Apoio Cliente',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('customer_sms_text');
            $table->dropColumn('shipping_services_notify');
            $table->dropColumn('hide_incidences_menu');
            $table->dropColumn('shipping_status_notify_recipient');
        });
    }
}
