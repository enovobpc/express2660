<?php

use App\Models\Billing\Item;
use App\Models\Billing\ItemStockHistory;
use App\Models\Billing\VatRate;
use App\Models\FleetGest\Part;
use App\Models\PackType;
use App\Models\Permission;
use App\Models\Route;
use App\Models\Service;
use App\Models\ShippingExpense;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Dev0701 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments_history', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments_history', 'trailer')) {
                $table->string('trailer')->nullable();
            }
        });

        if (!Schema::hasTable('delivery_manifests_expenses') && !Schema::hasTable('trips_expenses')) {
            Schema::create('trips_expenses', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('delivery_manifest_id')->unsigned();
                $table->date('date')->nullable();
                $table->enum('type', [ 'allowance', 'weekend', 'other' ]);
                $table->string('description')->nullable();
                $table->decimal('total', 10, 2)->nullable();
                $table->integer('operator_id')->unsigned()->nullable();
                $table->integer('provider_id')->nullable()->unsigned();
                $table->integer('purchase_invoice_id')->nullable()->unsigned();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('delivery_manifest_id')
                    ->references('id')
                    ->on('delivery_manifests');
                
                $table->foreign('operator_id')
                    ->references('id')
                    ->on('users');
            });
        }

        if (Schema::hasTable('delivery_manifests')) {
            Schema::table('delivery_manifests', function (Blueprint $table) {
                if (!Schema::hasColumn('delivery_manifests', 'allowances_price')) {
                    $table->decimal('allowances_price', 10, 2)
                        ->nullable()
                        ->after('cost_vat_rate_id');
                }

                if (!Schema::hasColumn('delivery_manifests', 'weekend_price')) {
                    $table->decimal('weekend_price', 10, 2)
                        ->nullable()
                        ->after('allowances_price');
                }

                if (!Schema::hasColumn('delivery_manifests', 'fuel_consumption')) {
                    $table->decimal('fuel_consumption', 10, 2)
                        ->nullable()
                        ->after('weekend_price');
                }
            });
        }

        /**
         * Logistic containers
         */

        $permission = Permission::where('name', 'logistic_containers')->first();
        if (!$permission) {
            Permission::insert([
                'name'          => 'logistic_containers',
                'display_name'  => 'Contentores',
                'group'         => 'Módulo de Logística',
                'module'        => 'logistic',
                'created_at'    => date('Y-m-d H:i:s')
            ]);
        }

        if(env('DB_DATABASE_LOGISTIC')) {
            if (!Schema::connection('mysql_logistic')->hasTable('containers')) {
                Schema::connection('mysql_logistic')->create('containers', function (Blueprint $table) {
                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('source')->index();;
                    $table->integer('location_id')->unsigned()->nullable();
                    $table->string('code');
                    $table->string('barcode');
                    $table->string('name');
                    $table->decimal('width', 10, 2)->nullable();
                    $table->decimal('height', 10, 2)->nullable();
                    $table->decimal('length', 10, 2)->nullable();
                    $table->decimal('max_weight', 10, 2)->nullable();
                    $table->string('obs');

                    $table->timestamps();
                    $table->softDeletes();
                });

                Schema::connection('mysql_logistic')->create('containers_history', function (Blueprint $table) {
                    $table->engine = 'InnoDB';
                    
                    $table->increments('id');
                    $table->enum('action', ['transfer', 'deallocation']);
                    $table->integer('container_id')->unsigned()->nullable();
                    $table->integer('source_id')->unsigned()->nullable();
                    $table->integer('destination_id')->unsigned()->nullable();
                    $table->timestamp('started_at')->nullable();
                    $table->timestamp('finished_at')->nullable();
                    $table->integer('user_id')->unsigned();

                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('container_id')
                        ->references('id')
                        ->on('containers')
                        ->onDelete('cascade');

                    $table->foreign('source_id')
                        ->references('id')
                        ->on('locations')
                        ->onDelete('cascade');

                    $table->foreign('destination_id')
                        ->references('id')
                        ->on('locations')
                        ->onDelete('cascade');
                });

                Schema::connection('mysql_logistic')->table('products_locations', function (Blueprint $table) {
                    $table->integer('container_id')
                        ->unsigned()
                        ->nullable()
                        ->after('location_id');
                });
            }

            if (!Schema::connection('mysql_logistic')->hasColumn('reception_orders_confirmations', 'container_id')) {
                Schema::connection('mysql_logistic')->table('reception_orders_confirmations', function (Blueprint $table) {
                    $table->integer('container_id')
                        ->unsigned()
                        ->nullable()
                        ->after('location_id');
                });
            }

            if (!Schema::connection('mysql_logistic')->hasColumn('devolutions_items', 'container_id')) {
                Schema::connection('mysql_logistic')->table('devolutions_items', function (Blueprint $table) {
                    $table->integer('container_id')
                        ->unsigned()
                        ->nullable()
                        ->after('location_id');
                });

                Schema::connection('mysql_logistic')->table('shipping_orders_lines', function (Blueprint $table) {
                    $table->integer('container_id')
                        ->unsigned()
                        ->nullable()
                        ->after('location_id');
                });
            }
        }
        /**--Logistic containers */

        Schema::table('pack_types', function (Blueprint $table) {
            if (!Schema::hasColumn('pack_types', 'type')) {
                $table->string('type')
                    ->default('other')
                    ->after('description');
            }
        });


        PackType::where('code', 'box')
            ->update(['type' => 'boxes']);

        PackType::where('code', 'pal')
            ->update(['type' => 'pallets']);

        PackType::where('code', 'env')
            ->update(['type' => 'docs']);

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'default_payment_method_id')) {
                $table->integer('default_payment_method_id')
                    ->unsigned()
                    ->nullable()
                    ->after('bank_mandate');
            }
        });

        Schema::table('routes', function (Blueprint $table) {
            if (!Schema::hasColumn('routes', 'services')) {
                $table->text('services')
                    ->nullable()
                    ->after('zip_codes');
            }

            if (!Schema::hasColumn('routes', 'schedules')) {
                $table->text('schedules')
                    ->nullable()
                    ->after('services');

                $routes = Route::get();
                foreach ($routes as $route) {
                    if ($route->operator_id || $route->provider_id) {
                        $route->schedules = [
                            [
                                'min_hour' => '00:00',
                                'max_hour' => '23:55',
                                'operator' => $route->operator_id,
                                'provider' => $route->provider_id
                            ]
                        ];
                        
                        $route->save();
                    }
                }
            }
        });

        Schema::table('operators_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('operators_tasks', 'start_hour')) {
                $table->string('start_hour', 5)
                    ->nullable()
                    ->after('date');
            }

            if (!Schema::hasColumn('operators_tasks', 'end_hour')) {
                $table->string('end_hour', 5)
                    ->nullable()
                    ->after('start_hour');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'allow_return')) {
                $table->boolean('allow_return')
                    ->default(true)
                    ->after('allow_pudos');
            }

            if (!Schema::hasColumn('services', 'settings')) {
                $table->text('settings')
                    ->nullable()
                    ->after('priority_color');
            }
        });

        $services = Service::all();
        foreach ($services as $service) {
            $service->allow_return = \Setting::get('app_rpack', false);
            $service->settings     = [
                'email_required'          => \Setting::get('customer_account_email_required', false),
                'without_pickup'          => \Setting::get('app_rpack', false),
                'webservices_auto_submit' => \Setting::get('webservices_auto_submit', false),
            ];

            $service->save();
        }

        Schema::table('providers', function (Blueprint $table) {
            if (Schema::hasColumn('providers', 'pickup_hour_difference')) {
                $table->dropColumn('pickup_hour_difference');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'pickup_hour_difference')) {
                $table->string('pickup_hour_difference', 5)
                    ->nullable()
                    ->after('max_hour');
            }
        });

        // if (!Schema::hasTable('pack_types_groups')) {
        //     Schema::create('pack_types_groups', function (Blueprint $table) {
        //         $table->engine = 'InnoDB';
        //         $table->increments('id');

        //         $table->string('source')->index();
        //         $table->string('code');
        //         $table->string('name');

        //         $table->timestamps();
        //         $table->softDeletes();
        //     });
        // }

        if (!Schema::hasTable('service_types')) {
            Schema::create('service_types', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');

                $table->string('source')->index();
                $table->string('name');
                $table->integer('sort')->unsigned();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('services', 'service_type_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->integer('service_type_id')
                    ->unsigned()
                    ->nullable()
                    ->after('provider_id');
            });
        }

        if (!Schema::hasColumn('operators_tasks', 'service_type_id')) {
            Schema::table('operators_tasks', function (Blueprint $table) {
                $table->integer('service_type_id')
                    ->unsigned()
                    ->nullable()
                    ->after('customer_id');
            });
        }

        if (!Schema::hasColumn('operators_tasks', 'full_address')) {
            \DB::statement('ALTER TABLE operators_tasks CHANGE COLUMN address full_address VARCHAR(255) NULL;');
            \DB::statement('ALTER TABLE operators_tasks CHANGE COLUMN operators operators TEXT NULL;');

            Schema::table('operators_tasks', function (Blueprint $table) {
                $table->string('address')
                    ->nullable()
                    ->after('full_address');

                $table->string('zip_code')
                    ->nullable()
                    ->after('address');

                $table->string('city')
                    ->nullable()
                    ->after('zip_code');

                $table->string('phone')
                    ->nullable()
                    ->after('city');
            });
        }

        if (!Schema::hasColumn('customers', 'is_independent')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->boolean('is_independent')
                    ->default(false)
                    ->after('customer_id');
            });
        }

        if (!Schema::hasColumn('shipping_expenses', 'every_arr')) {
            Schema::table('shipping_expenses', function (Blueprint $table) {
                $table->text('every_arr')
                    ->nullable()
                    ->after('trigger_value');
            });
        }

        // if (!Schema::hasTable('operators_tasks_scheduled')) {
        //     Schema::create('operators_tasks_scheduled', function (Blueprint $table) {
        //         $table->engine = 'InnoDB';
        //         $table->increments('id');

        //         $table->string('source')->index();
        //         $table->string('code');
        //         $table->string('name');

        //         $table->timestamps();
        //         $table->softDeletes();
        //     });
        // }

        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source');
                $table->string('name');
                $table->boolean('is_active');
                $table->string('obs');
                $table->smallInteger('sort')
                    ->unsigned()
                    ->nullable();

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('brands_models', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('brand_id')->unsigned();
                $table->string('name');
                
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('billing_products_stocks_history')) {
            Schema::table('billing_products', function (Blueprint $table) {
                $table->integer('provider_id')
                    ->unsigned()
                    ->nullable()
                    ->after('obs');

                $table->string('provider_reference')
                    ->nullable()
                    ->after('provider_id');

                $table->integer('brand_id')
                    ->unsigned()
                    ->nullable()
                    ->after('provider_reference');

                $table->integer('brand_model_id')
                    ->unsigned()
                    ->nullable()
                    ->after('brand_id');

                $table->decimal('sell_price', 10, 2)
                    ->nullable()
                    ->after('price');
                
                $table->decimal('stock_total', 10, 2)
                    ->default(0.00)
                    ->after('sell_price');

                $table->string('unity')
                    ->nullable()
                    ->after('stock_total');

                $table->integer('stock_min')
                    ->unsigned()
                    ->nullable()
                    ->default(0)
                    ->after('unity');

                $table->boolean('has_stock')
                    ->default(false)
                    ->after('stock_min');

                $table->boolean('is_fleet_part')
                    ->default(false)
                    ->after('is_service')
                    ->index();

                $table->boolean('is_customer_customizable')
                    ->default(false)
                    ->after('is_fleet_part')
                    ->index();



                $table->foreign('provider_id')
                    ->references('id')
                    ->on('providers');

                $table->foreign('brand_id')
                    ->references('id')
                    ->on('brands');

                $table->foreign('brand_model_id')
                    ->references('id')
                    ->on('brands_models');
            });

            Schema::create('billing_products_stocks_history', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('billing_product_id')->unsigned();
                $table->string('target')->index();
                $table->integer('target_id')->unsigned()->index();
                $table->integer('line_id')->unsigned()->index();
                $table->decimal('qty', 10, 2);
                $table->decimal('price', 10, 2)->nullable();
                
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('billing_product_id')
                    ->references('id')
                    ->on('billing_products');
            });

            Schema::table('customers', function (Blueprint $table) {
                $table->text('custom_billing_items')
                    ->nullable()
                    ->after('enabled_pudo_providers');
            });

            Schema::table('purchase_invoices_lines', function (Blueprint $table) {
                $table->integer('billing_product_id')
                    ->unsigned()
                    ->nullable();

                $table->foreign('billing_product_id')
                    ->references('id')
                    ->on('billing_products');
            });

            Schema::table('invoices_lines', function (Blueprint $table) {
                $table->integer('billing_product_id')
                    ->unsigned()
                    ->nullable()
                    ->after('key');

                $table->foreign('billing_product_id')
                    ->references('id')
                    ->on('billing_products');
            });

            $fleetParts = Part::get();
            foreach ($fleetParts as $part) {
                $billingProduct = new Item;
                unset($billingProduct->api_key);
                $billingProduct->source = config('app.source');
                $billingProduct->reference = $part->reference;
                $billingProduct->name = $part->name;
                $billingProduct->obs = $part->obs;
                $billingProduct->provider_id = $part->provider_id;
                $billingProduct->unity = 'un';
                $billingProduct->tax_rate = '23';
                $billingProduct->has_stock = true;
                $billingProduct->is_fleet_part = true;
                $billingProduct->save();

                if ($part->stock_total > 0.00) {
                    ItemStockHistory::setInitial($billingProduct, $part->stock_total, $part->cost_price ?? 0.00);
                }
            }
        }

        if (!Schema::hasColumn('shipments_packs_dimensions', 'pack_no')) {
            Schema::table('shipments_packs_dimensions', function (Blueprint $table) {
                $table->smallInteger('pack_no')
                    ->unsigned()
                    ->nullable()
                    ->after('description');
            });
        }

        // FIX SHIPPING EXPENSES IDS
        $shippingExpenses = ShippingExpense::filterSource()->get();
        foreach ($shippingExpenses as $expense) {
            if ($expense->vat_rate_global) {
                $vatRate = VatRate::getByCode($expense->vat_rate_global);
                if ($vatRate) {
                    $expense->vat_rate_global = $vatRate->id;
                }
            }

            $vatRatesArr = $expense->vat_rate_arr;
            if (!empty($vatRatesArr)) {
                foreach ($vatRatesArr as &$vatRate) {
                    if (empty($vatRate)) {
                        continue;
                    }

                    $vatRateAux = VatRate::getByCode($vatRate);
                    if ($vatRateAux) {
                        $vatRate = $vatRateAux->id;
                    }
                }
            }

            $expense->vat_rate_arr = $vatRatesArr;
            $expense->save();
        }
        //--

        /**
         * Ecommerce gateways
         */
        if (!Schema::hasTable('customers_ecommerce_gateways')) {
            Schema::create('customers_ecommerce_gateways', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('customer_id')->unsigned();
                $table->string('name');
                $table->string('method')->nullable();
                $table->string('endpoint')->nullable();
                $table->string('user')->nullable();
                $table->string('password')->nullable();
                $table->string('key')->nullable();
                $table->string('secret')->nullable();
                $table->text('settings')->nullable();
                
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('shipments', 'ecommerce_gateway_id')) {
            Schema::table('shipments', function (Blueprint $table) {
                $table->integer('ecommerce_gateway_id')
                    ->unsigned()
                    ->nullable()
                    ->index()
                    ->after('submited_at');

                $table->string('ecommerce_gateway_order_code')
                    ->nullable()
                    ->index()
                    ->after('ecommerce_gateway_id');

                $table->string('ecommerce_gateway_fullfillment_code')
                    ->nullable()
                    ->index()
                    ->after('ecommerce_gateway_order_code');
            });
        }
        /**-- */

        /**
         * Routes groups
         */
        if (!Schema::hasTable('routes_groups')) {
            Schema::create('routes_groups', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('code');
                $table->string('name');
                
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::table('routes', function (Blueprint $table) {
                $table->integer('route_group_id')
                    ->unsigned()
                    ->nullable()
                    ->after('source');
            });
        }
        /**-- */

        if (!Schema::hasColumn('shipping_expenses', 'billing_item_id')) {
            Schema::table('shipping_expenses', function (Blueprint $table) {
                $table->integer('billing_item_id')
                    ->unsigned()
                    ->nullable()
                    ->index()
                    ->after('source');
            });

            Schema::table('shipments_assigned_expenses', function (Blueprint $table) {
                $table->integer('billing_item_id')
                    ->unsigned()
                    ->nullable()
                    ->index()
                    ->after('provider_id');
            });
        }

        if (!Schema::hasColumn('shipping_expenses', 'discount_arr')) {
            Schema::table('shipping_expenses', function (Blueprint $table) {
                $table->text('discount_arr')
                    ->nullable()
                    ->after('every_arr');
            });
        }

        if (!Schema::hasColumn('providers', 'custom_expenses')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->text('custom_expenses')
                    ->nullable()
                    ->after('expenses_delivery');
            });

            Schema::table('shipments', function (Blueprint $table) {
                $table->decimal('cost_fuel_price', 10, 2)
                    ->nullable()
                    ->after('cost_expenses_price');

                $table->decimal('cost_fuel_tax', 10, 2)
                    ->nullable()
                    ->after('cost_fuel_price');
            });

            // Fix field length
            \DB::statement('ALTER TABLE services_volumetric_factor CHANGE COLUMN zone zone VARCHAR(255);');
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
