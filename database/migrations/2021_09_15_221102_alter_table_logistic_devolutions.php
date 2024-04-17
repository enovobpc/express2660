<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableLogisticDevolutions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if(env('DB_DATABASE_LOGISTIC')) {

            Schema::connection('mysql_logistic')->table('devolutions_items', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('devolutions_items', 'qty')) {
                    $table->integer('qty')
                        ->nullable()
                        ->after('location_id');
                }
            });

            Schema::connection('mysql_logistic')->table('shipping_orders', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders', 'qty_devolved')) {
                    $table->integer('qty_devolved')
                        ->nullable()
                        ->after('qty_satisfied');
                }
            });

            Schema::connection('mysql_logistic')->table('shipping_orders_lines', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders_lines', 'qty_devolved')) {
                    $table->integer('qty_devolved')
                        ->nullable()
                        ->after('qty_satisfied');
                }
            });

            Schema::connection('mysql_logistic')->table('devolutions', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('devolutions', 'obs')) {
                    $table->date('date')
                        ->nullable()
                        ->after('obs');
                }

                if (!Schema::connection('mysql_logistic')->hasColumn('devolutions', 'status')) {
                    $table->string('status', 10)
                        ->nullable()
                        ->index()
                        ->after('date');
                }

                if (!Schema::connection('mysql_logistic')->hasColumn('devolutions', 'total_qty_damaged')) {
                    $table->integer('total_qty_damaged')
                        ->after('total_qty');
                }
            });
        }

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'recipient_pudo_id')) {
                $table->integer('recipient_pudo_id')
                    ->unsiged()
                    ->nullable()
                    ->after('recipient_id');
            }

            if (!Schema::hasColumn('shipments', 'sender_state')) {
                $table->string('sender_state', 5)
                    ->nullable()
                    ->after('sender_city');
            }

            if (!Schema::hasColumn('shipments', 'recipient_state')) {
                $table->string('recipient_state', 5)
                    ->nullable()
                    ->after('recipient_city');
            }
        });

        /*Schema::table('shipments', function (Blueprint $table) {
            $table->string('sender_state', 5)->change();
            $table->string('recipient_state', 5)->change();
        });*/

        Schema::table('pickup_points', function (Blueprint $table) {
            if (!Schema::hasColumn('pickup_points', 'provider_code')) {
                $table->string('provider_code', 25)
                    ->nullable()
                    ->after('code');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'state')) {
                $table->string('state', 5)
                    ->nullable()
                    ->after('city');
            }

            if (!Schema::hasColumn('customers', 'billing_state')) {
                $table->string('billing_state', 5)
                    ->nullable()
                    ->after('billing_city');
            }

            if (!Schema::hasColumn('customers', 'enabled_pudo_providers')) {
                $table->string('enabled_pudo_providers')
                    ->nullable()
                    ->after('enabled_packages');
            }
        });

        Schema::table('customers_recipients', function (Blueprint $table) {
            if (!Schema::hasColumn('customers_recipients', 'state')) {
                $table->string('state', 5)
                    ->nullable()
                    ->after('city');
            }
        });

        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'state')) {
                $table->string('state', 5)
                    ->nullable()
                    ->after('city');
            }
        });

        Schema::table('purchase_invoices_lines', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoices_lines', 'qty')) {
                $table->decimal('qty', 10, 2)
                    ->change();
            }
        });

        Schema::table('invoices_lines', function (Blueprint $table) {
            if (Schema::hasColumn('invoices_lines', 'qty')) {
                $table->decimal('qty', 10, 2)
                    ->change();
            }

            if (!Schema::hasColumn('invoices_lines', 'obs')) {
                $table->text('obs')
                    ->nullable()
                    ->after('exemption_reason_code');
            }
        });

        Schema::table('shipments_packs_dimensions', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments_packs_dimensions', 'price')) {
                $table->decimal('price', 10, 2)
                    ->nullable()
                    ->after('type');
            }
        });

        if(in_array(anlutro\LaravelSettings\Facade::get('app_mode'), ['express', 'courier','food'])) {
            anlutro\LaravelSettings\Facade::set('shipments_show_charge_price', true);
            anlutro\LaravelSettings\Facade::set('customers_show_charge_price', true);
            anlutro\LaravelSettings\Facade::save();
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
