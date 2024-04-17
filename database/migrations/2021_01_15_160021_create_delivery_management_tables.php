<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryManagementTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('delivery_manifests')) {
            Schema::create('delivery_manifests', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('code')->index();
                $table->timestamp('pickup_date')->nullable();
                $table->timestamp('delivery_date')->nullable();
                $table->integer('operator_id')->unsigned()->index()->nullable();
                $table->integer('provider_id')->unsigned()->index()->nullable();
                $table->integer('pickup_route_id')->unsigned()->index()->nullable();
                $table->integer('delivery_route_id')->unsigned()->index()->nullable();
                $table->string('vehicle')->index()->nullable();
                $table->string('trailer')->index()->nullable();
                $table->integer('agency_id')->unsigned()->index()->nullable();
                $table->integer('auxiliar_id')->unsigned()->index()->nullable();
                $table->integer('created_by')->unsigned()->index()->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('agency_id')
                    ->references('id')
                    ->on('agencies');

                $table->foreign('provider_id')
                    ->references('id')
                    ->on('providers');

                $table->foreign('operator_id')
                    ->references('id')
                    ->on('users');

                $table->foreign('auxiliar_id')
                    ->references('id')
                    ->on('users');

                $table->foreign('created_by')
                    ->references('id')
                    ->on('users');

                $table->foreign('pickup_route_id')
                    ->references('id')
                    ->on('routes');

                $table->foreign('delivery_route_id')
                    ->references('id')
                    ->on('routes');
            });

            Schema::create('delivery_manifests_shipments', function (Blueprint $table) {

                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('delivery_manifest_id')->unsigned()->index();
                $table->integer('shipment_id')->unsigned()->index();
                $table->integer('sort')->unsigned()->index()->nullable();
                $table->timestamps();

                $table->foreign('delivery_manifest_id')
                    ->references('id')
                    ->on('delivery_manifests');

                $table->foreign('shipment_id')
                    ->references('id')
                    ->on('shipments');
            });
        }


        \App\Models\ShippingStatus::whereId(10)->update(['name' => 'Em recolha']);

        \App\Models\Permission::insert([
            'name' => 'delivery_management',
            'display_name' => 'Gestão de Distribuição',
            'group' => 'Envios e Recolhas',
            'module' => 'delivery_management'
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_manifests_shipments');
        Schema::dropIfExists('delivery_manifests');
    }
}
