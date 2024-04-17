<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranch2021111805 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('traceability_pending_shipments')) {
            Schema::create('traceability_pending_shipments', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('shipment_id');
                $table->integer('obs')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'agency_id')) {
                $table->unsignedInteger('agency_id')
                    ->nullable()
                    ->after('customer_id');
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
        Schema::dropIfExists('traceability_pending_shipments');
    }
}
