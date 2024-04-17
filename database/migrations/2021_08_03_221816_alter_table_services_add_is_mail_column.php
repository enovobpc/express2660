<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableServicesAddIsMailColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'insurance_tax')) {
                $table->decimal('insurance_tax', 10,2)
                    ->nullable()
                    ->after('volumetric_coeficient');
            }

            if (!Schema::hasColumn('customers', 'fuel_tax')) {
                $table->decimal('fuel_tax', 10,2)
                    ->nullable()
                    ->after('volumetric_coeficient');
            }
        });


        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'is_mail')) {
                $table->boolean('is_mail')
                    ->default(false)
                    ->after('is_regional');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'vat_rate')) {
                $table->decimal('vat_rate', 10,2)
                    ->nullable()
                    ->after('total_price');
            }
        });

        Schema::table('shipments_history', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments_history', 'deleted_at')) {
                $table->timestamp('deleted_at')
                    ->nullable()
                    ->after('updated_at');
            }

            if (!Schema::hasColumn('shipments_history', 'deleted_by')) {
                $table->integer('deleted_by')
                    ->unsigned()
                    ->nullable()
                    ->index()
                    ->after('api');
            }
        });

        Schema::table('refunds_control', function (Blueprint $table) {
            if (!Schema::hasColumn('refunds_control', 'payment_user_id')) {
                $table->integer('payment_user_id')
                    ->unsigned()
                    ->nullable()
                    ->index()
                    ->after('payment_date');
            }

            if (!Schema::hasColumn('refunds_control', 'received_user_id')) {
                $table->integer('received_user_id')
                    ->unsigned()
                    ->nullable()
                    ->index()
                    ->after('payment_date');
            }
        });

        if(env('DB_DATABASE_LOGISTIC')) {
            if (!Schema::connection('mysql_logistic')->hasTable('devolutions')) {
                Schema::connection('mysql_logistic')->create('devolutions', function (Blueprint $table) {

                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('source')->index();
                    $table->string('code')->index();
                    $table->integer('customer_id')->unsigned()->nullable();
                    $table->integer('shipment_id')->unsigned()->nullable();
                    $table->string('document');
                    $table->integer('total_items');
                    $table->integer('total_qty');
                    $table->integer('total_items_original');
                    $table->integer('total_qty_original');
                    $table->text('obs');
                    $table->integer('user_id')->unsigned()->nullable();
                    $table->timestamp('devolution_date');
                    $table->timestamps();
                    $table->softDeletes();
                });
            }

            if (!Schema::connection('mysql_logistic')->hasTable('devolutions_items')) {
                Schema::connection('mysql_logistic')->create('devolutions_items', function (Blueprint $table) {

                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->integer('devolution_id')->unsigned();
                    $table->integer('product_id')->unsigned()->nullable();
                    $table->integer('location_id')->unsigned()->nullable();
                    $table->enum('status', ['ok','damaged']);
                    $table->text('obs');
                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('devolution_id')
                        ->references('id')
                        ->on('devolutions');
                });
            }

            Schema::connection('mysql_logistic')->table('shipping_orders_lines', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders_lines', 'unity')) {
                    $table->string('unity', 5)
                        ->nullable()
                        ->default('uni')
                        ->after('product_location_id');
                }

                if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders_lines', 'serial_no')) {
                    $table->string('serial_no')
                        ->nullable()
                        ->after('barcode');
                }

                if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders_lines', 'lote')) {
                    $table->string('lote')
                        ->nullable()
                        ->after('barcode');
                }
            });

            Schema::connection('mysql_logistic')->table('reception_orders', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('reception_orders', 'total_price')) {
                    $table->decimal('total_price',10,2)
                        ->nullable()
                        ->after('total_qty_received');
                }
            });

            Schema::connection('mysql_logistic')->table('reception_orders_lines', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('reception_orders_lines', 'unity')) {
                    $table->string('unity', 5)
                        ->nullable()
                        ->default('uni')
                        ->after('product_location_id');
                }

                if (!Schema::connection('mysql_logistic')->hasColumn('reception_orders_lines', 'price')) {
                    $table->decimal('price', 10,2)
                        ->nullable()
                        ->after('qty');
                }

                if (!Schema::connection('mysql_logistic')->hasColumn('reception_orders_lines', 'qty_received')) {
                    $table->integer('qty_received')
                        ->nullable()
                        ->after('qty');
                }
            });

            if (!Schema::connection('mysql_logistic')->hasTable('shipping_orders_status')) {
                $status = \App\Models\Logistic\ShippingOrderStatus::find(4);
                if(!$status) {
                    $status->source = config('app.source');
                    $status->name   = 'Anulado';
                    $status->color  = '#ea0c0c';
                    $status->save();
                }


                $status = \App\Models\Logistic\ShippingOrderStatus::find(5);
                if(!$status) {
                    $status->source = config('app.source');
                    $status->name   = 'Pré-preparação';
                    $status->color  = '#9b59b6';
                    $status->save();
                }

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
