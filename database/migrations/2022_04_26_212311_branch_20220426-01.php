<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Branch2022042601 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments_traceability', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments_traceability', 'barcode')) {
                $table->string('barcode', 50)
                    ->nullable()
                    ->after('volume');
            }
        });

        $webserviceMethod = \App\Models\WebserviceMethod::firstOrNew([
            'method' => 'sending'
        ]);

        $webserviceMethod->method  = 'sending';
        $webserviceMethod->name    = 'Sending';
        $webserviceMethod->sources = '["'.config('app.source').'"]';
        $webserviceMethod->enabled = 1;
        $webserviceMethod->save();

        DB::statement("UPDATE services SET priority_level=null, priority_color=null WHERE priority_level=0");

        Schema::table('services', function (Blueprint $table) {

            if (!Schema::hasColumn('services', 'vat_rate')) {
                $table->string('vat_rate', 15)
                    ->nullable()
                    ->after('unity');
            }

            if (!Schema::hasColumn('services', 'assigned_intercity_service_id')) {
                $table->integer('assigned_intercity_service_id')
                    ->unsigned()
                    ->nullable()
                    ->after('assigned_service_id');
            }

            if (!Schema::hasColumn('services', 'zones_transit_max')) {
                $table->text('zones_transit_max')
                    ->nullable()
                    ->after('zones_provider');
            }

            if (!Schema::hasColumn('services', 'zones_transit')) {
                $table->text('zones_transit')
                    ->nullable()
                    ->after('zones_provider');
            }

            if (!Schema::hasColumn('services', 'delivery_weekdays')) {
                $table->string('delivery_weekdays', 255)
                    ->nullable()
                    ->after('week_days');
            }

            if (!Schema::hasColumn('services', 'pickup_weekdays')) {
                $table->string('pickup_weekdays', 255)
                    ->nullable()
                    ->after('week_days');
            }

            if (!Schema::hasColumn('services', 'transit_time_max')) {
                $table->decimal('transit_time_max', 10, 2)
                    ->nullable()
                    ->after('transit_time');
            }

            if (!Schema::hasColumn('services', 'allow_saturday')) {
                $table->boolean('allow_saturday')
                    ->default(0)
                    ->after('allow_kms');
            }

            if (!Schema::hasColumn('services', 'allow_pudos')) {
                $table->boolean('allow_pudos')
                    ->default(0)
                    ->after('allow_kms');
            }

            if (!Schema::hasColumn('services', 'allow_out_standard')) {
                $table->boolean('allow_out_standard')
                    ->default(1)
                    ->after('allow_kms');
            }

            if (!Schema::hasColumn('services', 'allow_pallets')) {
                $table->boolean('allow_pallets')
                    ->default(1)
                    ->after('allow_kms');
            }

            if (!Schema::hasColumn('services', 'allow_boxes')) {
                $table->boolean('allow_boxes')
                    ->default(1)
                    ->after('allow_kms');
            }

            if (!Schema::hasColumn('services', 'allow_docs')) {
                $table->boolean('allow_docs')
                    ->default(1)
                    ->after('allow_kms');
            }

            if (!Schema::hasColumn('services', 'allow_cod')) {
                $table->boolean('allow_cod')
                    ->default(1)
                    ->after('allow_kms');
            }

            if (!Schema::hasColumn('services', 'min_volumes')) {
                $table->integer('min_volumes')
                    ->nullable()
                    ->after('mapping_zones');
            }

            if (!Schema::hasColumn('services', 'max_dims')) {
                $table->decimal('max_dims', 10, 2)
                    ->nullable()
                    ->after('max_volumes');
            }

            if (!Schema::hasColumn('services', 'min_weight')) {
                $table->decimal('min_weight', 10, 2)
                    ->nullable()
                    ->after('max_volumes');
            }

            if (!Schema::hasColumn('services', 'filename')) {
                $table->string('filename')
                    ->nullable()
                    ->after('webservice_mapping');
            }

            if (!Schema::hasColumn('services', 'filepath')) {
                $table->string('filepath')
                    ->nullable()
                    ->after('webservice_mapping');
            }

            if (!Schema::hasColumn('services', 'description')) {
                $table->text('description')
                    ->nullable()
                    ->after('webservice_mapping');
            }

            if (!Schema::hasColumn('services', 'description2')) {
                $table->text('description2')
                    ->nullable()
                    ->after('webservice_mapping');
            }

        });

        if (Schema::hasColumn('services', 'week_days')) {
            DB::statement('UPDATE services SET pickup_weekdays=week_days, delivery_weekdays=\'["1","2","3","4","5"]\'');
        }

        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'week_days')) {
                $table->dropColumn('week_days');
            }
        });

        Schema::table('shipments_history', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments_history', 'vehicle')) {
                $table->text('vehicle', 25)
                    ->nullable()
                    ->after('city');
            }
        });

        Schema::table('shipments_packs_dimensions', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments_packs_dimensions', 'barcode')) {
                $table->string('barcode')
                    ->nullable()
                    ->after('description');
            }
        });

        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'operation_zip_codes')) {
                $table->text('operation_zip_codes')
                    ->nullable()
                    ->after('agency_id');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'prices_tables')) {
                $table->text('prices_tables')
                    ->nullable()
                    ->after('price_table_id');
            }
        });

        Schema::table('shipping_expenses', function (Blueprint $table) {

            if (!Schema::hasColumn('shipping_expenses', 'end_at')) {
                $table->date('end_at')
                    ->nullable()
                    ->after('customer_customization');
            }

            if (!Schema::hasColumn('shipping_expenses', 'start_at')) {
                $table->date('start_at')
                    ->nullable()
                    ->after('customer_customization');
            }
        });

        try {
            Schema::table('shipments', function (Blueprint $table) {
                if (Schema::hasColumn('shipments', 'fuel_tax')) {
                    DB::statement("ALTER TABLE shipments MODIFY COLUMN vat_rate DECIMAL(10,2) AFTER fuel_tax");
                }

             });

             DB::statement("ALTER TABLE shipments MODIFY COLUMN total_expenses_cost DECIMAL(10,2) AFTER submited_at");
             DB::statement("ALTER TABLE shipments MODIFY COLUMN cost_price DECIMAL(10,2) AFTER submited_at");
             DB::statement("ALTER TABLE shipments MODIFY COLUMN total_expenses DECIMAL(10,2) AFTER submited_at");
             DB::statement("ALTER TABLE shipments MODIFY COLUMN total_price DECIMAL(10,2) AFTER submited_at");
             DB::statement("ALTER TABLE shipments MODIFY COLUMN base_price DECIMAL(10,2) AFTER submited_at");
             DB::statement("ALTER TABLE shipments MODIFY COLUMN zone VARCHAR(5) AFTER submited_at");
             DB::statement("ALTER TABLE shipments MODIFY COLUMN custom_fields VARCHAR(500) AFTER optional_fields");
             DB::statement("ALTER TABLE customers MODIFY COLUMN shipping_services_notify VARCHAR(255)");

        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        Schema::table('shipments', function (Blueprint $table) {

            if (Schema::hasColumn('shipments', 'total_insurance_price')) {
                Schema::table('shipments', function (Blueprint $table) {
                    $table->renameColumn('total_insurance_price', 'insurance_price');
                });
            }

            if (!Schema::hasColumn('shipments', 'taxable_weight')) {
                $table->decimal('taxable_weight', 10, 2)
                    ->nullable()
                    ->after('provider_weight');
            }

            if (!Schema::hasColumn('shipments', 'fuel_tax')) {
                $table->decimal('fuel_tax', 10, 2)
                    ->nullable()
                    ->after('total_expenses');
            }

            if (!Schema::hasColumn('shipments', 'fuel_price')) {
                $table->decimal('fuel_price', 10, 2)
                    ->nullable()
                    ->after('total_expenses');
            }

            if (!Schema::hasColumn('shipments', 'extra_weight')) {
                $table->decimal('extra_weight', 10, 2)
                    ->nullable()
                    ->after('weight');
            }

            //BILLING FIELDS
            if (!Schema::hasColumn('shipments', 'shipping_base_price')) {
                $table->decimal('shipping_base_price', 10, 2)
                    ->nullable()
                    ->after('price_kg_unity');
            }

            if (!Schema::hasColumn('shipments', 'expenses_price')) {
                $table->decimal('expenses_price', 10, 2)
                    ->nullable()
                    ->after('price_kg_unity');
            }

            if (!Schema::hasColumn('shipments', 'shipping_price')) {
                $table->decimal('shipping_price', 10, 2)
                    ->nullable()
                    ->after('price_kg_unity');
            }


            if (!Schema::hasColumn('shipments', 'billing_zone')) {
                $table->string('billing_zone', 10)
                    ->nullable()
                    ->after('vat_rate');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {

            //cost fields
            if (!Schema::hasColumn('shipments', 'cost_billing_zone')) {
                $table->string('cost_billing_zone', 5)
                    ->nullable()
                    ->after('billing_zone');
            }

            if (!Schema::hasColumn('shipments', 'cost_billing_total')) {
                $table->decimal('cost_billing_total', 10, 2)
                    ->nullable()
                    ->after('billing_zone');
            }

            if (!Schema::hasColumn('shipments', 'cost_billing_vat')) {
                $table->decimal('cost_billing_vat', 10, 2)
                    ->nullable()
                    ->after('billing_zone');
            }

            if (!Schema::hasColumn('shipments', 'cost_billing_subtotal')) {
                $table->decimal('cost_billing_subtotal', 10, 2)
                    ->nullable()
                    ->after('billing_zone');
            }

            if (!Schema::hasColumn('shipments', 'cost_expenses_price')) {
                $table->decimal('cost_expenses_price', 10, 2)
                    ->nullable()
                    ->after('billing_zone');
            }

            if (!Schema::hasColumn('shipments', 'cost_shipping_price')) {
                $table->decimal('cost_shipping_price', 10, 2)
                    ->nullable()
                    ->after('billing_zone');
            }

            if (!Schema::hasColumn('shipments', 'cost_shipping_base_price')) {
                $table->decimal('cost_shipping_base_price', 10, 2)
                    ->nullable()
                    ->after('billing_zone');
            }

            //billing total
            if (!Schema::hasColumn('shipments', 'billing_total')) {
                $table->decimal('billing_total', 10, 2)
                    ->nullable()
                    ->after('vat_rate');
            }

            if (!Schema::hasColumn('shipments', 'billing_vat')) {
                $table->decimal('billing_vat', 10, 2)
                    ->nullable()
                    ->after('vat_rate');
            }

            if (!Schema::hasColumn('shipments', 'billing_subtotal')) {
                $table->decimal('billing_subtotal', 10, 2)
                    ->nullable()
                    ->after('vat_rate');
            }

            if (!Schema::hasColumn('shipments', 'billing_item')) {
                $table->string('billing_item', 15)
                    ->nullable()
                    ->after('vat_rate');
            }

            if (!Schema::hasColumn('shipments', 'vat_rate_id')) {
                $table->string('vat_rate_id', 3)
                    ->nullable()
                    ->after('vat_rate');
            }

            if (Schema::hasColumn('shipments', 'obs2')) {
                $table->dropColumn('obs2');
            }
            if (Schema::hasColumn('shipments', 'delivery_price')) {
                $table->dropColumn('delivery_price');
            }
            if (Schema::hasColumn('shipments', 'total_charge_price')) {
                $table->dropColumn('total_charge_price');
            }
            if (Schema::hasColumn('shipments', 'return_type')) {
                $table->dropColumn('return_type');
            }
            if (Schema::hasColumn('shipments', 'return_tracking_code')) {
                $table->dropColumn('return_tracking_code');
            }
            if (Schema::hasColumn('shipments', 'recanalize_tracking_code')) {
                $table->dropColumn('recanalize_tracking_code');
            }
            if (Schema::hasColumn('shipments', 'linked_tracking_code')) {
                $table->dropColumn('linked_tracking_code');
            }
            if (Schema::hasColumn('shipments', 'collection_tracking_code')) {
                $table->dropColumn('collection_tracking_code');
            }
            if (Schema::hasColumn('shipments', 'devolution_tracking_code')) {
                $table->dropColumn('devolution_tracking_code');
            }
            if (Schema::hasColumn('shipments', 'last_provider_id')) {
                $table->dropForeign('shipments_last_provider_id_foreign');
            }
            if (Schema::hasColumn('shipments', 'last_provider_id')) {
                $table->dropColumn('last_provider_id');
            }
            if (Schema::hasColumn('shipments', 'last_webservice_method')) {
                $table->dropColumn('last_webservice_method');
            }
            if (Schema::hasColumn('shipments', 'last_submited_at')) {
                $table->dropColumn('last_submited_at');
            }
            if (Schema::hasColumn('shipments', 'last_provider_tracking_code')) {
                $table->dropColumn('last_provider_tracking_code');
            }
            if (Schema::hasColumn('shipments', 'last_provider_cargo_agency')) {
                $table->dropColumn('last_provider_cargo_agency');
            }
            if (Schema::hasColumn('shipments', 'last_provider_sender_agency')) {
                $table->dropColumn('last_provider_sender_agency');
            }

            if (Schema::hasColumn('shipments', 'last_provider_recipient_agency')) {
                $table->dropColumn('last_provider_recipient_agency');
            }

        });

        \App\Models\Shipment::where('sender_id', 0)->update(['sender_id' => null]);

        DB::statement("ALTER TABLE shipments MODIFY COLUMN insurance_price DECIMAL(10,2) AFTER goods_price");



        //atualiza taxas combustivel
        if(Setting::get("fuel_tax")) {

            Setting::set('fuel_tax_invoice_detail', 1);
            Setting::save();

            $tax = Setting::get("fuel_tax");
            $taxPercent = $tax/100;
            $date = date('Y-m').'-01';

            DB::statement("update shipments set fuel_tax=".$tax." where billing_date>='".$date."' and invoice_id is null");
            DB::statement("update shipments set fuel_price=((COALESCE(total_price,0) + COALESCE(total_expenses,0))*".$taxPercent.") where billing_date >= '".$date."' and invoice_id is null");
        }

        //migra dados para os novos campos de base de dados
        DB::statement("update shipments set shipping_base_price = base_price, shipping_price=total_price, expenses_price=total_expenses, billing_zone=zone, billing_subtotal=(coalesce(total_price,0)+coalesce(total_expenses,0)+coalesce(fuel_price,0))");
        DB::statement("update shipments set cost_shipping_price=cost_price, cost_expenses_price=total_expenses_cost, cost_billing_zone=zone, cost_billing_subtotal=(coalesce(cost_price,0)+coalesce(total_expenses_cost,0))");
        DB::statement("update shipments set reference=null where reference=''");
        DB::statement("update shipments set reference2=null where reference2=''");
        DB::statement("update shipments set reference3=null where reference3=''");
        DB::statement("update shipments set sender_id=null where sender_id=0");
        DB::statement("update shipments set sender_state=null where sender_state=''");
        DB::statement("update shipments set sender_attn=null where sender_attn=''");
        DB::statement("update shipments set sender_vat=null where sender_vat=''");
        DB::statement("update shipments set sender_phone=null where sender_phone=''");

        DB::statement("update shipments set recipient_state=null where recipient_state=''");
        DB::statement("update shipments set recipient_attn=null where recipient_attn=''");
        DB::statement("update shipments set recipient_vat=null where recipient_vat=''");
        DB::statement("update shipments set recipient_phone=null where recipient_phone=''");
        DB::statement("update shipments set recipient_email=null where recipient_email=''");
        DB::statement("update shipments set incoterm=null where incoterm=''");
        DB::statement("update shipments set start_hour=null where start_hour=''");
        DB::statement("update shipments set end_hour=null where end_hour=''");
        DB::statement("update shipments set obs=null where obs=''");
        DB::statement("update shipments set obs_delivery=null where obs_delivery=''");
        DB::statement("update shipments set obs_internal=null where obs_internal=''");
        DB::statement("update shipments set vehicle=null where vehicle=''");
        DB::statement("update shipments set trailer=null where trailer=''");
        DB::statement("update shipments set provider_cargo_agency=null where provider_cargo_agency=''");
        DB::statement("update shipments set provider_sender_agency=null where provider_sender_agency=''");


        if (!Schema::hasTable('services_groups')) {
            Schema::create('services_groups', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->string('source')->index();
                $table->string('code', '25')->index();
                $table->string('name');
                $table->string('icon', '25')->nullable();
                $table->smallInteger('sort')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }


        $existsServiceGroups = \App\Models\ServiceGroup::first();
        if(!$existsServiceGroups) {
            $groups = $this->getServiceGroups();
            $insertArr = [];
            $datetime = date('Y-m-d H:i:s');
            $sort = 1;
            foreach ($groups as $key => $value) {

                $icon = $this->getServiceGroupsIcon($key);

                $insertArr[] = [
                    'source' => config('app.source'),
                    'code'   => $key,
                    'icon'   => $icon,
                    'name'   => $value,
                    'sort'   => $sort,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ];

                $sort++;
            }

            \App\Models\ServiceGroup::insert($insertArr);
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

    public function getServiceGroups() {
        return [
            'weight'          => 'Serviços taxados pelo PESO',
            'pallet'          => 'Serviços por Nº de PALETES',
            'volume'          => 'Serviços taxados por nº VOLUMES/UNIDADES',
            'hours'           => 'Serviços taxados pelo número HORAS',
            'costpercent'     => 'Serviços taxados por PERCENTAGEM DE CUSTO',
            'pallet_weight'   => 'Serviços de PALETES A PESO',
            'pallet_nacional' => 'Serviços de PALETES NACIONAL',
            'pallet_iberico'  => 'Serviços de PALETES IBÉRICOS',
            'collection'      => 'Serviços de RECOLHA ou DEVOLUÇÃO',
            'local'           => 'Serviços LOCAIS/REGIONAIS',
            'nacional'        => 'Serviços NACIONAIS',
            'iberico'         => 'Serviços IBÉRICOS',
            'internacional'   => 'Serviços INTERNACIONAIS',
            'int_economy'     => 'Serviços INTERNACIONAIS ECONOMY',
            'int_terrestrial' => 'Serviços INTERNACIONAIS (Terrestres)',
            'int_aerial'      => 'Serviços INTERNACIONAIS (Aéreos)',
            'm3'              => 'Serviços taxados pelos METROS CÚBICOS',
            'km'              => 'Serviços taxados pelo total de KM',
            'islands'         => 'Serviços para as ILHAS',
            'islands-air'     => 'Serviços para as ILHAS (Aéreo)',
            'islands-mar'     => 'Serviços para as ILHAS (Marítimo)',
            'vip'             => 'Serviços VIP',
            'urgent'          => 'Serviços URGENTES',
            'normal'          => 'Serviços NORMAIS',
            'mota'            => 'Serviços de MOTA',
            'carro'           => 'Serviços de CARRO',
            'furgao'          => 'Serviços de CARRINHA',
            'furgao3500'      => 'Serviços de FURGÃO',
            'import'          => 'Serviços de IMPORTAÇÃO',
            'export'          => 'Serviços de EXPORTAÇÃO',
            'other'           => 'Outros serviços',
        ];
    }

    public function getServiceGroupsIcon($group) {

        $groups = [
            'weight'          => 'fa fa-weight-hanging',
            'pallet'          => 'fa fa-pallet',
            'hours'           => 'fa fa-clock-alt',
            'costpercent'     => 'fa fa-percent',
            'volume'          => 'fa fa-boxes',
            'pallet_weight'   => 'fa fa-pallet',
            'pallet_nacional' => 'fa fa-pallet',
            'pallet_iberico'  => 'fa fa-pallet',
            'collection'      => 'fa fa-undo-alt',
            'local'           => 'fa fa-store-alt',
            'nacional'        => 'fa fa-weight-hanging',
            'iberico'         => 'fa fa-weight-hanging',
            'internacional'   => 'fa fa-globe-africa',
            'int_economy'     => 'fa fa-globe-europe',
            'int_terrestrial' => 'fa fa-truck-moving',
            'int_aerial'      => 'fa fa-plane',
            'm3'              => 'fa fa-cube',
            'km'              => 'fa fa-road',
            'islands'         => 'fa fa-water',
            'islands-air'     => 'fa fa-plane',
            'islands-mar'     => 'fa fa-ship',
            'vip'             => 'fa fa-business-time',
            'urgent'          => 'fa fa-stopwatch',
            'normal'          => 'fa fa-clock',
            'other'           => 'fa fa-weight-hanging',
        ];

        return @$groups[$group];
    }
}
