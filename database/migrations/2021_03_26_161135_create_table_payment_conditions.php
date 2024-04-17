<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('payment_conditions')) {
            Schema::create('payment_conditions', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('code', 4)->index();
                $table->string('name');
                $table->boolean('sales_visible')->default(1);
                $table->boolean('purchases_visible')->default(1);
                $table->string('software_code', 10)->index();
                $table->integer('sort')->index();
                $table->timestamps();
                $table->softDeletes();
            });


            $arr     = trans('admin/billing.payment-conditions');
            $created = date('Y-m-d H:i:s');
            $source  = config('app.source');
            $insertArr = [];
            $i = 1;
            foreach ($arr as $key => $name) {
                $insertArr[] = [
                    'source' => $source,
                    'code' => $key,
                    'name' => $name,
                    'sort' => $i,
                    'created_at' => $created
                ];

                $i++;
            }

            \App\Models\PaymentCondition::insert($insertArr);
        }


        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'currency')) {
                $table->string('currency', 3)
                    ->nullable()
                    ->after('total_expenses');
            }
        });

        Schema::table('pack_types', function (Blueprint $table) {
            if (!Schema::hasColumn('pack_types', 'assigned_service_id')) {
                $table->integer('assigned_service_id')
                    ->unsigned()
                    ->nullable()
                    ->after('name');
            }
        });

        Schema::table('shipments_packs_dimensions', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments_packs_dimensions', 'total_cost')) {
                $table->decimal('total_cost', 10,2)
                    ->nullable()
                    ->after('type');
            }

            if (!Schema::hasColumn('shipments_packs_dimensions', 'total_price')) {
                $table->decimal('total_price', 10,2)
                    ->nullable()
                    ->after('type');
            }
        });

        Schema::table('shipping_status', function (Blueprint $table) {
            if (!Schema::hasColumn('shipping_status', 'is_static')) {
                $table->boolean('is_static')
                    ->default(1)
                    ->after('is_visible');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'currency')) {
                $table->string('currency',4)
                    ->default('€')
                    ->after('payment_method');
            }
        });

        if(env('DB_DATABASE_LOGISTIC')) {

            Schema::connection('mysql_logistic')->table('shipping_orders_lines', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('shipping_orders_lines', 'product_location_id')) {
                    $table->integer('product_location_id')
                        ->unsigned()
                        ->nullable()
                        ->after('location_id');
                }
            });

            Schema::connection('mysql_logistic')->table('reception_orders_lines', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('reception_orders_lines', 'product_location_id')) {
                    $table->integer('product_location_id')
                        ->unsigned()
                        ->nullable()
                        ->after('location_id');
                }
            });

            Schema::connection('mysql_logistic')->table('products_locations', function (Blueprint $table) {
                if (!Schema::connection('mysql_logistic')->hasColumn('products_locations', 'stock_allocated')) {
                    $table->integer('stock_allocated')
                        ->nullable()
                        ->after('stock');
                }

                if (!Schema::connection('mysql_logistic')->hasColumn('products_locations', 'stock_available')) {
                    $table->integer('stock_available')
                        ->nullable()
                        ->after('stock');
                }
            });

            Schema::connection('mysql_logistic')->table('products', function (Blueprint $table) {

                if (!Schema::connection('mysql_logistic')->hasColumn('products', 'stock_available')) {
                    $table->integer('stock_available')
                        ->nullable()
                        ->after('stock_allocated');
                }
            });


            DB::connection('mysql_logistic')->statement('update products set stock_available = stock_total');
            DB::connection('mysql_logistic')->statement('update products_locations set stock_available = stock');

            $shippingOrderLines = \App\Models\Logistic\ShippingOrderLine::get();
            foreach ($shippingOrderLines as $line) {

                $loc = \App\Models\Logistic\ProductLocation::where('product_id', $line->product_id)
                    ->where('location_id', $line->location_id)
                    ->first();

                $line->product_location_id = @$loc->id;
                $line->save();
            }

            $receptionOrderLines = \App\Models\Logistic\ReceptionOrderLine::get();
            foreach ($receptionOrderLines as $line) {

                $loc = \App\Models\Logistic\ProductLocation::where('product_id', $line->product_id)
                    ->where('location_id', $line->location_id)
                    ->first();

                $line->product_location_id = @$loc->id;
                $line->save();
            }
        }

        $perm = \App\Models\Permission::where('name', 'account_emails')->first();
        if(!$perm) {
            $perm = new \App\Models\Permission();
            $perm->name         = 'email_accounts';
            $perm->display_name = 'Gestão Contas E-mail';
            $perm->group        = 'Configurações';
            $perm->module       = 'base';
            $perm->save();
        }

        $users = \App\Models\User::get();
        foreach ($users as $user) {
            if(!empty($user->fullname) && empty($user->name)) {
                $user->name = split_name($user->fullname);
                $user->save();
            } elseif(empty($user->fullname)) {
                $user->fullname = $user->name;
                $user->save();
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
        Schema::dropIfExists('payment_conditions');

        Schema::table('pack_types', function (Blueprint $table) {
            if (Schema::hasColumn('pack_types', 'assigned_service_id')) {
                $table->dropColumn('assigned_service_id');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'currency')) {
                $table->dropColumn('currency');
            }
        });

        Schema::table('shipping_status', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_status', 'is_static')) {
                $table->dropColumn('is_static');
            }
        });

        Schema::table('shipments_packs_dimensions', function (Blueprint $table) {
            if (Schema::hasColumn('shipments_packs_dimensions', 'total_cost')) {
                $table->dropColumn('total_cost');
            }

            if (Schema::hasColumn('shipments_packs_dimensions', 'total_price')) {
                $table->dropColumn('total_price');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
}
