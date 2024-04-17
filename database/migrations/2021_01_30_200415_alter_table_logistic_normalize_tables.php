<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLogisticNormalizeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(env('DB_DATABASE_LOGISTIC')) {
            //DB::connection('mysql_logistic')->statement("UPDATE products SET sku = SUBSTRING(barcode, 1, 50)");

            if (!Schema::connection('mysql_logistic')->hasTable('shipping_orders_status')) {
                Schema::connection('mysql_logistic')->create('shipping_orders_status', function (Blueprint $table) {

                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('source')->index();
                    $table->string('name');
                    $table->string('color');
                    $table->integer('sort')->unsigned()->index()->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
            }

            if (!Schema::connection('mysql_logistic')->hasTable('reception_orders_status')) {
                Schema::connection('mysql_logistic')->create('reception_orders_status', function (Blueprint $table) {

                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('source')->index();
                    $table->string('name');
                    $table->string('color');
                    $table->integer('sort')->unsigned()->index()->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
            }

            /*Schema::connection('mysql_logistic')->table('shipping_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('shipping_orders', 'status_id')) {
                    $table->integer('status_id')
                        ->unsigned()
                        ->nullable()
                        ->after('status');

                    $table->foreign('status_id')
                        ->references('id')
                        ->on('shipping_orders_status');
                }

                if (Schema::hasColumn('shipping_orders', 'status')) {
                    $table->dropColumn('status');
                }
            });*/


            \App\Models\Logistic\ShippingOrderStatus::insert([
                [
                    'source' => config('app.source'),
                    'name' => 'Pendente',
                    'color' => '#777777',
                    'sort' => '1',
                ],
                [
                    'source' => config('app.source'),
                    'name' => 'Em preparação',
                    'color' => '#028ce8',
                    'sort' => '2',
                ],
                [
                    'source' => config('app.source'),
                    'name' => 'Finalizado',
                    'color' => '#48AD01',
                    'sort' => '3',
                ],
            ]);

            \App\Models\Logistic\ReceptionOrderStatus::insert([
                [
                    'source' => config('app.source'),
                    'name' => 'Pré-recepção',
                    'color' => '#777',
                    'sort' => '1',
                ],
                [
                    'source' => config('app.source'),
                    'name' => 'Aguarda Alocação',
                    'color' => '#f6a800',
                    'sort' => '2',
                ],
                [
                    'source' => config('app.source'),
                    'name' => 'Finalizado',
                    'color' => '#48AD01',
                    'sort' => '3',
                ],
                [
                    'source' => config('app.source'),
                    'name' => 'Cancelado',
                    'color' => '#ec1c24',
                    'sort' => '4',
                ],
            ]);


            $shippingOrders = \App\Models\Logistic\ShippingOrder::with('lines')->get();
            foreach ($shippingOrders as $shippingOrder) {
                $shippingOrder->total_items = @$shippingOrder->lines->count();
                $shippingOrder->total_qty = @$shippingOrder->lines->sum('qty');
                $shippingOrder->status_id = 3;
                $shippingOrder->save();
            }
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
