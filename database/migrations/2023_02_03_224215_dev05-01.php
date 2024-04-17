<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Dev0501 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (env('DB_DATABASE_FLEET')) {
            Schema::connection('mysql_fleet')->table('fleet_vehicles', function (Blueprint $table) {
                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_vehicles', 'provider_id')) {
                    $table->integer('provider_id')
                        ->nullable()
                        ->after('operator_id');
                }
            });
        }

        if (!Schema::hasColumn('customers', 'is_commercial')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->boolean('is_commercial')
                    ->default(0)
                    ->after('ignore_mass_billing');
            });
        }

        if (!Schema::hasColumn('customers', 'hide_btn_shipments')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->boolean('hide_btn_shipments')
                    ->default(0)
                    ->after('hide_budget_btn');
            });
        }

        //CREATE TABLE CART_PRODUCTS
        if (env('DB_DATABASE_LOGISTIC')) {
            if (!Schema::connection('mysql_logistic')->hasTable('cart_products')) {
                Schema::connection('mysql_logistic')->create('cart_products', function (Blueprint $table) {

                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->integer('customer_id')->nullable();
                    $table->integer('product_id')->nullable();
                    $table->integer('qty')->nullable();
                    $table->string('reference')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
            }
        }

        if (env('DB_DATABASE_LOGISTIC')) {

            if (Schema::connection('mysql_logistic')->hasTable('cart_products')) {

                Schema::connection('mysql_logistic')->table('cart_products', function (Blueprint $table) {

                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'status')) {
                        $table->string('status')
                            ->nullable()
                            ->after('reference');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'submitted_at')) {
                        $table->string('submitted_at')
                            ->nullable()
                            ->after('status');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'submitted_by')) {
                        $table->integer('submitted_by')
                            ->nullable()
                            ->after('submitted_at');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'accepted_by')) {
                        $table->integer('accepted_by')
                            ->nullable()
                            ->after('submitted_by');
                    }

                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'refused_by')) {
                        $table->integer('refused_by')
                            ->nullable()
                            ->after('accepted_by');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'shipment_id')) {
                        $table->integer('shipment_id')
                            ->nullable()
                            ->after('refused_by');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'closed')) {
                        $table->boolean('closed')
                            ->default(0)
                            ->after('shipment_id');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'origin_name')) {
                        $table->string('origin_name')
                            ->nullable()
                            ->after('refused_by');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'origin_address')) {
                        $table->string('origin_address')
                            ->nullable()
                            ->after('origin_name');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'origin_zip_code')) {
                        $table->string('origin_zip_code')
                            ->nullable()
                            ->after('origin_address');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'origin_city')) {
                        $table->string('origin_city')
                            ->nullable()
                            ->after('origin_zip_code');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'origin_country')) {
                        $table->string('origin_country')
                            ->nullable()
                            ->after('origin_city');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'destination_name')) {
                        $table->string('destination_name')
                            ->nullable()
                            ->after('origin_country');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'destination_address')) {
                        $table->string('destination_address')
                            ->nullable()
                            ->after('destination_name');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'destination_zip_code')) {
                        $table->string('destination_zip_code')
                            ->nullable()
                            ->after('destination_address');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'destination_city')) {
                        $table->string('destination_city')
                            ->nullable()
                            ->after('destination_zip_code');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'destination_country')) {
                        $table->string('destination_country')
                            ->nullable()
                            ->after('destination_city');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'obs')) {
                        $table->string('obs')
                            ->nullable()
                            ->after('destination_country');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'origin_phone_number')) {
                        $table->string('origin_phone_number')
                            ->nullable()
                            ->after('origin_country');
                    }
                    if (!Schema::connection('mysql_logistic')->hasColumn('cart_products', 'destination_phone_number')) {
                        $table->string('destination_phone_number')
                            ->nullable()
                            ->after('destination_country');
                    }
                });
            }
            if (Schema::connection('mysql_logistic')->hasTable('products')) {
                Schema::connection('mysql_logistic')->table('products', function (Blueprint $table) {

                    if (!Schema::connection('mysql_logistic')->hasColumn('products', 'need_validation')) {
                        $table->boolean('need_validation')
                            ->default(0)
                            ->after('is_active');
                    }
                });
            }
        }

        if (env('DB_DATABASE_FLEET')) {
            Schema::connection('mysql_fleet')->table('fleet_vehicles', function (Blueprint $table) {
                if (!Schema::connection('mysql_fleet')->hasColumn('fleet_vehicles', 'tachograph_date')) {
                    $table->date('tachograph_date')
                        ->nullable()
                        ->after('iuc_date');
                }
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
        Schema::connection('mysql_logistic')->dropIfExists('cart_products');
    }
}
