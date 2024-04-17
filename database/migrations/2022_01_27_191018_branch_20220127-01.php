<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Branch2022012701 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        anlutro\LaravelSettings\Facade::set('shipment_alert_unpaid_invoices', true);
        anlutro\LaravelSettings\Facade::set('shipment_alert_payment_condition', ['prt','wall']);
        anlutro\LaravelSettings\Facade::save();

        try {
            $method = \App\Models\WebserviceMethod::firstOrNew([
                'method' => 'skynet'
            ]);
            $method->method  = 'skynet';
            $method->name    = 'SkyNet';
            $method->sources = [config('app.source')];
            $method->enabled = 1;
            $method->save();
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        if(empty(env('DB_CONNECTION_ENOVO'))) {
            throw new \Exception('Base de dados ENOVO não configurada no ficheiro .env');
        }

        $source = config('app.source');

        $users = \App\Models\User::whereNotNull('source')
            ->where(function($q){
                $q->where('password', '<>', '');
            })
            ->get();

        foreach ($users as $user) {

            if(!empty($user->password) && !str_contains($user->email, 'enovo.pt')) {
                $userAuth = App\Models\Core\UserAuth::firstOrNew([
                    'source'    => $source,
                    'source_id' => $user->id
                ]);

                $userAuth->source = $source;
                $userAuth->fill($user->toArray());
                $userAuth->password = $user->password;
                $userAuth->is_active = $user->active;
                $userAuth->save();
            }
        }

        DB::connection('mysql_fleet')->statement("ALTER TABLE fleet_vehicles CHANGE COLUMN status status ENUM('operacional','damaged','maintenance', 'inactive', 'sold', 'slaughter') DEFAULT 'operacional'");

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'billing_reference')) {
                $table->text('billing_reference')
                    ->nullable()
                    ->after('billing_email');
            }
        });

        Schema::table('customers_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('customers_messages', 'to_emails')) {
                $table->longText('to_emails')
                    ->nullable()
                    ->after('message');
            }
        });

        Schema::table('purchase_payment_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_payment_notes', 'discount')) {
                $table->decimal('discount', 10,2)
                    ->nullable()
                    ->after('subtotal');
            }

            if (!Schema::hasColumn('purchase_payment_notes', 'discount_unity')) {
                $table->text('discount_unity', 3)
                    ->nullable()
                    ->after('subtotal');
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'is_active')) {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('operator_id');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'babys')) {
                $table->integer('babys')
                    ->nullable()
                    ->after('hours');
            }

            if (!Schema::hasColumn('shipments', 'childs')) {
                $table->integer('childs')
                    ->nullable()
                    ->after('hours');
            }

            if (!Schema::hasColumn('shipments', 'adults')) {
                $table->integer('adults')
                    ->nullable()
                    ->after('hours');
            }
        });

        if(env('DB_DATABASE_LOGISTIC')) {

            if (!Schema::connection('mysql_logistic')->hasTable('reception_orders_confirmations')) {
                Schema::connection('mysql_logistic')->create('reception_orders_confirmations', function (Blueprint $table) {
                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->unsignedInteger('reception_order_id')->index();
                    $table->unsignedInteger('product_id')->index();
                    $table->unsignedInteger('location_id')->index();
                    $table->unsignedInteger('product_location_id')->index();
                    $table->integer('qty_received')->default(0);
                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('reception_order_id')
                        ->references('id')
                        ->on('reception_orders');

                    $table->foreign('product_id')
                        ->references('id')
                        ->on('products');

                    $table->foreign('location_id')
                        ->references('id')
                        ->on('locations');
                });
            }

            if (!Schema::connection('mysql_logistic')->hasTable('inventories')) {
                Schema::connection('mysql_logistic')->create('inventories', function (Blueprint $table) {
                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('source')->index();
                    $table->string('code')->index();
                    $table->integer('items')->default(0);
                    $table->integer('qty_existing')->default(0);
                    $table->integer('qty_real')->default(0);
                    $table->date('date')->nullable();
                    $table->string('status_id', 6)->nullable();
                    $table->unsignedInteger('customer_id')->index()->nullable();
                    $table->unsignedInteger('user_id')->index();
                    $table->timestamps();
                    $table->softDeletes();
                });
            }

            if (!Schema::connection('mysql_logistic')->hasTable('inventories_lines')) {
                Schema::connection('mysql_logistic')->create('inventories_lines', function (Blueprint $table) {
                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->unsignedInteger('inventory_id')->index();
                    $table->unsignedInteger('product_id')->index();
                    $table->unsignedInteger('location_id')->index()->nullable();
                    $table->unsignedInteger('customer_id')->index()->nullable();
                    $table->integer('qty_existing')->default(0);
                    $table->integer('qty_real')->default(0);
                    $table->integer('qty_available')->default(0);
                    $table->integer('qty_damaged')->default(0);
                    $table->integer('qty_expired')->default(0);
                    $table->integer('price')->default(0);
                    $table->boolean('is_active')->default(1);
                    $table->boolean('is_blocked')->default(1);
                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('inventory_id')
                        ->references('id')
                        ->on('inventories');

                    $table->foreign('product_id')
                        ->references('id')
                        ->on('products');

                    $table->foreign('location_id')
                        ->references('id')
                        ->on('locations');
                });
            }

            if (!Schema::connection('mysql_logistic')->hasTable('products_stocks_history')) {
                Schema::connection('mysql_logistic')->create('products_stocks_history', function (Blueprint $table) {
                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->date('date');
                    $table->string('unique_hash')->nullable()->index();
                    $table->unsignedInteger('warehouse_id')->index()->nullable();
                    $table->unsignedInteger('customer_id')->index()->nullable();
                    $table->unsignedInteger('product_id')->index();
                    $table->unsignedInteger('location_id')->nullable();
                    $table->integer('stock_available')->default(0);
                    $table->integer('stock_allocated')->default(0);
                    $table->integer('stock_total')->default(0);
                    $table->unsignedInteger('history_id')->index()->nullable();
                    $table->string('history_action')->nullable();
                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('product_id')
                        ->references('id')
                        ->on('products');

                    $table->foreign('location_id')
                        ->references('id')
                        ->on('locations');

                    $table->foreign('warehouse_id')
                        ->references('id')
                        ->on('warehouses');

                    $table->foreign('history_id')
                        ->references('id')
                        ->on('products_history');
                });
            }
        }

        Schema::table('payment_conditions', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_conditions', 'days')) {
                $table->integer('days')
                    ->nullable()
                    ->after('name');
            }
        });


        $conditions = \App\Models\PaymentCondition::get();
        foreach ($conditions as $condition) {

            $days = '30';
            if(str_contains($condition->code, 'd')) {
                $days = str_replace('d', '', $condition->code);
            }

            if($condition->code == 'dbt') {
                $days = '30';
            }

            if($condition->code == 'prt' || $condition->code == 'wall') {
                $days = '0';
            }

            $condition->days = $days;
            $condition->save();
        }

        $method = \App\Models\PaymentMethod::firstOrNew([
            'code' => 'factoring'
        ]);
        $method->source = config('app.source');
        $method->code   = 'factoring';
        $method->name   = 'Factoring';
        $method->is_active = 1;
        $method->save();
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