<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Branch2022112205 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'holidays_days')) {
                $table->integer('holidays_days')
                    ->nullable()
                    ->after('count_notices');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'time_delivering')) {
                $table->string('time_delivering', 20)
                    ->nullable()
                    ->after('shipping_services_notify');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'time_assembly')) {
                $table->string('time_assembly', 20)
                    ->nullable()
                    ->after('shipping_services_notify');
            }
        });

        Schema::table('delivery_manifests', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_manifests', 'start_hour')) {
                $table->string('start_hour', 5)
                    ->nullable()
                    ->after('auxiliar_id');
            }
        });

        Schema::table('delivery_manifests', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_manifests', 'avg_delivery_time')) {
                $table->string('avg_delivery_time', 5)
                    ->nullable()
                    ->after('end_hour');
            }
        });



        if (!Schema::hasTable('shipments_customer_supports')) {
            Schema::create('shipments_customer_supports', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('shipment_id')->unsigned()->index();
                $table->integer('user_id')->unsigned()->index();
                $table->string('subject');
                $table->string('action_taken');

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

                $table->foreign('shipment_id')
                    ->references('id')
                    ->on('shipments')
                    ->onDelete('cascade');
            });
        }
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
